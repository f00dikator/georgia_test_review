<?php

require_once 'GTR_DbEngine.php';
require_once 'include/GTR_Header.php';
require_once 'GTR_Session.php';

$SH = new GTR_Session();
$SH->processRequest();

if (!isset($_SESSION["GTR_USER"]) || !isset($_SESSION["GTR_USER"]["userid"]) 
   || !isset($_SESSION["GTR_USER"]["studentid"])) {
   header( "Location: index.php" ); 
}
include ( "GTR_ConductTest.php" );
include ( "GTR_Tutorial.php" );
include( "includes/header.php" );
include( "includes/navigation.php" );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>

<div id="undernav">
<div style="margin-top: 5px; margin-bottom: 5px; margin-right: 5px;">
<span style="text-align:left; float: left;">
<?php
$testid = $_GET['testid'];
if ($testid == NULL || !isset($testid) || $testid == "")
   $testid = $_SESSION['mytestid'];

$db = new GTR_DbEngine();
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
$section_name = "";
$sectionid = $_GET['sectionid'];
if ($sectionid == NULL || !isset($sectionid) || $sectionid == "")
   $sectionid = $_SESSION['mysectionid'];

if (is_array($testSections) && count($testSections)) {

   foreach( $testSections as $id => $name ) {

      if ($sectionid == $id) {

         $section_name = $name;
         echo "<span class=\"leftselected\">" . $name . "</span>";
      } else
         echo "<span class=\"leftitem\">" . $name . "</span>";
		
   } // foreach
}

?>
</ul>
<a href="#" name="top"></a>
<!-- main content -->
<div id="rightcontent">
<?php

if (isset($_SESSION['Percentage']) && isset($_POST['ComputeGrades'])) {

   showPercentage($testinfo);  
}
  
if (isset( $_POST['qNum']) ) 
{
     if ( $_SESSION['myreviewid'] == 0 )
     { 
	if (count($_SESSION['qIds']) <= $_POST['qNum'] ) {
      		generateGradeSheet();
	}
	else {
	  showQuestion( $_POST['qNum'] );
        }

     } else if ($_SESSION['myreviewid'] == 1) {
       
        $_SESSION['practiceqNum'] = $_POST['qNum'];
        showAnswer($_SESSION['practiceqNum']);
         
     } else if ( $_SESSION['myreviewid'] == 2 ) {

        $_SESSION['vocabqNum'] = $_POST['qNum'];
        showVocabFlash($_SESSION['vocabqNum']);  	
     }
     else if ( $_SESSION['myreviewid'] == 3 )
     {
	/*if (count($_SESSION['qIds']) <= $_POST['qNum'] )
      		echo "DONE!";
	else
		showFactoid( $_POST['qNum'] );*/
	$_SESSION['factoidqNum'] = $_POST['qNum'];
        showFactoidFlash($_SESSION['factoidqNum']);
     }
} 

if (isset($_POST['qAns'])) {

   if (count($_SESSION['qIds']) <= $_SESSION['practiceqNum'] ) {

      showPracticePercentage($testinfo,$section_name,$_SESSION['practiceqNum']); 
   } else {

      showQuestion($_SESSION['practiceqNum']); 
   }
}

if (isset($_POST['vocabqAns'])) {

   if (count($_SESSION['qIds']) <= $_SESSION['vocabqNum'] )
      echo "DONE!";
   else
      showVocabulary( $_SESSION['vocabqNum'] );    
}

if (isset($_POST['factoidqAns'])) {

   if (count($_SESSION['qIds']) <= $_SESSION['factoidqNum'] )
      echo "DONE!";
   else
      showFactoid( $_SESSION['factoidqNum'] );    
}

if ( isset( $_GET['tutorial'] ) ) {
   showTutorialSections( $_GET['tutorial'] );
} 
if ( isset( $_GET['tutorialsection']) && isset($_GET['qnum'] ) ) {

  showSection( $_GET['tutorialsection'], $_GET['qnum'] );
} 
if ( isset( $_GET['tutorialsection'])  && !isset($_GET['qnum'])) {

  showSection( $_GET['tutorialsection'], 1 );
}


