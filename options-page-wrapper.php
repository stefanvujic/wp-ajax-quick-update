<?php
if (isset($_POST['flash_options']) && $_POST['flash_options'] == 'true') {
  require_once( '../../../wp-load.php' );
}
if (isset($_POST['individual_flash_options']) && $_POST['individual_flash_options'] == 'true') {
  require_once( '../../../wp-load.php' );
}
if (isset($_POST['individual_flash_options_remove']) && $_POST['individual_flash_options_remove'] == 'true') {
  require_once( '../../../wp-load.php' );
}
if (isset($_POST['remove_flash_options']) && $_POST['remove_flash_options'] == 'true') {
  require_once( '../../../wp-load.php' );
}
if (isset($_POST['exclude_options']) && $_POST['exclude_options'] == 'true') {
  require_once( '../../../wp-load.php' );
}
if (isset($_POST['remove_exclude_options']) && $_POST['remove_exclude_options'] == 'true') {
  require_once( '../../../wp-load.php' );
}
if (isset($_POST['update_brand_sale_marker']) && $_POST['update_brand_sale_marker'] == 'true') {
  require_once( '../../../wp-load.php' );
}

session_start();

if (isset($_GET['download_file']) && $_GET['download_file'] == 'yes') {
    header('Location: ' . site_url() . '/wp-content/plugins/gibbys-quick-update/flash_pricing.xlsx');
    $_SESSION['products_for_excel_download'] = '';
}

global $wpdb;

//Brand and category inc/exl functions
require('brand-cat-flash-sale.php');


if($_GET['view'] == 'logs_quick_update') {
  if($_GET['sku'] != '') {
    //$sku_query = "WHERE sku = '" . $_GET['sku'] . "'";
  }
  echo '<div style="padding: 25px 0px 25px 0px; font-weight:700;">Product Update Logs <span style="font-weight:300;">(ordered by SKU then Date/Time of Change).</span></div>';
  echo '<table border="1" cellspacing="5" cellpadding="5">';
  echo '<tr><td><b>Change ID</b></td><td><b>IP Address</b></td><td><b>User</b></td><td><b>Post ID</b></td><td><b>SKU</b></td><td><b>Title</b></td><td><b>Item</b></td><td><b>From</b></td><td><b>To</b></td><td><b>Date/Time of Change</b></td><td style="width:150px;"><b>Source</b></td></tr>';
  //echo "SELECT * FROM wp_postmeta_backup_quick_update " . $sku_query . " ORDER BY sku ASC, time_of_change ASC";
  $changes = $wpdb->get_results("SELECT * FROM wp_postmeta_backup_quick_update " . $sku_query . " WHERE source != 'Rollover Script' ORDER BY time_of_change DESC, sku ASC");
  $ctr = 0;
  foreach($changes as $change){
    if($ctr == 1 && $change->sku != $old_sku) {
      //echo '<tr><td colspan="11" style="background-color: #cccccc; height: 1px;"></td></tr>';
    }
    echo '<tr><td>'.$change->change_id.'</td><td>'.$change->ip_address.'</td><td>'.$change->user.'</td><td>'.$change->post_id.'</td><td><b>'.$change->sku.'</b></td><td>'.$change->title.'</td><td>'.$change->meta_key.'</td><td>'.$change->meta_value_from.'</td><td>'.$change->meta_value_to.'</td><td>'.date('m-d-y H:i:s', $change->time_of_change).'</td><td>'.$change->source.'</td></tr>';
    $ctr = 1;
    $old_sku = $change->sku;
  }
  echo '</table>';
  die();
}
//Flash sale upload/import functions
require 'flash-sale-price-import.php';

if($_GET['view'] == 'logs_rollover') {
  if($_GET['sku'] != '') {
    //$sku_query = "WHERE sku = '" . $_GET['sku'] . "'";
  }
  echo '<div style="padding: 25px 0px 25px 0px; font-weight:700;">Product Update Logs <span style="font-weight:300;">(ordered by SKU then Date/Time of Change).</span></div>';
  echo '<table border="1" cellspacing="5" cellpadding="5">';
  echo '<tr><td><b>Change ID</b></td><td><b>IP Address</b></td><td><b>User</b></td><td><b>Post ID</b></td><td><b>SKU</b></td><td><b>Title</b></td><td><b>Item</b></td><td><b>From</b></td><td><b>To</b></td><td><b>Date/Time of Change</b></td><td style="width:150px;"><b>Source</b></td></tr>';
  //echo "SELECT * FROM wp_postmeta_backup_quick_update " . $sku_query . " ORDER BY sku ASC, time_of_change ASC";
  $changes = $wpdb->get_results("SELECT * FROM wp_postmeta_backup_quick_update " . $sku_query . " WHERE source = 'Rollover Script' ORDER BY sku ASC, time_of_change ASC");
  $ctr = 0;
  foreach($changes as $change){
    if($ctr == 1 && $change->sku != $old_sku) {
      echo '<tr><td colspan="11" style="background-color: #cccccc; height: 1px;"></td></tr>';
    }
    echo '<tr><td>'.$change->change_id.'</td><td>'.$change->ip_address.'</td><td>'.$change->user.'</td><td>'.$change->post_id.'</td><td><b>'.$change->sku.'</b></td><td>'.$change->title.'</td><td>'.$change->meta_key.'</td><td>'.$change->meta_value_from.'</td><td>'.$change->meta_value_to.'</td><td>'.date('m-d-y H:i:s', $change->time_of_change).'</td><td>'.$change->source.'</td></tr>';
    $ctr = 1;
    $old_sku = $change->sku;
  }
  echo '</table>';
  die();
}

