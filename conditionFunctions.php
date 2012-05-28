<?php


// 
//  Procedure
//    allConditionSequence
// 
//  Purpose
//    goes through a clause sequence evaluating individual clauses
//    must pass all clauses to return true
// 
//  Parameters
//    $conditionSequence, an array
// 
//  Produces
//    a boolean
// 
//  Preconditions
//    [none]
// 
//  Postconditions
//    The returned value will be true if and only if all of 
//    conditions in the sequence are true. 
function allConditionSequence($conditionSequence)
{ 

  global $studentProperties, $tentativePlace,$nonNumericArray;

  debugPrint("IN ALL CONDITION SEQUENCE", false);


  //starting after the qualifier, check each condition, first see if
  //the 'condition' is actually another conditionSequence, then 
  //see if the field is known by calling either checkTentativeCondition
  //or checkCondition. 
  for($index = 1; $index < sizeof($conditionSequence) ; $index++)
    {
      if(is_array($conditionSequence[$index]))
        {
          $result = checkConditionSequence($conditionSequence[$index]);
        }
      else
        {
          $explodedCond = explode(" " ,$conditionSequence[$index]);
          if(in_array($explodedCond[0],$tentativePlace))
            {
              $result = checkTentativeCondition($explodedCond);
            }
          else
            {
              $result = checkCondition($explodedCond);
            }
        }
      //If a condition returns, false, then stop evaluation.
      if(! $result)
        {
          return false;
        } 
    }
  return true;

}

// 
//  Procedure
//    someConditionSequence
// 
//  Purpose
//    goes through a clause sequence evaluating individual clauses
//    must pass only one clause to return true
// 
//  Parameters
//    $conditionSequence, an array
// 
//  Produces
//    a boolean, true if any of the conditions are true
//    otherwise the boolean is false. 
// 
//  Preconditions
//    $conditionSequence follows the grammar. 
// 
//  Postconditions
//    The value returned will be true if any of the conditions in the
//    condition sequence, other wise it will be false. 
function someConditionSequence($conditionSequence)
{
  global $studentProperties, $tentativePlace,$nonNumericArray;

  debugPrint("IN SOME CONDITION SEQUENCE", false);

  //starting after the qualifier, check each condition, first
  //see if the field is known by calling either checkTentativeConditions
  //or checkCondition. 
  for($index = 1; $index < sizeof($conditionSequence) ; $index++)
    {
     
      if(is_array($conditionSequence[$index]))
        {
          $result = checkConditionSequence($conditionSequence[$index]);
        }
      else
        {
          debugPrint("Some condition: $conditionSequence[$index]" , false);
          $explodedCond = explode(" " ,$conditionSequence[$index]);
          if(in_array($explodedCond[0],$tentativePlace))
            {
              $result = checkTentativeCondition($explodedCond);
            }
          else
            {
              $result = checkCondition($explodedCond);
            }
        }
      //If a condition returns true then stop evaluation.
      if($result)
        {
          return true;
        } 
    }
  //If none of the conditions are true return false
  return false;
  
}

// 
//  Procedure
//    checkTentativeCondition
// 
//  Purpose
//    determines if a tentative placement field is known and
//    and finds rule if not
// 
//  Parameters
//    $currentCondition, an array 
// 
//  Produces
//    $result, which is a boolean true if the condition has passed
//    false if teh condition has failed.
// 
//  Preconditions
//    $currentCondition has been exploded into an array
// 
//  Postconditions
//    The value returned will be true if the tentative field has been set. 
//    otherwise it will return false. 
function checkTentativeCondition($currentCondition)
{
  global $studentProperties, $sizeOfRules,$indent;

  //the condition is NOT known.
  if($studentProperties[$currentCondition[0].$currentCondition[2]] 
     === null)
    {
      $checkResult = false;
      $counter = 0;

      //as long as the field is not known, and we still have
      //rules to look through, find a rule, and see if it works
      //by calling checkRule
      while( $studentProperties[$currentCondition[0].$currentCondition[2]]
             == null 
             && ($counter < $sizeOfRules) && ! $checkResult)
        {
          $modifiedCondition = array($currentCondition[0] , "=",
                                     $currentCondition[2]);
          $searchResult=findRule($modifiedCondition, $counter);
          
          //if a rule is found, check the rule,
          //check rule is in ruleFunctions.php
          if($searchResult)
            {
              $checkResult = checkRule($searchResult);
            }
           $counter++;

        }
    }

  //evaluate the condition, and then return the result.
  $result = evaluateBasicClause($currentCondition);
  tfPrint("checkTentativeCondition", $result,false);
  return $result;
}

