<?php
// If the user has clicked the button to upload a new flash sale excel spreadsheet
if (isset($_GET['view']) && $_GET['view'] == 'upload_flash_file') {

    // If the upload form has been submitted
    if (isset($_GET['upload_attempt']) && $_GET['upload_attempt'] == 'yes') {

        // Make sure a file has been selected to upload
        if (isset($_FILES['excel_upload']) && !empty($_FILES['excel_upload']['name'])) {

            $file           = $_FILES['excel_upload'];
            $file_name      = $file['name'];
            $file_tmp_name  = $file['tmp_name'];
            $file_extension = end(explode('.', $file_name));
            $file_size      = filesize($file_tmp_name);

            // Make sure the file is not empty
            if ($file_size > 0) {

                // Make sure the extension is xlsx
                if ($file_extension == 'xlsx') {

                    // Save the excel spreadsheet to the plugin directory
                    $file_save_location = '/home/shopgibbyselectr/public_html/wp-content/plugins/gibbys-quick-update/';
                    if (move_uploaded_file($file_tmp_name, $file_save_location . 'temp-flash-sale.xlsx')) {

                        if (file_exists($file_save_location . 'temp-flash-sale.xlsx')) {

                            include('xlsx/Classes/PHPExcel/IOFactory.php');

                            ini_set('memory_limit', '2G');

                            // Load the excel file with phpexcel library
                            $excel_spreadsheet = $file_save_location . 'temp-flash-sale.xlsx';
                            try {
                                $excel_spreadsheet_type = PHPExcel_IOFactory::identify($excel_spreadsheet);
                                $xlsx_reader            = PHPExcel_IOFactory::createReader($excel_spreadsheet_type);
                                $excel                  = $xlsx_reader->load($excel_spreadsheet);
                            } catch (Exception $e) {
                                die('Error loading file "' . pathinfo($excel_spreadsheet, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                            }

                            $excel_sheet  = $excel->getSheet(0);
                            $row_count    = $excel_sheet->getHighestRow();
                            $column_count = $excel_sheet->getHighestColumn();

                            for ($row = 1; $row <= $row_count; $row++) {

                                if ($row > 1) {

                                    // Get the product information and loop through each column
                                    $product = $excel_sheet->rangeToArray('A' . $row . ':I' . $row, null, true, false);
                                    foreach ($product as $product_column) {

                                        // Get the product id and new data from the spreadsheet
                                        $product_id       = $product_column[0];
                                        $sku              = $product_column[1];
                                        $title            = $product_column[2];
                                        $regular_price    = $product_column[4];
                                        $sale_price       = $product_column[5];
                                        $shipping         = $product_column[6];
                                        $flash_sale_price = $product_column[7];
                                        $flash_shipping   = $product_column[8];

                                        // Get the current data for the product
                                        $current_sku              = get_post_meta($product_id, '_sku', true);
                                        $current_title            = get_the_title($product_id);
                                        $current_regular_price    = get_post_meta($product_id, '_regular_price', true);
                                        $current_sale_price       = get_post_meta($product_id, '_sale_price', true);
                                        $current_shipping         = get_post_meta($product_id, 'estimated_shipping', true);
                                        $current_flash_sale_price = get_post_meta($product_id, 'flash_price', true);
                                        $current_flash_shipping   = get_post_meta($product_id, 'flash_shipping', true);

                                        // Check if there are any changes to the sku in the spreadsheet and update the database is
                                        // - The URL encode ensures that all characters will be seen as the same unless thay have been changed
                                        if (urlencode($current_sku) != urlencode($sku)) {
                                            update_post_meta($product_id, '_sku', $sku);
                                        }

                                        // Check if there are any changes to the title in the spreadsheet and update the database if there is
                                        // - The URL encode ensures that all characters will be seen as the same unless thay have been changed
                                        // - By default WordPress adds wptexturize to the title so adding it to the spreadsheet title will ensure
                                        //   there are no issues with the quotes when comparing the titles otherwise almost all titles are different
                                        if (urlencode($current_title) != urlencode(wptexturize($title))) {
                                            wp_update_post([
                                                'ID'         => $product_id,
                                                'post_title' => $title,
                                            ]);
                                        }

                                        // Check if there any changes to the regular price and update the database if there are
                                        if ($current_regular_price != $regular_price) {
                                            update_post_meta($product_id, '_regular_price', number_format($regular_price, 2, '.', ''));
                                        }

                                        // Check if there any changes to the sale price and update the database if there are
                                        if ($current_sale_price != $sale_price) {
                                            update_post_meta($product_id, '_sale_price', number_format($sale_price, 2, '.', ''));
                                        }

                                        // Check if there any changes to the shipping price and update the database if there are
                                        if ($current_shipping != $shipping) {
                                            update_post_meta($product_id, 'estimated_shipping', number_format($shipping, 2, '.', ''));
                                        }

                                        // Check if there any changes to the flash sale price and update the database if there are
                                        if ($current_flash_sale_price != $flash_sale_price) {
                                            update_post_meta($product_id, 'flash_price', number_format($flash_sale_price, 2, '.', ''));
                                        }

                                        // Check if there any changes to the flash shipping price and update the database if there are
                                        if ($current_flash_shipping != $flash_shipping) {
                                            update_post_meta($product_id, 'flash_shipping', number_format($flash_shipping, 2, '.', ''));
                                        }

                                    }

                                }

                            }

                            ini_set('memory_limit', '265M');

                            // Delete the temporary flash sale excel spreadsheet
                            unlink($file_save_location . 'temp-flash-sale.xlsx');

                            header('Location: ' . site_url() . '/wp-admin/admin.php?page=gibbys_quick_update&file_imported=yes');

                        } else {
                            echo '<h3 style="color:red;">There was an issue whilst uploading the Excel file!</h3>';
                        }

                    } else {
                        echo '<h3 style="color:red;">There was an issue whilst uploading the Excel file!</h3>';
                    }

                } else {
                    echo '<h3 style="color:red;">Please upload an Excel file with the xlsx extension file!</h3>';
                }

            } else {
                echo '<h3 style="color:red;">Please upload a file that is not empty!</h3>';
            }

        } else {
            echo '<h3 style="color:red;">Please select a file to upload!</h3>';
        }

    }

    ?>

        <h2>Upload New Flash Sale XLSX</h2>

        <form action="<?php echo site_url() . '/wp-admin/admin.php?page=gibbys_quick_update&view=upload_flash_file&upload_attempt=yes'; ?>" method="POST" enctype="multipart/form-data">

            <input type="file" name="excel_upload">
            <br><br>
            <input type="submit" class="button-primary" value="Upload XLSX File">

        </form>

    <?php

    die;
}

// If the excel file has been imported successfully
if (isset($_GET['file_imported']) && $_GET['file_imported'] == 'yes') {
    ?>
        <br>
        <h1 style="color:green;">The Excel file was imported successfully!</h1>
        <br>
    <?php
}
?>