if($_GET['view'] == 'logs_price_change') {
  if($_GET['sku'] != '') {
    //$sku_query = "WHERE sku = '" . $_GET['sku'] . "'";
  }
  date_default_timezone_set('Canada/Eastern');
  $dropdown_options = $wpdb->get_results("SELECT DISTINCT prm.product_id, pm.meta_value FROM wp_price_monitor prm, wp_postmeta pm WHERE prm.product_id = pm.post_id AND pm.meta_key = '_sku' ORDER BY pm.meta_value ASC");
  echo '<div style="padding: 25px 0px 25px 0px; font-weight:700;">Price Changes Logs</div>';

  echo '<div style="padding: 5px 0px 5px 0px; font-weight:700;"><form action="#"><select name="product_id" onchange="submit();">';
  echo '<option value="0">Please Choose SKU:</option>';
  foreach($dropdown_options AS $dropdown_option) {
    if($dropdown_option->product_id == $_GET['product_id']) {
      $selected = 'SELECTED';
    } else {
      $selected = '';
    }
    echo '<option value="' . $dropdown_option->product_id. '" '.$selected.'>' . $dropdown_option->meta_value . '</option>';
  }
  echo '</select><input type="hidden" name="page" value="gibbys_quick_update"><input type="hidden" name="view" value="logs_price_change"></form></div>';
  if(isset($_GET['product_id']) AND is_numeric($_GET['product_id'])) {
    echo '<table border="1" cellspacing="5" cellpadding="5">';
    echo '  <tr>
              <td style="width:175px;"><b>SKU</b></td>
              <td><b>Price From</b></td>
              <td><b>Price To</b></td>
              <td>&nbsp;</td>
              <td><b>Sale Price From</b></td>
              <td><b>Sale Price To</b></td>
              <td>&nbsp;</td>
              <td><b>Regular Price From</b></td>
              <td><b>Regular Price To</b></td>
              <td>&nbsp;</td>
              <td><b>Flash Price From</b></td>
              <td><b>Flash Price To</b></td>
              <td>&nbsp;</td>
              <td><b>Sale Date From - From</b></td>
              <td><b>Sale Date From - To</b></td>
              <td>&nbsp;</td>
              <td><b>Sale Date To - From</b></td>
              <td><b>Sale Date To - To</b></td>
              <td>&nbsp;</td>
              <td style="width:175px;"><b>Date Time</b></td>
            </tr>';
    //echo "SELECT * FROM wp_postmeta_backup_quick_update " . $sku_query . " ORDER BY sku ASC, time_of_change ASC";
    $sql_query = '';
  // if(isset($_GET['product_id']) AND is_numeric($_GET['product_id'])) {
    $sql_query = ' AND prm.product_id = ' . (int)$_GET['product_id'];
  //}

  $changes = $wpdb->get_results("SELECT prm.*, pm.meta_value FROM wp_price_monitor prm, wp_postmeta pm WHERE prm.product_id = pm.post_id " . $sql_query . " AND pm.meta_key = '_sku' ORDER BY pm.meta_value ASC, prm.time_noted DESC");
  $ctr = 0;
  foreach($changes as $change){
    if($ctr == 1 && $change->meta_value != $old_meta_value) {
      echo '<tr><td colspan="11" style="background-color: #cccccc; height: 1px;"></td></tr>';
    }
    $css = 'background-color: #f1f1f1; width: 150px;';
    if($change->_price_from != $change->_price_to) {
      $css_price = 'background-color: #33cc33; width: 150px; font-weight: 700;';
    } else {
      $css_price = 'background-color: #f1f1f1; width: 150px;';
    }
    if($change->_sale_price_from != $change->_sale_price_to) {
      $css_sale_price = 'background-color: #33cc33; width: 150px; font-weight: 700;';
    } else {
      $css_sale_price = 'background-color: #f1f1f1; width: 150px;';
    }
    if($change->_regular_price_from != $change->_regular_price_to) {
      $css_regular_price = 'background-color: #33cc33; width: 150px; font-weight: 700;';
    } else {
      $css_regular_price = 'background-color: #f1f1f1; width: 150px;';
    }
    if($change->flash_price_from != $change->flash_price_to) {
      $css_flash_price = 'background-color: #33cc33; width: 150px; font-weight: 700;';
    } else {
      $css_flash_price = 'background-color: #f1f1f1; width: 150px;';
    }
    if($change->_sale_price_dates_from_from != $change->_sale_price_dates_from_to) {
      $css_sale_price_dates_from = 'background-color: #33cc33; width: 150px; font-weight: 700;';
    } else {
      $css_sale_price_dates_from = 'background-color: #f1f1f1; width: 150px;';
    }
    if($change->_sale_price_dates_to_from != $change->_sale_price_dates_to_to) {
      $css_sale_price_dates_to = 'background-color: #33cc33; width: 150px; font-weight: 700;';
    } else {
      $css_sale_price_dates_to = 'background-color: #f1f1f1; width: 150px;';
    }

    if($change->_sale_price_dates_from_from != '') {
      $sale_price_dates_from_from = date('m-d-y H:i:s', $change->_sale_price_dates_from_from);
    }

    if($change->_sale_price_dates_from_to != '') {
      $sale_price_dates_from_to = date('m-d-y H:i:s', $change->_sale_price_dates_from_to);
    }

    if($change->_sale_price_dates_to_from != '') {
      $sale_price_dates_to_from = date('m-d-y H:i:s', $change->_sale_price_dates_to_from);
    }

    if($change->_sale_price_dates_to_to != '') {
      $sale_price_dates_to_to = date('m-d-y H:i:s', $change->_sale_price_dates_to_to);
    }

    echo '<tr>
            <td>'.$change->meta_value.' (' . $change->product_id. ')</td>
            <td style="' . $css . '">'.$change->_price_from.'</td>
            <td style="' . $css_price . '">'.$change->_price_to.'</td>
            <td style="background-color: #bbbbbb;">&nbsp;</td>
            <td style="' . $css . '">'.$change->_sale_price_from.'</td>
            <td style="' . $css_sale_price . '">'.$change->_sale_price_to.'</td>
            <td style="background-color: #bbbbbb;">&nbsp;</td>
            <td style="' . $css . '">'.$change->_regular_price_from.'</td>
            <td style="' . $css_regular_price . '">'.$change->_regular_price_to.'</td>
            <td style="background-color: #bbbbbb;">&nbsp;</td>
            <td style="' . $css . '">'.$change->flash_price_from.'</td>
            <td style="' . $css_flash_price . '">'.$change->flash_price_to.'</td>
            <td style="background-color: #bbbbbb;">&nbsp;</td>';
      date_default_timezone_set('UTC');
      echo ' <td style="' . $css . '">'.$change->_sale_price_dates_from_from.'</td>
            <td style="' . $css_sale_price_dates_from . '">'.$change->_sale_price_dates_from_to.'</td>
            <td style="background-color: #bbbbbb;">&nbsp;</td>
            <td style="' . $css . '">'.$sale_price_dates_to_from.'</td>
            <td style="' . $css_sale_price_dates_to . '">'.$_sale_price_dates_to_to.'</td>
            <td style="background-color: #bbbbbb;">&nbsp;</td>';
      date_default_timezone_set('Canada/Eastern');
      echo  '<td>'.date('m-d-y H:i:s', $change->time_noted).'</td>
          </tr>';
    $ctr = 1;
    $old_meta_value = $change->meta_value;
  }
  echo '</table>';
}echo '<div style="padding: 25px 0px 5px 0px; font-weight:500; font-size: 12px;">&bull;&nbsp;This log does not monitor human interaction.<br>&bull;&nbsp;It works by comparing prices on all products, once every 5 minutes, to see if there has been a change in the database since the previous comparison. It then records the change/s found and displays them on this page.<br>&bull;&nbsp;Only products where a price change has been recorded are shown in the dropdown.<br>&bull;&nbsp;Changes are noted in green.</div>';
  die();
}

