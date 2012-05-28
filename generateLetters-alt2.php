<?php

//Max number of transfer courses allowed per letter
$transfer_course_max = 4;

//Count number of students processed
$count = 0;

print("Which year would you like to generate letters for?\n");
$year = trim(fgets(STDIN));

include('mysql-connection.inc');
include('generateLettersDetail.php');

$sqlQuery = "SELECT *, students.studentID, students.name ";
$sqlQuery .= " FROM students LEFT JOIN transfers ";
$sqlQuery .= "ON students.studentID = transfers.studentID";
$sqlQuery .= " WHERE students.startyear = ".$year." ";

//determine sorting option
do { 
  print("Please enter sorting criteria: \n".
        "1 - name\n".
        "2 - advisor, student name\n".
        "3 - pobox\n".
        "4 - studentID\n");
    switch ($selection = trim(fgets(STDIN))) 
      {
      case 1:
        $orderBy = ' ORDER BY students.name';
        break;
      case 2:
        $orderBy = " ORDER BY students.advisor1, students.name";
        break;
      case 3:
        $orderBy = " ORDER BY students.pobox";
        break;
      case 4:
        $orderBy = " ORDER BY students.studentID";
        break;
      default:
        print("please try again");
        $selection = null;
      }
 } while(! is_numeric($selection) || empty($selection));

$sqlQuery .= $orderBy;
$sqlResult = mysql_query($sqlQuery);

$namefile = "letters".$year."-alt2";
$filename = "$namefile.tex";
$file = fopen($filename, "w+");

