<?php
require_once "include/GTR_Header.php";

class GTR_DbEngine
{
  
	private $con; 

	public function __construct()
	{
		$this->con = @mysql_pconnect(SQL_HOST,SQL_USER,SQL_PASSWORD) or die ( "Cant connect to Database!" );
		@mysql_select_db( SQL_DB, $this->con ) or die ("Cant select database");
	}

	public function getCourses()
	{
		$courses = array();
		$result = $this->run_query("SELECT * FROM CourseTable");
                if (isset($result) && $result !== false) {

 		   while($row = mysql_fetch_array($result)) {

  		      $courses[ $row[ 'CourseName' ] ] = $row['CourseID'];
	  	   }
		   return $courses;
               }
               return false;
	}

	public  function getCourseTests( $courseId )
	{
		if (!isset($courseId) || $courseId == NULL || $courseId == "" || $courseId === false)
                   return false;

		$tests = array();
		$sql = "select distinct A.TestID, B.TestName from CourseTestTable A, TestTable B 
			where A.CourseID=" . $courseId . " and A.TestID=B.TestID";
		$result = $this->run_query( $sql );
                if (isset($result) && $result !== false) {

		   while( $row = mysql_fetch_array( $result ) ) {

  			$tests[ $row[ 'TestName' ] ] = $row['TestID'];
	  	   }
		   return $tests;
                }
	        return false;	
	}

	public function getTestInfo( $testId )
	{
		if (!isset($testId) || $testId == NULL || $testId == "" || $testId === false)
                   return false;
		$info = array();
		$sql = "select TestName from TestTable where TestID=" . $testId ;
		$result = $this->run_query( $sql );
                if (isset($result) && $result !== false) 
		{
			if( $row = mysql_fetch_array( $result ) ) 
			{
				if ( isset( $row['TestName'] ) )
					$info['name'] = $row['TestName'];
			}
		}
		return $info;
	}

	public function getSiblingTests( $testId )
	{
		if (!isset($testId) || $testId == NULL || $testId == "" || $testId === false)
                   return false;
		$tests = array();
		$subquery = "( select distinct CourseID from CourseTestTable where TestID=" . $testId . ")"; 
		$sql = "select distinct A.TestID, B.TestName from CourseTestTable A, TestTable B where A.CourseID=" 
			. $subquery . " and A.TestID=B.TestID";
	//	echo $sql;
		$result = $this->run_query( $sql );
                if (isset($result) && $result !== false) 
		{
			while( $row = mysql_fetch_array( $result ) ) 
			{
				if ( isset( $row['TestName'] ) && isset ( $row['TestID'] ) )
				{
					$tests[ $row['TestID'] ] = $row[ "TestName" ];
				}
			}
		}
		return $tests;
	}

	public function getTestSections($testId)
	{
		if (!isset($testId) || $testId == NULL || $testId == "" || $testId === false)
                   return false;
		$sections = array();
		$sql = "select SectionID, SectionName from CourseTestTable where TestID=" . $testId ;
		$result = $this->run_query( $sql );
                if (isset($result) && $result !== false) 
		{
			while( $row = mysql_fetch_array( $result ) ) 
			{
				$sections[ $row['SectionID'] ] = $row['SectionName'];
			}
		}
		return $sections;
	}
		
	public function getTestSectionReviewTypes( $id )
	{
		$reviewTypes = array();
		$sql = "select ReviewTypeID from SectionReviewTypesTable where SectionID=" . $id ;
		$result = $this->run_query( $sql );
                if (isset($result) && $result !== false) 
		{
			$i = 0;
			while( $row = mysql_fetch_array( $result ) ) 
			{
				$reviewTypes[ $i ] = $row['ReviewTypeID'];
				$i++;
			}
		}
		return $reviewTypes;
		
	}

   	public function getDomainNames( $qIds )
	{
		if ( !is_array( $qIds ) || count( $qIds ) <= 0 )
			return false;
		$domains = array();
		foreach( $qIds as $key=>$qId )
		{
			$sql = "select DomainName from CourseDomainTable where DomainID =
                                 ( select DomainID from DomainQuestionsTableUnicode where QuestionID = " . $qId . " ) ";
			//echo "<br>" . $sql . "<br>";
			$result = $this->run_query( $sql );
                	if (isset($result) && $result !== false) 
			{
				if ( $row = mysql_fetch_array( $result ) )
					$domains[ $qId ] = $row[ 'DomainName' ];
			}
		}
		return $domains;
	}
		