?>
<input type="hidden" class="plugin-url" value="<?php echo site_url() . '/wp-content/plugins/gibbys-quick-update/'; ?>">
<!--<h1>Quick Update - <span style="font-size: 12px;">(<a href="admin.php?page=gibbys_quick_update&view=logs_quick_update" target="_blank">Quick Update Log</a> : <a href="http://gibbyselectronicsupermarket.ca/specials_counted.txt" target="_blank">Specials Count</a> : <a href="admin.php?page=gibbys_quick_update&view=logs_rollover" target="_blank">Rollover Log</a> : <a href="admin.php?page=gibbys_quick_update&view=logs_price_change" target="_blank">Full Pricing Log</a>)</span></h1>-->
<h1>Quick Update - <span style="font-size: 12px;">(<a href="http://gibbyselectronicsupermarket.ca/specials_counted.txt" target="_blank">Specials Count</a> : <a href="admin.php?page=gibbys_quick_update&view=logs_quick_update" target="_blank">Quick Update Log</a> <!-- : <a href="admin.php?page=gibbys_quick_update&view=logs_rollover" target="_blank">Rollover Log</a>--> : <a href="admin.php?page=gibbys_quick_update&view=logs_price_change" target="_blank">Full Pricing Log</a>)</span></h1>
<?php
    //Product feed update functions - might move it to the top of the page once finalized
    require 'feed-update.php';

    // Populate array with all manufacturers
    $manufacturer_array = array();
    $manufacturer_ids = array();
    $manufacturer_id_name_links = array();
    $q = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE taxonomy LIKE 'yith_product_brand'");
    foreach($q as $manufacturer){
      $manufacturer_id = $manufacturer->term_id;
      $q = $wpdb->get_results("SELECT name FROM wp_terms WHERE term_id = '$manufacturer_id' ORDER BY name DESC");
      foreach($q as $manufacturer){
        $manufacturer_array[] = $manufacturer->name . '_' . $manufacturer_id;
        $manufacturer_ids[] = $manufacturer_id;
        $manufacturer_id_name_links[$manufacturer->name] = $manufacturer_id;
      }
    }
    sort($manufacturer_array);

    $paged = $_GET['paged'];

    // Query to populate array with all products like term searched for
    $choice = '';
    if($_GET['search_q'] != ''){
        $post_in = array();
        $q = $wpdb->get_results("SELECT ID FROM wp_posts p WHERE post_title LIKE '%" . $_GET['search_q'] . "%' AND (p.post_status = 'publish' OR p.post_status = 'private') ORDER BY post_title ASC");

        foreach($q as $product){
            $post_in[] = $product->ID;
        }
        $choice = $_GET['search_q'];
    } elseif($_POST['search_q'] != ''){
        $post_in = array();
        $q = $wpdb->get_results("SELECT ID FROM wp_posts p WHERE p.post_title LIKE '%" . $_POST['search_q'] . "%' AND (p.post_status = 'publish' OR p.post_status = 'private') ORDER BY p.post_title ASC");

        foreach($q as $product){
            $post_in[] = $product->ID;
        }
        $choice = $_POST['search_q'];
    }

    // Query to ony show products which belong to the brand selected in the dropdown
    if($_POST['search_brand'] != '' || $_GET['search_brand'] != ''){

      if(is_array($post_in)){
        $post_in_temp = array();
        foreach($post_in as $product){
          $post_terms = wp_get_post_terms($product, 'yith_product_brand');
          foreach($post_terms as $post_term){
            $brand_id = $post_term->term_id;
          }
          if($_POST['search_brand'] != ''){
            if($_POST['search_brand'] == $brand_id){
              $post_in_temp[] = $product;
            }
          }else if($_GET['search_brand'] != ''){
            if($_GET['search_brand'] == $brand_id){
              $post_in_temp[] = $product;
            }
          }
        }
        $post_in = $post_in_temp;
      }else{

        $post_in = array();
        $get_products = $wpdb->get_results("SELECT * FROM wp_posts p WHERE p.post_type = 'product' AND (p.post_status = 'publish' OR p.post_status = 'private')");
        foreach($get_products as $product){
          $id = $product->ID;
          //gets the primary brand id
          $primary_brand = get_post_meta($id, '_yoast_wpseo_primary_yith_product_brand');
          //gets the brand ids
          $post_terms = wp_get_post_terms($id, 'yith_product_brand');
          //if the product doesnt have a primary brand id
          // if(empty($primary_brand)) {
          //   foreach($post_terms as $post_term){
          //     $brand_id = $post_term->term_id;
          //   }
          // }else {
          //   $brand_id = $primary_brand[0];
          // }

          $brands_to_check = array();
          if(!empty($primary_brand)) {
            $brands_to_check[] = $primary_brand[0];
          }

          foreach($post_terms as $post_term){
            $brands_to_check[] = $post_term->term_id;
          }

          if($_POST['search_brand'] != ''){
            if(in_array($_POST['search_brand'] , $brands_to_check)){
              $post_in_temp[] = $product->ID;
            }
          }else if($_GET['search_brand'] != ''){
            if(in_array($_GET['search_brand'] , $brands_to_check)){
              $post_in_temp[] = $product->ID;
            }
          }
        }
        $post_in = $post_in_temp;

      }
    }

    //Show only products, no bundles
    if($_POST['show_flash'] == 2) {
      $show_flash = (int)$_POST['show_flash'];
      if($show_flash) {
        if(is_array($post_in)) {
            $temp_id_array = array();
            foreach($post_in AS $post_id) {
                //$q = $wpdb->get_row("SELECT count(*) as ttl FROM wp_posts p WHERE p.ID = " . $post_id . " AND (p.post_status = 'publish' OR p.post_status = 'private') AND NOT ( post_title LIKE '%Bundle%') ");
                $q = $wpdb->get_row("SELECT count(*) as ttl FROM wp_posts p WHERE p.ID = ". $post_id ." AND (p.post_status = 'publish' OR p.post_status = 'private') AND p.ID NOT IN (SELECT post_id FROM wp_postmeta WHERE meta_key = 'is_bundle' AND meta_value = 1)");
                if($q->ttl == 1) {
                  $temp_id_array[] = $post_id;
                }

            }
            $post_in = $temp_id_array;
        } else {
          $post_in = array();
          //$q = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE NOT (post_title LIKE '%Bundle%')  AND (post_status = 'publish' OR post_status = 'private') AND post_type = 'product' ORDER BY post_title ASC");
          $q = $wpdb->get_results("SELECT ID FROM wp_posts WHERE ID NOT IN (SELECT post_id FROM wp_postmeta WHERE meta_key = 'is_bundle' AND meta_value = 1) AND (post_status = 'publish' OR post_status = 'private') AND post_type = 'product' ORDER BY post_title ASC");
          foreach($q as $product){
            $post_in[] = $product->ID;
          }
        }
      }
    }

    //Show bundles only
    if($_POST['show_flash'] == 3) {
      $show_flash = (int)$_POST['show_flash'];
      if($show_flash) {
        if(is_array($post_in)) {
            $temp_id_array = array();
            foreach($post_in AS $post_id) {
              //  $q = $wpdb->get_row("SELECT count(*) as ttl FROM wp_posts p WHERE p.ID = " . $post_id . " AND (p.post_status = 'publish' OR p.post_status = 'private') AND  post_title LIKE '%bundle%'");
                $q = $wpdb->get_row("SELECT count(*) as ttl FROM wp_posts p LEFT JOIN wp_postmeta wp_postmeta1 ON wp_postmeta1.post_id = p.ID AND wp_postmeta1.meta_key = 'is_bundle' WHERE p.ID = ". $post_id ." AND (p.post_status = 'publish' OR p.post_status = 'private') AND wp_postmeta1.meta_value = 1");
                if($q->ttl == 1) {
                  $temp_id_array[] = $post_id;
                }

            }
            $post_in = $temp_id_array;
        } else {
          $post_in = array();
          //$q = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE (  post_title LIKE '%bundle%'  ) AND (post_status = 'publish' OR post_status = 'private') AND post_type = 'product' ORDER BY post_title ASC");
          $q = $wpdb->get_results("SELECT ID, post_title FROM wp_posts LEFT JOIN wp_postmeta wp_postmeta1 ON wp_postmeta1.post_id = ID AND wp_postmeta1.meta_key = 'is_bundle' WHERE wp_postmeta1.meta_value = 1 AND (post_status = 'publish' OR post_status = 'private') AND post_type = 'product' ORDER BY post_title ASC");
          foreach($q as $product){
            $post_in[] = $product->ID;
          }
        }
      }
    }

    //Show product with flash sale price only
    if($_POST['show_flash'] == 1) {
      $show_flash = (int)$_POST['show_flash'];
      if($show_flash) {
        if(is_array($post_in)) {
            $temp_id_array = array();
            foreach($post_in AS $post_id) {
                $q = $wpdb->get_row("SELECT count(*) as ttl FROM wp_posts p, wp_postmeta pm WHERE p.ID = " . $post_id . " AND p.ID = pm.post_id AND pm.meta_key = 'flash_price' AND pm.meta_value > 0 AND (p.post_status = 'publish' OR p.post_status = 'private')");
                if($q->ttl == 1) {
                  $temp_id_array[] = $post_id;
                }

            }
            $post_in = $temp_id_array;
        } else {
          $post_in = array();
          $q = $wpdb->get_results("SELECT p.ID FROM wp_posts p, wp_postmeta pm WHERE p.ID = pm.post_id AND pm.meta_key = 'flash_price' AND pm.meta_value > 0 AND (p.post_status = 'publish' OR p.post_status = 'private')");
          foreach($q as $product){
            $post_in[] = $product->ID;
          }
        }
      }
    } elseif($_GET['show_flash'] == 1) {
      $show_flash = (int)$_GET['show_flash'];
      if($show_flash) {
        if(is_array($post_in)) {
            $temp_id_array = array();
            foreach($post_in AS $post_id) {
                $q = $wpdb->get_row("SELECT count(*) as ttl FROM wp_posts p, wp_postmeta pm WHERE p.ID = " . $post_id . " AND p.ID = pm.post_id AND pm.meta_key = 'flash_price' AND pm.meta_value > 0 AND (p.post_status = 'publish' OR p.post_status = 'private')");
                if($q->ttl == 1) {
                  $temp_id_array[] = $post_id;
                }
            }
            $post_in = $temp_id_array;
        } else {
          $post_in = array();
          $q = $wpdb->get_results("SELECT p.ID FROM wp_posts p, wp_postmeta pm WHERE p.ID = pm.post_id AND pm.meta_key = 'flash_price' AND pm.meta_value > 0 AND (p.post_status = 'publish' OR p.post_status = 'private')");
          foreach($q as $product){
            $post_in[] = $product->ID;
          }
        }
      }
    }
    if($_POST['results'] != ''){
      $search_results = (int)$_POST['results'];
    } elseif($_GET['results'] != ''){
      $search_results = (int)$_GET['results'];
    }
    if(!is_numeric($search_results) OR $search_results < 50) {
        $search_results = 50;
    }


    //echo "<pre>".print_r($post_in,true)."</pre>";
    //$draft_pending_products = $wpdb->get_row("SELECT count(*) as TTL FROM wp_posts p WHERE p.post_type = 'product' AND p.post_status != 'publish'");
    //echo 'Products in Draft or Pending Status: ' . $draft_pending_products->TTL;

  if(sizeof($post_in) == 0) { // i.e. no filter
    $get_products = $wpdb->get_results("SELECT * FROM wp_posts p WHERE p.post_type = 'product' AND (p.post_status = 'publish' OR p.post_status = 'private')");
    foreach($get_products as $product){
      $post_in_temp[] = $product->ID;
    }

    $post_in = $post_in_temp;
  }


    $args = array(
        'post__in' => $post_in,
        'posts_per_page' => $search_results,
        'post_type' => 'product',
        //'orderby' => 'post__in',
        'orderby' => 'title',
        'order' => 'ASC',
        'paged' => $paged
    );
    $q = new WP_Query($args);

    if($_POST['search_brand'] != ''){
      $search_brand = $_POST['search_brand'];
    }else if($_GET['search_brand'] != ''){
      $search_brand = $_GET['search_brand'];
    }

    $products_for_excel_download = [];

    if($q->have_posts()){
        while($q->have_posts()){
            $q->the_post();
            $id = get_the_id();
            $products_for_excel_download[] = $id;
        }
    }

    $_SESSION['products_for_excel_download'] = json_encode($products_for_excel_download);

    ?> <form action="<?php echo site_url() . '/wp-admin/admin.php?page=gibbys_quick_update'; ?>" method="POST" class="quick-update-search-form" style="float: right;">
            <!-- <input type="hidden" name="page" value="gibbys_quick_update"> -->
            <input type="hidden" value="<?php if($choice){ echo $choice; } ?>" name="search_q">
            <select name="search_brand" style="display:none;">
              <option value="">Brand</option>
              <?php
                  foreach($manufacturer_array as $manufacturer){
                      $manufacturer = explode('_', $manufacturer);
                      $manufacturer_name = $manufacturer[0];
                      $manufacturer_id = $manufacturer[1];
                      ?>
                          <option value="<?php echo $manufacturer_id; ?>" <?php if($search_brand == $manufacturer_id){ echo 'selected="selected"'; } ?>><?php echo $manufacturer_name; ?></option>
                      <?php
                  }
              ?>
            </select>
            <input type="hidden" value="<?php if($show_flash > 0){ echo $show_flash; }else{ echo 0; } ?>" name="show_flash">
            Search Results per page: <select name="results" onchange="this.form.submit();">
            <option <?php if($search_results == 50) { echo 'SELECTED'; } ?> value="50">50</option>
            <option <?php if($search_results == 100) { echo 'SELECTED'; } ?> value="100">100</option>
            <option <?php if($search_results == 200) { echo 'SELECTED'; } ?> value="200">200</option>
            <option <?php if($search_results == 500) { echo 'SELECTED'; } ?> value="500">500</option>
            <?php if($_GET['paged']){ ?>
                <input type="hidden" name="paged" value="<?php echo $_GET['paged']; ?>">
            <?php } ?>
            <input class="button-primary quick-update-search-btn search-results-change" type="hidden" name="search_btn" value="Change">

        </form>
        <?php
        //if($_SERVER['REMOTE_ADDR'] == '2.101.248.212') {
            ?>

        <form action="<?php echo site_url() . '/wp-admin/admin.php?page=gibbys_quick_update'; ?>" method="POST" class="quick-update-search-form" style="float: right; padding-right:10px;">
            <!-- <input type="hidden" name="page" value="gibbys_quick_update"> -->
            <input type="hidden" value="<?php if($choice){ echo $choice; } ?>" name="search_q">
            <select name="search_brand" style="display:none;">
              <option value="">Brand</option>
              <?php
                  foreach($manufacturer_array as $manufacturer){
                      $manufacturer = explode('_', $manufacturer);
                      $manufacturer_name = $manufacturer[0];
                      $manufacturer_id = $manufacturer[1];
                      ?>
                          <option value="<?php echo $manufacturer_id; ?>" <?php if($search_brand == $manufacturer_id){ echo 'selected="selected"'; } ?>><?php echo $manufacturer_name; ?></option>
                      <?php
                  }
              ?>
            </select>
            <input type="hidden" value="<?php if($search_results){ echo $search_results; }else{ echo 50; } ?>" name="results">
            Products: <select name="show_flash" onchange="this.form.submit();">
            <option <?php if($show_flash == 0) { echo 'SELECTED'; } ?> value="0">All</option>
            <option <?php if($show_flash == 1) { echo 'SELECTED'; } ?> value="1">Flash</option>
            <option <?php if($show_flash == 2) { echo 'SELECTED'; } ?> value="2">Primary</option>
            <option <?php if($show_flash == 3) { echo 'SELECTED'; } ?> value="3">Bundles</option>
            <?php if($_GET['paged']){ ?>
                <input type="hidden" name="paged" value="<?php echo $_GET['paged']; ?>">
            <?php } ?>
            <input class="button-primary quick-update-search-btn search-results-change" type="hidden" name="search_btn" value="Change">
        </form>
        <?php //} ?>
        <form action="<?php echo site_url() . '/wp-admin/admin.php?page=gibbys_quick_update'; ?>" method="POST" class="quick-update-search-form">
            <input type="text" value="<?php echo $choice; ?>" class="all-options" name="search_q" placeholder="Product Name..." onfocus="this.placeholder=''" onblur="this.placeholder='Product Name...'" autocomplete="off">
            <select name="search_brand">
              <option value="">Brand</option>
              <?php
                  foreach($manufacturer_array as $manufacturer){
                      $manufacturer = explode('_', $manufacturer);
                      $manufacturer_name = $manufacturer[0];
                      $manufacturer_id = $manufacturer[1];
                      ?>
                          <option value="<?php echo $manufacturer_id; ?>" <?php if($search_brand == $manufacturer_id){ echo 'selected="selected"'; } ?>><?php echo $manufacturer_name; ?></option>
                      <?php
                  }
              ?>
            </select>
            <input type="hidden" value="<?php if($search_results){ echo $search_results; }else{ echo 50; } ?>" name="results">
            <input type="hidden" value="<?php if($show_flash > 0){ echo $show_flash; }else{ echo 0; } ?>" name="show_flash">
            <input class="button-primary quick-update-search-btn" type="submit" name="search_btn" value="Search">
            <!-- <input type="text" name="search_q" style="display: none;" autocomplete="off"> -->
            <a href="<?php echo site_url() . '/wp-admin/admin.php?page=gibbys_quick_update'; ?>"><input class="button-primary" type="button" value="Reset"></a>
        </form>

        <form action="" method="post">
            <?php
                if(get_option('gibbys_flash_sale') == 'on'){
                    $btn_text = 'Turn flash sale off';
                    $flash_sale_new = 'off';
                }else if(get_option('gibbys_flash_sale') == 'off'){
                    $btn_text = 'Turn flash sale on';
                    $flash_sale_new = 'on';
                }else{
                    $btn_text = 'Set flash sale';
                    $flash_sale_new = 'on';
                }
            ?>
            <input type="hidden" name="gibbys_flash_sale" value="<?php echo $flash_sale_new; ?>">
            <input type="hidden" value="<?php if($choice){ echo $choice; } ?>" name="search_q">
            <select name="search_brand" style="display:none;">
              <option value="">Brand</option>
              <?php
                  foreach($manufacturer_array as $manufacturer){
                      $manufacturer = explode('_', $manufacturer);
                      $manufacturer_name = $manufacturer[0];
                      $manufacturer_id = $manufacturer[1];
                      ?>
                          <option value="<?php echo $manufacturer_id; ?>" <?php if($search_brand == $manufacturer_id){ echo 'selected="selected"'; } ?>><?php echo $manufacturer_name; ?></option>
                      <?php
                  }
              ?>
            </select>
            <input type="hidden" value="<?php if($search_results){ echo $search_results; }else{ echo 50; } ?>" name="results">
            <input type="hidden" value="<?php if($show_flash == 1){ echo $show_flash; }else{ echo 0; } ?>" name="show_flash">
            <input type="submit" class="button-primary" style="margin-bottom:20px;" name="update_flash_sale" value="<?php echo $btn_text; ?>">
            <a href="<?php echo site_url(); ?>/wp-content/plugins/gibbys-quick-update/flash-sale-price-export.php?gibbysallowed=yes"><input type="button" class="button-primary" style="margin-bottom:20px;" value="Download Flash Sale Excel Spreadsheet"></a>
            <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=gibbys_quick_update&view=upload_flash_file"><input type="button" class="button-primary" style="margin-bottom:20px;" value="Upload Flash Sale Excel Spreadsheet"></a>
        </form>

        <?php require 'brand-cat-wrapper.php'; ?>
        <!-- get the site url for the AJAX call in the JS file -->
        <input type="hidden" id="site_url" value="<?php echo site_url(); ?>">

        <table class="widefat" style="width: 3700px;">
            <thead>

            <tr>
                <th style="width: 100px;">Model</th>
                <th style="width: 100px;">Note</th>
                <th style="width: 500px;">Name</th>
                <th style="width:40px;position:relative;left:-10px;">Quantity</th>
                <th style="width:80px;position:relative;left:-25px;">Regular<br>> Price</th>
                <th style="width:90px;position:relative;left:-55px;">Sale<br>> Price</th>
                <th style="width:80px;position:relative;left:-90px;">Flash<br>> Price</th>
                <th style="width:65px;position:relative;left:-130px;">Shipping</th>
                <th style="width:65px;position:relative;left:-160px;">Flash Shipping</th>
                <th style="width:40px;position:relative;left:-175px;">Brand<br>> Sale</th>
                <th style="width:100px;position:relative;left:-187px;">Bundle</th>
                <th style="width:60px;position:relative;left:-265px;">No Web<br>> Sale</th>
                <th style="width:50px;position:relative;left:-285px;">In Stock</th>
                <th style="width:40px;position:relative;left:-325px;">Recycle Fee</th>
                <th style="width:40px;position:relative;left:-330px;">Bundle Fee</th>
                <!-- <th>In Wishabi Feed</th>
                <th>In Amazon Feed</th> -->
                <th style="width:100px;position:relative;left:-330px;">UPC</th>
                <!-- <th>UPC</th>
                <th>Amazon Price</th> -->
                <th style="width:100px;position:relative;left:-325px;">Hide Price</th>
                <th style="width:120px;position:relative;left:-343px;">Special Expires<br>(YYYY-MM-DD)</th>
                <th style="width:40px;position:relative;left:-403px;">In ShopBot</th>
                <th style="width:80px;position:relative;left:-413px;">Manufacturer</th>
                <!-- <th style="width:80px;position:relative;left:-486px;">Sell Online</th> -->
                <th style="width:80px;position:relative;left:-486px;">Visibility</th>
                <th style="width:250px;position:relative;left:-529px;">Item #</th>

            </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="18"><div style="height:1px;background:#e1e1e1;width:100%;"></div></td>
                </tr>
                <?php
                  //TODO: need to modifty this to display variation products.
                    if($q->have_posts()){
                        while($q->have_posts()){
                            $q->the_post();

                            $id = get_the_id();
                            $prod = get_post_meta($id);
                            //print_r($prod);
                            $tax_ids = $wpdb->get_results("SELECT term_taxonomy_id FROM wp_term_relationships WHERE object_id = '$id'");
                            foreach($tax_ids as $tax_id){
                                if(in_array($tax_id->term_taxonomy_id, $manufacturer_ids)){
                                    $product_manufacturer = $tax_id->term_taxonomy_id;
                                }
                            }
                            //if the prod id can;t be found by taxonomy check for the primary brand
                            if ($product_manufacturer == "") {
                              $product_manufacturer = get_post_meta( $id, '_yoast_wpseo_primary_yith_product_brand', true);
                            }
                            ?>
                                <tr>
                                    <td><input type="text" value="<?php echo $prod['_sku'][0]; ?>" class="regular-text" id="sku_update" prod="<?php echo $id; ?>"></td>
                                    <td><input style="color: red; font-weight: bold;" type="text" value="<?php echo $prod['note'][0]; ?>" class="regular-text" id="note_update" prod="<?php echo $id; ?>"></td>                                    
                                    <td><input type="text" value="<?php the_title(); ?>" class="regular-text" id="name_update" prod="<?php echo $id; ?>"></td>
                                    <td style="position:relative;left:-5px;"><input type="text" value="<?php echo round($prod['_stock'][0]); ?>" class="regular-text quantity_box" <?php echo ($prod['_stock'][0] == 0) ? "style='background: #ffeb3b;'" : ""; ?> id="quantity_update" prod="<?php echo $id; ?>" <?php if ($prod['_manage_stock'][0] == 'no') { echo 'disabled'; } ?>></td>

                                    <td style="position:relative;left:-30px;"><input type="text" value="<?php echo $prod['_regular_price'][0]; ?>" class="regular-text" id="price_update" prod="<?php echo $id; ?>"></td>
                                    <?php if(get_option('gibbys_flash_sale') == 'off'){ ?>
                                        <td style="position:relative;left:-60px;"><input style="color: #ff0000;font-weight:bold;" type="text" value="<?php echo $prod['_sale_price'][0]; ?>" class="regular-text" id="sale_price_update" prod="<?php echo $id; ?>"></td>
                                    <?php }else{ ?>
                                        <!-- <td><input type="text" value="<?php echo $prod['_old_sale_price'][0]; ?>" class="regular-text" id="old_sale_price_update" prod="<?php echo $id; ?>"></td> -->
                                        <td style="position:relative;left:-60px;"><input style="font-weight:bold;" type="text" value="<?php echo $prod['_sale_price'][0]; ?>" class="regular-text" id="sale_price_update" prod="<?php echo $id; ?>"></td>
                                    <?php } ?>

                                    <!-- <td><input type="text" value="<?php if($prod['_sale_price_dates_to'][0] !== ''){ echo date('Y-m-d', $prod['_sale_price_dates_to'][0]); } ?>" class="regular-text" id="special_expires_update" prod="<?php echo $id; ?>"></td> -->
                                    <?php if(get_option('gibbys_flash_sale') == 'off'){ ?>
                                      <td style="position:relative;left:-90px;"><input type="text" value="<?php echo $prod['flash_price'][0]; ?>" class="regular-text" id="flash_price_update" prod="<?php echo $id; ?>"></td>
                                    <?php } else { ?>
                                      <td style="position:relative;left:-90px;"><input style="color:#ff0000;font-weight:bold;" type="text" value="<?php echo $prod['flash_price'][0]; ?>" class="regular-text" id="flash_price_update" prod="<?php echo $id; ?>"></td>
                                    <?php } ?>
                                    <td style="position:relative;left:-130px;"><input type="text" value="<?php echo $prod['estimated_shipping'][0]; ?>" class="regular-text" id="shipping_update" prod="<?php echo $id; ?>"></td>
                                    <td style="position:relative;left:-160px;"><input type="text" value="<?php echo $prod['flash_shipping'][0]; ?>" class="regular-text" id="flash_shipping_update" prod="<?php echo $id; ?>"></td>

                                    <td style="position:relative;left:-167px;"><input class="brand-sale-marker" prod="<?php echo $id; ?>" <?php if(get_post_meta($id, '_ind_flash_sale_marker', true)==1){ echo 'checked'; }; ?> type="checkbox" id="inc_in_flash_brand_sale"></td>
                                    <td style="position:relative;left:-175px;">
                                      <?php $is_bundle = get_post_meta($id, 'is_bundle', true);   ?>
                                        <input type="checkbox" class="is_bundle" name="is_bundle" value="<?php echo $id; ?>" <?php if($is_bundle == 1) {echo "checked";} ?>>
                                    </td>
                                    <td style="position:relative;left:-255px;">
                                      <?php $nowebsale = get_post_meta($id, 'nowebsale', true);   ?>
                                        <input type="checkbox" class="nowebsale" name="nowebsale" value="<?php echo $id; ?>" <?php if($nowebsale == 1) {echo "checked";} ?>>
                                    </td>

                                    <td style="position:relative;left:-290px;">
                                        <select id="in_stock_update" prod="<?php echo $id; ?>">
                                            <option value="instock" <?php if($prod['_stock_status'][0] == 'instock'){ echo 'selected="selected"'; } ?>>In Stock</option>
                                            <option value="outofstock" <?php if($prod['_stock_status'][0] == 'outofstock'){ echo 'selected="selected"'; } ?>>Out Of Stock</option>
                                        </select>
                                    </td>
                                    <td style="position:relative;left:-325px;"><input type="text" value="<?php echo $prod['handling_fee'][0]; ?>" class="regular-text" id="recycling_fee_update" prod="<?php echo $id; ?>"></td>
                                    <td style="position:relative;left:-330px;"><input type="text" value="<?php echo $prod['handling_fee_bundles'][0]; ?>" class="regular-text" id="recycling_bundles_fee_update" prod="<?php echo $id; ?>"></td>
                                    <?php
                                      $price_record = $wpdb->get_row("SELECT COUNT(*) TTL FROM wp_term_relationships WHERE object_id = " . $id . " AND term_taxonomy_id = 1109");
                                    ?>
                                   <!--  <td>
                                        <select id="upc_update" prod="<?php echo $id; ?>">
                                            <option value="No" <?php if($prod['upc'][0] == 'No'){ echo 'selected="selected"'; } ?>>No</option>
                                            <option value="Yes" <?php if($prod['upc'][0] == 'Yes'){ echo 'selected="selected"'; } ?>>Yes</option>
                                        </select>
                                    </td> -->
                                    <td style="position:relative;left:-330px;"><input type="text" value="<?php echo $prod['upc'][0]; ?>" class="regular-text" id="upc_update" prod="<?php echo $id; ?>"></td>
                                  <!--   <td>
                                        <select id="gtin_update" prod="<?php echo $id; ?>">
                                            <option value="No" <?php if($prod['gtin'][0] == 'No'){ echo 'selected="selected"'; } ?>>No</option>
                                            <option value="Yes" <?php if($prod['gtin'][0] == 'Yes'){ echo 'selected="selected"'; } ?>>Yes</option>
                                        </select>
                                    </td> -->
                                    <td style="position:relative;left:-325px;"><select id="hide_price_update" prod="<?php echo $id; ?>">
                                        <option value="showPrice" <?php if($prod['_displayPrice'][0] == 'showPrice'){ echo 'selected="selected"'; } ?>>Show Price</option>
                                        <option value="hidePrice" <?php if($prod['_displayPrice'][0] == 'hidePrice'){ echo 'selected="selected"'; } ?>>Hide Price</option>
                                    </select></td>
                                    <td style="padding-top:10px;position:relative;left:-343px;">----- N/A -----</td>
                                    <td style="position:relative;left:-403px;">
                                        <select id="in_shopbot_feed_update" prod="<?php echo $id; ?>">
                                            <option value="No" <?php if($prod['in_shopbot_feed'][0] == 'No'){ echo 'selected="selected"'; } ?>>No</option>
                                            <option value="Yes" <?php if($prod['in_shopbot_feed'][0] == 'Yes'){ echo 'selected="selected"'; } ?>>Yes</option>
                                        </select>
                                    </td>
                                    <td style="position:relative;left:-413px;">
                                        <select id="manufacturer_update" prod="<?php echo $id; ?>">
                                            <?php
                                                foreach($manufacturer_array as $manufacturer){
                                                    $manufacturer = explode('_', $manufacturer);
                                                    $manufacturer_name = $manufacturer[0];
                                                    $manufacturer_id = $manufacturer[1];
                                                    ?>
                                                        <option value="<?php echo $manufacturer_id; ?>" <?php if($manufacturer_id == $product_manufacturer){ ?> class="current-val" current-val="<?php echo $manufacturer_id_name_links[$manufacturer_name]; ?>" selected="selected" <?php } ?>><?php echo $manufacturer_name; ?></option>
                                                    <?php
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <!-- <td style="position:relative;left:-486px;">
                                        <select id="sell_online_update" prod="<?php echo $id; ?>">
                                            <option value="1" <?php if($price_record->TTL == 0){ echo 'selected="selected"'; } ?>>Yes</option>
                                            <option value="0" <?php if($price_record->TTL == 1){ echo 'selected="selected"'; } ?>>No</option>
                                        </select>
                                    </td> -->
                                    <?php
                                        // Functionality to get the visibility for this product
                                        $public  = $wpdb->get_row("SELECT COUNT(*) AS count FROM wp_posts WHERE ID = $id AND post_status = 'publish'");
                                        $private = $wpdb->get_row("SELECT COUNT(*) AS count FROM wp_posts WHERE ID = $id AND post_status = 'private'");
                                    ?>
                                    <td style="position:relative;left:-486px;">
                                        <select id="visibility_update" prod="<?php echo $id; ?>">
                                            <option value="publish" <?php if($public->count == 1 && $private->count == 0){ echo 'selected="selected"'; } ?>>Public</option>
                                            <option value="private" <?php if($private->count == 1 && $public->count == 0){ echo 'selected="selected"'; } ?>>Private</option>
                                        </select>
                                    </td>
                                    <td style="position:relative;left:-529px;"><input type="text" value="<?php echo $prod['item'][0]; ?>" class="regular-text" id="item_update" prod="<?php echo $id; ?>"></td>

                                </tr>
                            <?php
                        }
                    }
                ?>
            </tbody>
        </table>
   <!--  <div style="display:none;" class="url-pagination"><?php echo site_url() . $_SERVER['REQUEST_URI']; ?></div> -->
    <div style="display:none;" class="brand-pagination"><?php echo $search_brand; ?></div>
    <div style="display:none;" class="search-pagination"><?php echo str_replace(' ', '+', $choice); ?></div>
    <div style="display:none;" class="results-pagination"><?php echo $search_results; ?></div>
    <div style="display:none;" class="show-flash-pagination"><?php echo $show_flash; ?></div>
    <?php

    $big = 999999999; // need an unlikely integer

    echo '<div class="quick-update-pagination">' . paginate_links( array(
      'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
      'format' => '?paged=%#%',
      'current' => max( 1, $paged ),
      'total' => $q->max_num_pages,
        'show_all' => true
    ) ) . '</div>';

    ?>
        <select id="quick_update_pagination"></select>
    <?php

    wp_reset_query();

function this_log($message, $action = '') {
    $file = @fopen('../logs/' . time() . '_' . $action . '_log.html', 'a+');
    $line = $message.'<br>';
    @fwrite($file, $line);
    @fclose($file);

}
?>

<style media="screen">
input[type="text"]:disabled {
  background: #e8e8e8 !important;
  color: #e8e8e8 !important;
}
</style>
