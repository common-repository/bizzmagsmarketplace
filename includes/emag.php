<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
namespace BizzmagsMarketplace;
use \stdClass;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class emag{
    
    public $config;
    public $config_front;

    public function __construct(){
        $this->loadConfig();
    }
    public function loadConfig()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $this->config=new stdClass;
        $this->config_front=new stdClass;
        $sql=$wpdb->prepare("select * from ".$wpdb->prefix."bizzmags_config where 1");
        $results=$wpdb->get_results($sql);
        if($results){
            foreach($results as $r){
                if(!isset($this->config->{$r->config_name}))
                    $this->config->{$r->config_name}=$r->config_value;
                if(!isset($this->config_front->{$r->config_name}) && $r->show_front==1)
                    $this->config_front->{$r->config_name}=$r->config_value;
            }
        }
    }
    function is_woocommerce_activated(){
        if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
    }
    function saveEmagApiCredentials()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_admin_referer( 'bizzmagsmarketplace_save_api_credentials' );
        $emag_api_endpoint=isset($_POST['emag_api_endpoint'])?sanitize_text_field($_POST['emag_api_endpoint']):"";
        $api_user=isset($_POST['api_user'])?sanitize_text_field($_POST['api_user']):"";
        $api_password=isset($_POST['api_password'])?sanitize_text_field($_POST['api_password']):"";
        $this->updateConfigValue("emag_categ_cnt",0);

        if (filter_var($emag_api_endpoint, FILTER_VALIDATE_URL))
            $emag_api_endpoint = rtrim($emag_api_endpoint, '/');
        else
        {
             $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_config set config_value=%s where config_name='emag_api_endpoint'",array($emag_api_endpoint));
            $wpdb->query($sql);
            $this->loadConfig();
            $emag_api_endpoint="";
        }
        if($api_user!="" && $api_password!="" && $emag_api_endpoint!="")
        {
            $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_config set config_value=%s where config_name='emag_api_user'",array($api_user));
            $wpdb->query($sql);
            $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_config set config_value=%s where config_name='emag_api_password'",array($api_password));
            $wpdb->query($sql);
            $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_config set config_value=%s where config_name='emag_api_endpoint'",array($emag_api_endpoint));
            $wpdb->query($sql);
            $this->loadConfig();
            $check_categ_count=$this->doEmagRequest("category","count");
            if($check_categ_count=="")
                return array('status'=>'error','msg'=>__("The API Endpoint does not seam right","bizzmagsmarketplace"));
            $have_errors=$this->getResultError($check_categ_count);
            if($have_errors!="")
                return array('status'=>'error','msg'=>$have_errors);
            else
            {
                $cat_count=0;
                if(isset($check_categ_count->results) && is_object($check_categ_count->results) && isset($check_categ_count->results->noOfItems));
                    $cat_count=$check_categ_count->results->noOfItems;
                {
                    $this->saveLog(__("Saved API configuration","bizzmagsmarketplace"));
                    $this->updateConfigValue("emag_categ_cnt",$cat_count);
                    return array('status'=>'updated','msg'=>array(__("Emag API credentials saved with success","bizzmagsmarketplace")." ".__("Counted","bizzmagsmarketplace")." ".$cat_count." ".__("categories","bizzmagsmarketplace")));
                }
            }
        }
        else
            return array('status'=>'error','msg'=>__("Emag API Endoint, API user and password are required","bizzmagsmarketplace"));
    }
    public function getResultError($result)
    {
        //&& isset($result->isError) && $result->isError == true // they don't use true always...
        if (is_object($result) && isset($result->messages) && is_array($result->messages)) {
            foreach ($result->messages as $msg) {
                $this->saveLog($msg);
            }
            if(is_array($result->messages) && count($result->messages)==0)
                return "";
            return $result->messages;
        } elseif (is_object($result) && isset($result->message) && is_string($result->message)) {
            if ($result->message != '') {
                $this->saveLog($result->message);
            }

            return $result->message;
        }

        return '';
    }
    public function returnResultError($message="")
    {
        $result=new stdClass;
        $result->isError=1;
        $result->messages=array($message);
    }
    public function updateConfigValue($name="", $value="")
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $name=sanitize_text_field($name);
        if($name=='emag_rank_log')
            $value=sanitize_textarea_field($value);
        else
            $value=sanitize_text_field($value);
        if($name!="")
        {
            $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_config set config_value=%s where config_name=%s",array($value,$name));
            $wpdb->query($sql);
            $this->config->$name=$value;
        }
    }
    public function getConfigValue($name="")
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $name=sanitize_text_field($name);
        if($name!="")
        {
            $sql=$wpdb->prepare("select config_value from ".$wpdb->prefix."bizzmags_config where config_name=%s",array($name));
            $result=$wpdb->get_row($sql);
            if(isset($result->config_value))
                return $result->config_value;
        }
    }
    public function needToWait()
    {
        while (true) {
            $api_start = (int) $this->getConfigValue('emag_api_start');
            $diff = time() - $api_start;

            if ($diff < $this->config->emag_api_req_sec) {
                $sleep = $this->config->emag_api_req_sec - $diff;
                if ($sleep > 0) {
                    sleep($sleep);
                }
            } else {
                // Update the start time before exiting the loop
                $this->updateConfigValue('emag_api_start', time());
                return true;
            }
        }
    }
    
    public function doEmagRequest($resource="",$action="",$params="", $request_type="GET")
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $request_type=sanitize_text_field($request_type);
        $resource=sanitize_text_field($resource);
        $action=sanitize_text_field($action);
        $url="";
        $url=$this->config->emag_api_endpoint."/".$resource."/".$action;
        if($url=="")
            return $this->returnResultError(__("Seams that the API endpoint is not set","bizzmagsmarketplace"));
        $token=base64_encode($this->config->emag_api_user.":".$this->config->emag_api_password);
        $body="";

        $this->needToWait();
        
        if($request_type=="POST")
        {
            $response = wp_remote_post($url, array(
                'headers'     => array('Authorization' => 'Basic ' . $token),
                'body'        => $params,
                'method'      => 'POST',
                'data_format' => 'body',
                'timeout'     => 15,
            ));
        }
        else if($request_type=="PATCH")
        {
            $response = wp_remote_post($url, array(
                'headers'     => array('Authorization' => 'Basic ' . $token, 'Content-Type' => 'application/json'),
                'body'        => wp_json_encode($params),
                'method'      => 'PATCH',
                'timeout'     => 15,
            ));
        }
        else
        {
            $arguments=array('method' => $request_type,'body' => $params, 'headers'=>array('Authorization' => 'Basic ' . $token), 'timeout' => 15);
            $response = wp_remote_post( $url, $arguments );
        }
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return $this->returnResultError($error_message);
        }
        else
        {
            $body = wp_remote_retrieve_body( $response );
            $result=json_decode($body);
            return $result;
        }
    }
    public function saveLog($log="")
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if($log!="")
        {
            $user_id=get_current_user_id();
            $log=sanitize_textarea_field($log);
            $sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_logs set user_id=%d, log=%s, mdate=%s",array($user_id,$log,microtime(true)));
            $wpdb->query($sql);

            $sql=$wpdb->prepare("delete from ".$wpdb->prefix."bizzmags_emag_mktpl_logs where mdate<%s",strtotime(gmdate("Y-m-d")." -60 days"));
            $wpdb->query($sql);
        }
    }
    public function getCategoriesCountAllowed()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sql=$wpdb->prepare("select count(*) as total from ".$wpdb->prefix."bizzmags_emag_mktpl_category where is_allowed=1");
        $result=$wpdb->get_row($sql);
        if(isset($result->total))
            return $result->total;
    }
    public function getCategoriesCount($type="")
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $type=sanitize_text_field($type);
        if($type=="importing")
            $sql=$wpdb->prepare("select count(*) as total from ".$wpdb->prefix."bizzmags_emag_mktpl_category where imported=1");
        else
            $sql=$wpdb->prepare("select count(*) as total from ".$wpdb->prefix."bizzmags_emag_mktpl_category where 1");
        $result=$wpdb->get_row($sql);
        if(isset($result->total))
            return $result->total;
        return 0;
    }
    public function getBuyButtonProductsCount($bb1=1,$bb2=1)
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $bb1+=0;
        $bb2+=0;
        $sql=$wpdb->prepare("select count(*) as total from ".$wpdb->prefix."bizzmags_emag_mktpl_push_product where buy_button_rank>=%d and buy_button_rank<=%d and stock>0",array($bb1,$bb2));
        $result=$wpdb->get_row($sql);
        if(isset($result->total))
            return $result->total;
    }
    
    public function addActionSchedulerTask($task="", $args=array(), $group="")
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if($task!="")
        {
            if ( ! class_exists( 'ActionScheduler_Versions', false ) )
                require_once(WC()->plugin_path()."/packages/action-scheduler/action-scheduler.php");
            if(!as_has_scheduled_action($task,$args,$group))
            {
                $action_added=true;
                $action_id=as_enqueue_async_action( $task, $args, $group, true, 0);
                if(!$action_id)
                    $action_added=false;
                return $action_added;
            }
        }
        return false;
    }
    public function importEmagCategoriesHook()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_admin_referer( 'bizzmagsmarketplace_import_emag_categories' );
        $emag_cat_import_cancel=isset($_POST['emag_cat_import_cancel'])?(int)$_POST['emag_cat_import_cancel']:0;
        $emag_delete_categories=isset($_POST['emag_delete_categories'])?(int)$_POST['emag_delete_categories']:0;
        if($emag_cat_import_cancel==1)
        {
            $this->updateConfigValue("emag_cat_import_started",0);
            $this->saveLog(__("Cancelled the Emag categories import process","bizzmagsmarketplace"));
            $this->config->emag_cat_import_started=0;
            return array('status'=>'updated','msg'=>__("Cancelled the Emag categories import process","bizzmagsmarketplace"));
        }
        else if($emag_delete_categories==1)
        {
            $this->deleteImportedEmagCategories();
            $this->updateConfigValue("emag_cat_import_started",0);
            $this->saveLog(__("Deleted the imported Emag categories","bizzmagsmarketplace"));
            $this->config->emag_cat_import_started=0;
            return array('status'=>'updated','msg'=>__("Deleted the imported Emag categories","bizzmagsmarketplace"));
        }
        else if(is_file(WC()->plugin_path()."/packages/action-scheduler/action-scheduler.php"))
        {
            $action_added=$this->addActionSchedulerTask("bizzmagsmarketplace_import_categories_hook",array(),"bizzmagsmarketplace");
            if($action_added)
            {
                $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_emag_mktpl_category set imported=0 where 1");
                $wpdb->query($sql);
                $this->saveLog(__("Triggered the Emag categories import process","bizzmagsmarketplace"));
                $this->updateConfigValue("emag_cat_import_started",1);
                $this->config->emag_cat_import_started=1;
                return array('status'=>'updated','msg'=>__("Set the import Emag categories background process, the import will start shortly","bizzmagsmarketplace"));
            }
            else
            {
                $this->saveLog(__("Error in triggering the Emag categories import process","bizzmagsmarketplace"));
                return $this->returnResultError(__("Error in setting the import Emag categories background process, please contact support","bizzmagsmarketplace"));
            }
        }
    }
    
    public function deleteImportedEmagCategories()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sql=$wpdb->prepare("truncate table ".$wpdb->prefix."bizzmags_emag_mktpl_category");
        $wpdb->query($sql);
        $sql=$wpdb->prepare("truncate table ".$wpdb->prefix."bizzmags_emag_mktpl_category_char");
        $wpdb->query($sql);
        $sql=$wpdb->prepare("truncate table ".$wpdb->prefix."bizzmags_emag_mktpl_category_char_rel");
        $wpdb->query($sql);
        $sql=$wpdb->prepare("truncate table ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam");
        $wpdb->query($sql);
        $sql=$wpdb->prepare("truncate table ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_char");
        $wpdb->query($sql);
        $sql=$wpdb->prepare("truncate table ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_rel");
        $wpdb->query($sql);
    }
    function getImportCategStatusAjax()
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if($this->getConfigValue("emag_cat_import_started")==0)
        {
            echo "1";
            return;
        }
        $import_percent=0;
        $imported=$this->getCategoriesCount("importing");
        //echo esc_html__("Import running","dropshipping-romania-avex")." ";
        if($this->config->emag_categ_cnt>0)
            $import_percent=intval(($imported*100)/$this->config->emag_categ_cnt);
        ?>
        <div class="progress-bar">
            <span class="progress-bar-fill" style="width: <?php echo esc_attr($import_percent);?>%;"></span>
        </div>
        <?php
        echo esc_html($imported)."/".esc_html($this->config->emag_categ_cnt);
    }
    public function generateEmagProductUrl($url="")
    {
        if($url!="")
        {
            $url = strtolower($url);
            $url = str_replace(", ", "-", $url);
            $url = str_replace(" ", "-", $url);
            $url = preg_replace("/[^a-z0-9\-]/", "", $url);
            return $url;
        }
        return "";
    }
    
    public function importEmagCategories()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if($this->getConfigValue("emag_cat_import_started")==1)
        {
            $check_categ_count=$this->doEmagRequest("category","count");
            $cat_count=0;
            $have_errors=$this->getResultError($check_categ_count);
            if($have_errors=="" && isset($check_categ_count->results) && is_object($check_categ_count->results) && isset($check_categ_count->results->noOfItems));
                $cat_count=$check_categ_count->results->noOfItems;
            
            if($cat_count>0)
            {
                $this->updateConfigValue("emag_categ_cnt",$cat_count);
                if(isset($check_categ_count->results->noOfPages) && $check_categ_count->results->noOfPages>0);
                {
                    for($page=1;$page<=$check_categ_count->results->noOfPages;$page++)
                    {
                        if($this->getConfigValue("emag_cat_import_started")==1)
                        {
                            $result=$this->doEmagRequest("category","read",array("currentPage"=>$page),"POST");
                            $have_errors=$this->getResultError($result);
                            if($have_errors=="" && isset($result->results) && is_array($result->results))
                            {
                                foreach($result->results as $categ)
                                {
                                    if($this->getConfigValue("emag_cat_import_started")==1)
                                    {
$sql=$wpdb->prepare("
insert into ".$wpdb->prefix."bizzmags_emag_mktpl_category set
id=%d,
name=%s,
scm_id=%d,
parent_id=%d,
is_ean_mandatory=%d,
is_warranty_mandatory=%d,
is_allowed=%d,
imported=%d,
mdate=%d
on duplicate key update
name=%s,
scm_id=%d,
parent_id=%d,
is_ean_mandatory=%d,
is_warranty_mandatory=%d,
is_allowed=%d,
imported=%d,
mdate=%d
",array(
sanitize_text_field($categ->id),
sanitize_text_field($categ->name),
sanitize_text_field($categ->scm_id),
sanitize_text_field($categ->parent_id),
sanitize_text_field($categ->is_ean_mandatory),
sanitize_text_field($categ->is_warranty_mandatory),
sanitize_text_field($categ->is_allowed),
1,
time(),
sanitize_text_field($categ->name),
sanitize_text_field($categ->scm_id),
sanitize_text_field($categ->parent_id),
sanitize_text_field($categ->is_ean_mandatory),
sanitize_text_field($categ->is_warranty_mandatory),
sanitize_text_field($categ->is_allowed),
1,
time()
));
if(!$wpdb->query($sql) && $wpdb->last_error !== '')
{
    $this->saveLog(__("Import categories db error","bizzmagsmarketplace")." [1]");
    $this->updateConfigValue("emag_cat_import_started",0);
    return false;
}
if(isset($categ->characteristics) && is_array($categ->characteristics))
{
    foreach($categ->characteristics as $char)
    {
$sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_category_char set
id=%d,
cat_id=%d,
name=%s,
type_id=%d,
display_order=%d,
is_mandatory=%d,
is_mandatory_for_mktp=%d,
allow_new_value=%d,
is_filter=%d,
tags=%s,
value_tags=%s,
mdate=%d
on duplicate key update
name=%s,
type_id=%d,
display_order=%d,
is_mandatory=%d,
is_mandatory_for_mktp=%d,
allow_new_value=%d,
is_filter=%d,
tags=%s,
value_tags=%s,
mdate=%d
",array(
sanitize_text_field($char->id),
sanitize_text_field($categ->id),
sanitize_text_field($char->name),
sanitize_text_field($char->type_id),
sanitize_text_field($char->display_order),
sanitize_text_field($char->is_mandatory),
sanitize_text_field($char->is_mandatory_for_mktp),
sanitize_text_field($char->allow_new_value),
sanitize_text_field($char->is_filter),
sanitize_text_field($char->tags),
sanitize_text_field($char->value_tags),
time(),
sanitize_text_field($char->name),
sanitize_text_field($char->type_id),
sanitize_text_field($char->display_order),
sanitize_text_field($char->is_mandatory),
sanitize_text_field($char->is_mandatory_for_mktp),
sanitize_text_field($char->allow_new_value),
sanitize_text_field($char->is_filter),
sanitize_text_field($char->tags),
sanitize_text_field($char->value_tags),
time()
));
if(!$wpdb->query($sql) && $wpdb->last_error !== '')
{
    $this->saveLog(__("Import categories db error","bizzmagsmarketplace")." [2]");
    $this->updateConfigValue("emag_cat_import_started",0);
    return false;
}
$sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_category_char_rel set
cat_id=%d,
char_id=%d,
mdate=%d
on duplicate key update
mdate=%d
",array(
sanitize_text_field($categ->id),
sanitize_text_field($char->id),
time(),
time()
));
if(!$wpdb->query($sql) && $wpdb->last_error !== '')
{
    $this->saveLog(__("Import categories db error","bizzmagsmarketplace")." [3]");
    $this->updateConfigValue("emag_cat_import_started",0);
    return false;
}
    }
}
if(isset($categ->family_types) && is_array($categ->family_types))
{
    foreach($categ->family_types as $fam)
    {
$sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam set
id=%d,
cat_id=%d,
name=%s,
mdate=%d
on duplicate key update
name=%s,
mdate=%d
",array(
sanitize_text_field($fam->id),
sanitize_text_field($categ->id),
sanitize_text_field($fam->name),
time(),
sanitize_text_field($fam->name),
time()
));
if(!$wpdb->query($sql) && $wpdb->last_error !== '')
{
    $this->saveLog(__("Import categories db error","bizzmagsmarketplace")." [4]");
    $this->updateConfigValue("emag_cat_import_started",0);
    return false;
}
$sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_rel set
cat_id=%d,
fam_id=%d,
mdate=%d
on duplicate key update
mdate=%d
",array(
sanitize_text_field($categ->id),
sanitize_text_field($fam->id),
time(),
time()
));
if(!$wpdb->query($sql) && $wpdb->last_error !== '')
{
    $this->saveLog(__("Import categories db error","bizzmagsmarketplace")." [5]");
    $this->updateConfigValue("emag_cat_import_started",0);
    return false;
}
if(isset($fam->characteristics) && is_array($fam->characteristics))
{
    foreach($fam->characteristics as $fchar)
    {
$sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_char set
fam_id=%d,
cat_id=%d,
characteristic_id=%d,
characteristic_type=%d,
characteristic_family_type_id=%d,
is_foldable=%d,
display_order=%d,
mdate=%d
on duplicate key update
characteristic_type=%d,
characteristic_family_type_id=%d,
is_foldable=%d,
display_order=%d,
mdate=%d
",array(
sanitize_text_field($fam->id),
sanitize_text_field($categ->id),
sanitize_text_field($fchar->characteristic_id),
sanitize_text_field($fchar->characteristic_type),
sanitize_text_field($fchar->characteristic_family_type_id),
sanitize_text_field($fchar->is_foldable),
sanitize_text_field($fchar->display_order),
time(),
sanitize_text_field($fchar->characteristic_type),
sanitize_text_field($fchar->characteristic_family_type_id),
sanitize_text_field($fchar->is_foldable),
sanitize_text_field($fchar->display_order),
time()
));
if(!$wpdb->query($sql) && $wpdb->last_error !== '')
{
    $this->saveLog(__("Import categories db error","bizzmagsmarketplace")." [6]");
    $this->updateConfigValue("emag_cat_import_started",0);
    return false;
}
    }
}

    }
}
                                    }
                                    else
                                        break;
                                }
                            }
                        }
                        else
                            break;
                    }
                }
            }
        }
        $this->addNameIndexOnEmagCategories();
        $this->updateConfigValue("emag_cat_import_started",0);
        $this->saveLog(__("Finished the Emag categories import process","bizzmagsmarketplace"));
    }
    public function addNameIndexOnEmagCategories()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $emag_categories=$this->getEmagCategoryHierarchy();
        if(count($emag_categories)>0)
        {
            foreach($emag_categories as $cat)
            {
                $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_emag_mktpl_category set full_path=%s where id=%d",array($cat->name,$cat->id));
                $wpdb->query($sql);
                $name_index=str_replace("> ","",strtolower($cat->name));
                $clean_index = preg_replace('/[^a-zA-Z0-9\s]/', '', $name_index);
                $clean_string = preg_replace('/\s+/', ' ', $clean_index);
                $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_emag_mktpl_category set name_index=%s where id=%d",array($clean_string,$cat->id));
                $wpdb->query($sql);
            }
        }
    }
    function getWcCategoryHierarchy($parent_id = 0, $path = '')
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $args = array(
            'taxonomy'   => 'product_cat',
            'parent'     => $parent_id,
            'hide_empty' => false,
        );

        $categories = get_terms($args);

        $category_info = array();
        
        foreach ($categories as $category)
        {
            $current_path = $path ? $path . ' > ' . $category->name : $category->name;
            $children = get_term_children($category->term_id, 'product_cat');
            if (!empty($children))
            {
                $child_info = $this->getWcCategoryHierarchy($category->term_id, $current_path);
                $category_info = array_merge($category_info, $child_info);
            }
            else
            {
                $cat=new stdClass;
                $cat->name=$current_path;
                $cat->id=$category->term_id;
                $cat->matches=array();
                $category_info[]=$cat;
            }
        }

        return $category_info;
    }
    function getEmagCategoryHierarchy($parent_id = 0)
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sql = $wpdb->prepare("select id, name, is_allowed from ".$wpdb->prefix."bizzmags_emag_mktpl_category where parent_id = %d", $parent_id);
        $categories = $wpdb->get_results($sql);
        $category_info = array();
        foreach ($categories as $category)
        {
            $current_path = $category->name;
            $child_info = $this->getEmagCategoryHierarchy($category->id);
            if (!empty($child_info))
            {
                foreach ($child_info as &$child)
                {
                    $child->name = $current_path . ' > ' . $child->name;
                }
                $category_info = array_merge($category_info, $child_info);
            }
            else
            {
                $cat=new stdClass;
                $cat->name=$current_path;
                $cat->id=$category->id;
                $cat->is_allowed=$category->is_allowed;
                $category_info[]=$cat;
            }
        }
        return $category_info;
    }
    
    public function getNextProductMissingCategory()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sql=$wpdb->prepare("select p.ID as id, p.post_title as title from ".$wpdb->prefix."posts p left join ".$wpdb->prefix."bizzmags_emag_mktpl_product_category c on c.prod_id=p.ID where p.post_type='product' and p.post_status='publish' and c.template is null limit 1");
        return $wpdb->get_row($sql);
    }
    public function getAllBuyButtons()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sql=$wpdb->prepare("select DISTINCT(buy_button_rank) from ".$wpdb->prefix."bizzmags_emag_mktpl_push_product where pushed=1 and buy_button_rank>0 order by buy_button_rank");
        return $wpdb->get_results($sql);
    }
    
    public function getTotalsProductEmagCategory()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $totals=new stdClass;
        $totals->linked=0;
        $totals->missing=0;
        $sql=$wpdb->prepare("select count(*) as total from ".$wpdb->prefix."posts p left join ".$wpdb->prefix."bizzmags_emag_mktpl_product_category c on c.prod_id=p.ID where p.post_type='product' and c.template is null");
        $result=$wpdb->get_row($sql);
        if(isset($result->total))
            $totals->missing=(int)$result->total;
        $sql=$wpdb->prepare("select count(distinct(prod_id)) as total from ".$wpdb->prefix."bizzmags_emag_mktpl_product_category where 1");
        $result=$wpdb->get_row($sql);
        if(isset($result->total))
            $totals->linked=(int)$result->total;
        return $totals;
    }
    
    public function getEmagProductsPushTotals()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $totals=new stdClass;
        $totals->to_push=0;
        $totals->pushed=0;
        $totals->total_products=0;
        $sql=$wpdb->prepare("select 
        count(distinct(prod_id)) as total
        from ".$wpdb->prefix."bizzmags_emag_mktpl_push_product  
        where pushed=0");
        $result=$wpdb->get_row($sql);
        if(isset($result->total))
            $totals->to_push=$result->total;
        $sql=$wpdb->prepare("select 
        count(distinct(prod_id)) as total
        from ".$wpdb->prefix."bizzmags_emag_mktpl_push_product  
        where pushed=1");
        $result=$wpdb->get_row($sql);
        if(isset($result->total))
            $totals->pushed=$result->total;
        $sql=$wpdb->prepare("select 
        count(distinct(prod_id)) as total
        from ".$wpdb->prefix."bizzmags_emag_mktpl_product_category  
        where 1");
        $result=$wpdb->get_row($sql);
        if(isset($result->total))
            $totals->total_products=$result->total;
        return $totals;
    }
    public function getProductsToPushToEmag()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $allowed_selected=0;
        $show_later=0;
        $allowed_limit=10;
        $sql=$wpdb->prepare("select 
        p.prod_id
        from ".$wpdb->prefix."bizzmags_emag_mktpl_product_category p 
        inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category c on c.id=p.template 
        inner join ".$wpdb->prefix."posts post on post.ID=p.prod_id 
        left join ".$wpdb->prefix."bizzmags_emag_mktpl_push_product e on e.prod_id=p.prod_id 
        where 
        e.prod_id is null 
        order by p.prod_id
        limit %d",sanitize_text_field($allowed_limit));
        $results=$wpdb->get_results($sql);
        if(is_array($results) && count($results)>0)
        {
            $products=array();
            foreach($results as $result)
                $products[]=$result->prod_id;
            $products_str=implode(",",$products);
            $sql=$wpdb->prepare("select 
            p.prod_id,
            p.later,
            p.title, 
            p.template, 
            p.path, 
            post.post_title, 
            c.is_allowed, 
            c.id as cat_id, 
            c.requested 
            from ".$wpdb->prefix."bizzmags_emag_mktpl_product_category p 
            inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category c on c.id=p.template 
            inner join ".$wpdb->prefix."posts post on post.ID=p.prod_id 
            left join ".$wpdb->prefix."bizzmags_emag_mktpl_push_product e on e.prod_id=p.prod_id 
            where 
            p.prod_id in (%s)
            order by p.prod_id
            ",sanitize_text_field($products_str));
            return $wpdb->get_results($sql); 
        }
        return array();
    }
    
    public function substrKeepWordsOnly($string, $max_characters)
    {
        if (strlen($string) <= $max_characters)
            return $string;
        $trimmed_string = substr($string, 0, $max_characters);
        $last_space_position = strrpos($trimmed_string, ' ');
        if ($last_space_position !== false)
            $trimmed_string = substr($trimmed_string, 0, $last_space_position);
        return $trimmed_string;
    }

    public function getEmagVatRates()
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_ajax_referer( 'bizzmagsmarketplace_get_emag_vat_rate_nonce', 'security' );
        $result=$this->doEmagRequest("vat","read");
        $have_errors=$this->getResultError($result);
        if($have_errors=="" && isset($result->results) && is_array($result->results))
        {
            ?>
            <select id="emag_vat_select" name="emag_vat_select" class="button">
            <?php
            foreach($result->results as $res)
            {
                ?>
                <option<?php echo (($res->vat_id==$this->config->emag_vat_id)?' selected="selected"':"");?> value="<?php echo esc_attr($res->vat_id);?>"><?php echo esc_html($res->vat_rate);?>%</option>
                <?php
            }
            ?>
            </select>
            <?php
            foreach($result->results as $res)
            {
                ?>
                <input type="hidden" name="emag_vat_rate_<?php echo esc_attr($res->vat_id);?>" value="<?php echo esc_attr($res->vat_rate);?>" />
                <?php
            }
        }
        else
            echo esc_html($have_errors);
    }
    public function getEmagHandlingTimes()
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_ajax_referer( 'bizzmagsmarketplace_get_emag_handling_times_nonce', 'security' );
        $result=$this->doEmagRequest("handling_time","read");
        $have_errors=$this->getResultError($result);
        if($have_errors=="" && isset($result->results) && is_array($result->results))
        {
            ?>
            <select id="emag_handling_select" name="emag_handling_select" class="button">
            <?php
            foreach($result->results as $result)
            {
                ?>
                <option<?php echo (($result->id==$this->config->emag_handling_time)?' selected="selected"':"");?> value="<?php echo esc_attr($result->id);?>"><?php echo esc_html($result->id);?> <?php echo esc_html__("days","bizzmagsmarketplace");?></option>
                <?php
            }
            ?>
            </select>
            <?php
        }
        else
            echo esc_html($have_errors);
    }
    public function saveEmagConfiguration()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_admin_referer( 'wc_emag_save_emag_configuration' );
        $emag_vat_select=isset($_POST['emag_vat_select'])?sanitize_text_field($_POST['emag_vat_select']):0;
        $emag_handling_select=isset($_POST['emag_handling_select'])?sanitize_text_field($_POST['emag_handling_select']):0;
        $emag_resupply_days=isset($_POST['emag_resupply_days'])?sanitize_text_field($_POST['emag_resupply_days']):0;
        $emag_warranty_months=isset($_POST['emag_warranty_months'])?sanitize_text_field($_POST['emag_warranty_months']):0;
        $emag_product_status=isset($_POST['emag_product_status'])?sanitize_text_field($_POST['emag_product_status']):0;
        $emag_product_price_alter=isset($_POST['emag_product_price_alter'])?sanitize_text_field($_POST['emag_product_price_alter']):"";
        $emag_product_price_alter_value=isset($_POST['emag_product_price_alter_value'])?sanitize_text_field($_POST['emag_product_price_alter_value']):"";
        $emag_product_price_alter_type=isset($_POST['emag_product_price_alter_type'])?sanitize_text_field($_POST['emag_product_price_alter_type']):"";
        $emag_product_price_alter_formula=isset($_POST['emag_product_price_alter_formula'])?sanitize_text_field($_POST['emag_product_price_alter_formula']):"";
        $emag_min_sale_price=isset($_POST['emag_min_sale_price'])?sanitize_text_field($_POST['emag_min_sale_price']):0;
        $emag_max_sale_price=isset($_POST['emag_max_sale_price'])?sanitize_text_field($_POST['emag_max_sale_price']):0;
        $emag_recommended_price=isset($_POST['emag_recommended_price'])?sanitize_text_field($_POST['emag_recommended_price']):0;
        $emag_product_brand_meta=isset($_POST['emag_product_brand_meta'])?array_map('sanitize_text_field', $_POST['emag_product_brand_meta']):array();
        $emag_product_ean_meta=isset($_POST['emag_product_ean_meta'])?array_map('sanitize_text_field', $_POST['emag_product_ean_meta']):array();

        $emag_categ_img_category=isset($_POST['emag_categ_img_category'])?array_map('sanitize_text_field', $_POST['emag_categ_img_category']):array();
        $emag_categ_img_size=isset($_POST['emag_categ_img_size'])?array_map('sanitize_text_field', $_POST['emag_categ_img_size']):array();

        $emag_product_price_alter_value_commission_vat=isset($_POST['emag_product_price_alter_value_commission_vat'])?sanitize_text_field($_POST['emag_product_price_alter_value_commission_vat']):"";
        $emag_product_price_max_commission=isset($_POST['emag_product_price_max_commission'])?sanitize_text_field($_POST['emag_product_price_max_commission']):"";
        $emag_use_product_commission=isset($_POST['emag_use_product_commission'])?sanitize_text_field($_POST['emag_use_product_commission']):"";
        $emag_sync_product_commission=isset($_POST['emag_sync_product_commission'])?sanitize_text_field($_POST['emag_sync_product_commission']):"";

        $emag_vendor_name=isset($_POST['emag_vendor_name'])?sanitize_text_field($_POST['emag_vendor_name']):"";
        $emag_shipping_cost=isset($_POST['emag_shipping_cost'])?sanitize_text_field($_POST['emag_shipping_cost']):"";

        $emag_stock_zero_sku=isset($_POST['emag_stock_zero_sku'])?sanitize_text_field($_POST['emag_stock_zero_sku']):"";
        $emag_stock_zero_weight=isset($_POST['emag_stock_zero_weight'])?sanitize_text_field($_POST['emag_stock_zero_weight']):"";
        $emag_stock_zero_price=isset($_POST['emag_stock_zero_price'])?sanitize_text_field($_POST['emag_stock_zero_price']):"";
        $emag_stock_zero_stock_lower=isset($_POST['emag_stock_zero_stock_lower'])?sanitize_text_field($_POST['emag_stock_zero_stock_lower']):"";
        $emag_feed_override_product_data=isset($_POST['emag_feed_override_product_data'])?sanitize_text_field($_POST['emag_feed_override_product_data']):"";

        $emag_max_percent_substract_from_price=isset($_POST['emag_max_percent_substract_from_price'])?sanitize_text_field($_POST['emag_max_percent_substract_from_price']):"";

        $emag_add_percent_before_commission=isset($_POST['emag_add_percent_before_commission'])?sanitize_text_field($_POST['emag_add_percent_before_commission']):"";

        $emag_import_order_status=isset($_POST['emag_import_order_status'])?sanitize_text_field($_POST['emag_import_order_status']):"";
        $emag_prod_batch_import=isset($_POST['emag_prod_batch_import'])?sanitize_text_field($_POST['emag_prod_batch_import']):"";
        $emag_prod_batch_import_nr=isset($_POST['emag_prod_batch_import_nr'])?sanitize_text_field($_POST['emag_prod_batch_import_nr']):"";
        $emag_prod_batch_send_to_emag=isset($_POST['emag_prod_batch_send_to_emag'])?sanitize_text_field($_POST['emag_prod_batch_send_to_emag']):"";
        $emag_prod_batch_send_to_emag_nr=isset($_POST['emag_prod_batch_send_to_emag_nr'])?sanitize_text_field($_POST['emag_prod_batch_send_to_emag_nr']):"";
        $emag_dropshipping=isset($_POST['emag_dropshipping'])?sanitize_text_field($_POST['emag_dropshipping']):"";
        $emag_create_product_price_minus_percent=isset($_POST['emag_create_product_price_minus_percent'])?sanitize_text_field($_POST['emag_create_product_price_minus_percent']):"";

        $emag_order_carrier_free = isset($_POST['emag_order_carrier_free'])?sanitize_text_field($_POST['emag_order_carrier_free']):"";

        $emag_order_carrier_free=str_replace(" ", "", $emag_order_carrier_free);
        $emag_order_carrier_free_arr=explode(",",$emag_order_carrier_free);
        $emag_order_carrier_free_arr=array_unique($emag_order_carrier_free_arr);
        $result_array = array_map(function($string) {
            return str_replace(["\r", "\n"], '', $string);
        }, $emag_order_carrier_free_arr);
        $emag_order_carrier_free_arr=$result_array;
        $emag_order_voucher_zero=isset($_POST['emag_order_voucher_zero'])?sanitize_text_field($_POST['emag_order_voucher_zero']):"";
        $emag_send_to_emag_live=isset($_POST['emag_send_to_emag_live'])?sanitize_text_field($_POST['emag_send_to_emag_live']):"";

        $emag_stock_zero_sku=str_replace(" ", "", $emag_stock_zero_sku);
        $emag_stock_zero_sku_arr=explode(",",$emag_stock_zero_sku);
        $emag_stock_zero_sku_arr=array_unique($emag_stock_zero_sku_arr);
        $result_array = array_map(function($string) {
            return str_replace(["\r", "\n"], '', $string);
        }, $emag_stock_zero_sku_arr);
        $emag_stock_zero_sku_arr=$result_array;
        $emag_feed_override_product_data=str_replace(" ", "", $emag_feed_override_product_data);
        $emag_feed_override_product_data_arr=explode(",",$emag_feed_override_product_data);
        $emag_feed_override_product_data_arr=array_unique($emag_feed_override_product_data_arr);
        $result_array = array_map(function($string) {
            return str_replace(["\r", "\n"], '', $string);
        }, $emag_feed_override_product_data_arr);
        $emag_feed_override_product_data_arr=$result_array;

        $emag_dropshipping_orig=$this->config->emag_dropshipping;

        if(isset($_POST['emag_vat_select']) && isset($_POST['emag_vat_rate_'.$_POST['emag_vat_select']]))
        {
            $emag_vat_rate=sanitize_text_field($_POST['emag_vat_rate_'.$_POST['emag_vat_select']]);
            $this->updateConfigValue("emag_vat_rate",$emag_vat_rate);
            $this->updateConfigValue("emag_vat_id",$emag_vat_select);
        }
        if(isset($_POST['emag_handling_select']))
            $this->updateConfigValue("emag_handling_time",$emag_handling_select);
        $this->updateConfigValue("emag_resupply_days",$emag_resupply_days);
        $this->updateConfigValue("emag_warranty_months",$emag_warranty_months);
        $this->updateConfigValue("emag_product_status",$emag_product_status);
        $this->updateConfigValue("emag_product_price_alter",$emag_product_price_alter);
        $this->updateConfigValue("emag_product_price_alter_value",$emag_product_price_alter_value);
        $this->updateConfigValue("emag_product_price_alter_type",$emag_product_price_alter_type);
        $this->updateConfigValue("emag_product_price_alter_formula",$emag_product_price_alter_formula);
        $this->updateConfigValue("emag_min_sale_price",$emag_min_sale_price);
        $this->updateConfigValue("emag_max_sale_price",$emag_max_sale_price);
        $this->updateConfigValue("emag_recommended_price",$emag_recommended_price);
        $emag_product_brand_meta = array_unique($emag_product_brand_meta);
        $emag_product_ean_meta = array_unique($emag_product_ean_meta);
        $emag_categ_img_category = array_unique($emag_categ_img_category);
        $emag_categ_img_size = array_unique($emag_categ_img_size);
        $this->updateConfigValue("emag_product_brand_meta",wp_json_encode($emag_product_brand_meta));
        $this->updateConfigValue("emag_product_ean_meta",wp_json_encode($emag_product_ean_meta));

        $this->updateConfigValue("emag_categ_img_category",wp_json_encode($emag_categ_img_category));
        $this->updateConfigValue("emag_categ_img_size",wp_json_encode($emag_categ_img_size));

        //$this->updateConfigValue("emag_prod_batch_import",$emag_prod_batch_import);
        //$this->updateConfigValue("emag_prod_batch_import_nr",$emag_prod_batch_import_nr);

        //$this->updateConfigValue("emag_product_price_alter_value_commission_vat",$emag_product_price_alter_value_commission_vat);
        //$this->updateConfigValue("emag_product_price_max_commission",$emag_product_price_max_commission);
        //$this->updateConfigValue("emag_use_product_commission",$emag_use_product_commission);
        //$this->updateConfigValue("emag_sync_product_commission",$emag_sync_product_commission);

        $this->updateConfigValue("emag_vendor_name",$emag_vendor_name);
        $this->updateConfigValue("emag_shipping_cost",$emag_shipping_cost);
        
        //$this->updateConfigValue("emag_stock_zero_sku",implode(",",$emag_stock_zero_sku_arr));
        //$this->updateConfigValue("emag_stock_zero_weight",$emag_stock_zero_weight);
        //$this->updateConfigValue("emag_stock_zero_price",$emag_stock_zero_price);
        //$this->updateConfigValue("emag_stock_zero_stock_lower",$emag_stock_zero_stock_lower);
        
        
        //$this->updateConfigValue("emag_feed_override_product_data",implode(",",$emag_feed_override_product_data_arr));
        //$this->updateConfigValue("emag_max_percent_substract_from_price",$emag_max_percent_substract_from_price);
        //$this->updateConfigValue("emag_add_percent_before_commission",$emag_add_percent_before_commission);
        //$this->updateConfigValue("emag_import_order_status",$emag_import_order_status);

        $this->updateConfigValue("emag_dropshipping",$emag_dropshipping);
        //$this->updateConfigValue("emag_create_product_price_minus_percent",$emag_create_product_price_minus_percent);
        //$this->updateConfigValue("emag_order_voucher_zero",$emag_order_voucher_zero);
        $this->updateConfigValue("emag_send_to_emag_live",$emag_send_to_emag_live);
        //$this->updateConfigValue("emag_order_carrier_free",implode(",",$emag_order_carrier_free_arr));
        //$this->updateConfigValue("emag_prod_batch_send_to_emag",$emag_prod_batch_send_to_emag);
        //$this->updateConfigValue("emag_prod_batch_send_to_emag_nr",$emag_prod_batch_send_to_emag_nr);

        if($emag_dropshipping_orig!=$emag_dropshipping)
            return array('status'=>'updated','msg'=>__("Settings updated, reloading page","bizzmagsmarketplace"));
        else
            return array('status'=>'updated','msg'=>__("Settings updated","bizzmagsmarketplace"));
    }
    public function getProductMetas()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sql=$wpdb->prepare("
        select DISTINCT pm.meta_key 
        FROM ".$wpdb->prefix."postmeta pm
        INNER JOIN ".$wpdb->prefix."posts p ON pm.post_id = p.ID
        WHERE p.post_type = 'product'
        AND pm.meta_key LIKE %s
        AND pm.meta_key NOT IN ('_edit_lock', '_edit_last')
        ",'_%');
        return $wpdb->get_results($sql);
    }
    public function getProductVariationName($prod_id, $variation_id) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $product = wc_get_product($prod_id);
        if (!$product || 'variable' !== $product->get_type()) {
            return null;
        }
        $parent_title = $product->get_name();
        $variation = wc_get_product($variation_id);
        if (!$variation || $variation->get_parent_id() !== $prod_id) {
            return null;
        }
        $attributes = $variation->get_variation_attributes();
        $variation_title = $parent_title;
        foreach ($attributes as $attribute => $value) {
            $taxonomy = str_replace('attribute_', '', $attribute);
            $term = get_term_by('slug', $value, $taxonomy);
            if ($term && !is_wp_error($term)) {
                $variation_title .= ', ' . $term->name;
            }
        }
        return $variation_title;
    }
    function getWcProductDefaultCategory($product_id) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $product = wc_get_product($product_id);
        if(!$product)
            return 0;
        $categories = $product->get_category_ids();
        if (empty($categories)) {
            return 0;
        }
        $last_child_category = null;
        $max_depth = -1;
        foreach ($categories as $category_id) {
            $category = get_term($category_id, 'product_cat');
            $depth = 0;
            while ($category->parent != 0) {
                $depth++;
                $category = get_term($category->parent, 'product_cat');
            }
            if ($depth > $max_depth) {
                $max_depth = $depth;
                $last_child_category = get_term($category_id, 'product_cat');
            }
        }
        if(isset($last_child_category->term_id))
            return $last_child_category->term_id;
        return $last_child_category;
    }
    public function getProductToPushToEmag($prod_id=0,$variation_id=0)
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $prod_id+=0;
        $variation_id+=0;
        $emag=array();
        $emag['id']=0;
        $emag['prod_id']=$prod_id;
        $emag['variation_id']=$variation_id;
        $emag['category_id']=0;
        $emag['name']="";
        $emag['part_number']="";
        $emag['description']="";
        $emag['brand']="";
        $emag['weight']=0;
        $emag['images']=array();
        $emag['warranty']=0;
        $emag['ean']="";
        $emag['status']=0;
        $emag['sale_price']=0;
        $emag['recommended_price']=0;
        $emag['min_sale_price']=0;
        $emag['max_sale_price']=0;
        $emag['stock']=array();
        $emag['handling_time']=array('warehouse_id'=>1,'value'=>0);
        $emag['supply_lead_time']=0;
        $emag['vat_id']=0;
        if($prod_id>0)
        {
            $product_parent = wc_get_product($prod_id);
            $product = wc_get_product($prod_id);

            if(!$product_parent || !$product)
                return false;

            if($variation_id>0)
            {
                $product = new \WC_Product_Variation($variation_id);
                if(!$product)
                    return false;
            }

            $product_id = $product_parent->get_id();
            $price = wc_get_price_excluding_tax($product);
            $price_including_tax = wc_get_price_including_tax($product);

            $title = $product->get_title();

            if($variation_id>0)
                $title=$this->getProductVariationName($prod_id,$variation_id);

            $description = $product->get_description();
            if($variation_id>0 && $description=="")
                $description = $product_parent->get_description();
            $stock_quantity = $product->get_stock_quantity();
            $weight = (int)$product->get_weight();
            if(get_option('woocommerce_weight_unit')=='kg')
                $weight=$weight*1000;
            $sku = $product->get_sku();

            $product_image_id = $product->get_image_id();
            $product_image_url = wp_get_attachment_url($product_image_id);
            $image_ids = $product->get_gallery_image_ids();
            if(empty($image_ids))
                $image_ids = $product_parent->get_gallery_image_ids();
            $image_urls = array(array('url'=>$product_image_url));
            foreach ($image_ids as $image_id)
            {
                $image_url = wp_get_attachment_url($image_id);
                if ($image_url)
                    $image_urls[] = array('url'=>$image_url);
            }

            $category_ids = $product_parent->get_category_ids();
            $emag['category_id']=$this->getWcProductDefaultCategory($prod_id);
            $emag_categ_img_category=array();
            $emag_categ_img_size=array();

            $emag_categ_img_category_str=$this->config->emag_categ_img_category;
            if($emag_categ_img_category_str!="")
                $emag_categ_img_category=json_decode($emag_categ_img_category_str);
            $emag_categ_img_size_str=$this->config->emag_categ_img_size;
            if($emag_categ_img_size_str!="")
                $emag_categ_img_size=json_decode($emag_categ_img_size_str);
            if (is_array($category_ids) && count($category_ids) > 0) {
                foreach ($category_ids as $category_id) {
                    if(is_array($emag_categ_img_category) && is_array($emag_categ_img_size) && count($emag_categ_img_category)>0 && count($emag_categ_img_category)==count($emag_categ_img_size) && isset($emag_categ_img_category[0]) && $emag_categ_img_category[0]!="")
                    {
                        $key = array_search($category_id, $emag_categ_img_category);
                        if($key!==false)
                        {
                            $img_size=$emag_categ_img_size[$key];
                            if($img_size!="")
                            {
                                $product_image_id = $product->get_image_id();
                                $image_url = wp_get_attachment_image_src( $product_image_id, $img_size );
                                $image_urls = array(array('url'=>$image_url[0]));
                                $image_ids = $product->get_gallery_image_ids();
                                if(empty($image_ids))
                                    $image_ids = $product_parent->get_gallery_image_ids();
                                foreach ($image_ids as $image_id)
                                {
                                    $image_url = wp_get_attachment_image_src( $image_id, $img_size );
                                    if ($image_url && isset($image_url[0]))
                                        $image_urls[] = array('url'=>$image_url[0]);
                                }
                            }
                        }
                    }
                }
            }

            $wc_price=$price+0;
            if((int)$this->config->emag_add_percent_before_commission>0)
            {
                $diff=$wc_price*((int)$this->config->emag_add_percent_before_commission/100);
                $wc_price=$wc_price+$diff;
                $wc_price=number_format(round($wc_price,2),2,".","");
            }
            $emag_price=0;
            $alter_value=$this->config->emag_product_price_alter_value;

            $emag_product_price_max_commission=(int)$this->config->emag_product_price_max_commission;
            $commission_vat=$this->config->emag_product_price_alter_value_commission_vat;
            $emag_commission_percent=$alter_value;
            if($this->config->emag_product_price_alter_type=='percent' && $this->config->emag_product_price_alter=='addition' && $alter_value>0)
            {
                if($this->config->emag_product_price_alter_formula=='excluded')
                {
                    $emag_price=$wc_price;
                    $emag_price=number_format(round($emag_price,2),2,".","");
                    $emag_commission=$emag_price*($emag_commission_percent/100);
                    $emag_commission=number_format(round($emag_commission,2),2,".","");
                    $emag_price=$emag_price+$emag_commission;
                    $emag_price=number_format(round($emag_price,2),2,".","");
                }
            }

            $diff=$emag_price*$this->config->emag_min_sale_price/100;
            $emag_min_sale_price=$emag_price-$diff;
            $diff=$emag_price*$this->config->emag_max_sale_price/100;
            $emag_max_sale_price=$emag_price+$diff;
            $diff=$emag_price*$this->config->emag_recommended_price/100;
            $emag_recommended_price=$emag_price+$diff;
            $emag_product_brand_meta=array();
            $emag_product_ean_meta=array();
            $emag_product_brand_meta_str=$this->config->emag_product_brand_meta;
            if($emag_product_brand_meta_str!="")
                $emag_product_brand_meta=json_decode($emag_product_brand_meta_str);
            $emag_product_ean_meta_str=$this->config->emag_product_ean_meta;
            if($emag_product_ean_meta_str!="")
                $emag_product_ean_meta=json_decode($emag_product_ean_meta_str);
            $brand="";
            $ean="";

            if(is_array($emag_product_brand_meta) && count($emag_product_brand_meta)>0)
            {
                foreach($emag_product_brand_meta as $emag_prod_meta)
                {
                    if($emag_prod_meta!="")
                    {
                        $brand=get_post_meta($product_id,$emag_prod_meta,true);
                        if($brand!="")
                            break;
                    }
                }
            }
            if($emag_product_brand_meta_str=="" || (is_array($emag_product_brand_meta) && (count($emag_product_brand_meta)==0 || (count($emag_product_brand_meta)==1 && $emag_product_brand_meta[0]==""))))
            {
                $sql=$wpdb->prepare("select t.name FROM ".$wpdb->prefix."terms t
                INNER JOIN ".$wpdb->prefix."term_relationships tr ON t.term_id = tr.term_taxonomy_id
                INNER JOIN ".$wpdb->prefix."term_taxonomy tt ON t.term_id = tt.term_id
                WHERE tr.object_id = %d AND tt.taxonomy = 'product_brand'",$prod_id);
                $brand_res=$wpdb->get_var($sql);
                if($brand_res!="")
                    $brand=$brand_res;
            }
            if(is_array($emag_product_ean_meta) && count($emag_product_ean_meta)>0)
            {
                foreach($emag_product_ean_meta as $emag_prod_meta)
                {
                    if($emag_prod_meta!="")
                    {
                        $ean=get_post_meta($product_id,$emag_prod_meta,true);
                        if($ean!="")
                            break;
                    }
                }
            }

            if((float)$this->config->emag_vat_rate>0 && $price_including_tax<=$price)//we need to sned prices exc vat to emag
            {
                $emag_price=$emag_price/(1+$this->config->emag_vat_rate);
                $emag_price=number_format(round($emag_price,2),2,".","");
            }

            $emag['id']=$product_id;
            $emag['name']=$title;
            $emag['part_number']=$sku;
            $emag['description']=$description;
            $emag['brand']=$brand;
            $emag['weight']=$weight;
            $emag['images']=$image_urls;
            $emag['warranty']=$this->config->emag_warranty_months;
            $emag['ean']=array($ean);
            $emag['status']=$this->config->emag_product_status;
            $emag['sale_price']=number_format(round($emag_price,2),2,".","");
            $emag['recommended_price']=number_format(round($emag_recommended_price,2),2,".","");
            $emag['min_sale_price']=number_format(round($emag_min_sale_price,2),2,".","");
            $emag['max_sale_price']=number_format(round($emag_max_sale_price,2),2,".","");
            $emag['stock']=array('warehouse_id'=>1,'value'=>(int)$stock_quantity);
            $emag['handling_time']=array('warehouse_id'=>1,'value'=>$this->config->emag_handling_time);
            $emag['supply_lead_time']=$this->config->emag_resupply_days;
            $emag['vat_id']=$this->config->emag_vat_id;
        }
        return $emag;
    }
    
    public function calculateEmagCommissionPercent($commissionRate, $taxRate=19)
    {
        $commissionRateDecimal = $commissionRate / 100;
        $taxRateDecimal = $taxRate / 100;
        $effectiveCommissionRate = $commissionRateDecimal * (1 + $taxRateDecimal);
        $percentageToAdd = ($effectiveCommissionRate / (1 - $effectiveCommissionRate)) * 100;
        $percentageToAdd = number_format(round($percentageToAdd,2),2,".","");
        return $percentageToAdd;
    }
    
    public function getExcludesSku()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        return array();
    }
    public function getExcludesWeight()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $max_weight=$this->config->emag_stock_zero_weight;
        $max_weight+=0;
        if($max_weight>0)
        {
            $max_weight=$max_weight/1000;
            $products = $wpdb->get_results( $wpdb->prepare("
                select p.ID
                FROM ".$wpdb->prefix."posts p
                INNER JOIN ".$wpdb->prefix."postmeta pm ON p.ID = pm.post_id
                WHERE p.post_type = 'product'
                AND pm.meta_key = '_weight'
                AND CAST(pm.meta_value as DECIMAL(11,4)) > %s
            ",$max_weight));
            $product_objects = array_map('wc_get_product', wp_list_pluck($products, 'ID'));
            return $product_objects;
        }
        return array();
    }
    public function getExcludesPrice()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $min_price=$this->config->emag_stock_zero_price;
        $min_price+=0;
        if($min_price>0)
        {
            $products = $wpdb->get_results( $wpdb->prepare("
                select p.ID
                FROM ".$wpdb->prefix."posts p
                INNER JOIN ".$wpdb->prefix."postmeta pm ON p.ID = pm.post_id
                WHERE p.post_type = 'product'
                AND pm.meta_key = '_price'
                AND CAST(pm.meta_value as DECIMAL(11,4)) < %s
            ",$min_price));
            $product_objects = array_map('wc_get_product', wp_list_pluck($products, 'ID'));
            return $product_objects;
        }
        return array();
    }
    Public function getExcludesStockLower()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $product_ids = $wpdb->get_col( $wpdb->prepare( "
            select post_id
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_stock'
            AND meta_value < %d AND meta_value > 0
        ", $this->config->emag_stock_zero_stock_lower ) );

        // Fetch product objects
        $products = array_map( 'wc_get_product', $product_ids );
        return $products;
    }
    
    public function createDemoCategoryRecords()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sql=$wpdb->prepare("select count(*) as total from ".$wpdb->prefix."bizzmags_emag_mktpl_product_category");
        $result=$wpdb->get_row($sql);
        if(isset($result->total) && $result->total==0)
        {
            $sql=$wpdb->prepare("select ID from ".$wpdb->prefix."posts where post_type='product' order by ID desc limit 1");
            $result=$wpdb->get_row($sql);
            if(isset($result->ID) && $result->ID>0)
            {
                $prod_id=$result->ID;
                $product = wc_get_product($prod_id);
                if($product)
                {
                    $title = $product->get_title();
                    $sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_product_category set
                    prod_id=%d,
                    later='0',
                    product_title=%s,
                    id='1081',
                    template='116',
                    title='Produse cosmetica auto',
                    path='Auto > Car Accessories > Intretinere & Cosmetica auto > Produse cosmetica auto',
                    example_title='Set reparatie jante din aliaj de aluminiu',
                    example_image='',
                    mdate=%d",array(
                        sanitize_text_field($prod_id),
                        sanitize_text_field($title),
                        time()
                    ));
                    $wpdb->query($sql);
                }
            }
        }
    }
    function getWcChildCategories($parent_id, $parent_path, $prefix = '') {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $child_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => $parent_id
        ));

        $result = array();
        foreach ($child_categories as $child) {
            $full_path = $parent_path . ' > ' . $child->name;
            $cat=new stdClass;
            $cat->id_category=$child->term_id;
            $cat->name=$prefix . '---' . $child->name;
            $cat->full_path=$full_path;
            $result[] = $cat;
            // Recursively get child categories
            $result = array_merge($result, $this->getWcChildCategories($child->term_id, $full_path, $prefix . '---'));
        }

        return $result;
    }
    function getAllWcProductCategories() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if (class_exists('WooCommerce')) {
            $product_categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'parent' => 0
            ));
            if (is_wp_error($product_categories) || empty($product_categories)) {
                return array();
            }
            $categories_array = array();
            foreach ($product_categories as $category) {
                $full_path = $category->name;
                $cat=new stdClass;
                $cat->id_category=$category->term_id;
                $cat->name=$category->name;
                $cat->full_path=$full_path;
                $result[] = $cat;
                $categories_array[] = $cat;
                $categories_array = array_merge($categories_array, $this->getWcChildCategories($category->term_id, $full_path));
            }
            return $categories_array;
        }

        return array();
    }
    public function getCategoryMap()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $categories=$this->getAllWcProductCategories();
        $categoriesWithFullPath = [];
        $missing_categories=[];
        foreach ($categories as $category) {
            $category->first_product_id=0;
            $category->emag_cat_id=0;
            $category->has_access=0;
            $category->map_id=0;
            $category->emag_full_path='';
            $sql=$wpdb->prepare("select p.prod_id, p.cat_id as emag_cat_id, tt.term_id as id_category, ec.is_allowed, ec.full_path as emag_full_path
            from ".$wpdb->prefix."term_taxonomy tt
            inner join ".$wpdb->prefix."term_relationships tr on tr.term_taxonomy_id=tt.term_taxonomy_id
            inner join ".$wpdb->prefix."posts prod on prod.ID=tr.object_id
            inner join ".$wpdb->prefix."bizzmags_emag_mktpl_push_product p on tr.object_id=p.prod_id 
            inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category ec on ec.id=p.cat_id
            where tt.term_id=%d and tt.taxonomy = 'product_cat' order by p.cat_id desc limit 1",array($category->id_category));
            $results=$wpdb->get_results($sql);
            if(is_array($results))
            {
                foreach($results as $result)
                {
                    $category->first_product_id=(int)$result->prod_id;
                    $category->emag_cat_id=(int)$result->emag_cat_id;
                    $category->emag_full_path=$result->emag_full_path;
                    $category->has_access=(int)$result->is_allowed;
                    if((int)$category->id_category>0 && (int)$result->emag_cat_id>0)
                    {
                        $sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_category_map set
                        wc_cat_id=%d,
                        emag_cat_id=%d,
                        auto=1,
                        mdate=%d
                        on duplicate key update
                        mdate=%d",array($category->id_category,$category->emag_cat_id,time(),time()));
                        $wpdb->query($sql);
                        $affected_rows = $wpdb->rows_affected;
                        if ($affected_rows == 1)
                            $this->saveLog(__("Automatically added eMag category map","bizzmagsmarketplace")." ".$category->id_category." ".$category->emag_cat_id);
                        $sql=$wpdb->prepare("select id from ".$wpdb->prefix."bizzmags_emag_mktpl_category_map where wc_cat_id=%d and emag_cat_id=%d",array($category->id_category,$category->emag_cat_id));
                        $map_id=$wpdb->get_var($sql);
                        $category->map_id=$map_id;
                    }
                }
            }
            if((int)$category->first_product_id==0)//no products in category
            {
                $missing=new stdClass;
                $missing->id_category=$category->id_category;
                $missing->name=$category->full_path;
                $missing_categories[]=$missing;
                continue;
            }

            $categoriesWithFullPath[] = $category;
        }
        $sql=$wpdb->prepare("select cm.*, ec.full_path as emag_full_path, ec.is_allowed from ".$wpdb->prefix."bizzmags_emag_mktpl_category_map cm
            inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category ec on ec.id=cm.emag_cat_id
            where 1");
        $results=$wpdb->get_results($sql);
        if(is_array($results))
        {
            foreach($results as $result)
            {
                $found=false;
                foreach($categoriesWithFullPath as $category)
                {
                    if($result->wc_cat_id==$category->id_category && $category->emag_cat_id==$result->emag_cat_id)
                        $found=true;
                }
                if(!$found)
                {
                    for($i=0;$i<count($categoriesWithFullPath);$i++){
                        if($categoriesWithFullPath[$i]->id_category==$result->wc_cat_id){
                            unset($categoriesWithFullPath[$i]);
                        }
                    }
                    $cat=new stdClass;
                    $cat->map_id=$result->id;
                    $cat->id_category=(int)$result->wc_cat_id;
                    $cat->full_path=$this->getCategoryFullPath((int)$result->wc_cat_id);
                    $cat->emag_cat_id=(int)$result->emag_cat_id;
                    $cat->emag_full_path=$result->emag_full_path;
                    $cat->has_access=(int)$result->is_allowed;
                    $categoriesWithFullPath[]=$cat;
                }
            }
        }
        return [$categoriesWithFullPath,$missing_categories];
    }
    public function getCategoryFullPath($category_id) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $term = get_term($category_id, 'product_cat');
        if (is_wp_error($term) || !$term) {
            return '';
        }

        $path = $term->name;
        $parent_id = $term->parent;

        while ($parent_id) {
            $parent_term = get_term($parent_id, 'product_cat');
            if (is_wp_error($parent_term) || !$parent_term) {
                break;
            }
            $path = $parent_term->name . ' > ' . $path;
            $parent_id = $parent_term->parent;
        }
        return $path;
    }

    public function emagCatResultSearch()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_ajax_referer( 'bizzmagsmarketplace_emag_marketplace_cat_result_search_nonce', 'security' );
        $search = isset($_POST['search'])?sanitize_text_field($_POST['search']):"";
        if($search!="")
        {
            ?>
            <option value="">-</option>
            <?php
            $sql=$wpdb->prepare("select id, is_allowed, full_path from ".$wpdb->prefix."bizzmags_emag_mktpl_category where name_index like %s order by is_allowed desc",array('%'.$search.'%'));
            $results=$wpdb->get_results($sql);
            if(is_array($results))
            {
                foreach($results as $result)
                {
                    ?>
                    <option value="<?php echo esc_attr($result->id);?>" is_allowed="<?php echo esc_attr($result->is_allowed);?>">[<?php echo esc_html($result->id);?>] [<?php echo esc_html($result->is_allowed);?>] <?php echo esc_html($result->full_path);?></option>
                    <?php
                }
            }
        }
        else
        {
            echo '<option value="">'.esc_html__("No records","bizzmagsmarketplace").'</option>';
        }
    }
    public function addNewCategoryMap()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_admin_referer( 'bizzmagsmarketplace_emag_add_new_category_map' );
        $wc_cat_id = isset($_POST['new_map_wc_cat_id_select'])?(int)$_POST['new_map_wc_cat_id_select']:0;
        $emag_cat_id = isset($_POST['new_map_emag_cat_result'])?(int)$_POST['new_map_emag_cat_result']:0;
        if($wc_cat_id>0 && $emag_cat_id>0)
        {
            $sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_category_map set wc_cat_id=%d, emag_cat_id=%d, auto=0, mdate=%d on duplicate key update emag_cat_id=%d, auto=0, mdate=%d",array($wc_cat_id,$emag_cat_id,time(),$emag_cat_id,time()));
            $wpdb->query($sql);
            $this->saveLog(__("Added eMag category map","bizzmagsmarketplace")." ".$wc_cat_id." ".$emag_cat_id);
            return ['status'=>'updated','msg'=> __("Emag category map saved with success","bizzmagsmarketplace")];
        }
        else
        {
            return ['status'=>'error', $msg => __("Something is wrong, did you select a pair of categories","bizzmagsmarketplace")."?"];
        }
    }
    public function removeEmagCategoryMap()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_ajax_referer( 'bizzmagsmarketplace_emag_marketplace_remove_category_map_nonce', 'security' );
        $id = isset($_POST['id'])?sanitize_text_field($_POST['id']):0;
        if ($id > 0) {
            $wc_cat_id=0;
            $emag_cat_id=0;
            $sql=$wpdb->prepare("select m.* from ".$wpdb->prefix."bizzmags_emag_mktpl_category_map m where id=%d",array((int)$id));
            $result=$wpdb->get_row($sql);
            if(isset($result->wc_cat_id))
            {
                $wc_cat_id=$result->wc_cat_id;
                $emag_cat_id=$result->emag_cat_id;
            }
            $sql=$wpdb->prepare("delete from ".$wpdb->prefix."bizzmags_emag_mktpl_category_map where id=%d",(int)$id);
            $wpdb->query($sql);
            $this->saveLog(__("Removed eMag category map","bizzmagsmarketplace")." ".$wc_cat_id." ".$emag_cat_id);
        }
    }
    public function getCharacteristicsMap()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $return=new stdClass;
        $return->characteristics=[];
        $return->attributes=[];
        $sql=$wpdb->prepare("select distinct(emag_cat_id) from ".$wpdb->prefix."bizzmags_emag_mktpl_category_map where 1 order by emag_cat_id");
        $cat_ids=$wpdb->get_results($sql);
        if(is_array($cat_ids))
        {
            $mandatory=[];
            $unique_chars=[];
            $characteristics=[];
            foreach($cat_ids as $cat)
            {
                $sql=$wpdb->prepare("select c.* from ".$wpdb->prefix."bizzmags_emag_mktpl_category_char c
                    where c.cat_id=%d order by c.display_order",(int)$cat->emag_cat_id);
                $chars=$wpdb->get_results($sql);
                if(is_array($chars))
                {
                    foreach($chars as $char)
                    {
                        if(!in_array($char->id, $unique_chars))
                        {
                            $unique_chars[]=$char->id;
                            $char->map=[];
                            $characteristics[]=$char;
                        }
                        if($char->is_mandatory==1 && !in_array($char->id,$mandatory))
                            $mandatory[]=$char->id;
                    }
                }
            }
            $sql=$wpdb->prepare("select * from ".$wpdb->prefix."bizzmags_emag_mktpl_char_map where 1");
            $maps=$wpdb->get_results($sql);
            if(is_array($maps))
            {
                foreach($maps as $map)
                {
                    for($i=0;$i<count($characteristics);$i++)
                    {
                        if($characteristics[$i]->id==$map->char_id)
                            $characteristics[$i]->map[]=$map;
                    }
                }
            }
            foreach($mandatory as $mandatory_char)
            {
                for($i=0;$i<count($characteristics);$i++)
                {
                    if($characteristics[$i]->id==$mandatory_char)
                        $characteristics[$i]->is_mandatory=1;
                }
            }
            $unique_attributes=[];
            $attributes=[];
            $variations = wc_get_attribute_taxonomies();
            if ( ! empty( $variations ) ) {
                foreach ( $variations as $attribute ) {
                    if(!in_array("pa_".$attribute->attribute_name, $unique_attributes)){
                        $unique_attributes[]="pa_".$attribute->attribute_name;
                        $tmp=new stdClass;
                        $tmp->attribute="pa_".$attribute->attribute_name;
                        $tmp->name=$attribute->attribute_label." [A]";
                        $attributes[]=$tmp;
                    }
                }
            }
            
            $results = $wpdb->get_results("
                select meta_id, post_id, meta_value 
                FROM ".$wpdb->prefix."postmeta
                WHERE meta_key = '_product_attributes'
            ");

            if ( ! empty( $results ) ) {
                foreach ( $results as $row ) {
                    $attribs = maybe_unserialize( $row->meta_value );
                    if ( ! empty( $attribs ) ) {
                        foreach ( $attribs as $attribute_key => $attribute_data ) {
                            if(!in_array($attribute_key, $unique_attributes))
                            {
                                $unique_attributes[]=$attribute_key;
                                $tmp=new stdClass;
                                $tmp->attribute=$attribute_key;
                                $tmp->name=$attribute_data['name']." [CA]";
                                $attributes[]=$tmp;
                            }
                        }
                    }
                }
            }
            $return->characteristics=$characteristics;
            $return->attributes=$attributes;
        }
        return $return;
    }
    public function saveEmagCharacteristicMap()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_ajax_referer( 'bizzmagsmarketplace_save_characteristic_map_nonce', 'security' );
        $char_id=isset($_POST['char_id'])?(int)$_POST['char_id']:0;
        $attributes=isset($_POST['attributes'])?array_map("sanitize_text_field",$_POST['attributes']):[];
        if($char_id>0)
        {
            $sql=$wpdb->prepare("delete from ".$wpdb->prefix."bizzmags_emag_mktpl_char_map where char_id=%d",array($char_id));
            $wpdb->query($sql);
            if(is_array($attributes))
            {
                foreach($attributes as $attr)
                {
                    if($attr=='')
                        continue;
                    $sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_char_map set
                        char_id=%d,
                        attribute=%s,
                        mdate=%d
                        on duplicate key update
                        mdate=%d
                        ",array($char_id,$attr,time(),time()));
                    if(!$wpdb->query($sql) && $wpdb->last_error !== '')
                    {
                        echo esc_html__("Error","bizzmagsmarketplace");
                        exit;
                    }
                }
            }
            $this->saveLog(__("Saved characteristic map","bizzmagsmarketplace")." ".$char_id." ".implode(", ",$attributes));
            echo esc_html__("Saved","bizzmagsmarketplace");
        }
        else
            echo esc_html__("Missing char id","bizzmagsmarketplace");
    }
    public function loadCharMapDefaults()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_admin_referer( 'bizzmagsmarketplace_load_char_map_defaults' );
        $data=$this->getCharacteristicsMap();
        $characteristics=$data->characteristics;
        $attributes=$data->attributes;
        $defaults=[];
        foreach($characteristics as $char)
        {
            $char_name=strtolower(trim($char->name));
            $char_name=str_replace(":","",$char_name);
            foreach($attributes as $attr)
            {
                $attr_name=strtolower(trim($attr->name));
                $attr_name=str_ireplace(" [CA]","",$attr_name);
                $attr_name=str_ireplace(" [A]","",$attr_name);
                if($char_name==$attr_name)
                {
                    $defaults[]=$attr->name." -> ".$char->name;
                    $sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_char_map set
                        char_id=%d,
                        attribute=%s,
                        mdate=%d
                        on duplicate key update
                        mdate=%d
                        ",array($char->id,sanitize_text_field($attr->attribute),time(),time()));
                    $wpdb->query($sql);

                }
            }
        }
        if(count($defaults)>0)
        {
            $this->saveLog(__("Loaded default characteristic map for","bizzmagsmarketplace")." ".implode(", ",$defaults));
            return ['status'=>'updated','msg'=> __("Loaded default characteristic map for","bizzmagsmarketplace")." ".implode(", ",$defaults)];
        }
        else
        {
            $this->saveLog(__("Loaded default characteristic map","bizzmagsmarketplace"));
            return ['status'=>'updated','msg'=> __("Did not find any matches","bizzmagsmarketplace")];
        }
    }
    public function getFamilyMap()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $return=new stdClass;
        $return->attributes=[];
        $return->families=[];

        $attributes=[];
        $variations = wc_get_attribute_taxonomies();
        if ( ! empty( $variations ) ) {
            foreach ( $variations as $attribute ) {
                $tmp=new stdClass;
                $tmp->attribute=$attribute->attribute_name;
                $tmp->name=$attribute->attribute_label;
                $attributes[]=$tmp;
            }
        }
        $return->attributes=$attributes;

        $families=[];
        $sql=$wpdb->prepare("select distinct(emag_cat_id) from ".$wpdb->prefix."bizzmags_emag_mktpl_category_map where 1 order by emag_cat_id");
        $cat_ids=$wpdb->get_results($sql);
        $unique_fams=[];
        if(is_array($cat_ids))
        {
            foreach($cat_ids as $cat)
            {
                $sql=$wpdb->prepare("select f.id, f.name, f.cat_id
                    from ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam f
                    inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_char fc on fc.fam_id=f.id 
                    and fc.cat_id=f.cat_id
                    where f.cat_id=%d 
                    group by f.id order by fc.display_order",(int)$cat->emag_cat_id);
                $fams=$wpdb->get_results($sql);
                if(is_array($fams))
                {
                    foreach($fams as $fam)
                    {
                        if(!in_array($fam->id,$unique_fams))
                        {
                            $unique_fams[]=$fam->id;
                            $families[]=$fam;
                        }
                    }
                }
            }
            for($i=0;$i<count($families);$i++)
            {
                $families[$i]->chars=[];
                $sql=$wpdb->prepare("select c.id, c.is_mandatory, c.name FROM
                ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam f
                inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_char fc on fc.fam_id=f.id and fc.cat_id=f.cat_id
                inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category_char c on c.cat_id=fc.cat_id and c.id=fc.characteristic_id
                where 
                f.id=%d and f.cat_id=%d
                order by c.display_order",array((int)$families[$i]->id,(int)$families[$i]->cat_id));
                $families[$i]->chars=$wpdb->get_results($sql);

                $families[$i]->attributes=[];
                $sql=$wpdb->prepare("select attribute from ".$wpdb->prefix."bizzmags_emag_mktpl_fam_map where fam_id=%d",$families[$i]->id);
                $attr=$wpdb->get_row($sql);
                if(isset($attr->attribute))
                {
                    $attrs=explode(",",$attr->attribute);
                    $attrs=array_unique($attrs);
                    sort($attrs);
                    $families[$i]->attributes=$attrs;
                }
            }
            $return->families=$families;
        }
        return $return;
    }
    public function saveEmagFamilyMap()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_ajax_referer( 'bizzmagsmarketplace_save_family_map_nonce', 'security' );
        $fam_id=isset($_POST['fam_id'])?(int)$_POST['fam_id']:0;
        $attributes=isset($_POST['attributes'])?array_map("sanitize_text_field",$_POST['attributes']):[];
        if($fam_id>0)
        {
            $attributes=array_unique($attributes);
            sort($attributes);
            $attribute=implode(",",$attributes);
            $sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_fam_map set fam_id=%d, attribute=%s, mdate=%d on duplicate key update attribute=%s, mdate=%d",array($fam_id,sanitize_text_field($attribute),time(),sanitize_text_field($attribute),time()));
            $wpdb->query($sql);
            $this->saveLog(__("Saved family map","bizzmagsmarketplace")." ".$fam_id." ".implode(", ",$attributes));
            echo esc_html__("Saved","bizzmagsmarketplace");
        }
        else
            echo esc_html__("Error","bizzmagsmarketplace");
    }
    public function getLogsPages($logs_per_page=100)
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $logs_per_page=(int)$logs_per_page;
        $sql=$wpdb->prepare("select count(*) from ".$wpdb->prefix."bizzmags_emag_mktpl_logs where 1");
        $total=$wpdb->get_var($sql);
        if($total>0)
        {
            $arr=[];
            $pages=$total/$logs_per_page;
            $pages=ceil($pages);
            for($i=1;$i<=$pages;$i++)
            {
                $arr[]=$i;
            }
            return $arr;
        }
        return [1];
    }
    public function getLogs($logs_page=1,$logs_per_page=100)
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $logs_page=(int)$logs_page;
        $logs_per_page=(int)$logs_per_page;
        if($logs_page==1)
            $start=0;
        else
            $start=($logs_page-1)*$logs_per_page;
        $sql=$wpdb->prepare("select mdate, log from ".$wpdb->prefix."bizzmags_emag_mktpl_logs where 1 order by mdate desc limit %d, %d",array((int)$start,(int)$logs_per_page));
        return $wpdb->get_results($sql);
    }
    public function addSendtoEmagQueue($ids=[])
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if(is_array($ids))
        {
            foreach($ids as $id)
            {
                $sql=$wpdb->prepare("insert into ".$wpdb->prefix."bizzmags_emag_mktpl_send_to_emag set
                    prod_id=%d,
                    sent=0,
                    mdate=%d
                    on duplicate key update
                    sent=0,
                    mdate=%d
                    ",array($id,time(),time()));
                $wpdb->query($sql);
            }
        }
    }
    public function getSendToEmagCount($type="")
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if($type=="importing")
            $sql=$wpdb->prepare("select count(*) from ".$wpdb->prefix."bizzmags_emag_mktpl_send_to_emag where sent=1");
        else
            $sql=$wpdb->prepare("select count(*) from ".$wpdb->prefix."bizzmags_emag_mktpl_send_to_emag where sent=0");
        return $wpdb->get_var($sql);
    }
    public function sendToEmagHook()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_admin_referer( 'bizzmagsmarketplace_send_to_emag' );
        $send_to_emag_cancel=isset($_POST['send_to_emag_cancel'])?(int)$_POST['send_to_emag_cancel']:0;
        $send_to_emag_update_images=isset($_POST['send_to_emag_update_images'])?(int)$_POST['send_to_emag_update_images']:0;
        $send_to_emag_status=isset($_POST['send_to_emag_status'])?(int)$_POST['send_to_emag_status']:0;
        if($send_to_emag_cancel==1)
        {
            $this->updateConfigValue("emag_send_to_emag_started",0);
            $this->saveLog(__("Cancelled the Send to eMag process","bizzmagsmarketplace"));
            $this->removeSendToEmagQueue();
            $this->config->emag_send_to_emag_started=0;
            return array('status'=>'updated','msg'=>__("Cancelled the Send to eMag process","bizzmagsmarketplace"));
        }
        else if(is_file(WC()->plugin_path()."/packages/action-scheduler/action-scheduler.php"))
        {
            $action_added=$this->addActionSchedulerTask("bizzmagsmarketplace_send_to_emag_hook",array(),"bizzmagsmarketplace");
            if($action_added)
            {
                $this->updateConfigValue("emag_send_to_emag_force_update_images",$send_to_emag_update_images);
                $this->updateConfigValue("emag_send_to_emag_product_status",$send_to_emag_status);
                if($this->config->emag_send_to_emag_live=="yes")
                    $this->saveLog(__("Triggered the Send to eMag process LIVE","bizzmagsmarketplace"));
                else
                    $this->saveLog(__("Triggered the Send to eMag process TEST","bizzmagsmarketplace"));
                $this->updateConfigValue("emag_send_to_emag_started",1);
                $this->config->emag_send_to_emag_started=1;
                $this->updateConfigValue("emag_send_to_emag_cnt",(int)$this->getSendToEmagCount());
                return array('status'=>'updated','msg'=>__("Set the Send to eMag background process, it will start shortly","bizzmagsmarketplace"));
            }
            else
            {
                $this->saveLog(__("Error in triggering the Send to eMag process","bizzmagsmarketplace"));
                return $this->returnResultError(__("Error in setting the Send to eMag background process, please contact support","bizzmagsmarketplace"));
            }
        }
    }
    function getSendToEmagStatusAjax()
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_ajax_referer( 'bizzmagsmarketplace_get_send_to_emag_status_nonce', 'security' );
        if($this->getConfigValue("emag_send_to_emag_started")==0)
        {
            echo "1";
            return;
        }
        $imported=$this->getSendToEmagCount("importing");
        $import_percent=intval(($imported*100)/$this->config->emag_send_to_emag_cnt);
        ?>
        <div class="progress-bar">
            <span class="progress-bar-fill" style="width: <?php echo esc_attr($import_percent);?>%;"></span>
        </div>
        <?php
        echo esc_html($imported)."/".esc_html($this->config->emag_send_to_emag_cnt);
    }
    public function removeSendToEmagQueue()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $sql=$wpdb->prepare("truncate table ".$wpdb->prefix."bizzmags_emag_mktpl_send_to_emag");
        $wpdb->query($sql);
    }
    public function setSentToEmagQueue($prod_id)
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $prod_id=(int)$prod_id;
        $sql=$wpdb->prepare("update ".$wpdb->prefix."bizzmags_emag_mktpl_send_to_emag set sent=1 where prod_id=%d",$prod_id);
        $wpdb->query($sql);
    }
    public function getWcProductAttributes($product_id) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $product = wc_get_product($product_id);
        if (!$product) {
            return null;
        }
        $attributes = $product->get_attributes();
        $attributes_data = [];
        foreach ($attributes as $name => $attribute) {
            if ($attribute->is_taxonomy()) {
                $terms = wc_get_product_terms($product_id, $name, ['fields' => 'names']);
                $value = implode(', ', $terms);
            } else {
                $value = implode(', ', $attribute->get_options());
            }

            $attributes_data[] = [
                'name' => $name,
                'value' => $value,
            ];
        }
        return $attributes_data;
    }
    function getWcVariationAttributes($variation_id) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $variation = wc_get_product($variation_id);
        if (!$variation || 'variation' !== $variation->get_type()) {
            return null;
        }
        $attributes = $variation->get_variation_attributes();
        $attributes_data = [];
        foreach ($attributes as $attribute_name => $attribute_value) {
            $taxonomy = str_replace('attribute_', '', $attribute_name);
            $term = get_term_by('slug', $attribute_value, $taxonomy);
            if ($term && !is_wp_error($term)) {
                $name = $taxonomy;
                $value = $term->name;
            } else {
                $name = $attribute_name;
                $value = $attribute_value;
            }
            $attributes_data[] = [
                'name' => str_ireplace("attribute_","",$name),
                'value' => $value,
            ];
        }
        return $attributes_data;
    }
    function getWcVariationTaxonomyAttributes($variation_id) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $variation = wc_get_product($variation_id);
        if (!$variation || 'variation' !== $variation->get_type()) {
            return null;
        }
        $attributes = $variation->get_variation_attributes();
        $attributes_data = [];
        foreach ($attributes as $attribute_name => $attribute_value) {
            $taxonomy = str_replace('attribute_', '', $attribute_name);
            $term = get_term_by('slug', $attribute_value, $taxonomy);
            if ($term && !is_wp_error($term)) {
                $name = $taxonomy;
                $value = $term->name;
                $attributes_data[] = [
                    'name' => str_ireplace("attribute_","",$name),
                    'value' => $value,
                ];
            }
        }
        return $attributes_data;
    }
    public function sendToEmag()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        $force_update_images=$this->getConfigValue('emag_send_to_emag_force_update_images');
        $product_status=$this->getConfigValue('emag_send_to_emag_product_status');

        $prod_ids=[];
        if($this->config->emag_prod_batch_send_to_emag=='yes')
            $sql=$wpdb->prepare("select prod_id from ".$wpdb->prefix."bizzmags_emag_mktpl_send_to_emag where sent=0 limit %d",array((int)$this->config->emag_prod_batch_send_to_emag_nr));
        else
            $sql=$wpdb->prepare("select prod_id from ".$wpdb->prefix."bizzmags_emag_mktpl_send_to_emag where sent=0");
        $results=$wpdb->get_results($sql);
        if(is_array($results))
        {
            foreach($results as $result)
                $prod_ids[]=$result->prod_id;
        }
        $this->updateConfigValue("emag_send_to_emag_started",1);

        if(is_array($prod_ids))
        {
            $products=[];
            foreach($prod_ids as $prod_id)
            {
                $product = wc_get_product($prod_id);
                if(!$product)
                {
                    $this->saveLog(__("Could not load product","bizzmagsmarketplace")." ".$prod_id);
                    $this->setSentToEmagQueue($prod_id);
                    continue;
                }
                $variations = $product->get_children();
                if(!empty($variations))
                {
                    foreach($variations as $variation_id)
                    {
                        $tprod=$this->getProductToPushToEmag($prod_id, $variation_id);
                        if($tprod)
                            $products[]=$tprod;
                    }
                }
                else
                {
                    $tprod=$this->getProductToPushToEmag($prod_id);
                    if($tprod)
                        $products[]=$tprod;
                }
            }
            $all_emag_characteristics=[];
            if(count($products)>0)
            {
                $total_products_to_send=count($products);
                foreach($products as $prod)
                {
                    $prod_id=$prod['prod_id'];
                    $variation_id=$prod['variation_id'];
                    $product_parent = wc_get_product($prod_id);
                    $product = wc_get_product($prod_id);
                    $emag=$prod;
                    if(!$product_parent || !$product)
                    {
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }

                    if($variation_id>0)
                    {
                        $product = new \WC_Product_Variation($variation_id);
                        if(!$product)
                            continue;
                        $emag['id']=$variation_id;
                    }
                    
                    $emag['warranty']=$this->config->emag_warranty_months;
                    $emag['status']=$product_status;
                    $id_category_default=$prod['category_id'];
                    if($id_category_default>0)
                    {
                        $sql=$wpdb->prepare("select emag_cat_id from ".$wpdb->prefix."bizzmags_emag_mktpl_category_map where wc_cat_id=%d",$id_category_default);
                        $category_id=(int)$wpdb->get_var($sql);
                    }
                    if($category_id==0)
                    {
                        $this->saveLog(__("Error: Cannot load product category, please set the category map correctly","bizzmagsmarketplace")." ".$prod_id." ".$variation_id." ".$id_category_default);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    $emag['category_id']=$category_id;
                    $emag['characteristics']=[];

                    $attributes=$this->getWcProductAttributes($prod_id);
                    if($variation_id>0)
                        $attributes=$this->getWcVariationAttributes($variation_id);
                    if(is_array($attributes))
                    {
                        foreach($attributes as $attribute)
                        {
                            $sql=$wpdb->prepare("select char_id from ".$wpdb->prefix."bizzmags_emag_mktpl_char_map where attribute=%s",$attribute['name']);
                            $char_id=(int)$wpdb->get_var($sql);
                            if($char_id>0)
                                $emag['characteristics'][]=['id'=>$char_id,'value'=>$attribute['value']];
                            else
                                $this->saveLog(__("Warning: did not find characteristic map for","bizzmagsmarketplace")." ".$attribute['name']." ".$attribute['value']." ".$prod_id." ".$variation_id);
                        }
                    }
                    if($variation_id>0)
                    {
                        $variations=$this->getWcVariationTaxonomyAttributes($variation_id);
                        $var_attrs=[];
                        foreach($variations as $variation)
                            $var_attrs[]=str_ireplace("pa_","",$variation['name']);
                        $var_attrs=array_unique($var_attrs);
                        sort($var_attrs);
                        $var_attrs_str=implode(",",$var_attrs);
                        $sql=$wpdb->prepare("select fam_id from ".$wpdb->prefix."bizzmags_emag_mktpl_fam_map where attribute=%s",sanitize_text_field($var_attrs_str));
                        $fam_id=$wpdb->get_var($sql);
                        if($fam_id>0)
                        {
                            $emag['family']=array('id'=>$prod_id,'family_type_id'=>$fam_id,'name'=>$product_parent->get_title());
                            $sql=$wpdb->prepare("select c.id, c.is_mandatory, c.name FROM
                            ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam f
                            inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_char fc on fc.fam_id=f.id and fc.cat_id=f.cat_id
                            inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category_char c on c.cat_id=fc.cat_id and c.id=fc.characteristic_id
                            where 
                            f.id=%d and f.cat_id=%d",array((int)$fam_id,(int)$emag['category_id']));
                            $mandatory_fam=$wpdb->get_results($sql);
                            if(is_array($mandatory_fam))
                            {
                                $missing_char_ids=[];
                                if(is_array($emag['characteristics']))
                                {
                                    $to_send_chars=[];
                                    foreach($emag['characteristics'] as $char)
                                        $to_send_chars[]=$char['id'];
                                    foreach($mandatory_fam as $char_id)
                                    {
                                        $all_emag_characteristics[]=$char_id->id;
                                        if(!in_array($char_id->id, $to_send_chars))
                                            $missing_char_ids[]=$char_id->id;
                                    }
                                }
                                if(count($missing_char_ids)>0)
                                {

                                    $this->saveLog(__("Warning: Missing mandatory characteristics from family for","bizzmagsmarketplace")." ".$prod_id." ".$variation_id." ".implode(", ",$missing_char_ids));
                                }
                            }
                        }
                        else
                        {
                            $this->saveLog(__("Error: Missing family map","bizzmagsmarketplace")." ".$prod_id." ".$variation_id." ".$var_attrs_str);
                            $this->setSentToEmagQueue($prod_id);
                            continue;
                        }
                    }
                    
                    $emag['characteristics']=$this->unique_multidimensional_array($emag['characteristics']);
                    
                    if($force_update_images==1)
                        $emag['force_images_download']=1;

                    $missing_char_ids=[];
                    $sql=$wpdb->prepare("select c.name, c.id as char_id from ".$wpdb->prefix."bizzmags_emag_mktpl_category_char_rel cr
                    inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category_char c on c.id=cr.char_id and c.cat_id=cr.cat_id and c.cat_id=%d
                    where c.is_mandatory=1",(int)$emag['category_id']);
                    $mandatory=$wpdb->get_results($sql);
                    if(is_array($mandatory))
                    {
                        $mandatory_char_ids=[];
                        foreach($mandatory as $res)
                            $mandatory_char_ids[]=$res->char_id;
                        if(is_array($emag['characteristics']))
                        {
                            $to_send_chars=[];
                            foreach($emag['characteristics'] as $char)
                                $to_send_chars[]=$char['id'];
                            foreach($mandatory_char_ids as $char_id)
                            {
                                if(!in_array($char_id, $to_send_chars))
                                    $missing_char_ids[]=$char_id;
                            }
                        }
                    }
                    if(count($missing_char_ids)>0)
                        $this->saveLog(__("Warning: Missing mandatory characteristics for","bizzmagsmarketplace")." ".$prod_id." ".$variation_id." ".implode(", ",$missing_char_ids));

                    $sql=$wpdb->prepare("select c.name, c.id as char_id from ".$wpdb->prefix."bizzmags_emag_mktpl_category_char_rel cr
                    inner join ".$wpdb->prefix."bizzmags_emag_mktpl_category_char c on c.id=cr.char_id and c.cat_id=cr.cat_id and c.cat_id=%d
                    where 1",(int)$emag['category_id']);
                    $chars_res=$wpdb->get_results($sql);
                    $product_chars=[];
                    if(is_array($chars_res))
                    {
                        foreach($chars_res as $cr)
                            $product_chars[]=$cr->char_id;
                    }
                    $all_emag_characteristics=array_merge($all_emag_characteristics, $product_chars);
                    if(is_array($all_emag_characteristics) && is_array($emag['characteristics']))
                    {
                        $new_emag_characteristics=[];
                        foreach($emag['characteristics'] as $char)
                        {
                            if(in_array($char['id'], $all_emag_characteristics))
                                $new_emag_characteristics[]=$char;
                            else
                            {
                                $this->saveLog(__("Removed extra char id","bizzmagsmarketplace")." ".$prod_id." ".$variation_id." ".$char['id']);
                            }
                        }
                        $emag['characteristics']=$new_emag_characteristics;
                    }
                    if($emag['name']=="")
                    {
                        $this->saveLog(__("Warning: Missing product title, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if($emag['part_number']=="")
                    {
                        $this->saveLog(__("Warning: Missing product part_number, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if($emag['brand']=="")
                    {
                        $this->saveLog(__("Warning: Missing product brand, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if(is_array($emag['images']) && count($emag['images'])>0)
                    {
                        $img_cnt=0;
                        for($icnt=0;$icnt<count($emag['images']);$icnt++)
                        {
                            $img_cnt++;
                            if($img_cnt==1 || $img_cnt==2)
                            {
                                if(!isset($emag['images'][$icnt]['display_type']))
                                    $emag['images'][$icnt]['display_type']=$img_cnt;
                            }
                            else
                            {
                                if(!isset($emag['images'][$icnt]['display_type']))
                                    $emag['images'][$icnt]['display_type']=0;
                            }
                        }
                        foreach($emag['images'] as $image)
                        {
                            if(!isset($image['url']) || (isset($image['url']) && $image['url']==""))
                            {
                                $this->saveLog(__("Warning: Missing product image, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                                $this->setSentToEmagQueue($prod_id);
                                continue;
                            }
                        }
                    }
                    if($emag['warranty']==="")
                    {
                        $this->saveLog(__("Warning: Missing product waranty, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if((isset($emag['ean']) && is_array($emag['ean']) && isset($emag['ean'][0]) && $emag['ean'][0]=="") || !isset($emag['ean']))
                    {
                        $this->saveLog(__("Warning: Missing product ean, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if($emag['status']==="")
                    {
                        $this->saveLog(__("Warning: Missing product status, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if($emag['sale_price']==="" || (int)$emag['sale_price']==0)
                    {
                        $this->saveLog(__("Warning: Missing product sale_price, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if($emag['min_sale_price']==="" || (int)$emag['min_sale_price']==0)
                    {
                        $this->saveLog(__("Warning: Missing product min_sale_price, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if($emag['max_sale_price']==="" || (int)$emag['max_sale_price']==0)
                    {
                        $this->saveLog(__("Warning: Missing product max_sale_price, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    if((isset($emag['stock']) && is_array($emag['stock']) && isset($emag['stock'][0]) && $emag['stock'][0]==="") || !isset($emag['stock']))
                    {
                        $this->saveLog(__("Warning: Missing product stock, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    $emag['stock']=[$emag['stock']];
                    if((isset($emag['handling_time']) && is_array($emag['handling_time']) && isset($emag['handling_time'][0]) && $emag['handling_time'][0]==="") || !isset($emag['handling_time']))
                    {
                        $this->saveLog(__("Warning: Missing product handling_time, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    $emag['handling_time']=[$emag['handling_time']];
                    if($emag['vat_id']==="" || !is_numeric($emag['vat_id']))
                    {
                        $this->saveLog(__("Warning: Missing product vat_id, SKIP for", "bizzmagsmarketplace")." ".$prod_id." ".$variation_id);
                        $this->setSentToEmagQueue($prod_id);
                        continue;
                    }
                    unset($emag['prod_id']);
                    unset($emag['variation_id']);
                    if($this->config->emag_send_to_emag_live=='yes')
                    {
                        $emag_result = $this->doEmagRequest('product_offer', 'save', [$emag], 'POST');
                        $have_errors = $this->getResultError($emag_result);
                        if ($have_errors == ''){
                            $this->saveLog(__("Sent product to eMag with Success","bizzmagsmarketplace")." ".$emag['id']);
                        }
                        else
                            $this->saveLog(__("Error in sending product to eMag with","bizzmagsmarketplace")." ".$emag['id']);
                        $this->saveLog(print_r($emag,true));
                    }
                    else
                        $this->saveLog(print_r($emag,true));            
                    
                    $this->setSentToEmagQueue($prod_id);
                }
                if($this->config->emag_prod_batch_send_to_emag=='yes')
                {
                    $this->saveLog(__("Sent to eMag batch of","bizzmagsmarketplace")." ".$total_products_to_send." ".__("products","bizzmagsmarketplace"));
                    $to_import=$this->getSendToEmagCount();
                    if((int)$to_import>0)
                    {
                        if(is_file(WC()->plugin_path()."/packages/action-scheduler/action-scheduler.php"))
                        {
                            $action_added=$this->addActionSchedulerTask("bizzmagsmarketplace_send_to_emag_batch_reschedule_hook",array(),"bizzmagsmarketplace");
                            if(!$action_added)
                            {
                                $this->saveLog(__("Error in rescheduling the Send to eMag batch process","bizzmagsmarketplace"));
                                $this->updateConfigValue("emag_send_to_emag_started",0);
                            }
                        }
                    }
                    else
                    {
                        $this->saveLog(__("Finished the Send to eMag process","bizzmagsmarketplace"));
                        $this->updateConfigValue("emag_send_to_emag_started",0);
                        $this->removeSendToEmagQueue();
                    }
                }
                else
                {
                    $this->saveLog(__("Finished the Send to eMag process","bizzmagsmarketplace")." ".$total_products_to_send." ".__("products","bizzmagsmarketplace"));
                    $this->updateConfigValue("emag_send_to_emag_started",0);
                    $this->removeSendToEmagQueue();
                }
            }
            else
            {
                $this->saveLog(__("Nothing to send to emag","bizzmagsmarketplace"));
                $this->updateConfigValue("emag_send_to_emag_started",0);
            }
        }
        else
        {
            $this->saveLog(__("No products in send to emag queue","bizzmagsmarketplace"));
            $this->updateConfigValue("emag_send_to_emag_started",0);
        }
    }
    public function sendToEmagBatchReschedule()
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if(is_file(WC()->plugin_path()."/packages/action-scheduler/action-scheduler.php"))
        {
            $action_added=$this->addActionSchedulerTask("bizzmagsmarketplace_send_to_emag_hook",array(),"bizzmagsmarketplace");
            if(!$action_added)
            {
                $this->saveLog(__("Error in setting up the Send to eMag batch process","bizzmagsmarketplace"));
                $this->updateConfigValue("emag_send_to_emag_started",0);
            }
        }
    }
    public function unique_multidimensional_array($array) {
        $tempArray = array();
        $keyArray = array();
        
        foreach ($array as $val) {
            if (!in_array(serialize($val), $keyArray)) {
                $keyArray[] = serialize($val);
                $tempArray[] = $val;
            }
        }
        return $tempArray;
    }
    public function clearAllLogs()
    {
        global $wpdb;
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_admin_referer( 'bizzmagsmarketplace_clear_all_logs' );
        $sql=$wpdb->prepare("truncate table ".$wpdb->prefix."bizzmags_emag_mktpl_logs");
        if(!$wpdb->query($sql))
            return array('status'=>'error','msg'=>array(__("Error in clearing all logs","bizzmagsmarketplace")));
        else
            return array('status'=>'updated','msg'=>array(__("Cleared all logs with success","bizzmagsmarketplace")));
    }
}