	public  function getFactoids( $sectionId, $number )
	{
		if (!isset($sectionId) || $sectionId == NULL || $sectionId == "" || $sectionId === false)
                   return false;
		//echo "NUMBER=" . $number;
		$questionAnswers = array();
		$sql = "select SectionName from CourseTestTable where SectionID=".$sectionId ;
		$result = $this->run_query( $sql );
		if (isset($result) && $result !== false) 
		{
			if ( $row = mysql_fetch_array( $result ) )
			{
				if ( $row['SectionName'] == "Full Test Review" )
				{
					$questionAnswers = $this->getFullTestFactoid( $sectionId, $number );
					return $questionAnswers;
				}
			}
		}
		// Practice Questions for individual domains, do not consider percentage
		$subsql = "select DomainIDs from CourseTestTable where SectionID=" . $sectionId ;
		$sql = "select DomainID, Question, Answer from DomainFactoidTable where DomainID in ( (" . $subsql . ") )"; 
		//echo $sql;
		$questions = array();
		$result = $this->run_query( $sql );
	        if (isset($result) && $result !== false) 
		{
			$i = 0;
			while ( $row = mysql_fetch_array( $result ) )
			{
				$word = array();
				$word[ 'DomainID' ] = $row[ "DomainID" ];
				$word[ 'Question' ] = $row[ "Question" ];
				$word[ 'Answer' ] = $row[ "Answer" ];
				$questions[ $i ] = $word;
				$i++;				
			}
		}
		//echo " Number = ". $number;
		//echo " Questions got back = " . count ( $questions );
		if ( is_array( $questions ) && count( $questions ) > 0 )
		{
			if ( count ( $questions ) < $number )
				$number = count ( $questions );
			$qNums = array_rand( $questions, $number );
			if ( is_array( $qNums ) )
			{
				foreach ( $qNums as $key=>$qNum )
				{
					$qa = array();
			//			echo " QuestionID = " . $row[ "QuestionID" ] . "<br>";
					$questionAnswers[ $key ] = $questions[ $qNum ];
				
				}
			}
		}
		
//			echo "Vocabulary array : <br> ";
//			print_r( $questionAnswers) . "<br>";
		if( count( $questionAnswers ) > $number )
		{
			shuffle( $questionAnswers );
			$questionAnswers = array_slice( $questionAnswers, 0, $number );
		}
		return $questionAnswers;
	}		
	
