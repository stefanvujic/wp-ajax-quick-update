<?php
//echo shell_exec('pgrep -f bc_update.php');

//TODO: NEED TO CHANGE THE DB SETTINGS IN THE flash_sale_on/off files according to site
if($_POST['update_flash_sale'] && $_POST['gibbys_flash_sale']){
    update_option('gibbys_flash_sale', $_POST['gibbys_flash_sale']);
    //checks if any updating process running and if kills it
    $update_off = shell_exec('pgrep -f bc_update.php');
    //echo shell_exec('ps -fp '.$update_off);
    if ($update_off != '') {
      shell_exec('kill -9 '.$update_off);
    }

    $now = date("Y-m-d H:i:s");
    update_option( 'last_clicked', $now );

    $dir =  constant("WP_CONTENT_DIR");
    if($_POST['gibbys_flash_sale'] == 'on') {
      //Check if the counter process is already running in the background if yes it stops it
      $pid_off = shell_exec('pgrep -f flash_sale_off.php');
      //echo shell_exec('ps -fp '.$pid_off);   //Prints out the details of the process
      if ($pid_off != '') {
        shell_exec('kill -9 '.$pid_off);
      }
      //Calls a file that deletes/inserts the postmeta fields in the background
      shell_exec("php ".$dir."/plugins/gibbys-quick-update/flash_sale_on.php 'alert' >> ".$dir."/plugins/paging.log &");
    }elseif ($_POST['gibbys_flash_sale'] == 'off') {
      //Check if the counter process is already running in the background if yes it stops it
      $pid_on = shell_exec('pgrep -f flash_sale_on.php');
      //echo shell_exec('ps -fp '.$pid_on);   //Prints out the details of the process
      if($pid_on != ''){
        shell_exec('kill -9 '.$pid_on);
      }
      //Calls a file that deletes/inserts the postmeta fields in the background
      shell_exec("php ".$dir."/plugins/gibbys-quick-update/flash_sale_off.php 'alert' >> ".$dir."/plugins/paging.log &");
    }
}
?>
