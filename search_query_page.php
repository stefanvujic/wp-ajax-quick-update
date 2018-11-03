<?php
  global $wpdb;


//Updating product ids
if (isset($_POST['products'])) {
  $products = sanitize_text_field($_POST['products']);
  $group_id = sanitize_text_field($_POST['group-id']);
  
  $wpdb->update(
  	'wp_group_terms',
  	array(
  		'products' => $products
  	),
  	array(
      'ID' => $group_id
     )
  );
}

//Adding group terms
if (isset($_POST['add-group-term'])) {
  $name = sanitize_text_field($_POST['group']);
  $wpdb->insert(
  	'wp_group_terms',
  	array(
  		'group_name' => $name,
  	)
  );
}

//Deleting group terms
if (isset($_POST['delete-group'])) {
  $group_id = sanitize_text_field($_POST['group-id']);
  $wpdb->delete( 'wp_group_terms', array( 'ID' => $group_id ) );
}

//Exclude term from direct terms
if (isset($_POST['exclude'])) {
  //Get relavant data
  $key = sanitize_text_field($_POST['key']);
  $group_id = sanitize_text_field($_POST['group-id']);
  $exclude = sanitize_text_field($_POST['exclude']);
  $group_info = $wpdb->get_row( "SELECT search_terms FROM wp_group_terms WHERE ID =".$group_id);
  $group_info = unserialize($group_info->search_terms);
  if ($exclude == 'true') {
    $group_info[$key]['exc'] = 1;
  }else{
    $group_info[$key]['exc'] = 0;
  }
  $group_info = serialize($group_info);
  $wpdb->update(
  	'wp_group_terms',
  	array(
  		'search_terms' => $group_info
  	),
  	array(
      'ID' => $group_id
     )
  );
}

//Adding search terms
if (isset($_POST['add-search-query'])) {
  //Get relavant data
  $term = sanitize_text_field($_POST['term']);
  $url = sanitize_text_field($_POST['url']);
  $group_id = sanitize_text_field($_POST['group-id']);
  $group_info = $wpdb->get_row( "SELECT search_terms FROM wp_group_terms WHERE ID =".$group_id);
  $group_info = unserialize($group_info->search_terms);
  //Add new term
  $group_info[]= array(
    'term' => $term,
    'url'  => $url,
    'exc'  => 0
  );
  //Save new term in db
  $group_info = serialize($group_info);
  $wpdb->update(
  	'wp_group_terms',
  	array(
  		'search_terms' => $group_info
  	),
  	array(
      'ID' => $group_id
     )
  );
}

//Deals with removing items from DB
if (isset($_POST['remove-search-query']) && $_POST['remove-search-query'] == 'true') {
  $group_id = sanitize_text_field($_POST['group-id']);
  $key = sanitize_text_field($_POST['key']);
  $order = sanitize_text_field($_POST['order']);
  $order = explode(",",$order);
  $group_info = $wpdb->get_row( "SELECT search_terms FROM wp_group_terms WHERE ID =".$group_id);
  $group_terms = $group_info->search_terms;
  $group_terms = unserialize($group_terms);
  unset($group_terms[$key]);
  $sorted_group_terms = array();

  for ($i=0; $i < count($order); $i++) {
    $sorted_group_terms[$i] = $group_terms[$order[$i]];
  }
  $sorted_group_terms = serialize($sorted_group_terms);

  $wpdb->update(
  	'wp_group_terms',
  	array(
  		'search_terms' => $sorted_group_terms
  	),
  	array(
      'ID' => $group_id
     )
  );

}

//Update terms order
if (isset($_POST['update-order']) && $_POST['update-order'] == 'true') {
  $group_id = sanitize_text_field($_POST['group-id']);
  $order = sanitize_text_field($_POST['order']);
  $order = explode(",",$order);
  $group_info = $wpdb->get_row( "SELECT search_terms FROM wp_group_terms WHERE ID =".$group_id);
  $group_terms = $group_info->search_terms;
  $group_terms = unserialize($group_terms);
  $sorted_group_terms = array();

  for ($i=0; $i < count($order); $i++) {
    $sorted_group_terms[$i] = $group_terms[$order[$i]];
  }
  //echo "<pre>".print_r($sorted_group_terms, true)."</pre>";
  $sorted_group_terms = serialize($sorted_group_terms);

  $wpdb->update(
  	'wp_group_terms',
  	array(
  		'search_terms' => $sorted_group_terms
  	),
  	array(
      'ID' => $group_id
     )
  );
}

 ?>
<!-- get the site url for the AJAX call in the JS file -->
<input type="hidden" id="site_url" value="<?php echo site_url(); ?>">