	public function getFullTestFactoid( $sectionId, $number )
	{
//echo "NUMBER=" . $number . " Section = " . $sectionId;
		if (!isset($sectionId) || $sectionId == NULL || $sectionId == "" || $sectionId === false)
                   return false;
		$sql = "select TestID from CourseTestTable where SectionID=".$sectionId ;
		$result = $this->run_query( $sql );
	        if (isset($result) && $result !== false) 
		{
			if ( $row = mysql_fetch_array( $result ) )
				$testId = $row['TestID'];
		}
		if ( !isset( $testId ) )
			return false;

		$questionNumbers = array();
		$questionsAnswers = array();
		$sql = "select Percentage, DomainIDs from CourseTestTable where TestID=" . $testId ;

		$result = $this->run_query( $sql );

                if (isset($result) && $result !== false)
		{
			while( $row = mysql_fetch_array( $result ) ) 
			{
		     		if (isset($row[ DomainIDs ]) && $row[ DomainIDs ] !== false && $row[ DomainIDs ] != "") 
				{
		 		        $questionNumbers[ $row[ DomainIDs ] ] = round( $row[ Percentage ] / 100 * $number );
//echo "</br> Number of questions from domain ". $row[ DomainIDs ] . " is = " . $questionNumbers[ $row[ DomainIDs ] ];
				        $questions = array();
		        		$sql = "select DomainID, Question, Answer from DomainFactoidTable 
					  	    where DomainID in (". $row[ DomainIDs ] . ")";

//echo "</br>Sql for QuestionIds for Domain " . $row[ DomainIDs ] . " = " . $sql . "</br>";
		        		$res = $this->run_query( $sql );
				        $i = 0;
                		        if (isset($res) && $res !== false) 
					{
 					   while( $r = mysql_fetch_array( $res ) ) 
					   {
						$qa[ 'DomainID' ] = $r[ "DomainID" ];
						$qa['Question'] = $r['Question'];
						$qa['Answer'] = $r['Answer'];
						$questions[ $i ] = $qa;
						$i++;			
					   } // while
					   $domainQuestions[ $row[ DomainIDs ] ] = $questions;
//	print_r( $domainQuestions[ $row[ DomainIDs ] ] );
                        		}
	                     }
		       } // while
                }

//echo "</br>*********DomainQuestions**********</br>";
//print_r( $domainQuestions );
//print "<br/>questionNumbers ["; var_dump($questionNumbers); print "]";
 	        $i = 0;
                if (is_array($questionNumbers) && count($questionNumbers) ) 
		{
		   foreach( $questionNumbers as $key=>$value ) 
		   {
//print "<br/> Key ["; var_dump($key); print "]";
		        $qIds = array();
//echo "</br>*********Getting Random Questions, Source arrary = **********</br>";
//print_r( $domainQuestions[ $key ] );

			//echo "Number of Questions: " . $value . "<br>";

			if ( !is_array( $domainQuestions ) || count( $domainQuestions ) <=0 )
				return false;

			if ( !is_array( $domainQuestions[$key] ) || count( $domainQuestions[$key] ) <= 0 )
				continue;

			if ( count( $domainQuestions[ $key ] ) < $value )
				$value = count( $domainQuestions[$key] );

//print_r( $domainQuestions[$key] );
		        $qs = array_rand( $domainQuestions[ $key ], $value );
//print "<br/>qs[";var_dump($qs);print "]";
//echo "</br>*********Random QuestionIds from Domain = " . $key . "**********</br>";
//print_r( $qIds );
                        if (is_array($qs) && count($qs)) 
			{
			   foreach ( $qs as $k=>$q ) 
			   {
//	echo "<br>****************Key = ". $key . "Question Number = " . $q . " ***************<br> ";
				$questionsAnswers[ $i ] = $domainQuestions[ $key ][ $q ];
//	var_dump( $domainQuestions[ $key ] );
				$i++;
			   }
                        }
                   }
		}
		if( count( $questionAnswers ) > $number )
		{
			shuffle( $questionAnswers );
			$questionAnswers = array_slice( $questionAnswers, 0, $number );
		}
		
		return $questionsAnswers;
	}
	
	public  function getVocabulary( $sectionId, $number )
	{
		if (!isset($sectionId) || $sectionId == NULL || $sectionId == "" || $sectionId === false)
                   return false;
		//echo "NUMBER=" . $number;
		$questionAnswers = array();
		$sql = "select SectionName from CourseTestTable where SectionID=".$sectionId ;
		$result = $this->run_query( $sql );
		if (isset($result) && $result !== false) 
		{
			if ( $row = mysql_fetch_array( $result ) )
			{
				if ( $row['SectionName'] == "Full Test Review" )
				{
					$questionAnswers = $this->getFullTestVocabulary( $sectionId, $number );
					return $questionAnswers;
				}
			}
		}
		// Practice Questions for individual domains, do not consider percentage
		$subsql = "select DomainIDs from CourseTestTable where SectionID=" . $sectionId ;
		$sql = "select DomainID, Word, Meaning from DomainVocabularyTable where DomainID in ( (" . $subsql . ") )"; 
		//echo $sql;
		$questions = array();
		$result = $this->run_query( $sql );
	        if (isset($result) && $result !== false) 
		{
			$i = 0;
			while ( $row = mysql_fetch_array( $result ) )
			{
				$word = array();
				$word[ 'DomainID' ] = $row[ "DomainID" ];
				$word[ 'Word' ] = $row[ "Word" ];
				$word[ 'Meaning' ] = $row[ "Meaning" ];
				$questions[ $i ] = $word;
				$i++;				
			}
		}
		//echo " Number = ". $number;
		//echo " Questions got back = " . count ( $questions );
		if ( is_array( $questions ) && count ( $questions ) > 0 )
		{
			if ( count ( $questions ) < $number )
				$number = count ( $questions );
			$qNums = array_rand( $questions, $number );
			if ( is_array( $qNums ) )
			{
				foreach ( $qNums as $key=>$qNum )
				{
					$qa = array();
			//		echo " QuestionID = " . $row[ "QuestionID" ] . "<br>";
					$questionAnswers[ $key ] = $questions[ $qNum ];
				
				}
			}
		}
		
			//			echo "Vocabulary array : <br> ";
			//			print_r( $questionAnswers) . "<br>";
		if( count( $questionAnswers ) > $number )
		{
			shuffle( $questionAnswers );
			$questionAnswers = array_slice( $questionAnswers, 0, $number );
		}
		return $questionAnswers;
	}		

