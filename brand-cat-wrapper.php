<?php if(get_option('gibbys_flash_sale') == 'on') {
       echo '<h1>The Global Flash Sale is <span style="color:red;">ON</span></h1>';
     }else {
        echo '<h1>The Global Flash Sale is <span style="color:red;">OFF</span></h1>';
     }?>
    <div class="brand_category" style="background-color:white;padding:10px;margin-bottom:15px;margin-right:50px;float:left;height:350px;">
      <?php if(get_option('gibbys_flash_sale') == 'on') {
        echo '<h2>Exclude Brands and Categories</h2>';
      }else {
        echo '<h2>Brands and Categories Flash Sale</h2>';
      } ?>

      <div class="brand_flash_sale_main">
        <div>
          <?php if(get_option('gibbys_flash_sale') == 'on') { ?>
            <p>Select a category you want to exclude from the sale</p>
          <?php }else{ ?>
            <p>Select a category you want to put on sale</p>
        <?php } ?>
          <form action="">
          <?php

            $taxonomy     = 'product_cat';
            $orderby      = 'name';
            $show_count   = 0;      // 1 for yes, 0 for no
            $pad_counts   = 0;      // 1 for yes, 0 for no
            $hierarchical = 1;      // 1 for yes, 0 for no
            $title        = '';
            $empty        = 0;

            $args = array(
             'taxonomy'     => $taxonomy,
             'orderby'      => $orderby,
             'show_count'   => $show_count,
             'pad_counts'   => $pad_counts,
             'hierarchical' => $hierarchical,
             'title_li'     => $title,
             'hide_empty'   => $empty
            );
            $all_categories = get_categories( $args );
            $sale_items= get_option('flash_options');
            if(get_option('gibbys_flash_sale') == 'off') {
              echo '<select class="category">';
            }else{
              echo '<select class="category_remove">';
            }
            echo "<option value='default' selected>Choose category</option>";
            foreach ($all_categories as $cat) {
                if($cat->category_parent == 0) {
                $category_id = $cat->term_id;
                if(!in_array($category_id, $sale_items['categories'])) {
                    echo '<option value="'. $category_id .'">'. $cat->name .'</option>';

                }else {
                 echo '<option style="display:none;" value="'. $category_id .'">'. $cat->name .'</option>';
               }

                $args2 = array(
                        'taxonomy'     => $taxonomy,
                        'child_of'     => 0,
                        'parent'       => $category_id,
                        'orderby'      => $orderby,
                        'show_count'   => $show_count,
                        'pad_counts'   => $pad_counts,
                        'hierarchical' => $hierarchical,
                        'title_li'     => $title,
                        'hide_empty'   => $empty
                );
                $sub_cats = get_categories( $args2 );
                if($sub_cats) {
                    foreach($sub_cats as $sub_category) {
                        $category_id =  $sub_category->term_id;
                        if(!in_array($category_id, $sale_items['categories'])) {
                            echo '<option value="'. $sub_category->term_id .'">'. $sub_category->name .'</option>';

                        }else {
                          echo '<option style="display:none;" value="'. $sub_category->term_id .'">'. $sub_category->name .'</option>';
                        }
                          $args3 = array(
                                  'taxonomy'     => $taxonomy,
                                  'child_of'     => 0,
                                  'parent'       => $category_id,
                                  'orderby'      => $orderby,
                                  'show_count'   => $show_count,
                                  'pad_counts'   => $pad_counts,
                                  'hierarchical' => $hierarchical,
                                  'title_li'     => $title,
                                  'hide_empty'   => $empty
                          );
                          $sub_sub_cats = get_categories( $args3 );
                          foreach ($sub_sub_cats as $k=>$v) {
                              if(!in_array($v->term_id, $sale_items['categories'])) {
                                  echo '<option value="'. $v->term_id .'">'. $v->name .'</option>';

                              }else {
                               echo '<option style="display:none;" value="'. $v->term_id .'">'. $v->name .'</option>';

                              }

                          }
                        }

                      }
                  }
                  }

                  echo '</select>';
            ?>
          </form>
        </div>

        <div>
          <?php if(get_option('gibbys_flash_sale') == 'on') { ?>
            <p>Select a brand you want to excludefrom the sale</p>
          <?php }else{ ?>
            <p>Select a brand you want to put on sale </p>
        <?php } ?>

          <form action="">
            <?php
            $terms = get_terms('yith_product_brand',
                array(
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC'
                )
            );
            if(get_option('gibbys_flash_sale') == 'off') {
              echo '<select class="brand">';
            }else{
              echo '<select class="brand_remove">';
            }

            echo "<option id='' value='default' selected>Choose Brand</option>";
            foreach( $terms as $term ):
              $sale_items= get_option('flash_options');
              if(!in_array($term->term_id, $sale_items['brands'])) {
            ?>
              <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
            <?php
              }else {
                ?>
                  <option style="display:none;" value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                <?php
              }
            endforeach;
          echo '</select>';
           ?>
          </form>
        </div>
        <hr>
        <div id="brand_ind_wrapper">
          <h2>Product Sale</h2>
          <p>Select a brand to put the CHOSEN PRODUCTS on sale</p>
          <form action="">
            <?php
            $disabled = "";
            if (get_option('gibbys_flash_sale') == 'on') {
              $disabled = "disabled";
            }
            $terms = get_terms('yith_product_brand',
                array(
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC'
                )
            );
              echo '<select '.$disabled.'  class="brand_ind">';


            echo "<option value='default' selected>Choose Brand</option>";
            foreach( $terms as $term ):
              $sale_items= get_option('individual_flash_options');
              if(!in_array($term->term_id, $sale_items)) {
            ?>
              <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
            <?php
              }else {
                ?>
                  <option style="display:none;" value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                <?php
              }
            endforeach;
          echo '</select>';
           ?>
          </form>
        </div>

      </div>
    </div>



    <?php //} ?>
    <?php
    //function to return a product name by id and category
    function get_product_name($product_id, $type) {
      if($type == 'brands') {
        $terms = get_terms('yith_product_brand',
            array(
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC'
            )
        );

        foreach( $terms as $term ){
          if($term->term_id == $product_id) {
            return $term->name;
            break;
          }
        }
      }elseif ($type == 'categories') {
      $taxonomy     = 'product_cat';
      $orderby      = 'name';
      $show_count   = 0;      // 1 for yes, 0 for no
      $pad_counts   = 0;      // 1 for yes, 0 for no
      $hierarchical = 1;      // 1 for yes, 0 for no
      $title        = '';
      $empty        = 0;

      $args = array(
       'taxonomy'     => $taxonomy,
       'orderby'      => $orderby,
       'show_count'   => $show_count,
       'pad_counts'   => $pad_counts,
       'hierarchical' => $hierarchical,
       'title_li'     => $title,
       'hide_empty'   => $empty
      );
      $all_categories = get_categories( $args );
      foreach ($all_categories as $cat) {
          if($cat->category_parent == 0) {
          $category_id = $cat->term_id;
          if($category_id == $product_id) {
            return $cat->name;
          }

          $args2 = array(
                  'taxonomy'     => $taxonomy,
                  'child_of'     => 0,
                  'parent'       => $category_id,
                  'orderby'      => $orderby,
                  'show_count'   => $show_count,
                  'pad_counts'   => $pad_counts,
                  'hierarchical' => $hierarchical,
                  'title_li'     => $title,
                  'hide_empty'   => $empty
          );
          $sub_cats = get_categories( $args2 );
          if($sub_cats) {
              foreach($sub_cats as $sub_category) {
                  $category_id =  $sub_category->term_id;
                  if($category_id == $product_id) {
                    return $sub_category->name;
                  }
                  $args3 = array(
                          'taxonomy'     => $taxonomy,
                          'child_of'     => 0,
                          'parent'       => $category_id,
                          'orderby'      => $orderby,
                          'show_count'   => $show_count,
                          'pad_counts'   => $pad_counts,
                          'hierarchical' => $hierarchical,
                          'title_li'     => $title,
                          'hide_empty'   => $empty
                  );
                  $sub_sub_cats = get_categories( $args3 );
                  //echo "<pre>".print_r($sub_sub_cats,true)."</pre>";
                  // if($sub_sub_cats) {
                    foreach ($sub_sub_cats as $k=>$v) {
                      if($v->term_id == $product_id) {
                        return $v->name;
                      }
                    }

              }
          }

        }
      }

      }
    }
    ?>
    <?php
    if(get_option('gibbys_flash_sale') != 'on') {
     ?>
    <div class="sale" style="background-color:white;padding-left:75px;padding-top:10px;width:100%;height:360px;">
      <h2>Currently on Sale:</h2>
        <div class="cat_sale" style="height:35px;">
          <p><b>Categories</b></p>



            <?php
            //displays the cat_items from the option table
            $cat_items = get_option('flash_options');
            foreach($cat_items as $key=>$val){
              if($key == 'categories'){
                foreach($val as $k=>$id){
                  $prod_id = (int)$id;
                  $name = get_product_name($prod_id, 'categories');
                  ?>
                  <div class='remove-cat' style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' data-id='<?php echo $id;?>'><p style='display:inline;'><?php echo $name.' ';?></p><button class='category2'> X </button></div>
                  <?php
                }
              }
            }
            ?>
        </div>
        <div class="brand_sale" style="padding-top:30px;min-height:90px;margin-bottom:80px;">
          <p><b>Brands</b></p>
          <?php
            //displays the brand_items from the option table
            $brand_items = get_option('flash_options');
            $keys = array_keys($brand_items);
              foreach($brand_items as $key=>$val){
                if($key == 'brands'){
                  foreach($val as $k=>$id){
                    $prod_id = (int)$id;
                    $name = get_product_name($prod_id, 'brands');
                    ?>
                    <div class='remove-brand' style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' data-id='<?php echo $id;?>'><p style='display:inline;'><?php echo $name.' ';?></p><button class='brand2'> X </button></div>
                    <?php
                  }
                }
              }
          ?>
        </div>
        <div class="ind_brand_sale">
          <?php
            //displays the brand_items from the option table
            $brand_items = get_option('individual_flash_options');            
              foreach($brand_items as $key=>$val){
                    $prod_id = (int)$val;
                    $name = get_product_name($prod_id, 'brands');
                    ?>
                    <div class='remove-ind' style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' data-id='<?php echo $prod_id;?>'><p style='display:inline;'><?php echo $name.' ';?></p><button class='brand3'> X </button></div>
              <?php
              }
              ?>
        </div>
    </div>
  <?php }else {

  ?>
  <div class="exclude" style="background-color:white;padding-left:75px;padding-top:10px;width:100%;height:360px;">
    <h2>Currently Excluded:</h2>
      <div class="cat_exclude_div" style="height:35px;">
        <p><b>Categories</b></p>
        <?php
        //displays the cat_items from the option table
        $cat_items = get_option('exclude_options');
        foreach($cat_items as $key=>$val){
          if($key == 'categories'){
            foreach($val as $k=>$id){
              $prod_id = (int)$id;
              $name = get_product_name($prod_id, 'categories');
              ?>
              <div style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' id='<?php echo $id;?>'><p style='display:inline;'><?php echo $name.' ';?></p><button class='category_exclude'> X </button></div>
              <?php
            }
          }
        }
        ?>
      </div>
      <div class="brand_exclude_div" style="padding-top:30px;">
        <p><b>Brands</b></p>
        <?php
          //displays the brand_items from the option table
          $brand_items = get_option('exclude_options');
          $keys = array_keys($brand_items);
            foreach($brand_items as $key=>$val){
              if($key == 'brands'){
                foreach($val as $k=>$id){
                  $prod_id = (int)$id;
                  $name = get_product_name($prod_id, 'brands');
                  ?>
                  <div class='remove' style='display:inline;padding:2px;padding-right:0px;border:1px solid #d8d8d8; border-radius:6px;margin-right:10px;' id='<?php echo $id;?>'><p style='display:inline;'><?php echo $name.' ';?></p><button class='brand_exclude'> X </button></div>
                  <?php
                }
              }
            }
        ?>
      </div>
  </div>
<?php
 }
?>