//Set-up document
fwrite($file,"\documentclass{letter}
 \usepackage{setspace}
\pagestyle{empty}

\usepackage{color}
\usepackage{ifthen}
\usepackage{psfig}
\usepackage{graphicx}
\usepackage[T1]{fontenc}
\usepackage[scaled]{helvet}
\\renewcommand{\\familydefault}{\\sfdefault}
\usepackage{macros/generalMacros}
\usepackage{macros/csMacros}
\usepackage{macros/statMacros}
\usepackage{macros/mathMacros}
\usepackage{macros/transcriptMacros}
\usepackage{macros/letterSetup}
\usepackage{macros/unkPlaceMacros}
\usepackage[top=.5in, bottom=.5in, left=.85in, right=.85in]{geometry}
\\formating
\\begin{document}");

//go through the students generating a letter for each of them
$studentData = mysql_fetch_assoc($sqlResult);
//initial studentID for first comparison
$previous_studentID = 0;
//string needed to finish processing of previous student record
$finish_prev_rec = '';

while($studentData)
  {
    //Loop invariants
    //   $studentData contains next student record
    //   $transfer_course_total gives number of transfer courses printed 
    //        for prev. student
    //   $count gives number of students previously processed
    //   $previous_studentID gives ID of previous record processed
    //   $finish_prev_rec gives string needed to finish processing prev. student

    //check if current studentID is equal to previous id
    if($previous_studentID == $studentData['studentID'])
      {
        //check if you have reached max number of transfer course lines on letter
        if($transfer_course_total < $transfer_course_max)
          {
            fwrite ($file, '\item []');
            $transSep = "";
            $coursename = str_replace("&", '\&',$studentData['coursename']);
            $coursename = str_replace('"','',$coursename);
            $instName = str_replace('"','',$studentData['instname']);

            if(!empty($coursename))
              {
                fwrite ($file, $coursename);
                $transSep = ", ";
              }
            if(!empty($instName))
              {
                fwrite ($file, $transSep . $instName);
              }
            $transfer_course_total ++;
          }
        else
          {
            print("ERROR: Only room for ".$transfer_course_max.
                  " transcript lines\n"); 
            print("Omitting transfer data for ");
            print("studentID: ".$studentData['studentID']."\n");
          }        
      }
    else 
      { //current record represents new student
        //finish up record of previous student
        fwrite ($file, $finish_prev_rec);

        //begin new student on file
        $count++;
        $previous_studentID = $studentData['studentID'];

	//report processing to terminal
        $name =  str_replace('"','', $studentData['name']);
        $csplace = $studentData['reccsplace'];
        $mathplace = $studentData['recmathplace'];
        $statplace = $studentData['recstatplace'];
        print ("name: ".$name." placements: $csplace, $mathplace, $statplace \n");
        
        //make date for letter
        $date = date("F j, Y");
        
        //letterhead and opening
        fwrite($file, "\includegraphics [width = 6.25truein]{pictures/letterhead-cs-math}
 {\\normalfont


");

        //print date, name, advisor (if known), and address at top of letter
        fwrite($file, " Date: $date\\\\                                \n"); 
        fwrite($file, " To: $name");
        $advisor = str_replace('"','', trim($studentData['advisor1']));
	if(!empty($advisor))
          {
            fwrite($file," (Advisor:  $advisor)");
          }
        fwrite($file, "\\\\"); //finish name line

        $pobox = $studentData['pobox'];
        $edited_pobox = '';
        for($i=0;$i<strlen($pobox);$i++)
          {
            if(is_numeric($pobox[$i]))
              {
                $edited_pobox .= $pobox[$i];
              }
          }
        fwrite($file," Box:  $edited_pobox\\\
 Re: \\textcolor{blue}{Tentative Placement in Computer Science, Statistics, and Mathematics}}

 ");

        //Body: the intro, disclaimer, and proftable
        fwrite($file, "{\\normalfont \intro
\disclaimer
\\newline
\bigskip
\proftable
\\newline}");
        
        //Courses
        //retrieve course text/macros
        $csText = $csMacroArray[$csplace];                        
        $mathText = $mathMacroArray[$mathplace];
        $statText = $statMacroArray[$statplace];
        multipleUnknown($csText, $mathText, $statText);

        fwrite($file, "{\\normalfont 
$csText

$statText

$mathText

\bottompageone

\\vfill\eject}");

        //Page Two: Transcript
        fwrite($file, "\openingpagetwo
\\normalfont
\begintranscript 
");
        fwrite($file, transcriptStringMaker($studentData));
        
        //if student has transfer data
        if(!empty($studentData['coursename']))
          {
            fwrite ($file, "


{\\vspace{-.15in}}
{\\textcolor{blue} {\\vspace{-.15in} {\hspace{.25in} Transfer Credits }}} {\\vspace{-.07in}");
            fwrite ($file, "\begin {packed_enum}\item []");
            $coursename = str_replace("&", '\&',$studentData['coursename']);
            $coursename = str_replace('"','',$coursename);
            $instName = str_replace('"','',$studentData['instname']);
            fwrite ($file, $coursename);
            if(!empty($instName))
              {
                fwrite ($file, ", " . $instName);
              }
            //initialize counter for number of transfer courses
            $transfer_course_total = 1;

            //clarify string to be printed after transfer courses handled
            $finish_prev_rec = "\end {packed_enum}}
\\finishedtranscript
\studentpictures \bottompagetwo

\\vfill\eject";

          }
        else
          {
            //student has no transfer data so finish letter
            //  Page Two: Transcript
            fwrite($file, "
\\finishedtranscript
\\vfill
\studentpictures \bottompagetwo

\\vfill\eject");

            //  nothing to print during future iterations
            $finish_prev_rec = '';

          }
      }
    //set previous_studentID to the current studentID data
    $previous_studentID = $studentData['studentID'];
    //get next student
    $studentData = mysql_fetch_assoc($sqlResult);
  }

//finish last student
fwrite ($file, $finish_prev_rec);

//end the document
fwrite($file, "
\\end{document}");

//close the file.
fclose($file);
print("Total Number of students: ".$count."\n");
//print($sqlQuery."\n");
include('mysql-close.inc');
?>