	public function getFullTestVocabulary( $sectionId, $number )
	{
		//echo "NUMBER=" . $number . " Section = " . $sectionId;
		if (!isset($sectionId) || $sectionId == NULL || $sectionId == "" || $sectionId === false)
                   return false;
		$sql = "select TestID from CourseTestTable where SectionID=".$sectionId ;
		$result = $this->run_query( $sql );
	        if (isset($result) && $result !== false) 
		{
			if ( $row = mysql_fetch_array( $result ) )
				$testId = $row['TestID'];
		}
		if ( !isset( $testId ) )
			return false;

		$questionNumbers = array();
		$questionsAnswers = array();
		$sql = "select Percentage, DomainIDs from CourseTestTable where TestID=" . $testId ;

		$result = $this->run_query( $sql );

                if (isset($result) && $result !== false)
		{
			while( $row = mysql_fetch_array( $result ) ) 
			{
		     		if (isset($row[ DomainIDs ]) && $row[ DomainIDs ] !== false && $row[ DomainIDs ] != "") 
				{
		 		        $questionNumbers[ $row[ DomainIDs ] ] = round( $row[ Percentage ] / 100 * $number );
//echo "</br> Number of questions from domain ". $row[ DomainIDs ] . " is = " . $questionNumbers[ $row[ DomainIDs ] ];
				        $questions = array();
		        		$sql = "select DomainID, Word, Meaning from DomainVocabularyTable 
					  	    where DomainID in (". $row[ DomainIDs ] . ")";

//echo "</br>Sql for QuestionIds for Domain " . $row[ DomainIDs ] . " = " . $sql . "</br>";
		        		$res = $this->run_query( $sql );
				        $i = 0;
                		        if (isset($res) && $res !== false) 
					{
 					   while( $r = mysql_fetch_array( $res ) ) 
					   {
						$qa[ 'DomainID' ] = $r[ "DomainID" ];
						$qa['Word'] = $r['Word'];
						$qa['Meaning'] = $r['Meaning'];
						$questions[ $i ] = $qa;
						$i++;			
					   } // while
					   $domainQuestions[ $row[ DomainIDs ] ] = $questions;
				//	print_r( $domainQuestions[ $row[ DomainIDs ] ] );
                        		}
	                     }
		       } // while
                }

//echo "</br>*********DomainQuestions**********</br>";
//print_r( $domainQuestions );
//print "<br/>questionNumbers ["; var_dump($questionNumbers); print "]";
  		$i = 0;
                if (is_array($questionNumbers) && count($questionNumbers) ) 
		{
		   foreach( $questionNumbers as $key=>$value ) 
		   {
//print "<br/> Key ["; var_dump($key); print "]";
		        $qIds = array();
//echo "</br>*********Getting Random Questions, Source arrary = **********</br>";
//print_r( $domainQuestions[ $key ] );

			//echo "Number of Questions: " . $value . "<br>";
			if ( !is_array( $domainQuestions ) || count( $domainQuestions ) <=0 )
				return false;

			if ( !is_array( $domainQuestions[$key] ) || count( $domainQuestions[$key] ) <=0 )
			    continue;

			if ( count( $domainQuestions[ $key ] ) < $value )
				$value = count( $domainQuestions[$key] );
		//	print_r( $domainQuestions[$key] );
		        $qs = array_rand( $domainQuestions[ $key ], $value );
//print "<br/>qs[";var_dump($qs);print "]";
//echo "</br>*********Random QuestionIds from Domain = " . $key . "**********</br>";
//print_r( $qIds );
                        if (is_array($qs) && count($qs)) 
			{
			   foreach ( $qs as $k=>$q ) 
			   {
			//	echo "<br>****************Key = ". $key . "Question Number = " . $q . " ***************<br> ";
				$questionsAnswers[ $i ] = $domainQuestions[ $key ][ $q ];
			//	var_dump( $domainQuestions[ $key ] );
				$i++;
			   }
                        }
                   }
		}
		if( count( $questionAnswers ) > $number )
		{
			shuffle( $questionAnswers );
			$questionAnswers = array_slice( $questionAnswers, 0, $number );
		}
		return $questionsAnswers;
	}