// 
//  Procedure
//    checkCondition
// 
//  Purpose
//    determines if a placement field is known and
//    and finds rule if not
// 
//  Parameters
//    $currentCondition, an array 
// 
//  Produces
//    $result, a boolean representing if the condition has passed or failed
//
//  Preconditions
//    $currentCondition has been exploded into an array
// 
//  Postconditions
//     the value returned will be true if the current condition has been meet. 
function checkCondition($currentCondition)
{
  global $studentProperties, $sizeOfRules,$indent;

  debugPrint("Current  condition: $currentCondition[0] $currentCondition[1] ".
             "$currentCondition[2]" , false);
  
  if($studentProperties[$currentCondition[0]] === null)
    {
      $checkResult = false;
      $counter = 0;
      //while the field has no value, and not all of the rules have been run
      //search for new rule.
      while( $studentProperties[$currentCondition[0]] == null 
             && ($counter < $sizeOfRules) && ! $checkResult)
        {
         
          //Field for the condition is not known thus, find a rule to
          //verify current condition
          if($currentCondition[1] == "!=")
            {
              $condition = array($currentCondition[0], "=", $currentCondition[2]);
            }
          else
            {
              $condition = $currentCondition;
            }
          $searchResult = findRule($condition,$counter);
          if($searchResult)
            {
              $checkResult = checkRule($searchResult);
            }
           $counter++;

        }     
    }
  $result = evaluateBasicClause($currentCondition);
  tfPrint("checkCondition", $result,false);
  return $result;
}

// 
//  Procedure
//    evaluateBasicClause
// 
//  Purpose
//    evaluate the input clause to return a boolean
// 
//  Parameters
//    $clause, an array
// 
//  Produces
//    a boolean
// 
//  Preconditions
//   $clause is a valid clause, that follows the grammar
// 
//  Postconditions
//    Will return true if the clause is true, otherwise will return false.
function evaluateBasicClause($clause)
{
  $result = evaluateBasicClauseKernel($clause);
  tfPrint("evaluateBasicClause" , $result,false);
  return $result;
}
///////////////////////////////////////////////////
//The following fucntions are all involved in evaluating basic clauses
//each function is designed to handle a specific type of clause. Which 
//function is called is determined by evaluateBasicClauseKernel'



//choose which of the various evaluate functions to use
//based  on what type of field, tentative vs. normal
//and on what the values being compared are. numeric, nonnumeric, or 'unknown'
function evaluateBasicClauseKernel($clause)
{
  global $studentProperties, $tentativePlace,$nonNumericArray;

  debugPrint("\nIN EVALUATE BASIC CLAUSE: $clause[0] $clause[1] $clause[2], "
             . $studentProperties[$clause[0]],false);
 
  //if the clause involves a tentative placement field
  if(in_array($clause[0],$tentativePlace))
    {
      return evaluateTentativeClause($clause);
    }

  elseif(sizeof($clause) == 3)
    {
      //if the value on the right of the condition is a fieldname
      // then look it up in studentProperties and substitute its value. 
      if(array_key_exists($clause[2] , $studentProperties))
        {
          $clause[2] = $studentProperties[$clause[2]];
        }

      //when both of the values being compared are numeric
      if(is_numeric($clause[2]) && is_numeric($studentProperties[$clause[0]]))
        {
          return evaluateNumeric($clause);
        }

      //if the values are not numeric and not unknown, then they are
      //handled as non numeric values.
      elseif($studentProperties[$clause[0]] != 'unknown' && $clause[2] != 'unknown')
        {
          return evaluateNonNumeric($clause);
        }

      //if either of the values are 'unknown' handle accordingly. 
      else
        {
          return evaluateUnknown($clause);
        }
      
    }

  //clause is ( <field>, "between", <value>, <value> )
  elseif(sizeof($clause) == 4)
    {
      return evaluateBetween($clause);
    }
  //The inference engine does not know how to handle the clause it has been given
  //so return false.
  else
    {
      return false;
    }
}
// 
//  Procedure
//    evaluateTentativeClause
// 
//  Purpose
//    evaluate the input clause to return a boolean
// 
//  Parameters
//    $clause, an array
// 
//  Produces
//    a boolean
// 
//  Preconditions
//   $clause is a valid tentative clause, that follows the grammar
// 
//  Postconditions
//    Will return true if the clause is true, otherwise will return false.
function evaluateTentativeClause($clause)
{
  global $studentProperties, $tentativePlace,$nonNumericArray;
    
      //if operator is = return true if student property matches 
      if($clause[1] == "=")
        {
          return $studentProperties[$clause[0].$clause[2]] == $clause[2];
        }
      //if operator is != return true if student property does not 
      //match desired value
      else
        {
          return $studentProperties[$clause[0].$clause[2]] != $clause[2];
        }
}


