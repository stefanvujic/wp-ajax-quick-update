(function($){
    // Convert WordPress pagination into a dropdown pagination
    var current_page = $('.quick-update-pagination > span').html();
    $('#quick_update_pagination').html('<option selected="selected">' + current_page + '</option>');
    $('.quick-update-pagination > a').each(function(){
        var url = $(this).attr('href');
        var page_number = $(this).html();
        if(page_number !== '« Previous' && page_number !== 'Next »'){
            $('#quick_update_pagination').append('<option class="pagination-page" value="' + url + '">' + page_number + '</option>');
        }
    });

    if($('#quick_update_pagination > option[selected=selected]').html() == 'undefined'){
        $('#quick_update_pagination').hide();
    }

    // Redirect user when page is selected from pagination dropdown
    $('#quick_update_pagination').change(function(){
        var url = $('.url-pagination').html();
        var search = $('.search-pagination').html();
        var results = $('.results-pagination').html();
        var show_flash = $('.show-flash-pagination').html();
        var search_brand = $('.brand-pagination').html();
        //if(search !== ''){
            var pagination_url = $(this).attr('value').replace(url, '').replace('#038;search_q=' + search, '').replace('&search_q=' + search, '').replace('#038;results=' + results, '').replace('&results=' + results, '').replace('#038;show_flash=' + show_flash, '').replace('&show_flash=' + show_flash, '').replace('#038;search_brand=' + search_brand, '').replace('&search_brand=' + search_brand, '');
        //}else{
          //  var pagination_url = $(this).attr('value').replace(url, '').replace('#038;search_q', '').replace('&results=' + results, '');
        //}
         //var pagination_url = $(this).attr('value');
        if(search !== '') {
          pagination_url = pagination_url.concat('&search_q=' + search);
        }

        if(results !== '') {
          pagination_url = pagination_url.concat('&results=' + results);
        }

        if(show_flash !== '') {
          pagination_url = pagination_url.concat('&show_flash=' + show_flash);
        }

        if(search_brand !== '') {
          pagination_url = pagination_url.concat('&search_brand=' + search_brand);
        }

        //alert   (pagination_url);
        window.location.assign(pagination_url);
    });

    // Update product model (_sku)
    $('[id=sku_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'sku';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product note
    $('[id=note_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'note';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });    

    // Update product name (post_title)
    $('[id=name_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'title';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product flash shipping (flash_shipping)
    $('[id=flash_shipping_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'flash_shipping';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product shipping (estimated_shipping)
    $('[id=shipping_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'estimated_shipping';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product price (_regular_price)
    $('[id=price_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'regular_price';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product sale price (_sale_price)
    $('[id=sale_price_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'sale_price';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product old sale price (_old_sale_price)
    $('[id=old_sale_price_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'old_sale_price';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product flash price (flash_price)
    $('[id=flash_price_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'flash_price';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product special expires (_sale_price_dates_to)
    $('[id=special_expires_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'special_expires';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product recycling fee (handling_fee)
    $('[id=recycling_fee_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'handling_fee';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product recycling bundle fee (handling_fee_bundle)
    $('[id=recycling_bundles_fee_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'handling_fee_bundles';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product manage stock (_manage_stock)
    $('[id=manage_stock_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'manage_stock';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();

        if (new_val == 'yes') {
            $('[id=quantity_update][prod=' + prod + ']').removeAttr('disabled');
        } else if (new_val == 'no') {
            $('[id=quantity_update][prod=' + prod + ']').attr('disabled', 'disabled');
        }

        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product quantity (_stock)
    $('[id=quantity_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'stock';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        if (new_val == 0) {
          $(this).css('background', '#ffeb3b');
        }else {
          $(this).css('background', 'none');
        }
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
        if(new_val == 0 || new_val == ""){
          $('[id=in_stock_update][prod='+prod+']').val("outofstock");
          new_val2 = "outofstock";
        }else{
          $('[id=in_stock_update][prod='+prod+']').val("instock");
          new_val2 = "instock";
        }
        field2 = 'stock_status';
        $.post(plugin_url + 'quick_update_script.php', {field: field2, prod: prod, new_val: new_val2}, function(data){
            console.log(data);
        });
    });

    // Update product manufacturer (yith_product_brand)
    $('[id=manufacturer_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'manufacturer';
        var prod = $(this).attr('prod');
        var current_val = $('[prod=' + prod + '] > option.current-val').attr('current-val');
        var new_val = $(this).val();
        $('[prod=' + prod + '] > option.current-val').removeClass('current-val').removeAttr('current-val');
        $('[prod=' + prod + '] > option[value=' + new_val + ']').addClass('current-val').attr('current-val', new_val);
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, current_val: current_val, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product in stock (_stock_status)
    $('[id=in_stock_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'stock_status';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product sell online (_visibility)
    $('[id=sell_online_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'visibility';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product visibility (post_status)
    $('[id=visibility_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'post_status';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product packs or sets (packs_or_sets)
    $('[id=packs_sets_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'packs_or_sets';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product upc (upc)
    $('[id=upc_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'upc';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product item # (item)
    $('[id=item_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'item';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update product gtin (gtin)
    $('[id=gtin_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'gtin';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    // Update shopbot product price (shopbot_price)
    $('[id=shopbot_price_update]').change(function(){
        var plugin_url = $('.plugin-url').val();
        var field = 'shopbot_price';
        var prod = $(this).attr('prod');
        var new_val = $(this).val();
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: new_val}, function(data){
            console.log(data);
        });
    });

    //No web sale $marker
    $('.nowebsale').change(function(event){
      var result = confirm('Are you sure?');
      if ($(this).is(':checked')) {
        var value = true;
      }else {
        var value = false;
      }
      if (result) {
        var plugin_url = $('.plugin-url').val();
        var action;
        var prod = $(this).val();
        var field = 'nowebsale';
        var qtyField = $( ".quantity_box[prod='"+prod+"']" );

        if ($(this).is(':checked')) {
          action = "add";
          //disable quntity field
          $(qtyField).attr('disabled', true);
          //empty quantity field
          $(qtyField).val('');

        }else {
          action = "remove";
          //enable quantity field
          $(qtyField).attr('disabled', false);
        }
        //AJAX call
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: action}, function(data){});

      }else {
        //Keep the checkbox in the original state when action cancelled
        if (value == true) {
          $(this).prop('checked', false);
        }else {
          $(this).prop( 'checked', true);
        }
      }
    });

    //Is_bundle $marker
    $('.is_bundle').change(function(event){
        var plugin_url = $('.plugin-url').val();
        var action;
        var prod = $(this).val();
        var field = 'is_bundle';
        if ($(this).is(':checked')) {
          action = "add";
        }else {
          action = "remove";
        }
        //AJAX call
        $.post(plugin_url + 'quick_update_script.php', {field: field, prod: prod, new_val: action}, function(data){});
    });
    
})(jQuery);