	public  function getPracticeQuestionsAnswers( $sectionId, $number )
	{

		if (!isset($sectionId) || $sectionId == NULL || $sectionId == "" || $sectionId === false)
                   return false;
		//echo "NUMBER=" . $number;
		$questionAnswers = array();
		$sql = "select SectionName from CourseTestTable where SectionID=".$sectionId ;
		$result = $this->run_query( $sql );
		if (isset($result) && $result !== false) 
		{
			if ( $row = mysql_fetch_array( $result ) )
			{
				if ( $row['SectionName'] == "Full Test Review" )
				{
					$questionAnswers = $this->getDiagnosticQuestionsAnswers( $sectionId, $number );
					return $questionAnswers;
				}
			}
		}
		// Practice Questions for individual domains, do not consider percentage
		$subsql = "select DomainIDs from CourseTestTable where SectionID=" . $sectionId ;
		$sql = "select QuestionID from DomainQuestionsTableUnicode where DomainID in ( (" . $subsql . ") )"; 
		//echo $sql;
		$questions = array();
		$result = $this->run_query( $sql );
	        if (isset($result) && $result !== false) 
		{
			$i = 0;
			while ( $row = mysql_fetch_array( $result ) )
			{
				$questions[ $i ] = $row[ "QuestionID" ];
				$i++;				
			}
		}
		//echo " Number = ". $number;
		//echo " Questions got back = ";
		//print_r( $questions );
		if ( is_array( $questions ) && count( $questions ) > 0 )
		{
			if ( count ( $questions ) < $number )
				$number = count ( $questions );
			$qNums = array_rand( $questions, $number );
			if ( is_array( $qNums ) )
			{
				foreach ( $qNums as $key=>$qNum )
				{
			        	 $sql = "select A.DomainID, A.QuestionID, A.QuestionString, A.QuestionType, 
						A.Choice1, A.Choice2, A.Choice3, A.Choice4, A.File, B.AnswerString, 
						B.Reasoning from DomainQuestionsTableUnicode A, QuestionAnswerTableUnicode B 
						where A.QuestionID=" . $questions[ $qNum ] . 
				  		" and A.QuestionID=B.QuestionID ";
					//	echo "<br>" .$sql . "<br>";
					$result = $this->run_query( $sql );
		        		if (isset($result) && $result !== false) 
					{
						while ( $row = mysql_fetch_array( $result ) )
						{
							$qa = array();
							$qa[ 'DomainID' ] = $row[ "DomainID" ];
							$qa['QuestionString'] = $row['QuestionString'];
							$qa[ "QuestionType" ] = $row[ "QuestionType" ];
							/*
							$choices = $this->randomizeChoices( $row );
							$qa[ "Choice1" ] = $choices[ "Choice1" ];
							$qa[ "Choice2" ] = $choices[ "Choice2" ];
							$qa[ "Choice3" ] = $choices[ "Choice3" ];
							$qa[ "Choice4" ] = $choices[ "Choice4" ];
							*/
							$qa[ "Choice1" ] = $row[ "Choice1" ];
							$qa[ "Choice2" ] = $row[ "Choice2" ];
							$qa[ "Choice3" ] = $row[ "Choice3" ];
							$qa[ "Choice4" ] = $row[ "Choice4" ];
							$qa[ "AnswerString" ] = $row[ "AnswerString" ];
							$qa[ "Reasoning" ] = $row[ "Reasoning" ];
							$qa[ "File" ] = $row[ "File" ];
	
			//				echo " QuestionID = " . $row[ "QuestionID" ] . "<br>";
							$questionAnswers[ $row[ "QuestionID" ] ] = $qa;
						}
					}
				}
				
			}
		}
		
						//echo "Question Answers array : <br> ";
						//print_r( $questionAnswers) . "<br>";
		if( count( $questionAnswers ) > $number )
		{
			shuffle( $questionAnswers );
			$questionAnswers = array_slice( $questionAnswers, 0, $number );
		}
		return $questionAnswers;
		
	}
	

