<?php
  //these contain the helper functions for the inference engine
include('ruleFunctions.php');
include('conditionFunctions.php');
//include('modifiedCondition.php');





//Grades map to numeric values
//so we can compare grades
$nonNumericArray = array('F' => 1, 'D-' => 2,'D' => 3, 'D+' => 4, 'C-' => 5,
                         'C' => 6, 'C+' => 7,'B-' => 8,'B' => 9, 'B+' => 10,
                         'A-' => 11,'A' => 12,'A+' => 13,'low' => 1,
                         'poor' => 2, 'fair' => 3, 'good' => 4, 'high' => 5,
                         'superior' => 6, 'exceptional' => 7);

//listing of multi-value fields
$tentativePlace = array("TMATH-PLACE","TSTAT-PLACE","TCS-PLACE");


// 
//  Procedure
//    getPlacement
// 
//  Purpose
//    store and return placements found by backwards chaining
// 
//  Parameters
//    $rules, an array of rules made by gatherRules()
//    $studentProperties, associative array of one students data
// 
//  Produces
//    $results, an associative array with values being placments
// 
//  Preconditions
//    $rules are in correct format
//    $assocArrayOfRules, every value is false
//  Postconditions
//   $results has placements for the student
//   $countArray has been updated to reflect rules being run
function getPlacement($rules, $studentProperties)
{
  global $globalRules, $sizeOfRules, $studentProperties, $assocArrayOfRules;
  $globalRules = $rules;
  $sizeOfRules = sizeof($globalRules);
  
    $goalArray = array("MATH-PLACEMENT","CS-PLACEMENT","STAT-PLACEMENT");
  //$goalArray = array("CS-PLACEMENT","STAT-PLACEMENT");
  //$goalArray = array("MATH-PLACEMENT");
  //$goalArray = array("satreal");
  //$goalArray = array("stdscores");
  //$goalArray = array("stdscores","MATH-PLACEMENT","CS-PLACEMENT","STAT-PLACEMENT");

  //process goals in results array
  foreach($goalArray as $goal)
    {
      //iterate through all rules
      foreach($globalRules as $rule)
        {
          //if the conclusion field from the rule, and the name of the
          //goal match then this rule could achieve the goal.
          if(substr_compare($goal,$rule[1],0,strlen($goal)) == 0)
            {
              $key = "$".$rule[1];
              //Check to see if rule has already been run
              //if the rule has been run, then continue
              if($assocArrayOfRules[$key])
                {
                  continue;
                }
              else
                {
                  //Rule has not been run, so check to see if the rule
                  //passes, if it does pass then set the coresponding value in
                  //$returnReults. Then break, since the goal had been achieved. 
                  //checkRule is in ruleFunctions.php
                  if(checkRule($rule))
                    {
                      $returnResults[$goal] = $studentProperties[$goal];
                      break;
                    }
                }
            }
        }
    }
  //return the results
  return $returnResults;
}

//////////////////////////////////////////////////////////
//various specialized printing functions, that are used 
//for debugging purposes. These have no impact on the
//placements


//prints the statement in global debug is true, or the
//over ride paramater has been set true
function debugPrint($string , $overRide)
{
  global $debug, $indentBool, $indent;
  if($debug || $overRide)
    {
      if($indentBool)
        {
          printIndent($indent);
        }
      print($string. "\n");
    }
}

//prints true or false instead of 1 or "  ".
function tfPrint($name, $result,$overRide)
{
  global $debug, $indentBool, $indent;
  if($debug || $overRide)
    {
      if($indentBool)
        {
          printIndent($indent);
        }
      print("$name returning: ");
      if($result )
        {
          print("true\n");
        }
      else
        {
          print("false\n");
        }
    }
}

function simpletfPrint( $result,$overRide)
{
  global $debug, $indentBool, $indent;
  if($debug || $overRide)
    {
      if($result )
        {
          print("true\n");
        }
      else
        {
          print("false\n");
        }
    }
}
function printIndent($num)
{
  while($num > 0)
    {
      print("\t");
      $num--;
    }
}

?>