// 
//  Procedure
//    evaluateNumeric
// 
//  Purpose
//    evaluate the input clause to return a boolean
// 
//  Parameters
//    $clause, an array
// 
//  Produces
//    a boolean
// 
//  Preconditions
//   $clause is a valid  clause of length three, that has numeric arguments
//    that follows the grammar
// 
//  Postconditions
//    Will return true if the clause is true, otherwise will return false.
function evaluateNumeric($clause)
{
 global $studentProperties, $tentativePlace,$nonNumericArray;
  
 return numericEvaluation($clause[1],
                          $studentProperties[$clause[0]],
                          $clause[2]);
}


// 
//  Procedure
//    evaluateNonNumeric
// 
//  Purpose
//    evaluate the input clause to return a boolean
// 
//  Parameters
//    $clause, an array
// 
//  Produces
//    a boolean
// 
//  Preconditions
//   $clause is a valid  clause of length three, that has non-numeric arguments
//    that follows the grammar
// 
//  Postconditions
//    Will return true if the clause is true, otherwise will return false.
function evaluateNonNumeric($clause)
{ global $studentProperties, $tentativePlace,$nonNumericArray;
   if( $studentProperties[$clause[0]] !== null || $clause[0] == 'stdscores')
   {
     return numericEvaluation($clause[1],
                              $nonNumericArray[$studentProperties[$clause[0]]],
                              $nonNumericArray[$clause[2]]);
   }
   return false;
}


// 
//  Procedure
//    evaluateUnknown
// 
//  Purpose
//    evaluate the input clause to return a boolean
// 
//  Parameters
//    $clause, an array
// 
//  Produces
//    a boolean
// 
//  Preconditions
//   $clause is a valid  clause that has 'unknown' as an argument
// 
//  Postconditions
//    Will return true if the clause is true, otherwise will return false.
function evaluateUnknown($clause)
{
 global $studentProperties, $tentativePlace,$nonNumericArray;
  if($clause[1] == '=')
    {
      return $clause[2] === $studentProperties[$clause[0]];
    }

  if($clause[1] == '!=')
    {
      return $clause[2] !== $studentProperties[$clause[0]];
    }
  //Any other type of comparison is false since 'unknown'
  // is not > or < any value.
  return false;
}



// 
//  Procedure
//    evaluateBetween
// 
//  Purpose
//    evaluate the input clause to return a boolean
// 
//  Parameters
//    $clause, an array
// 
//  Produces
//    a boolean
// 
//  Preconditions
//   $clause is a valid 'between' clause
// 
//  Postconditions
//    Will return true if the clause is true, otherwise will return false.
function evaluateBetween($clause)
{
 global $studentProperties, $tentativePlace,$nonNumericArray;

 //if the values are numeric then just see if
 //   $clause[2] <= val <= $clause[3]
 if(is_numeric($clause[2]) && is_numeric($studentProperties[$clause[0]]))
   { 
     $val = $studentProperties[$clause[0]];
     return ($val >= $clause[2] && $val <= $clause[3]);
   }
 
 //otherwise check make sure that the studentProperties[$clause[0]]
 //is not 'unknown' and then make the two comparisons.
 if($studentProperties[$clause[0]] != 'unknown')
   {
     $firstComparison = numericEvaluation(">=",
                                          $nonNumericArray[$studentProperties[$clause[0]]],
                                          $nonNumericArray[$clause[2]]);
     $secondComparison = numericEvaluation("<=",
                                           $nonNumericArray[$studentProperties[$clause[0]]],
                                           $nonNumericArray[$clause[3]]);
     
     return ( $firstComparison && $secondComparison);
   }
 //otherwise return false. 
 else
   {
     return false;
   }
}



// 
//  Procedure
//    numericEvaluation
// 
//  Purpose
//    take two arguments and an operator and evaluate them
//    to a boolean
// 
//  Parameters
//    $op, a operator as a string type
//    $firstarg, anytype that is comparable by $op
//    $secarg, anytype that is comparable by $op
// 
//  Produces
//    a boolean
// 
//  Preconditions
//    $firstarg and $secarg are of the same type and comparable
// 
//  Postconditions
//   The returned value will be true if
//        $firstarg  $op  $secarg 
//   is true.
function numericEvaluation($op, $firstarg, $secarg)
{
  if($op == "<")
    {
      return ($firstarg < $secarg);
    }
  elseif($op == "<=")
    {
      return($firstarg <= $secarg);
    }
  elseif($op == '=')
    {
      return($firstarg == $secarg); 
    }
  elseif($op == "!=")
    {
      return ($firstarg != $secarg);
    }
  elseif($op == ">")
    {
      return ($firstarg > $secarg);
    }
  elseif($op == ">=")
    {
      return($firstarg >= $secarg);
    }
  else
    {
      return false;
    }

}
?>