	public  function getDiagnosticQuestionsAnswers( $sectionId, $number )
	{
		if (!isset($sectionId) || $sectionId == NULL || $sectionId == "" || $sectionId === false)
                   return false;
		//echo "NUMBER=" . $number;
		$sql = "select TestID from CourseTestTable where SectionID=".$sectionId ;
		$result = $this->run_query( $sql );
	        if (isset($result) && $result !== false) 
		{
			if ( $row = mysql_fetch_array( $result ) )
				$testId = $row['TestID'];
		}
		if ( !isset( $testId ) )
			return false;

		$questionNumbers = array();
		$questionsAnswers = array();
		$sql = "select Percentage, DomainIDs from CourseTestTable where TestID=" . $testId ;

		$result = $this->run_query( $sql );

                if (isset($result) && $result !== false) {

		   while( $row = mysql_fetch_array( $result ) ) {

		     if (isset($row[ DomainIDs ]) && $row[ DomainIDs ] !== false &&
                         $row[ DomainIDs ] != "") {

 		        $questionNumbers[ $row[ DomainIDs ] ] = round( $row[ Percentage ] / 100 * $number );
//echo "</br> Number of questions from domain ". $row[ DomainIDs ] . " is = " . $questionNumbers[ $row[ DomainIDs ] ];

		        $questions = array();
		        $sql = "select DomainID, QuestionID from DomainQuestionsTableUnicode 
		  	    where DomainID in (". $row[ DomainIDs ] . ")";

//echo "</br>Sql for QuestionIds for Domain " . $row[ DomainIDs ] . " = " . $sql . "</br>";
		        $res = $this->run_query( $sql );
		        $i = 0;
                        if (isset($res) && $res !== false) {
 
			   while( $r = mysql_fetch_array( $res ) ) {

				$questions[ $i ] = $r [ QuestionID ];
				$i++;			
			   } // while
			   $domainQuestions[ $row[ DomainIDs ] ] = $questions;
                        }
                     }
		  } // while
                }

//echo "</br>*********DomainQuestions**********</br>";
//print_r( $domainQuestions );
//print "<br/>questionNumbers ["; var_dump($questionNumbers); print "]";
                if (is_array($questionNumbers) && count($questionNumbers) ) {

		   foreach( $questionNumbers as $key=>$value ) {

		     if (isset($key)/* && is_array($domainQuestions[$key]) && count ($domainQuestions[$key])*/) {
//print "<br/> Key ["; var_dump($key); print "]";
		        $qIds = array();
//echo "</br>*********Getting Random Questions, Source arrary = **********</br>";
//print_r( $domainQuestions[ $key ] );

//			echo "Number of Questions: " . $value . "<br>";
			if ( !is_array( $domainQuestions ) || count( $domainQuestions ) <=0 )
				return false;

			if ( !is_array( $domainQuestions[$key] ) || count( $domainQuestions[$key] ) <= 0 )
				continue;

			if ( count( $domainQuestions[ $key ] ) < $value )
				$value = count( $domainQuestions[$key] );
//			print_r( $domainQuestions[$key] );
		        $qIds = array_rand( $domainQuestions[ $key ], $value );
//print "<br/>qIds[";var_dump($qIds);print "]";
//echo "</br>*********Random QuestionIds from Domain = " . $key . "**********</br>";
//print_r( $qIds );
                        if (is_array($qIds) && count($qIds)) {
  
			   foreach ( $qIds as $qId ) {

			      if (isset($qId) && $qId !== false && $qId != "") {

			         $sql = "select A.DomainID, A.QuestionID, A.QuestionString, A.QuestionType, A.Choice1, 
					A.Choice2, A.Choice3, A.Choice4, A.File, B.AnswerString, B.Reasoning 
					from DomainQuestionsTableUnicode A, 
				  QuestionAnswerTableUnicode B where A.QuestionID=" . $domainQuestions[ $key ][ $qId ] . 
				  " and A.QuestionID=B.QuestionID ";

//echo "</br></br>sql = " . $sql . "</br></br>";
			         $result = $this->run_query( $sql );

			         if (isset($result) && $result !== false) {

			            if ( $row = mysql_fetch_array( $result ) ) {

 				       $qa = Array();
				       $qa[ 'DomainID' ] = $row[ "DomainID" ];
				       $qa[ "QuestionString" ] = $row[ "QuestionString" ];
				       $qa[ "QuestionType" ] = $row[ "QuestionType" ];
                                       /*
				       $choices = $this->randomizeChoices( $row );
				       $qa[ "Choice1" ] = $choices[ "Choice1" ];
				       $qa[ "Choice2" ] = $choices[ "Choice2" ];
				       $qa[ "Choice3" ] = $choices[ "Choice3" ];
				       $qa[ "Choice4" ] = $choices[ "Choice4" ];
                                       */
				       $qa[ "Choice1" ] = $row[ "Choice1" ];
				       $qa[ "Choice2" ] = $row[ "Choice2" ];
				       $qa[ "Choice3" ] = $row[ "Choice3" ];
				       $qa[ "Choice4" ] = $row[ "Choice4" ];
				       $qa[ "AnswerString" ] = $row[ "AnswerString" ];
				       $qa[ "Reasoning" ] = $row[ "Reasoning" ];
				       $qa[ "File" ] = $row[ "File" ];
//echo "</br></br>Question for QID = " . $row[ "QuestionID" ] . "</br>";
//print_r( $qa );
				       if (isset($qa["QuestionString"]) && $qa["QuestionString"]!="" &&
					   $qa["QuestionString"]!=null  
					&& isset($qa[ "AnswerString" ]) && $qa[ "AnswerString" ]!="" &&
					   $qa["AnswerString"]!=null
					&& isset($qa[ "Choice1" ]) && $qa["Choice1"]!="" && 
					   $qa["AnswerString"]!=null)
				          $questionsAnswers[ $row[ "QuestionID" ] ] = $qa;
				    }
                                 }
                              }
			   } // foreach
                        }
                     }
		   } // foreach
                }
//echo "Final Question Answer Table: </br></br> ";
//var_dump(count( $questionsAnswers ));
//var_dump($number);
//print_r( $questionsAnswers );	
		if( count( $questionsAnswers ) > $number )
		{
			shuffle( $questionsAnswers );
			$questionsAnswers = array_slice( $questionsAnswers, 0, $number );
		}
		return $questionsAnswers;

	}

