<?php
  $DB = @mysql_pconnect('localhost','georgiatestr','damelkmon') or die ( "Cant connect to Database!" );
  @mysql_select_db( 'georgiatestr', $DB );
  mysql_query("SET NAMES 'utf8'") || die ("Failed");

  if (isset($_POST['ta'])) {
    //mysql_query("UPDATE document SET unicodeText='{$_POST['ta']}' WHERE ID=1") || die ("Update failed");
  }
  /*$contents = file("./mytest.txt");
  if (is_array($contents)) {
    $value = $contents[0];
    var_dump($value);
    $sql = "INSERT INTO document (id,unicodeText) Values(1,'" . $value . "');";
    mysql_query($sql) || die("Insert failed");
  }*/
  $result = mysql_query("SELECT QuestionString FROM DomainQuestionsTableUnicode");
  $rows=array();
//  $result = mysql_query("SELECT unicodeText FROM document where id=44");
  while ( $row = mysql_fetch_array( $result ) ) {
     $rows[] = $row['QuestionString'];
  }
?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
 <title>Test</title>
 <meta http-equiv="content-type" content="text/html; charset=utf-8">
 </head>
 <body>
  <p>Posted: <?php echo $_POST['ta'];?></p>
  <form enctype="multipart/form-data" method="post" action="mytest.php">
 <fieldset>
 <ul><?php
	 foreach($rows as $row) {
            echo "<li>";
	    var_dump($row);
            echo "</li>";
         }
        ?>
 </ul>
 <input type="submit" />
 </fieldset>
 </form>
 </body>
 </html>
