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
if($action=="bizzmagsmarketplace_emag_add_new_category_map")
{
    check_admin_referer( 'bizzmagsmarketplace_emag_add_new_category_map' );
    $msg=$emag->addNewCategoryMap();
}

?>
<h2><?php esc_html_e('Category Map','bizzmagsmarketplace');?></h2>
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



if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
    $categories_arr=$emag->getCategoryMap();
    $categories=$categories_arr[0];
    $missing_categories=$categories_arr[1];
    $product_categories_ordered=$emag->getAllWcProductCategories();
?>
<p><?php esc_html_e('Here you can map eMag categories with WC product categories, this map is not used for the products CSV Feed, it is used only for sending products via the eMag API','bizzmagsmarketplace');?>
</p>

<div class="container emag-no-pad">
<div class="emag-dash-row">

    <div class="row">
    <div class="col-1 mb-2">
      <strong><?php esc_html_e("WC ID","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-4 mb-2">
        <strong><?php esc_html_e("WC Category","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("eMag ID","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-4 mb-2">
        <strong><?php esc_html_e("eMag Category","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("Access","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("Edit","bizzmagsmarketplace");?></strong>
    </div>
    </div>

    <?php
    if(is_array($categories) && count($categories)>0)
    {
        foreach($categories as $category)
        {
            if(!$category)
                continue;
            ?>
    <div class="row" id="row_map_id_<?php echo esc_attr($category->map_id);?>">
    <div class="col-1 mb-2">
        <?php echo esc_html($category->id_category);?>
    </div>
    <div class="col-4 mb-2">
        <?php echo esc_html($category->full_path);?>
    </div>
    <div class="col-1 mb-2">
        <?php echo esc_html($category->emag_cat_id);?>
    </div>
    <div class="col-4 mb-2">
        <?php echo esc_html($category->emag_full_path);?>
    </div>
    <div class="col-1 mb-2">
        <?php echo esc_html($category->has_access);?>
    </div>
    <div class="col-1 mb-2" id="map_remove_row_{{category.map_id}}">
        <a href="javascript:;" onClick="javascript:removeCategoryMap('<?php echo esc_js($category->map_id);?>');" class="button button-secondary">-</a>
    </div>
    </div>
            <?php
        }
    }
    else
    {
        ?>
    <div class="row">
    <div class="col mb-2">
        <strong><?php esc_html_e("No records","bizzmagsmarketplace");?></strong>
    </div>
    </div>
        <?php
    }
    ?>
<form method="post" action="" autocomplete="off" onSubmit="return validateNewCategoryMap();">
<?php $nonce=wp_create_nonce( 'bizzmagsmarketplace_emag_add_new_category_map' );?>
<input type="hidden" name="action" value="bizzmagsmarketplace_emag_add_new_category_map" />
<input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce);?>" />
    <div class="row mb-2">
    <div class="col-1 mb-2" id="new_map_wc_cat_id">
        
    </div>
    <div class="col-4 mb-2">
        <input style="width:100%;" type="search" placeholder="<?php echo esc_attr(__("Search WC category","bizzmagsmarketplace"));?>" id="new_map_wc_full_path" class="button" value="" autocomplete="off" />
        <select style="width:100%;" id="new_map_wc_cat_id_select" name="new_map_wc_cat_id_select" onChange="javascript:updateWcCatId();" class="button mt-2">
            <option value=""> - </option>
            <?php
            if(is_array($product_categories_ordered) && count($product_categories_ordered)>0)
            {
                foreach($product_categories_ordered as $cat)
                {
                    ?>
                    <option value="<?php echo esc_attr($cat->id_category);?>"><?php echo esc_html($cat->name);?> [<?php echo esc_html($cat->id_category);?>]</option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <div class="col-1 mb-2" id="new_map_emag_cat_id">
        
    </div>
    <div class="col-4 mb-2">
        <input style="width:100%;" type="search" placeholder="<?php echo esc_attr(__("Please type at least 3 characters","bizzmagsmarketplace"));?>" onKeyUp="javascript:searchEmagCategory();" id="new_map_emag_full_path" class="button" value="" autocomplete="off" />
        <br />
        <select style="width:100%;" id="new_map_emag_cat_result" name="new_map_emag_cat_result" class="button mt-2" onChange="javascript:handleEmagCatSelect();">
          <option value="">-</option>
        </select>
        <span id="new_map_emag_cat_result_search" style="display:none;"><?php esc_html_e("searching","bizzmagsmarketplace");?>...</span>
    </div>
    <div class="col-1 mb-2" id="new_map_emag_has_access">
        
    </div>
    <div class="col-1 mb-2" id="new_map_add_button">
        <input type="submit" class="btn btn-primary" value="+">
    </div>
    </div>
</form>

<?php
$nonce_1=wp_create_nonce( 'bizzmagsmarketplace_emag_marketplace_cat_result_search_nonce' );
$nonce_2=wp_create_nonce( 'bizzmagsmarketplace_emag_marketplace_remove_category_map_nonce' );

$data_js="
function searchWcCategory()
{
    jQuery('#new_map_wc_full_path').on('keyup input', function() {
      var searchText = jQuery(this).val().toLowerCase();
      var the_select=jQuery('#new_map_wc_cat_id_select');
      if(searchText==''){
        the_select.val('');
      }
      var foundMatch = false;
      the_select.find('option').each(function() {
          var optionText = jQuery(this).text().toLowerCase();
          if (searchText === '' || optionText.includes(searchText)) {
              jQuery(this).show();
              if (!foundMatch) {
                  foundMatch = true;
                  the_select.val(jQuery(this).val());
              }
          } else {
              //jQuery(this).hide();
          }
      });
      if (!foundMatch) {
            the_select.val('');
      }
  });
}
jQuery(document).ready(function() {
  searchWcCategory();
});
function validateNewCategoryMap()
{
  if(parseInt(jQuery('#new_map_wc_cat_id_select').val())>0 && parseInt(jQuery('#new_map_emag_cat_result').val())>0)
  {
    return true;
  }
  else
  {
    alert('".esc_js(__("Something is wrong, did you select a pair of categories","bizzmagsmarketplace"))."?');
    return false;
  }
}
function handleEmagCatSelect()
{
  let emag_cat_id=jQuery('#new_map_emag_cat_result').val();
  if(emag_cat_id!='')
  {
    jQuery('#new_map_emag_cat_id').html(emag_cat_id);
    let is_allowed=jQuery('#new_map_emag_cat_result option:selected').attr('is_allowed');
    jQuery('#new_map_emag_has_access').html(is_allowed);
  }
}
function searchEmagCategory()
{
  var search=jQuery('#new_map_emag_full_path').val();
  if(search.length>=3)
  {
    jQuery('#new_map_emag_cat_result').hide();
    jQuery('#new_map_emag_cat_result_search').show();

    jQuery.ajax({
        method: 'POST',
        data: { 
            'action': 'bizzmagsmarketplace_emag_marketplace_cat_result_search',
            'search': search,
            'security': '".esc_js($nonce_1)."',
        },
        url: '".esc_url(admin_url('admin-ajax.php'))."',
        async: true
    })
    .done(function( msg ) {
        jQuery('#new_map_emag_cat_result').html(msg);
        jQuery('#new_map_emag_cat_result').show();
        jQuery('#new_map_emag_cat_result_search').hide();
        if(jQuery('#new_map_emag_cat_result option').length>1)
        {
            var foundMatch = false;
            jQuery('#new_map_emag_cat_result option').each(function(index,val){
            if(jQuery(val).val()==''){
                if (!foundMatch) {
                    foundMatch = true;
                    let results=parseInt(jQuery('#new_map_emag_cat_result option').length)-1;
                    jQuery(val).html(results+' '+'".esc_js(__("Results found","bizzmagsmarketplace"))."');
                }
            }
            });
        }
    });
  }
}
function updateWcCatId()
{
  jQuery('#new_map_wc_cat_id').html(jQuery('#new_map_wc_cat_id_select').val());
}
function removeCategoryMap(id)
{
    if(confirm('".esc_js(__("Remove Category map","bizzmagsmarketplace"))."?'))
    {
      jQuery('#map_remove_btn_'+id).val('...');
      jQuery.ajax({
          method: 'POST',
          data: { 
              'action': 'bizzmagsmarketplace_emag_marketplace_remove_category_map',
              'security': '".esc_js($nonce_2)."',
              'id': id
          },
          url: '".esc_url(admin_url('admin-ajax.php'))."',
          async: true
      })
      .done(function( msg ) {
          jQuery('#row_map_id_'+id).css('opacity','0.5');
          jQuery('#row_map_id_'+id).css('color','red');
          jQuery('#map_remove_row_'+id).html('-');
      });
    }
}
";
wp_register_script( 'bizzmagsmarketplace_emag_add_new_category_map_js', '' );
wp_enqueue_script( 'bizzmagsmarketplace_emag_add_new_category_map_js' );
wp_add_inline_script("bizzmagsmarketplace_emag_add_new_category_map_js",$data_js);
?>

</div>
</div>

<?php
}
else
    esc_html_e("Please set up the credentials first","bizzmagsmarketplace");
?>