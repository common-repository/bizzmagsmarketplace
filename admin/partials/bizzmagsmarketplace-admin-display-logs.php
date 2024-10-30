<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

$msg="";
$action=isset($_POST['action'])?sanitize_text_field($_POST['action']):"";
if($action=="bizzmagsmarketplace_clear_all_logs")
{
    check_admin_referer( 'bizzmagsmarketplace_clear_all_logs' );
    $msg=$emag->clearAllLogs();
}
?>

<h2><?php esc_html_e('Logs','bizzmagsmarketplace');?></h2>
<?php
if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
?>
<p><?php esc_html_e('Here you can see system logs','bizzmagsmarketplace');?></p>
<?php
if($msg!="")
{
    if(is_array($msg['msg']))
    {
        foreach($msg['msg'] as $m)
        {
            ?>
            <div class="<?php echo esc_attr($msg['status']);?> notice is-dismissible inline">
                <p><?php echo esc_html($m);?></p>
            </div>
            <?php
        }
    }
    else
    {
    ?>
    <div class="<?php echo esc_attr($msg['status']);?> notice is-dismissible inline">
        <p><?php echo esc_html($msg['msg']);?></p>
    </div>
    <?php
    }
}

$logs_per_page=isset($_GET['logs_per_page'])?(int)$_GET['logs_per_page']:100;
$logs_page=isset($_GET['logs_page'])?(int)$_GET['logs_page']:1;

$logs_pages=$emag->getLogsPages($logs_per_page);
$logs=$emag->getLogs($logs_page,$logs_per_page);
?>
<form method="post" action="" autocomplete="off" onSubmit="return confirm('<?php echo esc_js(__("Are you sure you want to delete all logs?","bizzmagsmarketplace"));?>')">
<?php $nonce=wp_create_nonce( 'bizzmagsmarketplace_clear_all_logs' );?>
<input type="hidden" name="action" value="bizzmagsmarketplace_clear_all_logs" />
<input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce);?>" />
<input type="submit" class="button button->secondary" value="<?php echo esc_attr(__("Clear logs","bizzmagsmarketplace"));?>" />
</form>
<div class="container emag-no-pad">
<div class="emag-dash-row">

<div class="row">
  <div class="col mb-2">
    <label for="logs_pages"><?php esc_html_e("Page","bizzmagsmarketplace");?></label>
  </div>
  <div class="col mb-2">
    <?php
    if(is_array($logs_pages) && count($logs_pages)>0)
    {
        ?>
        <select id="logs_pages" onchange="javascript:toggleButton();" class="form-control">
            <?php
            foreach($logs_pages as $page)
            {
                ?>
                <option <?php echo (($page==$logs_page)?' selected="selected"':'');?> value="<?php echo esc_attr($page);?>"><?php echo esc_html($page);?></option>
                <?php
            }
            ?>
        </select>
        <?php
    }
    ?>
  </div>
  <div class="col mb-2">
    <label for="logs_per_page"><?php esc_html_e("Per Page","bizzmagsmarketplace");?></label>
  </div>
  <div class="col mb-2">
    <select id="logs_per_page" onChange="javascript:toggleButton();" class="form-control">
          <option <?php echo (($logs_per_page == 10)?' selected="selected"':'');?> value="10">10</option>
          <option <?php echo (($logs_per_page == 25)?' selected="selected"':'');?> value="25">25</option>
          <option <?php echo (($logs_per_page == 50)?' selected="selected"':'');?> value="50">50</option>
          <option <?php echo (($logs_per_page == 100)?' selected="selected"':'');?> value="100">100</option>
          <option <?php echo (($logs_per_page == 250)?' selected="selected"':'');?> value="250">250</option>
      </select>
  </div>
</div>
<?php
    if(is_array($logs) && count($logs)>0)
    {
        foreach($logs as $log)
        {
            ?>

    <div class="row">
    <div class="col mb-2">
        <?php echo esc_html(gmdate("d/m/Y H:i:s",$log->mdate));?>
    </div>
    <div class="col mb-2">
        <?php echo nl2br(esc_html($log->log));?>
    </div>
    </div>
            <?php
        }
    }
    else
    {
?>
    <div class="row mb-2">
    <div class="col mb-2">
        <?php esc_html_e("No records","bizzmagsmarketplace");?>
    </div>
    </div>
<?php
    }
?>
</div>
</div>
<?php
$data_js="
var logs_page=".$logs_page.";
var logs_per_page=".$logs_per_page.";

var page_url='".esc_js(admin_url('admin.php'))."?page=bizzmagsmarketplace-logs';
function toggleButton()
{
  var concat_url='&logs_page='+jQuery('#logs_pages').val();
  if(logs_per_page!=jQuery('#logs_per_page').val())
  {
      jQuery('#logs_pages').val(1);
  }
  concat_url+='&logs_per_page='+jQuery('#logs_per_page').val();
  if(jQuery('#logs_per_page').val()==100 && jQuery('#logs_pages').val()==1)
  {
      concat_url='';
  }
  window.location.href=page_url+concat_url;
}
";
if($data_js!='')
{
    wp_register_script( 'bizzmagsmarketplace_logs_page_inline_js', '' );
    wp_enqueue_script( 'bizzmagsmarketplace_logs_page_inline_js' );
    wp_add_inline_script("bizzmagsmarketplace_logs_page_inline_js",$data_js);
}
}
else
    esc_html_e("Please set up the credentials first","bizzmagsmarketplace");
?>