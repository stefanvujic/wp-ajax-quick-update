<?php
$servername = "localhost";
$username = "shopgibb_wp";
$password = "4LW5!6Rm";
$dbname = "shopgibb_wp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//check if the sale is on and query the appropriate table
$marker = "SELECT option_value FROM wp_options WHERE option_name LIKE 'gibbys_flash_sale'";
$sale = $conn->query($marker);

if ($sale->num_rows > 0) {
    // output data of each row
    while($row = $sale->fetch_assoc()) {
      if($row["option_value"] == 'on') {
        $query ="SELECT * FROM wp_options WHERE option_name LIKE 'exclude_options'";
        $sale_marker = 'on';
      }else {
        $query = "SELECT * FROM wp_options WHERE option_name LIKE 'flash_options'";
        $sale_marker = 'off';
      }
    }
}
$check = "SELECT * FROM wp_options WHERE option_name = '_flash_sale_marker'";
$process = $conn->query($check);
while($row = $process->fetch_assoc()) {
  $status = $row["option_value"];
}
if($status == 0){
  //Delay process
  $items = $conn->query($query);
  if ($items->num_rows > 0) {
      while($row = $items->fetch_assoc()) {
        $products = unserialize($row["option_value"]);
      }
  }
  sleep(600);
  foreach ($products['categories'] as $key => $value) {
       $cats[]=$value;
  }
  foreach ($products['brands'] as $key => $value) {
       $brands[]=$value;
  }
  foreach ($cats as $key => $value) {
      $sql = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_product_cat' AND meta_value =".$value;
      $pid = $conn->query($sql);
      while($row = $pid->fetch_assoc()) {
        $ids[] = $row["post_id"];
      }
  }
  foreach ($brands as $key => $value) {
    $sql = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_yith_product_brand' AND meta_value = ".$value;
    $pid = $conn->query($sql);
    while($row = $pid->fetch_assoc()) {
      $ids[] = $row["post_id"];
    }
  }

  for ($i=0; $i < count($ids); $i++) {
    if($sale_marker == 'on') {
    $q = "UPDATE wp_postmeta SET meta_value= '0' WHERE meta_key = '_flash_sale_marker' AND post_id = ".$ids[$i];
    }elseif ($sale_marker == 'off') {
    $q = "UPDATE wp_postmeta SET meta_value= '1' WHERE meta_key = '_flash_sale_marker' AND post_id = ".$ids[$i];
    }
    $conn->query($q);
  }
}elseif ($status == 1) {
  $items = $conn->query($query);
  if ($items->num_rows > 0) {
      while($row = $items->fetch_assoc()) {
        $products = unserialize($row["option_value"]);
      }
  }
  foreach ($products['categories'] as $key => $value) {
       $cats[]=$value;
  }
  foreach ($products['brands'] as $key => $value) {
       $brands[]=$value;
  }
  foreach ($cats as $key => $value) {
      $sql = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_product_cat' AND meta_value =".$value;
      $pid = $conn->query($sql);
      while($row = $pid->fetch_assoc()) {
        $ids[] = $row["post_id"];
      }
  }
  foreach ($brands as $key => $value) {
    $sql = "SELECT post_id FROM wp_postmeta WHERE meta_key = '_yoast_wpseo_primary_yith_product_brand' AND meta_value = ".$value;
    $pid = $conn->query($sql);
    while($row = $pid->fetch_assoc()) {
      $ids[] = $row["post_id"];
    }
  }

  for ($i=0; $i < count($ids); $i++) {
    if($sale_marker == 'on') {
    $q = "UPDATE wp_postmeta SET meta_value= '0' WHERE meta_key = '_flash_sale_marker' AND post_id = ".$ids[$i];
    }elseif ($sale_marker == 'off') {
    $q = "UPDATE wp_postmeta SET meta_value= '1' WHERE meta_key = '_flash_sale_marker' AND post_id = ".$ids[$i];
    }
    $conn->query($q);
  }
}


?>
