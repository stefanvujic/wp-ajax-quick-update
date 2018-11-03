jQuery( document ).ready(function( $ ) {
  var site_url = $('#site_url').val();
  //Enable disable form submit btn
     $('#search_term').keyup(function() {
       if($(this).val() != "" && $('#url').val() != "" ) {
         $('#add').attr("disabled", false);
       }else {
         $('#add').attr("disabled", true);
       }
     });

     $('#url').keyup(function() {
       if($(this).val() != "" && $('#search_term').val() != "" ) {
         $('#add').attr("disabled", false);
       }else {
         $('#add').attr("disabled", true);
       }
     });

     $('#group_term').keyup(function() {
       if($(this).val() != "" ) {
         $('#add-group').attr("disabled", false);
       }else {
         $('#add-group').attr("disabled", true);
       }
     });

     $('#group-selector').change(function() {
       if($(this).val() != "default" ) {
         $('#select-group').attr("disabled", false);
       }else {
         $('#select-group').attr("disabled", true);
       }
     });

  //Deal with adding search terms
  $( "#add" ).click( function() {
      //Convert quotes and slashes to xml version to avoid string issues
      var term = $("#search_term").val().trim().replace(/"/g, '&quot;').replace(/'/g, "&apos;").replace(/\\/g, "&#92;");
      var url = $('#url').val().trim();
      $("#search_term").val(term);
      $('#url').val(url);
      $("button").each(function(i) {
          $(this).attr('disabled', true);
      });
      $('*').css('cursor', 'wait');
      $( "#new-term").submit();
  });

  //Deal with exlude terms from direct terms
  $( ".exclude-check" ).click( function() {
      var groupid = $('#group_id').val();
      var id = $(this).attr('data-id');
      var checked = $(this).is(':checked');
      $( "#exclude-term input[name='key']" ).val(id);
      $( "#exclude-term input[name='group-selector']" ).val(groupid);
      $( "#exclude-term input[name='group-id']" ).val(groupid);
      $( "#exclude-term input[name='exclude']" ).val(checked);
      $("button").each(function(i) {
          $(this).attr('disabled', true);
      });
      $("exclude-check").each(function(i) {
          $(this).attr('disabled', true);
      });
      $('*').css('cursor', 'wait');
      $( "#exclude-term").submit();
  });

  //When remove button clicked call the remove function
  $( ".term-list" ).on( "click", "button", function(e) {
      var id = $(this).attr('data-id');
      var groupid = $(this).attr('data-group-id');
      $("#"+id).remove();
      var order="";
      $("#sortable li").each(function(i) {
          if (order=='')
              order = $(this).attr('id');
          else
              order += "," + $(this).attr('id');
      });
      $( "#remove-item input[name='order']" ).val(order);
      $( "#remove-item input[name='key']" ).val(id);
      $( "#remove-item input[name='group-selector']" ).val(groupid);
      $( "#remove-item input[name='group-id']" ).val(groupid);
      // $('#order').html('New Order:' + order);
      $("button").each(function(i) {
          $(this).attr('disabled', true);
      });
      $('*').css('cursor', 'wait');
      $( "#remove-item").submit();

  });

  $( "#order-update" ).click( function() {
      var groupid = $('#group_id').val();
      var order="";
      $("#sortable li").each(function(i) {
          if (order=='')
              order = $(this).attr('id');
          else
              order += "," + $(this).attr('id');
      });
      $( "#update input[name='order']" ).val(order);
      $( "#update input[name='group-id']" ).val(groupid);
      $("button").each(function(i) {
          $(this).attr('disabled', true);
      });
      $('*').css('cursor', 'wait');
      $( "#update").submit();

  });

  $( "#update-products" ).click( function() {
    if ($("#product-ids").val() == "") {
      var r = confirm("The field is empty! Are you sure you want to continue?");
      if (r == true) {
          $("button").each(function(i) {
              $(this).attr('disabled', true);
          });
          $('*').css('cursor', 'wait');
          $( "#products").submit();
      }
    }else {
      var str = $("#product-ids").val();
      if (str.match(/[a-z]/i)) {
          alert("Invalid character found!");
      }else {
        $("button").each(function(i) {
            $(this).attr('disabled', true);
        });
        $('*').css('cursor', 'wait');
        $( "#products").submit();
      }


    }
  });

});
