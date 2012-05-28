<?php

include('mysql-connection.inc');
include('placementRules.php');
include('inferenceEngine.php');
include('inferenceInteractions.php');
global $countArray,$mathUpdate, $csUpdate, $statUpdate,$updatePart2, $startyear;
$countArray = array();

//turn off error reporting for access to associative array
//  when key is not yet defined
error_reporting(E_ALL ^ E_NOTICE);

//MAIN PROGRAM
 inferenceInteractions();

   
setupForConsultations();
$start_time = microtime(true);

runConsultationsForGroup();


include('mysql-close.inc');

//Supporting functions
//runs a group of students through the inference engine.
//takes the global mysqlResult and then runs through
//each student. 
function runConsultationsForGroup()
{
  global $arrayOfRules, $countArray,$mathUpdate, $csUpdate, $statUpdate,$updatePart2;
  global $printRuleDetail, $printAllRuleDetail, $debug, $start_time;
  global $mysqlResult, $mysqlQuery, $numStudents;
  global $studentProperties;

  $studentProperties = mysql_fetch_assoc($mysqlResult);

////////////////////////////////////////////////////////////////////////
  while($studentProperties)
    {
      
      //this is proccesses the individual student. 
      runConsultationForStudent($studentProperties);  
      
       //add this students placements to the update query
      addUpdate($studentProperties);
      
      //get information for the next student
      $studentProperties = mysql_fetch_assoc($mysqlResult);    
    }
  
  //////////////////////////////////////////////////////////
  
  //add 'WHERE studentID IN (...)' to the end of the update queries.
  $mathUpdate .= " END". $updatePart2 . ")";
  $csUpdate .= " END". $updatePart2 . ")";
  $statUpdate .= " END". $updatePart2 . ")";
  
  //execute the database updates  
  mysql_query($mathUpdate);
  mysql_query($csUpdate);
  mysql_query($statUpdate);
  
  //finish up the time taken to run the inference engine
  $end_time = microtime(true);
  $totalTime = number_format($end_time - $start_time,4);
  
  //print the rule statistics, see function below
  printRuleStatistics();
    
  print("Placing took: ".$totalTime." seconds\n");
}


//runs the inference engine on a specific student. 
function runConsultationForStudent()
{
  global $arrayOfRules, $countArray,$mathUpdate, $csUpdate, $statUpdate,$updatePart2;
  global $printRuleDetail, $printAllRuleDetail, $debug;
  global $mysqlResult, $mysqlQuery, $numStudents;
  global $studentProperties, $assocArrayOfRules;

  //reset the assoccArrayOfRules, so that none of the rules
  //have been run
  foreach($arrayOfRules as $rule)
    {
      $assocArrayOfRules["$"."$rule[0]"] = false;
    }
    
  //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  //THIS IS THE CALL THAT RETURNS THE PLACEMENTS
  $results = getPlacement($arrayOfRules, $studentProperties);
  //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  print($studentProperties['name'] . " Math: " . $results['MATH-PLACEMENT'].
        "Stat: " . $results['STAT-PLACEMENT'] . " CS: ". $results['CS-PLACEMENT']."\n");
}

//intialize variables that are use latter
//also makes the database query. 

function setupForConsultations()
{
  global $arrayOfRules, $countArray,$mathUpdate, $csUpdate, $statUpdate,$updatePart2;
  global $printRuleDetail, $printAllRuleDetail, $debug, $selector;
  global $mysqlResult, $mysqlQuery, $numStudents;
  $printRuleDetail = true;
  $printAllRuleDetail = true;
  $debug = false;

  //set up the countArray which keeps track of how many times rules have been run
  foreach($arrayOfRules as $rule)
    {
      $countArray["$rule[0]"] = array("pass" => 0 , "fail" => 0);
    }


 
  $mysqlQuery = "SELECT * FROM students where $selector";
  
  $mysqlResult = mysql_query($mysqlQuery);
  $numStudents = mysql_num_rows($mysqlResult);
  
  //set up the initial part of the update queries.
  $mathUpdate ="UPDATE students SET recmathplace = CASE studentID";
  $csUpdate = "UPDATE students SET reccsplace = CASE studentID";
  $statUpdate = "UPDATE students SET recstatplace = CASE studentID";
  $updatePart2 = " WHERE studentID IN ( 0";
  
}

//prints out how many times each rule has been run, and how many times it has
//passed and how many times it has failed.
function printRuleStatistics()
{
  global $printRuleDetail, $printAllRuleDetail,$countArray;
  global $numStudents, $clausesRun, $totalTime;
  if($printRuleDetail)
    {
      foreach(array_keys($countArray) as $key)
        {
          $rule = $countArray[$key];
          $total = $rule["pass"] + $rule["fail"];
          $rulesRun += $total;
          if($printAllRuleDetail || $rule["pass"] || $rule["fail"])
            {
              print($key . "\tTOTAL: ".$total .
                    "\tpass: " . $rule["pass"]. " \tfail: ".
                    $rule["fail"]. "\n");
            }
        }
      print("proccessed a total of $numStudents students.\n".
            "ran a total of $rulesRun rules.\n".
            "an average of ". $rulesRun / $numStudents ." rules per student\n");
    }
 }


//this function adds the placements in studentPlacement to the 
//update queries. 
function addUpdate($studentProperties)
{
  global $csUpdate, $mathUpdate, $statUpdate, $updatePart2;
  $studentID = $studentProperties['studentID'];
  if($studentID)
    {
      $csUpdate .= " WHEN $studentID THEN '".
        $studentProperties['CS-PLACEMENT']."'";
      $mathUpdate .= " WHEN ". $studentID ." THEN '".
        $studentProperties['MATH-PLACEMENT']."'";
      $statUpdate .= " WHEN $studentID THEN '".
        $studentProperties['STAT-PLACEMENT']."'";
      $updatePart2 .= ",".$studentID ;
    }
}


?>
