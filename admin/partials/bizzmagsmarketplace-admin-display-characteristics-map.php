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
if($action=="bizzmagsmarketplace_load_char_map_defaults")
{
    check_admin_referer( 'bizzmagsmarketplace_load_char_map_defaults' );
    $msg=$emag->loadCharMapDefaults();
}
?>

<h2><?php esc_html_e('Characteristics Map','bizzmagsmarketplace');?></h2>
<?php
if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
    $data=$emag->getCharacteristicsMap();
$characteristics=$data->characteristics;
$attributes=$data->attributes;
?>
<p><?php esc_html_e('Here you can map eMag characteristics with WC product attributes, this map is not used for the products CSV Feed, it is used only for sending products via the eMag API','bizzmagsmarketplace');?></p>
<form method="post" action="" autocomplete="off" onSubmit="return confirm('<?php echo esc_js(__("Map attributes with characteristics by name","bizzmagsmarketplace"));?>?')">
<?php $nonce=wp_create_nonce( 'bizzmagsmarketplace_load_char_map_defaults' );?>
<input type="hidden" name="action" value="bizzmagsmarketplace_load_char_map_defaults" />
<input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce);?>" />
<input type="submit" value="<?php echo esc_attr(__("Load defaults","bizzmagsmarketplace"));?>" class="button button-secondary" />
</form>
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
?>
<div class="container emag-no-pad">
<div class="emag-dash-row">
<?php
if(is_array($characteristics) && count($characteristics)>0)
{
    ?>
    <div class="row mb-2">
    <div class="col-3 mb-2">
        <strong><?php esc_html_e("eMag Characteristic","bizzmagsmarketplace");?></strong>
        <span style="cursor:pointer;" onClick="javascript:jQuery('.char_map_optional_char').toggle();"><?php esc_html_e("Toggle Optional","bizzmagsmarketplace");?></span>
    </div>
    <div class="col-3 mb-2">
        <strong><?php esc_html_e("Search","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-3 mb-2">
        <strong><?php esc_html_e("WC Attribute","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("Save","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        
    </div>
    <div class="col-1 mb-2">
        
    </div>
    </div>
    <?php
    foreach($characteristics as $char)
    {
        $cnt=0;
        $cls="";
        if($char->is_mandatory==0)
        {
            $cls=' char_map_optional_char';
        }
        foreach($char->map as $map)
        {
    ?>
    <div class="row mb-2<?php echo esc_attr($cls);?>"<?php echo (($char->is_mandatory==0)?' style="display:none;"':"");?>>
    <div class="col-3 mb-2">
        <?php
        if($cnt==0)
        {
            if($char->is_mandatory==0)
            {
            ?>
            <strong style="color:blue;">*</strong>
            <?php
            }
            else
            {
                ?>
            <strong style="color:red;">*</strong>
                <?php
            }
        ?>
        <?php echo esc_html($char->name);?> [<?php echo esc_html($char->id);?>]
        <?php
        }
        ?>
    </div>
    <div class="col-3 mb-2">
        <input style="width:100%;" type="search" class="search_attribute button" char_id="<?php echo esc_attr($char->id);?>" />
    </div>
    <div class="col-3 mb-2">
        <select onChange="javascript:saveCharAttribute(jQuery('#save_btn_<?php echo esc_js($char->id);?>'));" style="width:100%;" class="button attributes_<?php echo esc_attr($char->id);?>">
            <?php
            if(is_array($attributes))
            {
                ?>
                <option value=""> - </option>
                <?php
                foreach($attributes as $attribute)
                {
                    ?>
                    <option <?php echo (($attribute->attribute==$map->attribute)?' selected="selected"':'');?>value="<?php echo esc_attr($attribute->attribute);?>"><?php echo esc_html($attribute->name);?></option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <div class="col-1 mb-2">
        <?php
        if($cnt==0)
        {
        ?>
        <input type="button" id="save_btn_<?php echo esc_attr($char->id);?>" class="button button-secondary" value="<?php echo esc_attr(__("Save","bizzmagsmarketplace"));?>" onClick="javascript:saveCharAttribute(this);" char_id="<?php echo esc_attr($char->id);?>" />
        <?php
        }
        ?>
    </div>
    <div class="col-1 mb-2">
        <?php
        if($cnt==0)
        {
        ?>
        <input char_id="<?php echo esc_attr($char->id);?>" type="button" class="button button-secondary" onClick="javascript:addNewMap(this);" value="+" />
        <?php
        }
        ?>
    </div>
    <div class="col-1 mb-2">
        <?php
        if($cnt>0)
        {
        ?>
        <input onclick="javascript:removeCharAttribute(this, <?php echo esc_attr($char->id);?>);" type="button" class="button button-secondary" value="-">
        <?php
        }
        ?>
    </div>
    </div>
<?php
        $cnt++;
    }
    if(count($char->map)==0)
    {
?>
    <div class="row mb-2<?php echo esc_attr($cls);?>"<?php echo (($char->is_mandatory==0)?' style="display:none;"':"");?>>
    <div class="col-3 mb-2">
        <?php
        if($char->is_mandatory==0)
        {
        ?>
        <strong style="color:blue;">*</strong>
        <?php
        }
        else
        {
            ?>
        <strong style="color:red;">*</strong>
            <?php
        }
        ?>
        <?php echo esc_html($char->name);?> [<?php echo esc_html($char->id);?>]
    </div>
    <div class="col-3 mb-2">
        <input style="width:100%;" type="search" class="search_attribute button" char_id="<?php echo esc_attr($char->id);?>" />
    </div>
    <div class="col-3 mb-2">
        <select onChange="javascript:saveCharAttribute(jQuery('#save_btn_<?php echo esc_js($char->id);?>'));" style="width:100%;" class="button attributes_<?php echo esc_attr($char->id);?>">
            <?php
            if(is_array($attributes))
            {
                ?>
                <option value=""> - </option>
                <?php
                foreach($attributes as $attribute)
                {
                    ?>
                    <option value="<?php echo esc_attr($attribute->attribute);?>"><?php echo esc_html($attribute->name);?></option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <div class="col-1 mb-2">
        <input type="button" id="save_btn_<?php echo esc_attr($char->id);?>" class="button button-secondary" value="<?php echo esc_attr(__("Save","bizzmagsmarketplace"));?>" onClick="javascript:saveCharAttribute(this);" char_id="<?php echo esc_attr($char->id);?>" />
    </div>
    <div class="col-1 mb-2">
        <input char_id="<?php echo esc_attr($char->id);?>" type="button" class="button button-secondary" onClick="javascript:addNewMap(this);" value="+" />
    </div>
    <div class="col-1 mb-2">
    </div>
    </div>
<?php
    }
    }
    $nonce_1=wp_create_nonce( 'bizzmagsmarketplace_save_characteristic_map_nonce' );
    $data_js="

function addNewMap(obj)
{
    var char_id=jQuery(obj).attr('char_id');
    var row_to_add = jQuery(obj).parent().parent();
    the_element=row_to_add.clone();
    var div_attr_name = the_element.find('div').eq(0);
    div_attr_name.html('');
    var div_search = the_element.find('input');
    div_search.val('');
    var div_attributes = the_element.find('select');
    div_attributes.val('');
    var div_save = the_element.find('div').eq(3);
    div_save.html('');
    var div_add = the_element.find('div').eq(4);
    div_add.html('');
    var div_del = the_element.find('div').eq(5);
    div_del.html('<input onClick=\"javascript:removeCharAttribute(this, '+char_id+');\" type=\"button\" class=\"button button-secondary\" value=\"-\" />');
    row_to_add.after(the_element);
    setSearches();
}
function removeCharAttribute(obj, char_id)
{
    var row_to_del = jQuery(obj).parent().parent();
    jQuery(row_to_del).remove();
    saveCharAttribute(jQuery('#save_btn_'+char_id));
}
function setSearches()
{
    jQuery('.search_attribute').on('keyup input', function() {
      var searchText = jQuery(this).val().toLowerCase();
      var char_id=jQuery(this).attr('char_id');
      var the_select=jQuery(this).closest('div').nextAll('div').find('select').first()
      ;
      jQuery('#save_btn_'+char_id).val('".esc_js(__("Save","bizzmagsmarketplace"))."');
      jQuery('#save_btn_'+char_id).addClass('button button-secondary');
      jQuery('#save_btn_'+char_id).removeClass('button button-primary');
      if(searchText==''){
        the_select.val('');
        saveCharAttribute(jQuery('#save_btn_'+char_id));
      }
      var foundMatch = false;
      the_select.find('option').each(function() {
          var optionText = jQuery(this).text().toLowerCase();
          if (searchText === '' || optionText.includes(searchText)) {
              jQuery(this).show();
              if (!foundMatch) {
                  foundMatch = true;
                  the_select.val(jQuery(this).val());
                  if(searchText.length>=3){
                    saveCharAttribute(jQuery('#save_btn_'+char_id));
                  }
              }
          } else {
              jQuery(this).hide();
          }
      });
      if (!foundMatch) {
            the_select.val('');
      }
  });
}
jQuery(document).ready(function() {
  setSearches();
});

function saveCharAttribute(obj)
{
    var char_id=jQuery(obj).attr('char_id');
    var attributes=[];
    if(parseInt(char_id)>0)
    {
        jQuery('.attributes_'+char_id).each(function(index,val){
            attributes.push(jQuery(val).val());
            });
        jQuery('#save_btn_'+char_id).val('...');
        jQuery.ajax({
            method: 'POST',
            data: { 
                'action': 'bizzmagsmarketplace_save_characteristic_map', 
                'security': '".esc_js($nonce_1)."',
                'char_id': char_id,
                'attributes': attributes,
            },
            url: '".esc_url(admin_url('admin-ajax.php'))."',
            async: true
        })
        .done(function( msg ) {
            jQuery('#save_btn_'+char_id).val(msg);
            jQuery('#save_btn_'+char_id).removeClass('button button-secondary');
            jQuery('#save_btn_'+char_id).addClass('button button-primary');
        });
    }
    else
    {
        alert('".esc_js(__("Something is wrong, did not receive all data","bizzmagsmarketplace"))."');
    }
}

";

if($data_js!='')
{
    wp_register_script( 'bizzmagsmarketplace_char_map_page_inline_js', '' );
    wp_enqueue_script( 'bizzmagsmarketplace_char_map_page_inline_js' );
    wp_add_inline_script("bizzmagsmarketplace_char_map_page_inline_js",$data_js);
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
}
else
    esc_html_e("Please set up the credentials first","bizzmagsmarketplace");
?>