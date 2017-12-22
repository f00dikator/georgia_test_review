<?php

require_once( "include/GTR_Header.php" );

function showTutorials()
{
	$ROOT = $_SERVER["DOCUMENT_ROOT"];
	$tutorials = scandir( $ROOT . TUTORIAL_DIRECTORY );	
	if ( is_array( $tutorials ) && count($tutorials))
	{
		foreach ( $tutorials as $tutorial )
		{
			if ( $tutorial == "." || $tutorial == ".." )
				continue;
			echo "<li>";
			echo "<a href=\"review.php?tutorial=" . $tutorial . "\">" . $tutorial . "</a>";
			echo "</li>";
		}
	}
}

function showTutorialSections( $tutorial )
{
	//echo " Scanning directory = " . TUTORIAL_DIRECTORY . "/" . $tutorial;
	$ROOT = $_SERVER["DOCUMENT_ROOT"];
	$sections = scandir( $ROOT . TUTORIAL_DIRECTORY . "/" . $tutorial );	
	//echo "Tutorial Section = " . $tutorial;
	if ( is_array( $sections ) && count($sections))
	{
		foreach ( $sections as $section )
		{
			if ( $section == "." || $section == ".." )
				continue;
			echo "<li>";
			echo "<a href=\"review.php?tutorialsection=" . $tutorial. "/" . $section . "\">" . $section. "</a>";
			echo "</li>";
		}
	}
	
}

function showSection( $section, $qNum )
{
	 $ROOT = $_SERVER["DOCUMENT_ROOT"];
	$files = scandir ( $ROOT . TUTORIAL_DIRECTORY . "/" . $section ) ;
	if ( is_array( $files ) )
	{
		$files = array_slice( $files, 2 );
		if ( count( $files ) < $qNum )
		{
			echo "Tutorial Completed";
			return;
		}
		sort( $files, SORT_NUMERIC );
		echo "<table><tr><td>";
		echo "<img src=\"" . TUTORIAL_DIRECTORY ."/". $section ."/". $files[ $qNum-1 ] . "\" height=500 width=500/>";
		echo "</tr></td>";
    		
                if ($qNum > 1) {

    		   echo "<tr><td align=\"center\"><a href=\"review.php?tutorialsection=" . $section . 
			"&qnum=" . ($qNum-1) . "\">Previous  </a>";
                
                   echo "<a href=\"review.php?tutorialsection=" . $section . 
			"&qnum=" . ($qNum+1) . "\">Next</a></td></tr>";
                } else {
 
                   echo "<tr><td align=\"center\"><a href=\"review.php?tutorialsection=" . $section . 
			"&qnum=" . ($qNum+1) . "\">Next </a></td></tr>";              
                  
                }
		$qNum++;
		echo "</table>";
                echo "</form>";
	}	
}

?>
