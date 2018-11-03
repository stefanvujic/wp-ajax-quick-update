<?php
    include('../../../wp-load.php');
    global $wpdb;

    //purge litespeed cache
    if (class_exists('LiteSpeed_Cache_API')) {
        LiteSpeed_Cache_API::purge_all();
    }

    $product_id = $_POST['prod'];
    $value = $_POST['new_val'];
    $current_value = $_POST['current_val'];

    define('POST_META_TABLE', 'wp_postmeta'); // Normal wp_postmeta
    define('POST_META_TABLE_BACKUP', 'wp_postmeta_backup_quick_update'); // Normal wp_postmeta

    $post_field_name = $_POST['field'];
    switch($post_field_name){

        case 'sku':
          $post_field = '_sku';
          db_log($product_id, $post_field, $value);
          //$wpdb->update('wp_postmeta', array('meta_value' => $value), array('post_id' => $product_id, 'meta_key' => '_sku'));

          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_sku'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", '_sku', '" . $value . "')");

          echo 'Model updated for product with ID: ' . $product_id . '!. New value: ' . $value;
        break;

        case 'note':
          $post_field = 'note';
          db_log($product_id, $post_field, $value);
          
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'note'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", 'note', '" . $value . "')");
          echo 'Note updated for product with ID: ' . $product_id . '!. New value: ' . $value;
        break;        

        case 'title':
          $post_field = 'title';
          $value = stripslashes($value);
          db_log($product_id, $post_field, $value);
          $wpdb->update('wp_posts', array('post_title' => $value), array('ID' => $product_id));
          echo 'Name updated for product with ID: ' . $product_id . '!';
        break;

        case 'flash_shipping':
          $post_field = 'flash_shipping';
          db_log($product_id, $post_field, $value);
          update_post_meta($product_id, 'flash_shipping', $value);
          echo 'Flash shipping updated for product with ID: ' . $product_id . '!';
        break;

        case 'estimated_shipping':
          $post_field = 'estimated_shipping';
          db_log($product_id, $post_field, $value);
          //$wpdb->update('wp_postmeta', array('meta_value' => $value), array('post_id' => $product_id, 'meta_key' => 'estimated_shipping'));
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'estimated_shipping'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", 'estimated_shipping', '" . $value . "')");
          echo 'Shipping updated for product with ID: ' . $product_id . '!';
        break;

        case 'regular_price':
          $post_field = '_regular_price';
          db_log($product_id, $post_field, $value);
          //$wpdb->update('wp_postmeta', array('meta_value' => $value), array('post_id' => $product_id, 'meta_key' => '_regular_price'));
          //$wpdb->update('wp_postmeta', array('meta_value' => $value), array('post_id' => $product_id, 'meta_key' => '_price'));

          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_regular_price'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", '_regular_price', '" . $value . "')");
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_price'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", '_price', '" . $value . "')");


          echo 'Price updated for product with ID: ' . $product_id . '!';
        break;

        case 'sale_price':

          $post_field = '_sale_price';
          db_log($product_id, $post_field, $value);
          $value = str_replace(' ', '', $value);
          //if($value != 0) {
            $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_sale_price'");
            $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", '_sale_price', '" . $value . "')");
            $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_price'");
            $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", '_price', '" . $value . "')");
          //}

          if($value == ''){
            $regular_price = get_post_meta($product_id, '_regular_price');
            update_post_meta($product_id, '_price', $regular_price[0]);
          }

          echo 'Sale price updated for product with ID: ' . $product_id . '!';
        break;

        case 'old_sale_price':
          $post_field = '_old_sale_price';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_old_sale_price'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", '_old_sale_price', '" . $value . "')");
          echo 'Old Sale price updated for product with ID: ' . $product_id . '!';
        break;

        case 'hide_price':
          $post_field = '_displayPrice';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_displayPrice'");
          $wpdb->insert('wp_postmeta', array('post_id' => $product_id, 'meta_key' => '_displayPrice', 'meta_value' => $value));
          echo 'Hide price updated for product with ID:' . $product_id . '!';
        break;

        case 'flash_price':
          $post_field = 'flash_price';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'flash_price'");
          if($value > 0) {
            $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", 'flash_price', '" . $value . "')");
          }
          echo 'Flash price updated for product with ID: ' . $product_id . '!';
        break;

        case 'special_expires':
          $post_field = '_sale_price_dates_to';
          db_log($product_id, $post_field, $value);
          if($value !== ''){
              $value = strtotime($value);
          }
          //$wpdb->update('wp_postmeta', array('meta_value' => $value), array('post_id' => $product_id, 'meta_key' => '_sale_price_dates_to'));
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_sale_price_dates_to'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", '_sale_price_dates_to', '" . $value . "')");
          echo 'Special expires updated for product with ID: ' . $product_id . '!';
        break;

        case 'handling_fee':
          $post_field = 'handling_fee';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'handling_fee'");
          $wpdb->insert('wp_postmeta', array('post_id' => $product_id, 'meta_key' => 'handling_fee', 'meta_value' => $value));
          echo 'Recycling fee updated for product with ID: ' . $product_id . '!';
        break;

        case 'handling_fee_bundles':
          $post_field = 'handling_fee_bundles';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'handling_fee_bundles'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", 'handling_fee_bundles', '" . $value . "')");
          echo 'Recycling fee bundle updated for product with ID: ' . $product_id . '!';
        break;

        case 'manage_stock':
          $post_field = '_manage_stock';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_manage_stock'");
          $wpdb->insert('wp_postmeta', array('post_id' => $product_id, 'meta_key' => '_manage_stock', 'meta_value' => $value));
          echo 'Manage stock updated for product with ID: ' . $product_id . '!';
        break;

        case 'stock':
          $post_field = '_stock';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_stock'");
          $wpdb->insert('wp_postmeta', array('post_id' => $product_id, 'meta_key' => '_stock', 'meta_value' => $value));
          echo 'Quantity updated for product with ID: ' . $product_id . '!';
        break;

        case 'manufacturer':
          $post_field = 'term_taxonomoy_id';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_term_relationships WHERE object_id = " . $product_id  . " AND term_taxonomy_id = " . $current_value);
          $wpdb->query("INSERT INTO wp_term_relationships VALUES (" . $product_id .", " . $value . ", 0)");
          echo 'Manufacturer updated for product with ID: ' . $product_id . '!';
        break;

        case 'stock_status':
          $post_field = '_stock_status';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = '_stock_status'");
          $wpdb->insert('wp_postmeta', array('post_id' => $product_id, 'meta_key' => '_stock_status', 'meta_value' => $value));
          echo 'In stock updated for product with ID: ' . $product_id . '!';
        break;

        case 'visibility':
          $post_field = 'visibility';
          db_log($product_id, $post_field, $value);
          if($value == 0) {
            $wpdb->query("DELETE FROM wp_term_relationships WHERE object_id = " . $product_id . " AND term_taxonomy_id = 1109");
            $wpdb->query("INSERT INTO wp_term_relationships VALUES (" . $product_id .", 1109, 0)");
          } elseif($value == 1) {
            $wpdb->query("DELETE FROM wp_term_relationships WHERE object_id = " . $product_id . " AND term_taxonomy_id = 1109");
          }
          echo 'Sell online updated for product with ID: ' . $product_id . '!';
        break;

        case 'post_status':
          $post_field = 'post_status';
          db_log($product_id, $post_field, $value);
          $wpdb->update('wp_posts', array('post_status' => $value), array('ID' => $product_id));
          echo 'Visibility updated for product with ID: ' . $product_id . '!';
        break;

        case 'shopbot_price':
          $post_field = 'shopbot_price';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'shopbot_price'");
          $wpdb->query("INSERT INTO wp_postmeta VALUES ('', " . $product_id .", 'shopbot_price', '" . $value . "')");
          echo 'Shopbot price updated for product with ID: ' . $product_id . '!';
        break;

        case 'upc':
          $post_field = 'upc';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'upc'");
          $wpdb->insert('wp_postmeta', array('post_id' => $product_id, 'meta_key' => 'upc', 'meta_value' => $value));
          echo 'UPC updated for product with ID: ' . $product_id . '!';
        break;

        case 'item':
          $post_field = 'item';
          db_log($product_id, $post_field, $value);
          $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'item'");
          $wpdb->insert('wp_postmeta', array('post_id' => $product_id, 'meta_key' => 'item', 'meta_value' => $value));
          echo 'Item # updated for product with ID: ' . $product_id . '!';
        break;

        // case 'gtin':
        //   $post_field = 'gtin';
        //   db_log($product_id, $post_field, $value);
        //   $wpdb->query("DELETE FROM wp_postmeta WHERE post_id = " . $product_id . " AND meta_key = 'gtin'");
        //   $wpdb->insert('wp_postmeta', array('post_id' => $product_id, 'meta_key' => 'gtin', 'meta_value' => $value));
        //   echo 'GTIN updated for product with ID: ' . $product_id . '!';
        // break;

        case 'nowebsale':
          if($value == 'add') {
            update_post_meta($product_id, 'nowebsale', 1);
            update_post_meta($product_id, '_manage_stock', 'no');
            update_post_meta($product_id, '_stock_status', 'instock');
            update_post_meta($product_id, '_stock', '');

          }else {
            update_post_meta($product_id, 'nowebsale', 0);
            update_post_meta($product_id, '_stock_status', 'outofstock');
            update_post_meta($product_id, '_stock', '0');
            update_post_meta($product_id, '_manage_stock', 'yes');
          }
        break;

        case 'is_bundle':
          if($value == 'add') {
            update_post_meta($product_id, 'is_bundle', 1);
          }else {
            update_post_meta($product_id, 'is_bundle', 0);
          }
        break;

        default:break;
    }

    function db_log($product_id, $meta_key, $meta_value_to) {
      global $wpdb;
      $user_id = get_current_user_id();
      $user = $wpdb->get_row("SELECT user_nicename AS value FROM wp_users WHERE ID = " . $user_id);
      $user = $user->value;
      $sku = $wpdb->get_row("SELECT meta_value AS value FROM wp_postmeta WHERE meta_key LIKE '_sku' AND post_id = " . $product_id);
      $sku = $sku->value;
      $title = $wpdb->get_row("SELECT post_title AS value FROM wp_posts WHERE ID = " . $product_id);
      $title = $title->value;
      $existing_record = $wpdb->get_row("SELECT meta_value AS value FROM wp_postmeta WHERE meta_key LIKE '" . $meta_key . "' AND post_id = " . $product_id);
      $meta_value_from = $existing_record->value;
      if(is_null($meta_value_from)) {
            $meta_value_from = '';
      }
      $product = array('change_id' => '', 'ip_address' => $_SERVER['REMOTE_ADDR'], 'user' => $user, 'post_id' => $product_id, 'sku' => $sku, 'title' => $title, 'meta_key' => $meta_key, 'meta_value_from' => $meta_value_from, 'meta_value_to' => $meta_value_to, 'time_of_change' => time(), 'source' => 'Quick Update');
      $wpdb->insert('wp_postmeta_backup_quick_update', $product);
    }
?>
