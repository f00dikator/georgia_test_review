<?php

   require_once 'GTR_DbEngine.php';
   require_once 'include/GTR_Header.php';
   require_once 'GTR_Session.php';
  
   $SH = new GTR_Session();
   $SH->processRequest();

//   session_start();
   if (isset($_POST['action']) && $_POST['action'] == "login") {

      $username = trim($_POST['username']);
      $password = trim($_POST['password']);
    
    if ($username == '') {
        echo "<br />Invalid login/password combination";
    } else if ($password == '') {

        echo "<br />Invalid login/password combination";
    } else {

        GTR::cleanseArgs($username);
        GTR::cleanseArgs($password);

	if ( preg_match("/[a-zA-Z0-9\s]+/",$username,$matches)) {

	   $db_gtr = new GTR_DbEngine();   	      
	   if ( ($userrec = $db_gtr->authenticate($username,$password)) !== false) {

	       echo "<html>";
               echo "<br />Welcome " . $username;
	       // load user info in the session
	       $_SESSION['GTR_USER']['userid']   = $userrec['UserID'];
	       $_SESSION['GTR_USER']['username'] = $userrec['UserName'];
               $_SESSION['GTR_USER']['contact1']    = $userrec['Contact1'];
               $_SESSION['GTR_USER']['contact2']    = $userrec['Contact2'];

	       echo "<br /> Session ";
	       var_dump($_SESSION);
	       echo "<form name=\"logoutform\" id=\"logoutform\" enctype=\"multipart/form-data\" action=\"login_form.php\"
                     method=\"post\" style=\"margin-top: 5px; margin-bottom: 5px; margin-right: 5px;\">"; 

	       echo "</br>";
	       echo " TestID: <input type=\"text\" name=\"testid\" /> ";
	       echo " Number of Questions: <input type=\"text\" name=\"number\" /> ";
	       echo " <input type=\"submit\" /> ";
	       echo " </form></body> ";
	       
		echo "<a href=\"#\" onClick=\"javascript:document.logoutform.submit();\"
                     style=\"border: 1px solid gray; color: black; background-color: lavender;\">&nbsp;logout&nbsp;</a>";
               echo "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"logout\">
                     <!--<input type=\"submit\" value=\"login\">--> </form>";	
		echo "</html>";
	   }
	   else {
	       echo "<br />Invalid login/password combination. Try again."; 

	   }
	}
    }

  }
  else if ( isset( $_POST['testid'] ) && isset( $_POST['number'] ) )
  {
	$db = new GTR_DbEngine();
	$qAnswers = $db->getTestQuestionsAnswers( $_POST['testid'], $_POST['number'] );
	$_SESSION[ 'questions' ] = $qAnswers;
	showQuestion( );
	
  }
  else if ( isset( $_POST['qId'] ) )
  {
	showQuestion( $_POST[ 'qId' ] );
  } 

  else {

     if (isset($_POST['action']) == "logout") {
       
       if (isset($_SESSION['GTR_USER'])) {
 
         unset($_SESSION['GTR_USER']);
	 unset($_SESSION);
       }
       echo "<br />" . $username . "has successfully logged out.";
       
     }	


   echo "<form name=\"loginform\" id=\"loginform\" enctype=\"multipart/form-data\" action=\"login_form.php\" 
	 method=\"post\" style=\"margin-top: 5px; margin-bottom: 5px; margin-right: 5px;\">";
   echo	"username:&nbsp;<input type=\"text\" name=\"username\" id=\"username\" size=\"15\" maxlength=\"35\" 
	style=\"font-size: 10px; background-color: #FFFFFF; border: 1px solid #666666;\">&nbsp;&nbsp;";
   echo "password:&nbsp;<input type=\"password\" name=\"password\" id=\"password\" size=\"15\" maxlength=\"15\" 
         style=\"font-size: 10px; background-color: #FFFFFF; border: 1px solid #666666;\">&nbsp;&nbsp;";
   echo	"<a href=\"#\" onClick=\"javascript:document.loginform.submit();\" 
         style=\"border: 1px solid gray; color: black; background-color: lavender;\">&nbsp;login&nbsp;</a>"; 
   echo "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"login\">
	 <!--<input type=\"submit\" value=\"login\">--> </form>";

   }

   function showQuestion( $qId )
   {
	if ( isset( $qId ) )
	{
		$qa1 = $_SESSION[ 'questions' ][ $qId ];
       		var_dump( $_SESSION );
		$qa = next( $_SESSION[ 'questions' ] );
	}
	else
	{
		$question = each ( $_SESSION[ 'questions' ] );
		$qa = $question['value'];
		$qId = $question['key'];
	}
	echo "<form name=\"testform\" id=\"testtform\" enctype=\"multipart/form-data\" action=\"login_form.php\"
                     method=\"post\" style=\"margin-top: 5px; margin-bottom: 5px; margin-right: 5px;\">"; 
	echo "</br>";
	echo $i . ". " . $qa[ "QuestionString" ] . "</br></br>";
	echo "<input type=\"radio\" checked=\"checked\" name=\"Choice1\"> ". $qa["Choice1"]  . " </br>";
	echo "<input type=\"radio\" name=\"Choice2\" >". $qa["Choice2"]  ." </br>";
	echo "<input type=\"radio\" name=\"Choice3\" >". $qa["Choice3"]  ." </br>";
	echo "<input type=\"radio\" name=\"Choice4\" >". $qa["Choice4"]  ." </br>";
	echo "<input type=\"submit\" name=\"next\" value=\"Next\">";
	echo "<input type=\"hidden\" name=\"qId\" value=". $qId . ">";
	echo "</br>";
        echo "</form>";
	$i++;
		
   }
?>
