<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////Brand and cat Flash Sale/////////////////////////////////////////////
//purge litespeed cache
if (class_exists('LiteSpeed_Cache_API')) {
    LiteSpeed_Cache_API::purge_all();
}

//Delete all cat and brands if the global flashsale is ON or OFF
if($_POST['update_flash_sale']){
  if($_POST['gibbys_flash_sale'] == "on") {
    delete_option('flash_options');
    delete_option('exclude_options');
    delete_option('individual_flash_options');
  }elseif ($_POST['gibbys_flash_sale'] == "off") {
    delete_option('flash_options');
    delete_option('exclude_options');
    delete_option('individual_flash_options');
  }
  update_option('gibbys_flash_sale', $_POST['gibbys_flash_sale']);
}

// Deal with the data when selecting brand flash option for specific product which is posted from AJAX when sale is OFF
if (isset($_POST['individual_flash_options']) && $_POST['individual_flash_options'] == 'true') {
  global $wpdb;
  //Get newly added brand id
  $id = $_POST['brand_id'];

  $sale_items = get_option('individual_flash_options');
  if(empty($sale_items)){
  $sale_items = array();
  }
  //Add the new brand id to existing ones
  $sale_items[] = $id;
  update_option('individual_flash_options' , $sale_items);

  //Get all products within the brand
  $query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$id;
  $all_products_in_brand = $wpdb->get_results($query);

  //Prepare results
  foreach ($all_products_in_brand as $key => $value) {
      $all_products[] = $value->post_id;
  }
  var_dump($all_products);
  //get selected products
  $products = $wpdb->get_results("SELECT post_id FROM wp_postmeta WHERE meta_key = '_ind_flash_sale_marker' AND meta_value = 1");
  var_dump($products);
  //Updates the individual _flash_sale_marker
  if ($products != "") {
    foreach ($products as $key => $value) {
      if (in_array($value->post_id, $all_products)) {
        update_post_meta( $value->post_id, '_flash_sale_marker', '1' );
      }
    }
  }
  die;
}

// Remove the data when selecting brand flash option for specific product which is posted from AJAX when sale is OFF
if (isset($_POST['individual_flash_options_remove']) && $_POST['individual_flash_options_remove'] == 'true') {
  $id = $_POST['id'];
  //Get brands on sale
  $sale_items = get_option('individual_flash_options');

  //Remove and update brands on sale
  $key = array_search($id,$sale_items);
  unset($sale_items[$key]);
  update_option('individual_flash_options' , $sale_items);

  //Update individual post_meta
  $query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$id;
  $all_products_in_brand = $wpdb->get_results($query);

  //Prepare results
  foreach ($all_products_in_brand as $key => $value) {
      $all_products[] = $value->post_id;
  }

  //get selected products
  $products = $wpdb->get_results("SELECT post_id FROM wp_postmeta WHERE meta_key = '_ind_flash_sale_marker' AND meta_value = 1");

  //Prepare results
  foreach ($products as $key => $value) {
      $selected_products[] = $value->post_id;
  }

  //check what products are selected within the brand
  $products_to_remove = array_intersect($all_products,$selected_products);

  //Remove products from sale
  foreach ($products_to_remove as $product) {
    update_post_meta( $product, '_flash_sale_marker', '0' );
  }

}

//Update individual product marker using the checkbox

if (isset($_POST['update_brand_sale_marker']) && $_POST['update_brand_sale_marker'] == 'true') {
  $id = $_POST['id'];
  $action = $_POST['action'];

  //Get all brands on sale
  $sale_items = get_option('individual_flash_options');

  //Get product brand taxonomy ids
  $args = array('fields' => 'ids');
  $brands = wp_get_object_terms( $id, 'yith_product_brand', $args );

  //Check if the brand is currently on individual sale
  $brand_currently_on_sale = 0;
  if(!empty($brands)) {
    foreach ($brands as $key => $value) {
      if (in_array($value, $sale_items)) {
        $brand_currently_on_sale = 1;
      }
    }
  }

  if($action == "add") {
    update_post_meta( $id, '_ind_flash_sale_marker', '1' );
    //if the brand is currently on sale put the product on sale too
    if($brand_currently_on_sale = 1) {
      update_post_meta( $id, '_flash_sale_marker', '1' );
    }
  }else {
    update_post_meta( $id, '_ind_flash_sale_marker', '0' );

    //if the brand or cat is not on sale by the brand/cat flash sale
    //and if its on sale by the individual brands sale exclude
    $sale_items = get_option('flash_options');
    $global_brand_sale = 0;

    if(!empty($sale_items) && !empty($brands)) {
      foreach ($brands as $key => $value) {
        if (in_array($value, $sale_items['brands']) || in_array($value, $sale_items['categories'] )) {
          $global_brand_sale = 1;
        }
      }
    }

    if($brand_currently_on_sale == 1 && $global_brand_sale == 0) {
      update_post_meta( $id, '_flash_sale_marker', '0' );
    }

  }
  die;
}


