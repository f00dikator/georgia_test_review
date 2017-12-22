<?php

  function showSuperscript($str=null) 
  {

    if (isset($str) && $str!=null && (($i = strpos($str,"^")) > 0) ) {

       $strarr = array();
       $strarr = explode("^",$str);
       if (is_array($strarr) && count($strarr)) {
 
          $expstr = $strarr[0];
          for ($j = 0;$j<count($strarr);$j++) {

             $expstr .= "<SUP>" . $strarr[1] . "</SUP>"; 
          }       
          return $expstr;
        }
    }

    return $str;
  }

  function conductTest( $qa )
  {
    $_SESSION[ 'questions' ] = $qa;
    $_SESSION[ 'qIds' ] = array_keys( $qa );
    $_SESSION['totalCorrectPracticeAnswers'] = 0;
    showQuestion(0);
  }
 
  function showQuestion($qNum)
  {
    echo " <body><form action=\"review.php\" method=\"post\"> ";
    echo "</br>";
    if (isset($_POST["Choice"])) {

        $lastQId = $_SESSION[ 'qIds' ][ $qNum-1 ];
	$lastQa = $_SESSION[ 'questions' ][ $lastQId ];
	$_SESSION[ 'answers' ][ $lastQId ] = $_POST[ 'Choice' ];

    }
    $qId = $_SESSION[ 'qIds' ][ $qNum ];        
    $qa = $_SESSION[ 'questions' ][ $qId ];
    $qNum++;
    
    echo "<div id=\"question\">";    
    echo "<table height=\"100%\" width=\"100%\">";
    if ( $qa[ "File" ] != "" && stristr( $qa[ "File" ], ".gif" )) {

	 echo "<tr><td align=\"center\"><img width=500 height=500 src=\"" . IMG_DIRECTORY . 
		$qa[ "File" ] . "\"/></td></tr>";

    } else if ( $qa[ "File" ] != "" && stristr( $qa[ "File" ], ".doc" ) ) {

       $ROOT = $_SERVER["DOCUMENT_ROOT"];
       $file = $ROOT . DOCS_DIRECTORY . $qa[ "File" ];
       if (is_file($file) && is_readable($file)) {

          $file_contents = file_get_contents($file);
          echo "<tr><td>" . $file_contents . "</td></tr><br/>";
       }

    }

    echo "<tr><td><h4>". $qNum . ". " . $qa[ "QuestionString" ] . "</h4></td></tr>";
    echo "<tr><td><input type=\"radio\" checked=\"checked\" name=\"Choice\" value=\"".$qa["Choice1"]."\">".$qa["Choice1"]."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice2"]."\">".$qa["Choice2"]."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice3"]."\">".$qa["Choice3"]."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice4"]."\">".$qa["Choice4"]."</td></tr>"  ;
    echo "<tr><td align=\"center\"><input type=\"submit\" align=\"center\" name=\"next\" value=\"Next\"></td></tr>";
    echo "</table>";
    echo "<input type=\"hidden\" name=\"qNum\" value=". $qNum . ">";
    echo "</div></form>";
  }

  function showAnswer($qNum) {

    echo " <body><form action=\"review.php\" method=\"post\"> ";
    if (isset($_POST["Choice"])) {

        $lastQId = $_SESSION[ 'qIds' ][ $qNum-1 ];
	$lastQa = $_SESSION[ 'questions' ][ $lastQId ];
        if (is_array($lastQa) && count($lastQa))
           $realAnswer = $lastQa["AnswerString"];
        $userAnswer = GTR::escape_slash($_POST[ "Choice" ]);

    }
    $qa = $_SESSION[ 'questions' ][ $lastQId ];
    $label = "";
    $isCorrect = false;
    if (strcmp($realAnswer,$userAnswer) == 0) { 
   
       $label = "<ul id=\"subtest\"><span id=\"correct\" class=\"correct\"><center>Correct!</center></span></ul>";
       $isCorrect = true;
       $_SESSION['totalCorrectPracticeAnswers']++;
    } else
       $label = "<ul id=\"subtest\"><span id=\"correct\" class=\"incorrect\"><center>Incorrect!</center></span></ul>";

     $choice1 = $qa["Choice1"];
     $choice2 = $qa["Choice2"];
     $choice3 = $qa["Choice3"];
     $choice4 = $qa["Choice4"];

      switch ($realAnswer) {

        case $qa["Choice1"] :
		 $choice1 = "<span id=\"correct-practice\" class=\"correct-practice\">".
				$qa["Choice1"] . "</span>";
		 break;
    
        case $qa["Choice2"] : 
		$choice2 = "<span id=\"correct-practice\" class=\"correct-practice\">".
				$qa["Choice2"] . "</span>";
		break;
        case $qa["Choice3"] : 
		$choice3 = "<span id=\"correct-practice\" class=\"correct-practice\">".
				$qa["Choice3"] . "</span>";
		break;
        case $qa["Choice4"] : 
		$choice4 = "<span id=\"correct-practice\" class=\"correct-practice\">".
				$qa["Choice4"] . "</span>";
		break;
	default : break;

      } // switch

      if ($isCorrect === false) {

          switch($userAnswer) {

             case $qa["Choice1"] :
			$choice1 = "<span id=\"incorrect-practice\" class=\"incorrect-practice\">".
                                $qa["Choice1"] . "</span>";		
			break;
	     case $qa["Choice2"] :
			$choice2 = "<span id=\"incorrect-practice\" class=\"incorrect-practice\">".
                                $qa["Choice2"] . "</span>";		
			break;
	     case $qa["Choice3"] :
			$choice3 = "<span id=\"incorrect-practice\" class=\"incorrect-practice\">".
                                $qa["Choice3"] . "</span>";		
			break;
	     case $qa["Choice4"] :
			$choice4 = "<span id=\"incorrect-practice\" class=\"incorrect-practice\">".
                                $qa["Choice4"] . "</span>";		
			break;
	     default:
			break;
	  }
      }

    echo "<div id=\"question\">";
    echo "<table>";

    if ( $qa[ "File" ] != "" && stristr( $qa[ "File" ], ".gif" ) ) {

	 echo "<tr><td align=\"center\"><img width=500 height=500 src=\"" . 
		IMG_DIRECTORY . $qa[ "File" ] . "\"/></td></tr><br/>";

    } else if ( $qa[ "File" ] != "" && stristr( $qa[ "File" ], ".doc" ) ) {

       $ROOT = $_SERVER["DOCUMENT_ROOT"];
       $file = $ROOT . DOCS_DIRECTORY . $qa[ "File" ];
       if (is_file($file) && is_readable($file)) {

          $file_contents = file_get_contents($file);
          echo "<tr><td>" . $file_contents . "</td></tr><br/>";
       }

    }
    
    echo "<tr><center><td width=100% align=\"center\">" . $label . "</td></center></tr><br/>";
    echo "<tr><td><h4>". $qNum . ". " . $qa[ "QuestionString" ] . "</h4></td></tr>";
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\""
         .$qa["Choice1"]."\" disabled>".$choice1."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice2"]."\" disabled>".$choice2."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice3"]."\" disabled>".$choice3."</td></tr>"  ;
    echo "<tr><td><input type=\"radio\" name=\"Choice\" value=\"".$qa["Choice4"]."\" disabled>".$choice4."</td></tr>"  ;
    echo "<tr><td><div id=\"reasoning\">" . $qa["Reasoning"] . "</div></td></tr>"; 
    echo "<tr><td><center><input type=\"submit\" name=\"next\" value=\"Next\"></center></td></tr>";
    echo "</table>";
    echo "<input type=\"hidden\" name=\"qAns\" value=". $qNum . ">";
    echo "</div></form>";
   
  }

  function showPercentage($testinfo=array())
  {
     if (isset($_SESSION['Percentage']) && is_array($_SESSION['Percentage']) && count($_SESSION['Percentage'])) {

       echo "<fieldset>";
       $perc = $_SESSION['Percentage'];
       echo "<center><h2>" . $testinfo['name'] ." Diagnostic Summary </h2></center>";
       echo "<ul id=\"subtest\">";
       echo "<h4>STUDENT ID - " . $_SESSION['GTR_USER']['studentid'] . "</h4>";
       echo "<li>Overall Percentage on " . $testinfo['name'] . "<b> " . $perc['Overall'] . "%</b></li>";
       if (isset($perc['Overall']))
          unset($perc['Overall']);

       echo "</ul>";
       echo "<center><h1>Domain Performance Summary</h1></center>";
       echo "<ul id=\"subtest\">";
       if (is_array($perc) && count($perc)) {
 
         foreach ($perc as $domain=>$dperc) {

            echo "<li>" . $domain . " Percentage <b>" . $dperc . "%</b></li>";
         } // foreach
       }
       echo "</ul>";
       echo "</fieldset>"; 
       unset($_SESSION['Percentage']); 
     }
     
  }

  function showPracticePercentage($testinfo=array(),$sectionname="",$totalQs=0)
  {
     if ( isset($_SESSION['totalCorrectPracticeAnswers']) && $_SESSION['totalCorrectPracticeAnswers'] > 0 
	&& $totalQs > 0) {

       $correct = $_SESSION['totalCorrectPracticeAnswers'];
       echo "<fieldset>";
       $perc = $_SESSION['Percentage'];
       echo "<center><h2>" . $testinfo['name'] ." Practice Test Summary </h2></center>";
       echo "<ul id=\"subtest\">";
       echo "<h4>STUDENT ID - " . $_SESSION['GTR_USER']['studentid'] . "</h4>";
       echo "<li>Overall Percentage on " . $sectionname . "<b> " . (100*$correct)/$totalQs . "%</b></li>";

       echo "</ul>";
       echo "</fieldset>"; 
  //   unset($_SESSION['totalCorrectPracticeAnswers']); 
     }
     
  }

  function generateGradeSheet()
  {
    $qNum = 0;
    $domainQs = array();
    $domainAs = array();
    $total_correct = 0;
    // last answer never made it to the grade sheet
    // adding special case here - and then clearing the choice var
    $db = new GTR_DbEngine();
    $domains = $db->getDomainNames( $_SESSION[ 'qIds' ] );
    if (isset($_POST["Choice"])) {

	$numQ = count($_SESSION[ 'qIds' ])-1;	
	if ($numQ > 0 && isset($numQ))
           $lastQId = $_SESSION[ 'qIds' ][ $numQ ];
        if (isset($lastQId)) {

	   $lastQa = $_SESSION[ 'questions' ][ $lastQId ];
	   $_SESSION[ 'answers' ][ $lastQId ] = $_POST[ 'Choice' ];
        }
	unset($_POST["Choice"]);
    }


    echo " <form action=\"review.php\" method=\"post\">";
    echo " <div id=important> Grade Sheet </div> ";

    if (isset($_SESSION['qIds']) && is_array($_SESSION['qIds']) && count($_SESSION['qIds'])) 
    {

      foreach($_SESSION['qIds'] as $qId) {

	$qNum++;
	$qa = $_SESSION[ 'questions' ][ $qId ];
	$question = $qa[ 'QuestionString' ];
        $domain = $domains[ $qId ];
        if (isset($domain) && $domain != "") {

           if (count($domainQs) && array_key_exists($domain,$domainQs)) {

              $domainQs[$domain]++;
           } else {

              $domainQs[$domain] = 1;
	      $domainAs[$domain] = 0;
           }
               
        }  
       
	$answer = $qa[ 'AnswerString' ];
	$answered = $_SESSION[ 'answers' ][ $qId ];
	$correct = ( $answer == GTR::escape_slash($answered) )? "Correct" : "Incorrect";
        if ( $answer == GTR::escape_slash($answered) ) { 
           $domainAs[$domain]++;
           $total_correct++;
        }


	echo "<fieldset>"; 
        echo "<ul id=\"subtest\">";  
        echo "<li>Question : " . $question . "</li>"; 
        echo "<li>Domain : " . $domain . "</li>"; 
        echo "<li>Correct Answer : " . $answer . "</li>";
        echo "<li>You Answered : " . GTR::escape_slash($answered) . "</li>";
        
        if ($correct == "Correct") 
           echo "<br/><span id=\"correct-gradesheet\" class=\"correct-gradesheet\">Your answer is " . 
		$correct . "</span>";
        else
           echo "<br/><span id=\"incorrect-gradesheet\" class=\"incorrect-gradesheet\">Your answer is " . 
		$correct . "</span>";
   
        echo "</ul>";
        echo "</fieldset>";

       } //foreach

       $Percentage = array();
       $Percentage['Overall'] = ($total_correct*100)/$qNum;
       foreach ($domainQs as $dname=>$dcount) {

          $dpercent = (($domainAs[$dname]/$dcount) * 100);
          $Percentage[$dname] = $dpercent;
       } // foreach

       $_SESSION['Percentage'] = $Percentage;


       echo "<input type=\"submit\" name=\"ComputeGrades\" value=\"Compute Grades\">";
       echo "</form>";

      }
  }

  function conductFactoidTest( $qa )
  {
	$_SESSION[ 'factoids' ] = $qa;
        unset ( $_SESSION[ 'qIds' ] );
	$_SESSION[ 'qIds' ] = array_keys( $qa );
	showFactoid( 0 );
	//var_dump( $_SESSION );
  }
  
  function showFactoid($qNum)
  {
    echo " <body><form action=\"review.php?reviewid=". $_GET['reviewid'] ."\" method=\"post\"> ";
    echo "</br>";
    $qId = $_SESSION[ 'qIds' ][ $qNum ];        
    $qa = $_SESSION[ 'factoids' ][ $qId ];
    //print_r( $qa );
    $qNum++;
        
    echo "<div id=\"question\">";
    echo "<table>";
    echo "<tr><td><h4>". $qNum . ". " . $qa[ "Question" ] . "</h4></td></tr>";
  //echo "<tr><td> \tAnswer: " . $qa[ "Answer" ] . "</td></tr>";
    echo "<tr><td><input type=\"submit\" name=\"next\" value=\"Show Answer\"></td></tr>";
    echo "</table>";
    echo "<input type=\"hidden\" name=\"qNum\" value=". $qNum . ">";
    echo "</div></form>";
   }
 
   function showFactoidFlash($qNum)
   {
    echo " <body><form action=\"review.php?reviewid=". $_GET['reviewid'] ."\" method=\"post\"> ";
    echo "</br>";
    $qId = $_SESSION[ 'qIds' ][ $qNum - 1];        
    $qa = $_SESSION[ 'factoids' ][ $qId ];
    //print_r( $qa );
    $qNum++;
        
    echo "<div id=\"question\">";
    echo "<table>";
    echo "<tr><td><h4> \tAnswer: " . $qa[ "Answer" ] . "</h4></td></tr>";
    echo "<tr><td><input type=\"submit\" name=\"next\" value=\"Next\"></td></tr>";
    echo "</table>";
    echo "<input type=\"hidden\" name=\"factoidqAns\" value=". $qNum . ">";
    echo "</div></form>";
   }
 
   function conductVocabularyTest( $qa )
   {
	$_SESSION[ 'vocabulary' ] = $qa;
        unset ( $_SESSION[ 'qIds' ] );
	$_SESSION[ 'qIds' ] = array_keys( $qa );
	showVocabulary( 0 );
	//var_dump( $_SESSION );
  }
  
  function showVocabulary($qNum)
  {
    echo " <body><form action=\"review.php?reviewid=". $_GET['reviewid'] ."\" method=\"post\"> ";
    echo "</br>";
    $qId = $_SESSION[ 'qIds' ][ $qNum ];        
    $qa = $_SESSION[ 'vocabulary' ][ $qId ];
    //print_r( $qa );
    $qNum++;
        
    echo "<div id=\"question\">";
    echo "<table>";
    echo "<tr><td><h4>". $qNum . ". " . $qa[ "Word" ] . "</h4></td></tr>";
    //echo "<tr><td> \tMeaning: " . $qa[ "Meaning" ] . "</td></tr>";
    echo "<tr><center><td><input type=\"submit\" name=\"next\" value=\"Show Meaning\"></td></center></tr>";
    echo "</table>";
    echo "<input type=\"hidden\" name=\"qNum\" value=". $qNum . ">";
    echo "</div></form>";
   }

   function showVocabFlash($qNum)
   {
    echo " <body><form action=\"review.php?reviewid=". $_GET['reviewid'] ."\" method=\"post\"> ";
    echo "</br>";
    $qId = $_SESSION[ 'qIds' ][ $qNum - 1];        
    $qa = $_SESSION[ 'vocabulary' ][ $qId ];
    //print_r( $qa );
    $qNum++;
        
    echo "<div id=\"question\">";
    echo "<table>";
    echo "<tr><td><h4> \tMeaning: " . $qa[ "Meaning" ] . "</h4></td></tr>";
    echo "<tr><td><input type=\"submit\" name=\"next\" value=\"Next\"></td></tr>";
    echo "</table>";
    echo "<input type=\"hidden\" name=\"vocabqAns\" value=". $qNum . ">";
    echo "</div></form>";
   }
 
?>