if ( isset( $_GET['sectionid'] ) && isset( $_GET['option'] ) && isset($_GET['reviewid']) )
{
        $_SESSION['mytestid'] = $_GET['testid'];
        $_SESSION['mysectionid'] = $_GET['sectionid'];
        $_SESSION['myreviewid'] = $_GET['reviewid'];
	switch( $_GET['reviewid'] )
	{
		case 0:
			$qa = $db->getDiagnosticQuestionsAnswers( $_GET['sectionid'], $_GET['option'] );
//print "OPTION " . $_GET['option'];
                        if (is_array($qa) && count($qa) > 0)
			{
//$ctr = 0;
			   while ( count($qa ) < $_GET['option'] )
			   {

//print "<br/>DEBUG START QA[";
//var_dump(count($qa));
//print "]<BR/>";
				$moreqa = array();
				$num = $_GET['option']-count($qa);
				$moreqa = $db->getDiagnosticQuestionsAnswers( $_GET['sectionid'], $_GET['option'] );
//if($ctr <= 10) {
//print "<br/>DEBUG START MOREQA[";
//var_dump(count($moreqa));
//print "]<BR/>";
//} else
//exit;
				//echo "More Questions <br>" ;
				//print_r( $moreqa );
//print "<BR/>NUM " . $num;
				if ( is_array( $moreqa ) && count( $moreqa ) > 0 ) 
				{
					$qs = array_rand( $moreqa, count( $moreqa ) );
//var_dump( $qs );
//					if ( $num == 1 )
//						array_push( $qa, $moreqa[$qs] );
//					else
					{
					    $i = 0;
					    while( $i < $num )
					    {
						if (is_array($moreqa[$qs[$i]]) && count($moreqa[$qs[$i]]) 
						/*&& !isset($qa[$qs[$i]])*/) {

//print "<br/> MOREQA " . $i;
//var_dump($moreqa[$qs[$i]]);
						   $qa[$qs[$i]+rand(0,99999)] = $moreqa[$qs[$i]];
                                                }
						$i++;
					    }
//print "<BR/>i " . $i;
					}
				}
				//echo "<br> Final Set <br>";
				//print_r( $qa );
//$ctr++;
			   }

//print "<br/>DEBUG START COUNT QA[";
//var_dump(count($qa));
//print "<br/>";
//var_dump($qa);
//print "]<BR/>";
//exit;
			   conductTest( $qa ); 
			}
                        else
			   echo "Data not available";
			break;
		case 1:
			$qa = $db->getPracticeQuestionsAnswers( $_GET['sectionid'], $_GET['option'] );
                        if (is_array($qa) && count($qa) > 0)
			{
			   while ( count($qa ) < $_GET['option'] )
			   {

				$moreqa = array();
				$num = $_GET['option']-count($qa);
				$moreqa = $db->getPracticeQuestionsAnswers( $_GET['sectionid'], $_GET['option'] );
				//echo "More Questions <br>" ;
				//print_r( $moreqa );
				if ( is_array( $moreqa ) && count( $moreqa ) > $num ) 
				{
					$qs = array_rand( $moreqa, count($moreqa) );
					//var_dump( $qs );
				        $i = 0;
					while( $i < $num )
					{
					   if (is_array($moreqa[$qs[$i]]) && count($moreqa[$qs[$i]]) 
						/*&& !isset($qa[$qs[$i]])*/) {

//print "<br/> MOREQA " . $i;
//var_dump($moreqa[$qs[$i]]);
					       $qa[$qs[$i]+rand(0,99999)] = $moreqa[$qs[$i]];
                                           }
					   $i++;
					}

				/*	if ( $num == 1 )
						array_push( $qa, $qs );
					else
					{
					    $i = 0;
					    while( $i < $num )
					    {
						$qa[$qs[$i]] = $moreqa[$qs[$i]];
						$i++;
					    }
					}*/
				}
				//echo "<br> Final Set <br>";
				//print_r( $qa );
			   }
			   conductTest( $qa );
                        }
			else
			   echo "Data not available";
                         
			break;
		case 2:
			$qa = $db->getVocabulary( $_GET['sectionid'], $_GET['option'] );
                        if (is_array($qa) && count($qa) > 0)
			{
			   if ( count($qa ) < $_GET['option'] )
			   {
				$moreqa = $db->getVocabulary( $_GET['sectionid'], 20 );
				//echo "More Questions <br>" ;
				//print_r( $moreqa );
				$num = $_GET['option']-count($qa);
				if ( is_array( $moreqa ) && count( $moreqa ) > $num ) 
				{
					$qs = array_rand( $moreqa, $num );
					//var_dump( $qs );
					if ( $num == 1 )
						array_push( $qa, $qs );
					else
					{
					    $i = 0;
					    while( $i < $num )
					    {
						$qa[$qs[$i]] = $moreqa[$qs[$i]];
						$i++;
					    }
					}
				}
				//echo "<br> Final Set <br>";
				//print_r( $qa );
			   }
			   conductVocabularyTest( $qa ); 
                        }
                        else
			   echo "Data not available";
			break;
		case 3:
			$qa = $db->getFactoids( $_GET['sectionid'], $_GET['option'] );
                        if (is_array($qa) && count($qa) > 0)
			{
			   if ( count($qa ) < $_GET['option'] )
			   {
				$moreqa = $db->getFactoids( $_GET['sectionid'], 20 );
				//echo "More Questions <br>" ;
				//print_r( $moreqa );
				$num = $_GET['option']-count($qa);
				if ( is_array( $moreqa ) && count( $moreqa ) > $num ) 
				{
					$qs = array_rand( $moreqa, $num );
					//var_dump( $qs );
					if ( $num == 1 )
						array_push( $qa, $qs );
					else
					{
					    $i = 0;
					    while( $i < $num )
					    {
						$qa[$qs[$i]] = $moreqa[$qs[$i]];
						$i++;
					    }
					}
				}
				//echo "<br> Final Set <br>";
				//print_r( $qa );
			   }
			   conductFactoidTest( $qa ); 
                        }
                        else
			   echo "Data not available";
			break;
		case 4:
		//	echo "Taking Tutorial";
			showTutorials(); 
			break;
		case 5:
			echo "Taking MC Worksheet";
			break;
		case 6:
			echo "Taking Matching Worksheet";
	}
}
echo "</div>";
echo "</html>";
?>
<? include( "includes/footer.php" ); ?>
