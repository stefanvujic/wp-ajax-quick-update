<?php
    session_start();
    include('../../../wp-load.php');
    include('xlsx/Classes/PHPExcel.php');

    global $wpdb;

    if (isset($_GET['gibbysallowed']) && $_GET['gibbysallowed'] == 'yes') {

        // Get an array of the products which are currently visible on the page
        $products_in_view          = json_decode($_SESSION['products_for_excel_download']);
        $products_in_view_imploded = implode(',', $products_in_view);

        echo 'Creating the file...' . '<br />';

        $excel = new PHPExcel();
        $excel->getProperties()->setCreator('Paramount')->setTitle('Flash Sale Spreadsheet');

        // Loop through all of the products on the site and store the following in an array:
        // - SKU
        // - Title
        // - Regular Price
        // - Sale Price
        // - Shipping Price
        // - Flash Sale Price
        // - Flash Shipping Price
        // - UPC

        $products     = [];
        $get_products = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE post_type = 'product' AND post_status = 'publish' AND ID IN ($products_in_view_imploded)");

        foreach ($get_products as $product) {

            // Get the information for the product
            $id                   = $product->ID;
            $title                = $product->post_title;
            $sku                  = get_post_meta($id, '_sku', true);
            $regular_price        = get_post_meta($id, '_regular_price', true);
            $sale_price           = get_post_meta($id, '_sale_price', true);
            $shipping_price       = get_post_meta($id, 'estimated_shipping', true);
            $flash_sale_price     = get_post_meta($id, 'flash_price', true);
            $flash_shipping_price = get_post_meta($id, 'flash_shipping', true);
            $upc                  = get_post_meta($id, 'upc', true);


            // Functionality to get the primary product category for the product
            if (!empty(get_post_meta($id, '_yoast_wpseo_primary_product_cat', true))) {
                $category_name = get_term(get_post_meta($id, '_yoast_wpseo_primary_product_cat', true), 'product_cat')->name;

                if (empty($category_name)) {
                    $category_name = wp_get_post_terms($id, 'product_cat')[0]->name;
                }
            } else {
                $category_name = wp_get_post_terms($id, 'product_cat')[0]->name;
            }

            // Add everything to the products array
            $products[] = [
                'id'             => $id,
                'title'          => $title,
                'sku'            => $sku,
                'category'       => $category_name,
                'regular_price'  => $regular_price,
                'sale_price'     => $sale_price,
                'shipping'       => $shipping_price,
                'flash_price'    => $flash_sale_price,
                'flash_shipping' => $flash_shipping_price,
                'upc'            => $upc,
            ];

        }

        // Set the headings for the excel file
        $excel->setActiveSheetIndex(0)
              ->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'SKU')
              ->setCellValue('C1', 'Title')
              ->setCellValue('D1', 'Primary Category')
              ->setCellValue('E1', 'Regular Price')
              ->setCellValue('F1', 'Sale Price')
              ->setCellValue('G1', 'Shipping Price')
              ->setCellValue('H1', 'Flash Sale Price')
              ->setCellValue('I1', 'Flash Shipping Price')
              ->setCellValue('J1', 'UPC');


        $row = 1;
        foreach ($products as $product) {

            $row++;
            $excel->getActiveSheet()
                  ->setCellValue('A' . $row, $product['id'])
                  ->setCellValue('B' . $row, $product['sku'])
                  ->setCellValue('C' . $row, $product['title'])
                  ->setCellValue('D' . $row, $product['category'])
                  ->setCellValue('E' . $row, $product['regular_price'])
                  ->setCellValue('F' . $row, $product['sale_price'])
                  ->setCellValue('G' . $row, $product['shipping'])
                  ->setCellValue('H' . $row, $product['flash_price'])
                  ->setCellValue('I' . $row, $product['flash_shipping'])
                  ->setCellValue('J' . $row, $product['upc']);

            // Set the number format for the price field so that the spreadsheet will not remove .00
            $excel->getActiveSheet()->getStyle('E' . $row)->getNumberFormat()->setFormatcode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            $excel->getActiveSheet()->getStyle('F' . $row)->getNumberFormat()->setFormatcode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            $excel->getActiveSheet()->getStyle('G' . $row)->getNumberFormat()->setFormatcode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            $excel->getActiveSheet()->getStyle('H' . $row)->getNumberFormat()->setFormatcode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            $excel->getActiveSheet()->getStyle('I' . $row)->getNumberFormat()->setFormatcode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            $excel->getActiveSheet()->getStyle('J' . $row)->getNumberFormat()->setFormatcode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


        }

        // Lock the entire sheet then unclock all columns except the id and category
        $excel->getActiveSheet()->getProtection()->setSheet(true);
        $excel->getActiveSheet()->getStyle('B2:C' . (count($products_in_view) + 1))->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $excel->getActiveSheet()->getStyle('E2:J' . (count($products_in_view) + 1))->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

        // Create the excel file
        $excel->getActiveSheet()->setTitle('Flash Sale Spreadsheet');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('flash_pricing.xlsx');

        echo 'File Created!' . '<br />';

        // Take the user back to the quick update page
        header('Location: ' . site_url() . '/wp-admin/admin.php?page=gibbys_quick_update&download_file=yes');
        exit;

    } else {

        exit;

    }

?>
