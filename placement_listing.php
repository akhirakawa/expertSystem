<?php
include ('mysql-connection.inc');

print("Enter year of placements\n");
$year = trim(fgets(STDIN));

//Form select query
$listing_query = "SELECT studentID, name, reccsplace, recmathplace, recstatplace ";
$listing_query .= "FROM students where startyear = ".$year;
//$listing_query .= " ORDER BY recmathplace, studentID";
$listing_query .= " ORDER BY name";
$listing_result = mysql_query($listing_query);

$headerformat = "%s\t%10s\t%13s\t%s\t%s\n";

$format = "%s\t%30s\t%10s\t%10s\t%10s\n";

//List Header
printf($headerformat,
       "StudentID","Name","CS","Math","Stat");

//get first student
$student_data = mysql_fetch_row($listing_result);

while($student_data)
  {
    //print student data by given format
    printf("%s\t%30s\t%5s\t%5s\t%5s\n",$student_data[0],
           $student_data[1],$student_data[2],$student_data[3],$student_data[4]);

    //get next student
    $student_data = mysql_fetch_row($listing_result);
  }
include('mysql-close.inc');
?>