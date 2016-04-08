<?php
/*
Plugin Name: Dead links(404) finder
Plugin URI: https://github.com/3tern1ty/az-404-finder
Description: Find dead links(404) among all links internal links of your site
Author name: 3tern1ty
Version: 0.1
Author URI: http://bluemountainfengshui.org
/*

The MIT License (MIT)

Copyright (c) 2016 3tern1ty

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

function az_404_finder_plugin_install(){
  add_option('az-404-links');
}
register_activation_hook(__FILE__, 'az_404_finder_plugin_install');


function az_404_finder_plugin_remove(){
  delete_option('az-404-links');
}
register_uninstall_hook(__FILE__, 'az_404_finder_plugin_remove');


function az_404_finder_add_menu_page(){
  add_menu_page('404 links finder', '404 finder', 'manage_options', plugin_basename( __FILE__ ), 'az_404_finder_display', 'dashicons-search', 22);
}
add_action('admin_menu', 'az_404_finder_add_menu_page');

function az_404_finder_display(){
  $domain = '';
  if (isset($_POST['script'])) {
    if(function_exists('current_user_can') && !current_user_can('manage_options')){
      die('Are u hacker?');
    }
    if (function_exists('check_admin_referer') ) {
      check_admin_referer('az_404_finder_nonce');
    }

    if( isset($_POST['domain']) && !empty($_POST['domain']) ){
      $domain = rtrim(ltrim($_POST['domain']));
      $fArray = file(plugin_dir_path(__FILE__) . '404.php');
      $fArray[2] = preg_replace( '~.pages\s=\sarray.*~', '$pages = array(\''. $domain .'/\');', $fArray[2] );
      file_put_contents( plugin_dir_path(__FILE__) . '404.php', $fArray );
      var_dump($fArray);
      // if( $fh = fopen(plugin_dir_path(__FILE__) . '404.php', 'r+') ){
      //   while ( !feof($fh) ){
      //     $temp = fgets($fh);
      //     if(preg_match('~.pages\s=\sarray.*~', $temp)){
      //         $temp = preg_replace( '~.pages\s=\sarray.*~', '$pages = array(\''. $domain .'/\');', $temp );
      //         fwrite( $fh,  $temp);
      //         fclose($fh);
      //         break;
      //     }
      //     //  //if( preg_replace( '/.pages\s=\sarray.*/', '$pages = array(\''. $domain .'/\');', fgets($fh) ) )
      //     //  $temp = fgets($fh);
      //     //  $temp = preg_replace( '~.pages\s=\sarray.*~', '$pages = array(\''. $domain .'/\');', $temp );
      //     //  var_dump($temp);
      //     //  //$temp = fgets($fh);
      //     //  //if( preg_replace( '/.pages\s=\sarray.*/', '$pages = array(\''. $domain .'/\');', $temp ) )
      //     //  //{
      //     //    //fwrite( $fh,  $temp);
      //     //
      //     // //   var_dump($temp);
      //     // //   break;
      //     // // }
      //   }
      // }

      //shell_exec("/path/to/php /path/to/send_notifications.php '".$post_id."' 'alert' >> /path/to/alert_log/paging.log &");
      $command = 'php -q '. plugin_dir_path(__FILE__) . '404.php > '. plugin_dir_path(__FILE__) .'parse.log 2>&1 &';
      shell_exec($command);
    }
    else {
      echo '<script>alert("Empty domain");</script>';
    }
  }
  ?>
    <div class="wrap">
      <h1>Check any site for links</h1>
      <p>Enter domain name of site you want to check. Example: "http://google.com"<br>
      <form  name="az_404_finder_form" class="az_rel_nofollow_form" action="<?php echo $_SERVER['PHP_SELF'];?>?page=<?php echo plugin_basename(__FILE__);?>" method="post">
        <?php
          if (function_exists ('wp_nonce_field') )
          {
              wp_nonce_field('az_404_finder_nonce');
          }
        ?>
        <input size="50" name="domain" type="text" value="<?php echo $domain;?>"/></p>
        <p>Search results:<br>
        <textarea style="width:100%;" rows="12" readonly><?php
            if( file_exists(__DIR__.'/log/404log.txt') ) {
              $lines = file(__DIR__.'/log/404log.txt');
              foreach($lines as $line)
              {
                echo($line);
              }
            }else{
              echo 'No results yet';
            }
          ?></textarea>
        </p>
        <p class="submit">
            <input type="hidden" name="script" value="start" />
           <input type="submit" name="submit" class="button button-primary button-large" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
      <!-- <a class="button button-primary button-large" href="<?php echo admin_url();?>admin.php?page=<?php echo plugin_basename( __FILE__ );?>?script=start">Start Check</a> -->
    </div>

  <?php
}




?>
