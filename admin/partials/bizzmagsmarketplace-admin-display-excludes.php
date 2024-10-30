<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

?>
<h2><?php esc_html_e('Excludes','bizzmagsmarketplace');?></h2>
<?php
if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
?>
<p><?php esc_html_e('Here you can see what products are excluded (setting stock 0) for the feed, this is not used in this free version of the plugin','bizzmagsmarketplace');?>
    <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
</p>

<div class="container emag-no-pad">
<div class="emag-dash-row">
<?php
echo "<br /><strong>";
esc_html_e("Exclude by SKU","bizzmagsmarketplace");
echo "</strong><br />";
$products=$emag->getExcludesSku();
if(count($products)>0)
{
    $cnt=0;
    ?>
    <table>
        <thead>
            <tr>
                <td><?php esc_html_e("Cnt","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Product","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Sku","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Stock","bizzmagsmarketplace");?></td>
            </tr>
        </thead>
    <?php
    foreach($products as $product)
    {
        $cnt++;
        $title = $product->get_title();
        $url = $product->get_permalink();
        $sku = $product->get_sku();
        $stock = $product->get_stock_quantity();
        ?>
        <tr>
            <td><?php echo esc_html($cnt);?></td>
            <td><a href="<?php echo esc_url($url);?>" target="_blank"><?php echo esc_html($title);?></a></td>
            <td><?php echo esc_html($sku);?></td>
            <td><?php echo esc_html($stock);?></td>
        </tr>
        <?php
    }
    ?>
    </table>
    <?php
}
else
    esc_html_e("No records","bizzmagsmarketplace");

echo "<br /><strong>";
esc_html_e("Exclude by Weight","bizzmagsmarketplace");
echo "</strong><br />";
$products=$emag->getExcludesWeight();
if(count($products)>0)
{
    $cnt=0;
    ?>
    <table>
        <thead>
            <tr>
                <td><?php esc_html_e("Cnt","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Product","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Weight","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Sku","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Stock","bizzmagsmarketplace");?></td>
            </tr>
        </thead>
    <?php
    foreach($products as $product)
    {
        $cnt++;
        $title = $product->get_title();
        $url = $product->get_permalink();
        $sku = $product->get_sku();
        $weight = $product->get_weight();
        $stock = $product->get_stock_quantity();
        ?>
        <tr>
            <td><?php echo esc_html($cnt);?></td>
            <td><a href="<?php echo esc_url($url);?>" target="_blank"><?php echo esc_html($title);?></a></td>
            <td><?php echo esc_html($weight);?></td>
            <td><?php echo esc_html($sku);?></td>
            <td><?php echo esc_html($stock);?></td>
        </tr>
        <?php
    }
    ?>
    </table>
    <?php
}
else
    esc_html_e("No records","bizzmagsmarketplace");

echo "<br /><strong>";
esc_html_e("Exclude by Price","bizzmagsmarketplace");
echo "</strong><br />";
$products=$emag->getExcludesPrice();
if(count($products)>0)
{
    $cnt=0;
    ?>
    <table>
        <thead>
            <tr>
                <td><?php esc_html_e("Cnt","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Product","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Price","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Sku","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Stock","bizzmagsmarketplace");?></td>
            </tr>
        </thead>
    <?php
    foreach($products as $product)
    {
        $cnt++;
        $title = $product->get_title();
        $url = $product->get_permalink();
        $sku = $product->get_sku();
        $stock = $product->get_stock_quantity();
        $price = wc_get_price_excluding_tax($product);
        ?>
        <tr>
            <td><?php echo esc_html($cnt);?></td>
            <td><a href="<?php echo esc_url($url);?>" target="_blank"><?php echo esc_html($title);?></a></td>
            <td><?php echo esc_html($price);?></td>
            <td><?php echo esc_html($sku);?></td>
            <td><?php echo esc_html($stock);?></td>
        </tr>
        <?php
    }
    ?>
    </table>
    <?php
}
else
    esc_html_e("No records","bizzmagsmarketplace");

echo "<br /><strong>";
esc_html_e("Exclude by Stock lower","bizzmagsmarketplace");
echo "</strong><br />";
$products=$emag->getExcludesStockLower();
if(count($products)>0)
{
    $cnt=0;
    ?>
    <table>
        <thead>
            <tr>
                <td><?php esc_html_e("Cnt","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Product","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Price","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Sku","bizzmagsmarketplace");?></td>
                <td><?php esc_html_e("Stock","bizzmagsmarketplace");?></td>
            </tr>
        </thead>
    <?php
    foreach($products as $product)
    {
        $cnt++;
        $title = $product->get_title();
        $url = $product->get_permalink();
        $sku = $product->get_sku();
        $stock = $product->get_stock_quantity();
        $price = wc_get_price_excluding_tax($product);
        ?>
        <tr>
            <td><?php echo esc_html($cnt);?></td>
            <td><a href="<?php echo esc_url($url);?>" target="_blank"><?php echo esc_html($title);?></a></td>
            <td><?php echo esc_html($price);?></td>
            <td><?php echo esc_html($sku);?></td>
            <td><?php echo esc_html($stock);?></td>
        </tr>
        <?php
    }
    ?>
    </table>
    <?php
}
else
    esc_html_e("No records","bizzmagsmarketplace");
?>
</div>
</div>
<?php
}
else
    esc_html_e("Please set up the credentials first","bizzmagsmarketplace");
?>