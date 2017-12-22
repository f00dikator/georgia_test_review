<?php

require_once 'GTR_DbEngine.php';
require_once 'include/GTR_Header.php';
require_once 'GTR_Session.php';

$SH = new GTR_Session();
$SH->processRequest();

if (!isset($_SESSION["GTR_USER"]) || !isset($_SESSION["GTR_USER"]["userid"]) || 
   (!isset($_SESSION["GTR_USER"]["studentid"]))) {
   header( "Location: index.php" ); 
}
include( "includes/header.php" );
include( "includes/navigation.php" );

?>
<div id="undernav">
<div style="margin-top: 5px; margin-bottom: 5px; margin-right: 5px;">
<span style="text-align:left; float: left;">
<?php

$testid = $_GET['testid'];
$db = new GTR_DbEngine();
//$testinfo["name"] = "9th Grade Literature and Composition";
//$sibling_tests = array(3=>"Language Arts Graduation Test",11=>"American Literature and Composition",
                      //9=>"9th Grade Literature and Composition"); 
$testinfo = $db->getTestInfo($testid);
$sibling_tests = $db->getSiblingTests( $testid );

if (is_array($sibling_tests) && count($sibling_tests) ) {

   foreach( $sibling_tests as $id => $name ) {

      if ($testid == $id) {

	echo "<span class=\"testmenuselected\">&nbsp;" . $name . "&nbsp;</span>"; 

      } else {

	echo "<span class=\"testmenu\"><a href=\"exam.php?testid=" . $id . "\">&nbsp;" . $name . "&nbsp;</a></span>";
      }		
   } // foreach
}
?>		
</span>

<form name="loginform" id="loginform" enctype="multipart/form-data" action="index.php" method="post" style="margin-top: 5px; margin-bottom: 5px; margin-right: 5px;">
<?php echo "Student ID " . $_SESSION['GTR_USER']['studentid'] . "&nbsp;"; ?>
<a href="#" onClick="javascript:document.loginform.submit();" style="border: 1px solid gray; color: black; background-color: lavender;">&nbsp;logout&nbsp;</a>
<input type="hidden" name="action" id="action" value="logout"/>
</form>
</div>
</div>

<!-- bread crumb trail -->
<div id="subundernav">
<a href="index.php">Home</a>&nbsp;&#187;&nbsp;<a href="#"><? echo $testinfo['name']; ?></a>
</div>
<br />

<!-- left navigation box -->
<ul id="leftbar">
<?php

$testSections = $db->getTestSections($testid);
if (is_array($testSections) && count($testSections)) {

   foreach( $testSections as $id => $name ) {

      echo "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?testid=" . $testid . "#" . $id . "\">" . $name . "</a></li>";
   } // foreach
}

?>
</ul>
<a href="#" name="top"></a>
<!-- main content -->
<div id="rightcontent">
	<div id="important"><? echo $testinfo['name']; ?>&nbsp;&nbsp;<a href="#" onMouseover="ddrivetip('<? //echo $exam['description']; ?>', 'Lavender');" onMouseout="hideddrivetip();">[more info]</a></div>
	<ul id="test">
<?php

 if (is_array($testSections) && count($testSections)) {

    foreach($testSections as $id => $name ) {

	echo "<a name=\"" . $id . "\"></a>\n";
	echo "<li>\n";
	echo 	"<fieldset>\n";
	echo 	"<legend>" . $name . "</legend>\n";
	echo 		"<ul id=\"subtest\">\n";

        $review_type_ids = $db->getTestSectionReviewTypes( $id );

        if (is_array($review_type_ids) && count($review_type_ids)) {

	   foreach($review_type_ids as $key=>$review_id) {

  	      $review_type = GTR::getReviewType($review_id);

	      if (isset($review_type) && is_array($review_type)) {

                 if ($review_type['options'] == '') {

		    echo "<li><a href=\"review.php?testid=" . $testid . "&sectionid=" . $id . "&reviewid=" . 
                         $review_id . "&option=10\">" . $review_type['name'] . "</a></li>\n";
	         } else {
		
  	           $name_size = strlen( $review_type['name'] );
		   $dot_count = 40 - $name_size;
		   echo	"<li>" . $review_type['name'] . str_repeat('.', $dot_count) . " select quantity: ";
		   $options = explode( ',', $review_type['options'] );

                   if (is_array($options) && count($options)) {

		      foreach($options as $option) {

		 	 echo "<a href=\"review.php?testid=" . $testid . "&sectionid=" . $id . 
                          "&reviewid=" . $review_id . "&option=" . $option . "\">" . $option . "</a>&nbsp;";
		      }
		      echo "</li>\n";
                   }
                 }
              }
	   } // foreach
        }
	echo "</ul>\n";
	echo "<span id=\"backtotop\" class=\"backtotop\"><a href=\"#top\">back to top</a></span>\n";				
	echo "</fieldset>\n";
	echo "</li>\n";
	echo "<br />\n";

    } // foreach
}	
?>
<? include( "includes/footer.php" ); ?>