<div id="main_widget_area">
  <h1>Search Query</h1>
  <div style="background-color:white;padding:10px;margin-bottom:15px;margin-right:20px;">
    <div>
      <div style="display:inline-block; width:48%;height:170px;">
        <h2>Groups</h2>
        <p>To modify search terms select a group</p>
        <form action="" method="post">
          <select name="group-selector" id="group-selector" style="width:90%;">
            <option value="default">Please select a group</option>
            <?php
            $group_names = $wpdb->get_results( "SELECT ID,group_name FROM wp_group_terms ORDER BY group_name ASC", ARRAY_N );

            for ($i=0; $i < count($group_names) ; $i++) {
              ?>
              <option value="<?php echo $group_names[$i][0]; ?>"><?php echo $group_names[$i][1]; ?></option>
              <?php
            }
            ?>
          </select><br><br>
          <button type="submit" id="select-group" class="button-primary" style="margin-bottom:10px;" disabled>Select Group</button>
        </form>
      </div>

      <div style="display:inline-block; width:48%;height:170px;">
        <h2>Term Groups</h2>
        <p>Add a new group</p>
        <form action="" method="post">
          <input style="width: 100%;" type="text" name="group" id="group_term" value=""><br><br>
          <button type="submit" name="add-group-term" id="add-group" class="button-primary" style="margin-bottom:10px;" disabled>Add Group</button>
        </form>
      </div>
    </div>
    <hr>
      <?php if (isset($_POST['group-selector']) && !empty($_POST['group-selector'])) {
        $group_info = $wpdb->get_row( "SELECT * FROM wp_group_terms WHERE ID =".sanitize_text_field($_POST['group-selector']));
        $group_id = $group_info->ID;
        $group_name = $group_info->group_name;
        $group_terms = $group_info->search_terms;
        $group_terms = unserialize($group_terms);


      ?>
      <input type="hidden" id="group_id" name="" value="<?php echo $group_id; ?>">
      <div>
        <h2>GROUP: <?php echo $group_name; ?></h2>

        <form action="" method="post">
          <input type="hidden" name="group-id" value="<?php echo $group_id; ?>">
          <button type="submit" name="delete-group" class="button-primary" onclick="return confirm('Are you sure you want to delete this group?');">Delete Group</button>
        </form>

        <p>Specify product IDs for <?php echo $group_name; ?></p>
        <i>Use comma-separated values. E.g. 12345,67890 </i>
        <form id="products" action="" method="post">
          <?php $product_ids = $wpdb->get_row( "SELECT products FROM wp_group_terms WHERE ID = ".$group_id); ?>
          <input type="hidden" name="group-id" value="<?php echo $group_id; ?>">
          <input type="hidden" name="group-selector" value="<?php echo $group_id; ?>">
          <input style="width: 75%;" type="text" name="products" id="product-ids" value="<?php echo $product_ids->products; ?>">
        </form>
        <br>
        <button id="update-products" type="button" class="button-primary" style="margin-bottom:10px;">Update products</button>
        <br><br>
        <p>Add a new search term for <?php echo $group_name; ?></p>

        <form id="new-term" action="" method="post">
          <input style="width: 75%;" type="text" name="term" id="search_term" value="">
          <p>URL associated with search term</p>
          <input style="width: 75%;" type="text" name="url" id="url" value="">
          <input type="hidden" name="group-id" value="<?php echo $group_id; ?>">
          <input type="hidden" name="group-selector" value="<?php echo $group_id; ?>">
          <input type="hidden" name="add-search-query" value="<?php echo $group_id; ?>">
        </form>
        <br>
        <button type="button" id="add" class="button-primary" style="margin-bottom:10px;" disabled>Add Term</button>
        <br>

      <h2>Current Search Terms</h2>
        <div <?php if(empty($group_terms)) {echo 'style="display:none;"';} ?> tyle="margin-bottom:50px;" class="term-list">
          <ul id="sortable">
            <?php
            foreach ($group_terms as $key => $value) {
              if(!empty($group_terms[$key]['term'])) {
              ?>
              <li id='<?php echo $key;?>' class='ui-state-default'><div style='width:25%;'><?php echo $group_terms[$key]['term'];?></div><div style='width:69%;'><?php echo $group_terms[$key]['url'];?></div>
                <div style='width:2%;'>
                  <input class="exclude-check" type="checkbox" data-id="<?php echo $key;?>" value="" <?php if($group_terms[$key]['exc'] == 1) { echo "checked";}?>>
                </div>
                <div style='width:3%;'>
                <button data-id='<?php echo $key;?>' data-group-id='<?php echo $group_id; ?>' class='remove-btn'> X </button>
                </div>
              </li>
              <?php
              }
            }
            ?>
          </ul>
          <p id="order"></p>
        </div>
        <p id="empty"><?php if(empty($group_terms[0])){echo "You haven't added any search term yet.";} ?></p>
      </div>
      <!-- update form -->
      <form action="" id="update" method="post">
        <input style="width: 835px;" type="hidden" name="update-order" value="true">
        <input style="width: 835px;" type="hidden" name="group-id" value="">
        <input style="width: 835px;" type="hidden" name="order" value="">
        <input type="hidden" name="group-selector" value="<?php echo $group_id; ?>">
      </form>
      <button type="button" id="order-update" class="button-primary">Update Order</button>
      <?php } ?>

    </div>
  </div>


<!-- Remove form -->
<form id="remove-item" action="" method="post">
  <input type="hidden" name="remove-search-query" value="true">
  <input type="hidden" name="group-id" value="">
  <input type="hidden" name="group-selector" value="">
  <input type="hidden" name="key" value="">
  <input type="hidden" name="order" value="">
</form>
<!-- Exclude form -->
<form id="exclude-term" action="" method="post">
  <input type="hidden" name="exclude" value="">
  <input type="hidden" name="key" value="">
  <input type="hidden" name="group-id" value="">
  <input type="hidden" name="group-selector" value="">
</form>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<style>
  #sortable { list-style-type: none; margin: 0; padding: 0; width: 75%; cursor: move;}
  #sortable li { margin: 5px 0px 0px 0px; padding: 10px 5px 10px 5px;  font-size: 15px;}
  #sortable li span { position: absolute; margin-left: -1.3em; }
  #sortable div { display: inline-block;}
</style>

<script>
$( function() {
  $( "#sortable" ).disableSelection();
} );

$( "#sortable" ).sortable({
    axis: 'y',
    update: function (event, ui) {
      var site_url = $('#site_url').val();
      var groupid = $('#group_id').val();
      var order="";
      $("#sortable li").each(function(i) {
          if (order=='')
              order = $(this).attr('id');
          else
              order += "," + $(this).attr('id');
      });

    }
});
</script>
