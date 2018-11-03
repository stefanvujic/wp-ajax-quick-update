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
$delete = "DELETE FROM wp_postmeta WHERE meta_key = '_flash_sale_marker'";
$delete_marker = "DELETE FROM wp_options WHERE option_name = '_flash_sale_marker'";
$insert_marker = "INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('_flash_sale_marker', '0', 'yes')";
$conn->query($delete_marker);
$conn->query($insert_marker);
$conn->query($delete);
$sql = "SELECT ID FROM wp_posts WHERE post_type = 'product' AND post_status = 'publish'";
$all_products = $conn->query($sql);
if ($all_products->num_rows > 0) {
    // output data of each row
    while($row = $all_products->fetch_assoc()) {
      $insert = "INSERT INTO wp_postmeta( meta_key, meta_value, post_id) VALUES ('_flash_sale_marker', 1, ".$row['ID'].")";
      $conn->query($insert);
    }
    $update_marker = "UPDATE wp_options SET option_value='1' WHERE option_name = '_flash_sale_marker'";
    $conn->query($update_marker);
} else {
    echo "0 results";
}

$conn->close();
?>
