<?php

//SS - changes
require_once 'GTR_DbEngine.php';
require_once 'include/GTR_Header.php';
require_once 'GTR_Session.php';
  
$SH = new GTR_Session();
$SH->processRequest();

include( "includes/header.php" ); 
include( "includes/navigation.php" ); 

$errmsg = NULL;
if (isset($_POST['action']) && $_POST['action'] == "login") {

   $username = trim($_POST['username']);
   $password = trim($_POST['password']);
   if ($username == '') {
        $errmsg = "<span style=\"color:red;font-weight:bold;\">Invalid login/password combination</span>";

   } else if ($password == '') {

        $errmsg = "<span style=\"color:red;font-weight:bold;\">Invalid login/password combination</span>";
   } else {

       GTR::cleanseArgs($username);
       GTR::cleanseArgs($password);

       if ( preg_match("/[a-zA-Z0-9\s]+/",$username,$matches)) {

          $db_gtr = new GTR_DbEngine();   	      
	  if ( ($userrec = $db_gtr->authenticate($username,$password)) !== false) {

	     // load user info in the session
	     $_SESSION['GTR_USER']['userid']   = $userrec['UserID'];
	     $_SESSION['GTR_USER']['username'] = $userrec['UserName'];
             $_SESSION['GTR_USER']['contact1']    = $userrec['Contact1'];
             $_SESSION['GTR_USER']['contact2']    = $userrec['Contact2'];

	  } else {

             $errmsg = "<span style=\"color:red;font-weight:bold;\">Invalid login/password combination. Try again.</span>"; 

	  }
       } else {

	  $errmsg = "<span style=\"color:red;font-weight:bold;\">Invalid login/password combination. Try again.</span>";
       }
    }

  } else if (isset($_POST['action']) && $_POST['action'] == "poststudentid" &&
	isset($_SESSION['GTR_USER'])	) {

     $studentid = trim($_POST['studentid']);
     if ($studentid == '') {

        $errmsg = "<span style=\"color:red;font-weight:bold;\">Invalid Student ID</span>";
     } else {

         GTR::cleanseArgs($studentid);
         $_SESSION['GTR_USER']['studentid'] = $studentid;
     }
  } else {

     if (isset($_POST['action']) == "logout") {
       
       if (isset($_SESSION['GTR_USER'])) {
 
         unset($_SESSION['GTR_USER']);
	 unset($_SESSION);
       }
       
     }	
 }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <meta http-equiv="content-type" content="text/html; charset=utf-8">
 </head>
<div id="undernav">
<form name="loginform" id="loginform" enctype="multipart/form-data" action="index.php" method="post" style="margin-top: 5px; margin-bottom: 5px; margin-right: 5px;">
<? if( isset($_SESSION['GTR_USER']) && isset($_SESSION['GTR_USER']['userid']) && 
       !isset($_SESSION['GTR_USER']['studentid']) ){ ?>

                Student ID:&nbsp;<input type="text" name="studentid" id="studentid" size="15" maxlength="35" style="font-size: 10px; background-color: #FFFFFF; border: 1px solid #666666;">&nbsp;&nbsp;
               <a href="#" onClick="javascript:document.loginform.submit();" style="border: 1px solid gray; color: black; background-color: lavender;">&nbsp;Submit&nbsp;</a>
                <input type="hidden" name="action" id="action" value="poststudentid">

<? }else if ( isset($_SESSION['GTR_USER']) && isset($_SESSION['GTR_USER']['userid']) &&
       isset($_SESSION['GTR_USER']['studentid'])) { ?>

		<?php echo "Student ID " . $_SESSION['GTR_USER']['studentid'] . "&nbsp;"; ?>
		<a href="#" onClick="javascript:document.loginform.submit();" style="border: 1px solid gray; color: black; background-color: lavender;">&nbsp;logout&nbsp;</a>
		<input type="hidden" name="action" id="action" value="logout">

<? }else{ ?>
		username:&nbsp;<input type="text" name="username" id="username" size="15" maxlength="35" style="font-size: 10px; background-color: #FFFFFF; border: 1px solid #666666;">&nbsp;&nbsp;
		password:&nbsp;<input type="password" name="password" id="password" size="15" maxlength="15" style="font-size: 10px; background-color: #FFFFFF; border: 1px solid #666666;">&nbsp;&nbsp;
		<a href="#" onClick="javascript:document.loginform.submit();" style="border: 1px solid gray; color: black; background-color: lavender;">&nbsp;login&nbsp;</a> 
		<input type="hidden" name="action" id="action" value="login">
		<!--<input type="submit" value="login">-->
		<?php if( !empty($error) ) echo "<br />" . $error; ?>
<? } ?>
</form>
</div>

<!-- bread crumb trail -->
<div id="subundernav">
<a href="index.php">Home</a>
</div>
<br />

<!-- left navigation box -->
<ul id="leftbar">
<li>
<a href="index.php?show=about">about us</a>
</li>
<li>
<a href="index.php?show=contact">contact us</a>
</li>
<li>
<a href="index.php?show=subscribe">subscription</a>
</li>
</ul>

