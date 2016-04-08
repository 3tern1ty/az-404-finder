<?php
  ini_set('max_execution_time',2147483647);
  $pages = array('');
  $log_file = __DIR__.'/log/404log.txt';
  // if(!file_exists($log_file)){
  //   file_put_contents($log_file, '<?php\n');
  // }

  $links = array();

  $k=0;
  file_put_contents($log_file, 'Search 404 pages in domain: '.$pages[0].PHP_EOL, FILE_APPEND);
  file_put_contents($log_file, 'Search start at: '.date(DATE_RFC2822).PHP_EOL, FILE_APPEND);
  file_put_contents($log_file, 'Page contains 404 url -> 404 url'.PHP_EOL, FILE_APPEND);

  while (!empty($pages[$k])) {                      // for page changes


    $added[ $pages[$k] ] = array();
    // $ping = 0;
    do{

      $ch = curl_init($pages[$k]);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_VERBOSE, 1);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      $html = 0;
      $html = curl_exec($ch);
      curl_close($ch);
      if( empty($html) ){
        echo '<p style="color:red">Connection trouble with ' . $pages[$k] . '</p>';
        echo 'Error: <br>' . curl_errno($ch);
      }
      // if ( $ping > 0 ) {
      //   echo '<p style="color:red">Connection trouble with ' . $pages[$k] . '</p>';
      // }
      // $ping++;
    }while ( empty ($html) );

    $html = preg_split('/\n/', $html, 2);

    $httpHeaderAnswerCode = explode(' ' , $html[0])[1];

    if ($httpHeaderAnswerCode == '404' || $httpHeaderAnswerCode == '301') {
      $page404[$pages[$k]]= array();
      foreach ($added as $key => $value) {
            if(in_array($pages[$k], $value)){
              array_push($page404[$pages[$k]], $key );
              file_put_contents($log_file, $key . ' -> ' .$pages[$k].PHP_EOL, FILE_APPEND);

            }
      }

    }
    else {
      if ( preg_match_all('/href=["|\'](' . $pages[0] . '\/[^wp][A-Za-z0-9\-\/]{0,}?["|\'])/', $html[1], $urls) ){
          $links = preg_replace( '/\"/', '', preg_replace('/\'/', '', $urls[1]  ) );

        $i=0;
        while (!empty($links[$i])) {                     // for check page
          $write = false;
          for($j=0; $j<count($pages); $j++) {   // for check links in page
            if( $links[$i] == $pages[$j]){
              $write = true;
              break;
            }
          }

          if ($write == false) {
            array_push($pages, $links[$i]);
            array_push($added[ $pages[$k] ], $links[$i]);
            // echo  $pages[$k] . '---->' . $links[$i] .'<br>';
            // ob_flush();
            // flush();


          }

          $i++;
        }

      }
    }
    // TEST SECTION
     if ($k==10){
    //   $page404[$pages[$k]]= array();
    //   array_push($page404[$pages[$k]], 'SRANb' );
    //   file_put_contents($log_file, $k . ' -> ' . $pages[$k].PHP_EOL, FILE_APPEND);
    //   foreach ($page404 as $key => $value) {
    //    file_put_contents($log_file, $value. ' -> ' . $key .PHP_EOL, FILE_APPEND);
    //   }
       break;
     }

    $k++;
  }
  file_put_contents($log_file, 'Search end at: '.date(DATE_RFC2822).PHP_EOL.PHP_EOL, FILE_APPEND);

//Сделать запись результатов в базу данный через SQL запрос в таблицу опций и поставить проверку в основном файле на то что опция обновлена вывести сообщение через admin_notice


/*
  update_option('az-404-links', $page404);

  function sample_admin_notice__success1() {
    ?>
      <div class="notice notice-success is-dismissible">
          <p>Links checking done. See <a href="<?php echo admin_url('admin.php?page=az-404-finder/Faz-404-finder.php');?>az-404-finder/az-404-finder.php">here</a></p>
      </div>
    <?php
  }
  add_action( 'admin_notices', 'sample_admin_notice__success1' );
*/


  // echo "time start: ". $startTime ."<br>";
  // echo "time end: ". date(DATE_RFC2822)."<br>";
  // echo "<h1> pages array</h1>";
  // echo '<pre>';
  //   print_r  ($pages);
  // echo '</pre><br>';
  // echo "<h1>ADDED PAGES array</h1>";
  // echo '<pre>';
  //   print_r  ( $added );
  // echo '</pre>';
  // echo "<h1>PAGES 404 array</h1>";
  // echo '<pre>';
  //
  //   print_r  ( $page404 );
  // echo '</pre>';

?>
