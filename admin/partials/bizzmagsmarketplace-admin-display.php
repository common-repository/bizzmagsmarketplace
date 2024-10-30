<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

require_once BIZZMAGSMARKETPLACE_PLUGIN_PATH  . 'includes/emag.php';
$emag=new BizzmagsMarketplace\emag;


$default_tab = "bizzmagsmarketplace";
$tab = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : $default_tab;
?>
<div class="wrap bizzmagsmarketplace-wrap">
<!-- Print the page title -->
<h1 id="bizzmagsmarketplace_top_row">
    <?php esc_html_e("Bizzmags Marketplace","bizzmagsmarketplace");?>
</h1>
<!-- Here are our tabs -->
<nav class="nav-tab-wrapper">
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Dashboard','bizzmagsmarketplace');?></a>
  <?php
  if($emag->config->emag_dropshipping=='yes')
  {
  ?>
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-categories");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-categories'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Categories','bizzmagsmarketplace');?></a>
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-category");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-category'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Category','bizzmagsmarketplace');?></a>
  <?php
  }
  ?>
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-configuration");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-configuration'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Configuration','bizzmagsmarketplace');?></a>
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-prices");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-prices'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Prices','bizzmagsmarketplace');?></a>

  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-category-map");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-category-map'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Category Map','bizzmagsmarketplace');?></a>

  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-characteristics-map");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-characteristics-map'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Characteristics Map','bizzmagsmarketplace');?></a>
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-family-map");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-family-map'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Family Map','bizzmagsmarketplace');?></a>
  
  <?php
  if($emag->config->emag_dropshipping=='yes')
  {
  ?>
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-excludes");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-excludes'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Excludes','bizzmagsmarketplace');?></a>
  <?php
  }
  ?>
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-logs");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-logs'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Logs','bizzmagsmarketplace');?></a>
  <a href="<?php echo esc_url(admin_url()."admin.php?page=bizzmagsmarketplace-help");?>" class="nav-tab <?php if($tab==='bizzmagsmarketplace-help'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Help','bizzmagsmarketplace');?></a>
</nav>

<div class="tab-content">
<?php switch($tab) :
case 'bizzmagsmarketplace-categories':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-categories.php";
    break;
case 'bizzmagsmarketplace-category':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-category.php";
    break;
case 'bizzmagsmarketplace-configuration':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-configuration.php";
    break;
case 'bizzmagsmarketplace-offers':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-offers.php";
    break;
case 'bizzmagsmarketplace-prices':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-prices.php";
    break;
case 'bizzmagsmarketplace-excludes':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-excludes.php";
    break;
case 'bizzmagsmarketplace-category-map':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-category-map.php";
    break;
case 'bizzmagsmarketplace-characteristics-map':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-characteristics-map.php";
    break;
case 'bizzmagsmarketplace-family-map':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-family-map.php";
    break;
case 'bizzmagsmarketplace-logs':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-logs.php";
    break;
case 'bizzmagsmarketplace-help':
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-help.php";
    break;
default:
    require_once plugin_dir_path(__FILE__)."bizzmagsmarketplace-admin-display-dashboard.php";
    break;
endswitch; ?>
</div>
</div>