	public  function run_query( $sql = null)
	{
//	echo " Query = " . $sql . "<br>";
                $result = false;
                mysql_query( "set names 'utf8'" ) || die( " Cant execute setnames ") ; 
                if (isset($sql) && $sql != null && $sql != "" && $sql !== false) {

		   $result = mysql_query($sql);
                }
//	echo " Query = " . print_r( $result ) . "<br>";
		return $result;
	}

	public function authenticate( $user="", $passwd="")
	{
		if (isset($user) && $user != null && isset ($passwd) && $passwd != null
                    && $user != "" && $passwd != "") {

		   $sql = "select * from Users where UserID='" . $user . "'";
		   $result = $this->run_query( $sql );
                   if (isset($result) && $result !== false) {

		      if ($row = mysql_fetch_array( $result )) {

			  if ( $row[ Password ] == $passwd )
				return $row;
	              }
                   } 		
		}
		return false;
	}
	public function randomizeChoices( $row )
	{
		$choices[0] = $row[ "Choice1" ];
		$choices[1] = $row[ "Choice2" ];
		$choices[2] = $row[ "Choice3" ];
		$choices[3] = $row[ "Choice4" ];
		$temp = array_rand( $choices, 4 );
		$rc[ "Choice1" ] = $choices[ $temp[0] ];
		$rc[ "Choice2" ] = $choices[ $temp[1] ];
		$rc[ "Choice3" ] = $choices[ $temp[2] ];
		$rc[ "Choice4" ] = $choices[ $temp[3] ];
		return $rc;
	}


	public  function __destruct()
	{
/*	        if (isset ($this->con)) {

		   mysql_close($this->con);
                }*/
	}
}
?>
