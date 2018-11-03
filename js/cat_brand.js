jQuery( document ).ready(function( $ ) {

  $( ".brand" ).change( function(event) {
    var result = confirm('Are you sure?');
    if (result) {
        add_to_list_remove_from_dropdown(event.target);

      //AJAX call for brand when selected
      var brand_id = event.target.value;
      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'flash_options':'true', 'type':'brand', brand_id:brand_id}, function (data) {console.log(data);}).done(function() {
        enable();
      })
      //Sets back the dropdown to default
      $(".brand option").each(function(){
        if ($(this).text() == "Choose Brand")
          $(this).attr("selected","selected");
      });
    }else {
      $(".brand option").each(function(){
        if ($(this).text() == "Choose Brand")
          $(this).attr("selected","selected");
      });
    }
  });

  $( ".brand_ind" ).change( function(event) {
    var result = confirm('Are you sure?');
    if (result) {
      var brand_id = event.target.value;
      if(brand_id != 'default') {

        add_to_list_remove_from_dropdown(event.target);
        //AJAX call for brand when selected      
        var site_url = $('#site_url').val();
        $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'individual_flash_options':'true', 'type':'brand', brand_id:brand_id}, function () {}).done(function() {
          enable();
        })
        //Sets back the dropdown to default
        $(".brand_ind option").each(function(){
          if ($(this).text() == "Choose Brand") {
            $(this).attr("selected","selected");
          }
        });
      }
    }else {
      $(".brand_ind option").each(function(){
        if ($(this).text() == "Choose Brand")
          $(this).attr("selected","selected");
      });
    }
  });

  $( ".category" ).change(function(event) {

    var result = confirm('Are you sure?');
    if (result) {
      add_to_list_remove_from_dropdown(event.target);

      // AJAX call for category when selected
      var category_id = event.target.value;
      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'flash_options':'true', 'type':'category', category_id:category_id}, function (data)  {console.log(data);}).done(function() {
        enable();
      })
      //Sets back the dropdown to default
      $(".category option").each(function(){
        if ($(this).text() == "Choose category")
          $(this).attr("selected","selected");
      });

    }else {
      $(".category option").each(function(){
        if ($(this).text() == "Choose category")
          $(this).attr("selected","selected");
      });
    }
  });

  function add_to_list_remove_from_dropdown(item) {

    //Creates a button under currently on sale and removes the option from dropdown
    if(item.className == 'category'){
      var name = $(".category > option[value="+item.value+"]").html();
      $( ".cat_sale" ).append( "<div class='remove-cat' style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' data-id='"+item.value+"'><p style='display:inline;padding:2px;'>"+name+" "+"</p><button class='category2'> X </button></div>");
      $(".category option[value='" + item.value + "']").hide();
    }
    if(item.className == 'brand'){
      var name = $(".brand > option[value="+item.value+"]").html();
      $( ".brand_sale" ).append( "<div class='remove-brand' style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' data-id='"+item.value+"'><p style='display:inline;'>"+name+" "+"</p><button class='brand2'> X </button></div>");
      $(".brand option[value='" + item.value + "']").hide();
    }
    if(item.className == 'brand_ind'){
      var name = $(".brand_ind > option[value="+item.value+"]").html();
      $( ".ind_brand_sale" ).append( "<div class='remove-ind' style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' data-id='"+item.value+"'><p style='display:inline;'>"+name+" "+"</p><button class='brand3'> X </button></div>");
      $(".brand_ind option[value='" + item.value + "']").hide();
    }

    //Disable all btn and change cursor to avoid creating too many ajax cals at the same time
    disable();

  }
  $( ".sale" ).on( "click", "button", function() {
      remove($(this).parent().attr('data-id'), this.className, $(this).siblings().text(), $(this).parent().prop('className') );
  });
  function remove(item_id, item_class, item_txt, parent_class){

    //create a Jquery selector from class and attr data-id
    var elem = '.'+parent_class+'[data-id='+item_id+']';

    $(elem).remove();

    //Removes the currently under sale buttons and adds back to the dropdown list
      disable();
    if(item_class == 'category2'){
      $(".category option[value='" + item_id + "']").show();
      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'remove_flash_options':'true', 'type':'categories', id:item_id}, function (data)  {console.log(data);}).done(function() {
        enable();
      })
    }
    if(item_class == 'brand2'){
      $(".brand option[value='" + item_id + "']").show();

      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'remove_flash_options':'true', 'type':'brands', id:item_id}, function (data) {console.log(data);}).done(function() {
        enable();
      })
    }
    if(item_class == 'brand3'){
      $(".brand_ind option[value='" + item_id + "']").show();

      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'individual_flash_options_remove':'true', 'type':'brands', id:item_id}, function () {}).done(function() {
        enable();
      })
    }

  }
  //Exclude functions
  $( ".category_remove" ).change(function(event) {
    var result = confirm('Are you sure?');
    if (result) {
      add_to_list_remove_from_dropdown_exlude(event.target);

      // AJAX call for category when selected
      var category_id = event.target.value;
      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'exclude_options':'true', 'type':'category', category_id:category_id}, function (data)  {console.log(data);}).done(function() {
        enable();
      })
      //Sets back the dropdown to default
      $(".category_remove option").each(function(){
        if ($(this).text() == "Choose category")
          $(this).attr("selected","selected");
      });
    }else {
      $(".category_remove option").each(function(){
        if ($(this).text() == "Choose category")
          $(this).attr("selected","selected");
      });
    }
  });

  $( ".brand_remove" ).change( function(event) {
    var result = confirm('Are you sure?');
    if (result) {
      add_to_list_remove_from_dropdown_exlude(event.target);

      //AJAX call for brand when selected
      var brand_id = event.target.value;
      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'exclude_options':'true', 'type':'brand', brand_id:brand_id}, function (data) {console.log(data);}).done(function() {
        enable();
      })
      //Sets back the dropdown to default
      $(".brand_remove option").each(function(){
        if ($(this).text() == "Choose Brand")
          $(this).attr("selected","selected");
      });
    }else {
      $(".brand_remove option").each(function(){
        if ($(this).text() == "Choose Brand")
          $(this).attr("selected","selected");
      });
    }
  });

  function add_to_list_remove_from_dropdown_exlude(item) {
    //Creates a button under currently on sale and removes the option from dropdown
    if(item.className == 'category_remove'){
      var name = $(".category_remove > option[value="+item.value+"]").html();
      $( ".cat_exclude_div" ).append( "<div style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' id='"+item.value+"'><p style='display:inline;padding:2px;'>"+name+" "+"</p><button class='category_exclude'> X </button></div>");
      $(".category_remove option[value='" + item.value + "']").hide();
    }
    if(item.className == 'brand_remove'){
      var name = $(".brand_remove > option[value="+item.value+"]").html();
      $( ".brand_exclude_div" ).append( "<div style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' id='"+item.value+"'><p style='display:inline;'>"+name+" "+"</p><button class='brand_exclude'> X </button></div>");
      $(".brand_remove option[value='" + item.value + "']").hide();
    }

  }
  $( ".exclude" ).on( "click", "button", function() {
      remove_exclude_options($(this).parent().attr('id'), this.className, $(this).siblings().text());
  });
  function remove_exclude_options(item_id, item_class, item_txt){
  //Removes the currently under sale buttons and adds back to the dropdown list
    $("#"+item_id).remove();
    if(item_class == 'category_exclude'){
      $(".category_remove option[value='" + item_id + "']").show();
      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'remove_exclude_options':'true', 'type':'categories', id:item_id}, function (data)  {console.log(data);}).done(function() {
        enable();
      })
    }
    if(item_class == 'brand_exclude'){
      $(".brand_remove option[value='" + item_id + "']").show();

      var site_url = $('#site_url').val();
      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'remove_exclude_options':'true', 'type':'brands', id:item_id}, function (data) {console.log(data);}).done(function() {
        enable();
      })
    }

  }

  //Update brand sale option when checkbox clicked
  // Update product name (post_title)
  $('.widefat').on('change', 'input.brand-sale-marker', function(){
      disable();
      var site_url = $('#site_url').val();
      var id = $(this).attr('prod');
      var action;
      if ($(this).is(':checked')) {
        action = "add";
      }else {
        action = "remove";
      }

      $.post(site_url+'/wp-content/plugins/gibbys-quick-update/options-page-wrapper.php', {'update_brand_sale_marker':'true', id:id, action:action}, function () {});
      setTimeout(function(){
        enable();
      }, 1000);


  });

  function enable() {
    $("button").each(function(i) {
        $(this).attr('disabled', false);
    });
    $(".brand_flash_sale_main select").each(function(i) {
        $(this).attr('disabled', false);
    });
    $(".brand-sale-marker").each(function(i) {
        $(this).attr('disabled', false);
    });
    $('*').css('cursor', 'default');
  }

  function disable () {
    $("button").each(function(i) {
        $(this).attr('disabled', true);
    });
    $(".brand_flash_sale_main select").each(function(i) {
        $(this).attr('disabled', true);
    });
    $(".brand-sale-marker").each(function(i) {
        $(this).attr('disabled', true);
    });
    $('*').css('cursor', 'wait');
  }

});