<!-- main content -->
<div id="rightcontent">
<?

if ( (isset($_SESSION['GTR_USER']) && isset($_SESSION['GTR_USER']['userid']) &&
	isset($_SESSION['GTR_USER']['studentid'])) && 
    ((isset($_GET['testid']) && isset ($_GET['number'])) || isset( $_POST['qNum'])) ) {
 
  function showQuestion($qNum)
  {
    echo " <body><form action=\"index.php\" method=\"post\"> ";
    echo "</br>";
    if (isset($_POST["Choice"])) {

        $lastQId = $_SESSION[ 'qIds' ][ $qNum-1 ];
	$lastQa = $_SESSION[ 'questions' ][ $lastQId ];
	$_SESSION[ 'answers' ][ $lastQId ] = $_POST[ 'Choice' ];

    }
    $qId = $_SESSION[ 'qIds' ][ $qNum ];        
    $qa = $_SESSION[ 'questions' ][ $qId ];
    $qNum++;
        
    echo "<table>";
    echo "<tr><td>". $qNum . ". " . $qa[ "QuestionString" ] . "</td></tr>";
    echo "<tr><td><input type=\"radio\" checked=\"checked\" name=\"Choice\" value=\""
         .$qa["Choice1"]."\">".$qa["Choice1"]."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice2"]."\">".$qa["Choice2"]."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice3"]."\">".$qa["Choice3"]."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice4"]."\">".$qa["Choice4"]."</td></tr>"  ;
    echo "<tr><td><input type=\"submit\" name=\"next\" value=\"Next\"></td></tr>";
    echo "</table>";
    echo "<input type=\"hidden\" name=\"qNum\" value=". $qNum . ">";
    echo "</form>";
  }

  function generateGradeSheet()
  {
    $qNum = 0;
    echo " <body><form action=\"index.php\" method=\"post\"> ";
    echo " <h2>< Grade Sheet </h2> ";
    if (isset($_SESSION['qIds']) && is_array($_SESSION['qIds']) && count($_SESSION['qIds'])) {

      echo " <table> ";
      echo " <b><tr><b><td> Question Number </td><td> Question </td><td> Answer </td><td> 
	   You Answered </td><td> Grade </td></b></tr></b> ";

      foreach($_SESSION['qIds'] as $qId) {

	$qNum++;
	$qa = $_SESSION[ 'questions' ][ $qId ];
	$question = $qa[ 'QuestionString' ];
	$answer = $qa[ 'AnswerString' ];
	$answered = $_SESSION[ 'answers' ][ $qId ];
	$correct = ( $answer == $answered )? "Correct" : "Incorrect";
		
	echo " <tr><td> ". $qNum . " </td><td> ". $question . " </td><td> ". $answer . " </td><td "
	     . $answered . " </td><td> ". $correct . "</td></tr>";

      } // foreach
      echo "</table>";
    }
	
  }

  if (isset($_GET['testid']) && isset ($_GET['number'])) {

   $db = new GTR_DbEngine();
   $qAnswers = $db->getTestQuestionsAnswers( $_GET["testid"], $_GET["number"] );
   $_SESSION['questions'] = $qAnswers;
   $_SESSION['qIds'] = array_keys( $qAnswers );
   showQuestion(0);

  } else if (isset( $_POST['qNum'])) {

   if (count($_SESSION['qIds']) <= $_POST['qNum'])
      generateGradeSheet();
   else
      showQuestion($_POST['qNum']);
  } 
 
} else if ( (!isset($_SESSION['GTR_USER']) || !isset($_SESSION['GTR_USER']['userid'])) &&
    ((isset($_GET['testid']) && isset ($_GET['number'])) || isset( $_POST['qNum']))) {

   echo "<p> Please login first.</p>";
   if (isset($_GET['testid']))
      unset($_GET['testid']);
   if (isset($_GET['number']))
      unset($_GET['number']);
   if (isset($_POST['qNum']))
      unset($_POST['qNum']);
   
} else if (isset($_GET['show']) && $_GET['show'] != "") {
 
  $value = $_GET['show'];
  switch($value) {
  
     case "about": include("GTR_About.php");
		   unset($_GET['show']);
		   break;
     case "contact": include("GTR_Contact.php");
		   unset($_GET['show']);
		   break;
     case "subscribe": include("GTR_Subscribe.php");
		   unset($_GET['show']);
		   break;
     default :  break;
		
  }
} else if ( (!isset($_SESSION['GTR_USER']['studentid'])) &&
    ((isset($_GET['testid']) && isset ($_GET['number'])) || isset( $_POST['qNum']))) {

   echo "<p> Please enter your Student ID first.</p>";
   if (isset($_GET['testid']))
      unset($_GET['testid']);
   if (isset($_GET['number']))
      unset($_GET['number']);
   if (isset($_POST['qNum']))
      unset($_POST['qNum']);

} else {
  include( "GTR_Content.php" ); 
}

include( "includes/footer.php" ); ?>
</div>
</html>
