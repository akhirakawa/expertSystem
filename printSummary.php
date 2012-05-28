<?php
include('mysql-connection.inc');
include('toEngArray.php');

global $startyear, $argv, $argc;

if ($argc <= 1) 
  {
    print("Please enter start year: ");
    $startyear =  trim(fgets(STDIN));
  }
else
  {
    $startyear = $argv[1];
  }


$csCourseArray = array('105','103', '151','152','153', '200');

$mathCourseArray =array('100','123','130', '131','132', '133','215');
$statCourseArray = array('115', '208','209','210', '300');
$apArray = array('apcalcab', 'apcalcbc', 'apcsab', 'apcsa', 'apstat');
$ibArray = array('ibmat', 'ibcs');
foreach($apArray as $apTest)
{
  print($toEngArray[$apTest]."\n");
  print("1\t|2\t|3\t|4\t|5\t|\n");
  print("------------------------------------------\n");
  for($score = 1; $score < 6; $score++)
    {
      $sqlQuery = "SELECT count($apTest) FROM students WHERE startyear = $startyear && $apTest = $score";
      $result = mysql_query($sqlQuery);
      print(mysql_result($result,0)."\t|");
    }
  print("\n\n\n");
} 


foreach($ibArray as $ibTest)
{
  print($toEngArray[$ibTest]."\n");
  print("1\t|2\t|3\t|4\t|5\t|6\t|7\t|8\t|9\t|\n");
  print("-----------------------------------------------------\n");
  for($score = 1; $score < 10; $score++)
    {
      $sqlQuery = "SELECT count($ibTest) FROM students WHERE startyear = $startyear && $ibTest = $score";
      $result = mysql_query($sqlQuery);
      print(mysql_result($result,0)."\t|");
    }
  print("\n\n\n");
} 

print("Computer Science Placement\n");
foreach($csCourseArray as $course)
{
  print($course."\t|");
}
print("\n----------------------------------------\n");
foreach($csCourseArray as $course)
{
  $sqlQuery = "SELECT count(reccsplace) FROM students WHERE startyear = $startyear && reccsplace = $course";
  $result = mysql_query($sqlQuery);
  print(mysql_result($result,0)."\t|");
}
print("\n\n\n");
print("Math Placement\n");

foreach($mathCourseArray as $course)
{
  print($course."\t|");
}
print("\n----------------------------------------\n");
foreach($mathCourseArray as $course)
{
  $sqlQuery = "SELECT count(recmathplace) FROM students WHERE startyear = $startyear && recmathplace = $course";
  $result = mysql_query($sqlQuery);
  print(mysql_result($result,0)."\t|");
}


print("\n\n\n");
print("Statistics Placement\n");

foreach($statCourseArray as $course)
{
  print($course."\t|");
}
print("\n----------------------------------------\n");
foreach($statCourseArray as $course)
{
  $sqlQuery = "SELECT count(recstatplace) FROM students WHERE startyear = $startyear && recstatplace = $course";
  $result = mysql_query($sqlQuery);
  print(mysql_result($result,0)."\t|");
}

print("\n\n");