// Deal with the data when selecting category or brand flash option which is posted from AJAX when sale is OFF
if (isset($_POST['flash_options']) && $_POST['flash_options'] == 'true') {
  global $wpdb;

  $type = $_POST['type'];

  switch ($type) {
    case 'category':
      $id                  = $_POST['category_id'];
      $flash_options_index = 'categories';
      //Find product ids from this category
      $query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$_POST['category_id'];
    break;
    case 'brand':
      $id                  = $_POST['brand_id'];
      $flash_options_index = 'brands';
      //Find product ids from this brand
      //$query="SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_yith_product_brand' AND meta_value =".$_POST['brand_id'];
      $query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$_POST['brand_id'];
    break;
    default: $id = 0;
    break;
  }
  $sale_items = get_option('flash_options');
  if(empty($sale_items)){
  $sale_items = array(
    'categories' => array(),
    'brands' => array()
   );
  }

  array_push($sale_items[$flash_options_index], $id);
  update_option('flash_options' , $sale_items);
  // echo '<pre>' . var_export(get_option('flash_options'), true) . '</pre>';

  //Updates the individual _flash_sale_marker
  $progress = get_option('_flash_sale_marker');
  if($progress == 1 ) {
    $products = $wpdb->get_results($query);
    if ($products != "") {
      foreach ($products as $prod => $value) {
        update_post_meta( $value->post_id, '_flash_sale_marker', '1' );
      }
    }
  }else {
    $dir =  constant("WP_CONTENT_DIR");
    $pid_off = shell_exec('pgrep -f bc_update.php');
    if ($pid_off != '') {
      shell_exec('kill -9 '.$pid_off);
    }
    shell_exec("php ".$dir."/plugins/gibbys-quick-update/bc_update.php 'alert' >> ".$dir."/plugins/paging.log &");
  }
  die;
  //Kill the page so no html is output
}

// Deal with the data when removing category or brand flash option which is posted from AJAX
if (isset($_POST['remove_flash_options']) && $_POST['remove_flash_options'] == 'true') {
  $item = (int)$_POST['id'];
  $type = $_POST['type'];
  $sale_items = get_option('flash_options');
  var_dump($sale_items);
  function unset_item($array, $type, $item) {
    foreach($array as $key=>$val){
        if($key == $type){
          foreach($val as $k=>$v){
            if($v == $item) {
              unset($array[$type][$k]);
            }
          }
        }
    }
    update_option('flash_options', $array);
    return $array;
  }
  $new_array = unset_item($sale_items, $type, $item);


  if($_POST['type'] == 'categories') {
    //$query = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_product_cat' AND meta_value =".$item;
    $query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$_POST['category_id'];
  }elseif ($_POST['type'] == 'brands') {
    //$query = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_yith_product_brand' AND meta_value =".$item;
    $query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$item;
  }

  //$query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$item;

  $products = $wpdb->get_results($query);
  var_dump($products);
  if ($products != "") {
    foreach ($products as $prod => $value) {
      update_post_meta( $value->post_id, $key = '_flash_sale_marker', $value = '0' );
    }
  }
  die;
  //Kill the page so no html is output
}

// Deal with the data when excluding category or brand flash option which is posted from AJAX
if (isset($_POST['exclude_options']) && $_POST['exclude_options'] == 'true') {
  global $wpdb;
  $type = $_POST['type'];

  switch ($type) {
    case 'category':
      $id                  = $_POST['category_id'];
      $flash_options_index = 'categories';
      //$query="SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_product_cat' AND meta_value =".$_POST['category_id'];
      $query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$_POST['category_id'];
    break;
    break;
    case 'brand':
      $id                  = $_POST['brand_id'];
      $flash_options_index = 'brands';
      $query="SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_yith_product_brand' AND meta_value =".$_POST['brand_id'];
      //$query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$_POST['brand_id'];
    break;
    default: $id = 0;
    break;
  }

  $exc_items = get_option('exclude_options');

  if(empty($exc_items)){
  $exc_items = array(
    'categories' => array(),
    'brands' => array()
   );
  }

  array_push($exc_items[$flash_options_index], $id);
  update_option('exclude_options' , $exc_items);
  //echo '<pre>' . var_export(get_option('exclude_options'), true) . '</pre>';

  //Updates the individual _flash_sale_marker
  $progress = get_option('_flash_sale_marker');
  if($progress == 1 ) {
    $products = $wpdb->get_results($query);
    if ($products != "") {
      foreach ($products as $prod => $value) {
        update_post_meta( $value->post_id, $key = '_flash_sale_marker', $value = '0' );
      }
    }
  }else {
    $dir =  constant("WP_CONTENT_DIR");
    $pid_off = shell_exec('pgrep -f bc_update.php');
    if ($pid_off != '') {
      shell_exec('kill -9 '.$pid_off);
    }
    shell_exec("php ".$dir."/plugins/gibbys-quick-update/bc_update.php 'alert' >> ".$dir."/plugins/paging.log &");
  }
  die;
  //Kill the page so no html is output
}
// Deal with the data when removing exclusion category or brand flash option which is posted from AJAX
if (isset($_POST['remove_exclude_options']) && $_POST['remove_exclude_options'] == 'true') {
  $item = (int)$_POST['id'];
  $type = $_POST['type'];
  $sale_items = get_option('exclude_options');

  function unset_item($array, $type, $item) {
    foreach($array as $key=>$val){
        if($key == $type){
          foreach($val as $k=>$v){
            if($v == $item) {
              unset($array[$type][$k]);
            }
          }
        }
    }
  update_option('exclude_options', $array);
  return $array;
  }
  $new_array = unset_item($sale_items, $type, $item);
  echo '<pre>' . var_export(get_option('exclude_options'), true) . '</pre>';
  if($_POST['type'] == 'categories') {
    $query = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_product_cat' AND meta_value =".$item;
  }elseif ($_POST['type'] == 'brands') {
    $query = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_yith_product_brand' AND meta_value =".$item;
    //$query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$item;
  }

  //$query="SELECT object_id AS post_id  FROM wp_term_relationships WHERE term_taxonomy_id = ".$item;
  $products = $wpdb->get_results($query);

  if ($products != "") {
    foreach ($products as $prod => $value) {
      update_post_meta( $value->post_id, $key = '_flash_sale_marker', $value = '1' );
    }
  }
  die;
  //Kill the page so no html is output
}
?>
