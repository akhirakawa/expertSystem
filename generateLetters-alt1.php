<?php

//Max number of transfer courses allowed per letter
$transfer_course_max = 4;

$count = 0;

print("Which year would you like to generate letters for?\n");
$year = trim(fgets(STDIN));

include('mysql-connection.inc');
include('generateLettersDetail.php');

$sqlQuery = "SELECT *, students.studentID, students.name ";
$sqlQuery .= " FROM students LEFT JOIN transfers ";
$sqlQuery .= "ON students.studentID = transfers.studentID";
$sqlQuery .= " WHERE students.startyear = ".$year." ";

$selection = null;
while(! is_numeric($selection) || empty($selection))
  { 
    print("Please enter sorting criteria: \n".
          "1 - name\n".
          "2 - advisor, student name\n".
          "3 - pobox\n".
          "4 - studentID\n");
    $selection = trim(fgets(STDIN));
    if($selection == 1)
      {
        $orderBy = ' ORDER BY students.name';
      }
    elseif($selection == 2)
      {
        $orderBy = " ORDER BY students.advisor1, students.name";
      }
    elseif($selection == 3)
      {
                $orderBy = " ORDER BY students.pobox";
      }
    elseif($selection == 4)
      {
        $orderBy = " ORDER BY students.studentID";
      }
    else
      {
        print("please try again");
        $selection = null;
      }
  }


$sqlQuery .= $orderBy;
$sqlResult = mysql_query($sqlQuery);

$namefile = "letters".$year."-alt1";
$filename = "$namefile.tex";
$file = fopen($filename, "w+");

//initialize end
$end = false;

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

while($studentData)
  {
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
      {
        //$end will be true if current student has transfer data
        if($end)
          {
            //end packed_enum list and finish letter
            fwrite ($file,  '\end {packed_enum}}');
            //Page Two: Transcript
            fwrite($file, "
\\finishedtranscript
\studentpictures ");
            
            //PageTwo: Pictures
            fwrite($file, "\bottompagetwo

\\vfill\eject");
            $previous_studentID = $studentData['studentID'];
            // $studentData = mysql_fetch_assoc($sqlResult);
          }

        //new student
        $count++;
        $end = false;
        $name = $studentData['name'];
        $name = str_replace('"','',$name);
        
        $advisor = trim($studentData['advisor1']);
	$advisor = str_replace('"','',$advisor);

        $pobox = $studentData['pobox'];
        $newbox = '';
        for($i=0;$i<strlen($pobox);$i++)
          {
            if(is_numeric($pobox[$i]))
              {
                $newbox .= $pobox[$i];
              }
          }
        $pobox = $newbox;
	
        $csplace = $studentData['reccsplace'];
        $mathplace = $studentData['recmathplace'];
        $statplace = $studentData['recstatplace'];
        print ("name: ".$name." placements: $csplace, $mathplace, $statplace \n");
        
        $csText = $csMacroArray[$csplace];                        
        $mathText = $mathMacroArray[$mathplace];
        $statText = $statMacroArray[$statplace];
        multipleUnknown($csText, $mathText, $statText);

        //make date for letter
        $date = date("F j, Y");
        
        
        ///////////////////////////////////////////////////////
        //write the file, 
        
        
        //letterhead and opening
        fwrite($file, "\includegraphics [width = 6.25truein]{pictures/letterhead-cs-math}
 {\\normalfont


 Date: $date\\\                                
 To: $name ");
if(!empty($advisor))
{
fwrite($file,"(Advisor:  $advisor)\\\ ");
}
else{
fwrite($file,"\\\ ");
}                        
 fwrite($file,"Box:  $pobox\\\
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
            $end = true;
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
          }
        else
          {
            //student has no transfer data so finish letter
            $end = false;

            //Page Two: Transcript
            fwrite($file, "
\\finishedtranscript
\\vfill
\studentpictures ");
            
            fwrite($file, "\bottompagetwo

\\vfill\eject");

          }
      }
    //set previous_studentID to the current studentID data
    $previous_studentID = $studentData['studentID'];
    //get next student
    $studentData = mysql_fetch_assoc($sqlResult);
  }
//check to see if loop ended on student with transfer data
//if so close {packed_enum} and finish printing letter
if($end)
  {
    //Page Two: Transcript
    fwrite($file, "
\end {packed_enum}
\\finishedtranscript
\vfill
\studentpictures \bottompagetwo");
  }

//end the document
fwrite($file, "
\\end{document}");

//close the file.
fclose($file);
print("Total Number of students: ".$count."\n");
//print($sqlQuery."\n");
include('mysql-close.inc');

?>
