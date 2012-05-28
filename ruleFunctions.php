<?php

// 
//  Procedure
//    checkRule
// 
//  Purpose
//    checks each condition of a particular rule
//    by short-circuit evaluation
// 
//  Parameters
//    $rule, an array 
// 
//  Produces
//    a boolean
// 
//  Preconditions
//    $rule is in correct format
// 
//  Postconditions
//    $rule's conclusion will be executed if conditions are met
function checkRule($rule)
{
  global $globalRules, $studentProperties, $tentativePlace, $sizeOfRules;
  global $assocArrayOfRules, $countArray;
  debugPrint("\nIN CHECK RULE: $rule[0]", false);
  
  //if the conditions in the condition sequence are meet
  //make the conclusion. Regardless of outcome mark rule as run.
  $result = checkConditionSequence($rule[2]);

  tfPrint("$rule[0]", $result, $false);

  //update assocArrayOfRules to indicate that this rule
  //has been run
  $assocArrayOfRules["$".$rule[0]] = true;
 
  //if the rule has passed ($result is true) then update the countArray to indicate that
  //then execute the rules conclusion 
  if($result)
    {
      $countArray[$rule[0]]["pass"]++;
      $conclusion = explode(" ",$rule[1]);

      debugPrint("$rule[0] : Success!", false);
      debugPrint("Setting $conclusion[0] to ". $conclusion[2]."!!!!!!!!!", false);

      makeConclusion($conclusion);
      
    }
  //otherwise the rule has not passed,
  //and update the countArray to indicate this. 
  else
    {
      $countArray[$rule[0]]["fail"]++;
    }
  return $result;
}

// 
//  Procedure
//    makeConclusion
// 
//  Purpose
//    makes a conclusion. Sets the appropriate field in studentProperties
//    to the value in the conclusion.
// 
//  Parameters
//    $conclusion, an array 
// 
//  Produces
//    
// 
//  Preconditions
//    $conclusion is in correct format
// 
//  Postconditions
//    $rule's conclusion will be executed
function makeConclusion($conclusion)
{
  global $studentProperties, $tentativePlace;
  $conclusionLength = sizeof($conclusion);
  //if the conclusion is a regular conclusion.
  //examples are 'stdscores = high' or 'TMATH-PLACE = 131' 
  if($conclusionLength == 3 && $conclusion[1] == '=')
    {
      //if the conclusion set a tentative field then 
      //append the course number ($conclusion[2] to the end of
      //the field ($conclusion[0] to generate the proper
      //field name to update
      if(in_array($conclusion[0],$tentativePlace))
        {
          $appendResult = $conclusion[0].$conclusion[2];
          $studentProperties[$appendResult] = $conclusion[2];
        }
      //otherwise no special proccsessing is required.                         
      else
        {
          $studentProperties[$conclusion[0]] = $conclusion[2];
        }
    }
  //if the conclusion is setting one field to be the same as another field
  //examples are  'satreal =field sat2math1'
  elseif($conclusion[1] == '=field')
    {
      $studentProperties[$conclusion[0]] = $studentProperties[$conclusion[2]];
    }


  //if the conclusion sets the conclusion field to be the
  //sum of two values proccess as follows.
  elseif($conclusionLength >= 4 && $conclusion[1] == 'add')
    {
      $sum = 0;
      for($i = 2; $i < sizeof($conclusion); $i++)
        {
          if(is_numeric( $conclusion[$i]))
            {
              $sum += $conclusion[$i];
            }
          else
            {
              $sum += $studentProperties[$conclusion[$i]];
            }
        }
      $studentProperties[$conclusion[0]] = $sum;
    }

  debugPrint("$conclusion[0] :". $studentProperties[$conclusion[0]], false);
  
}

// 
//  Procedure
//    checkConditionSequence
// 
//  Purpose
//    checks conditionSequence qualifier and calls correct
//    evaluation function
// 
//  Parameters
//    $conditionSequence, an array 
// 
//  Produces
//    $result, a boolean
// 
//  Preconditions
//    [none]
// 
//  Postconditions
//    [none]
function checkConditionSequence($conditionSequence)
{
  if($conditionSequence[0] == 'all')
    {
      $result = allConditionSequence($conditionSequence);
    }
  elseif($conditionSequence[0] == 'some')
    {
      $result = someConditionSequence($conditionSequence);
    }
  elseif($conditionSequence[0] == 'true')
    {
      $result = true;
    }
  return $result;
}


// 
//  Procedure
//    findRule
// 
//  Purpose
//    iterates through array of rules and finds a rule that concludes
//    the $condition passed as a parameter
// 
//  Parameters
//    $condition, an array
//    &$counter, an integer, used for bookkeeping
//               it is used to ensure that when a rule
//               fails, the search for a new rule, begins at the proper
//               index.
// 
//  Produces
//    returns a rule that could conclude the condition
// 
//  Preconditions
//    [none]
// 
//  Postconditions
//    [none]
function findRule($condition, &$counter)
{
  global $globalRules, $assocArrayOfRules, $indent,$sizeOfRules;

  global $nonNumericArray;

  debugPrint("\nFINDING RULE for: $condition[0] $condition[1] $condition[2]"
             , false);
  
  //iterate through rules
  // foreach($globalRules as $rule)
  for(;$counter < $sizeOfRules; $counter++)
    {
      $rule = $globalRules[$counter];
      //the currentRuleConclusion, is at index of of $rule
      //it is exploded into an array 
      $currentRule = explode(" ",$rule[1]);
      
      //Check to see if fields used in condition
      //and field used in the conclusion match
      if(substr_compare($condition[0],$currentRule[0],0) == 0)
        {
          //if the rule conclusion is not making a simple assignment
          //and the field matches then return this rule,
          //the reason is that for  conditions like 'AdjSemOfMath >= 7'
          //the only rule has a conclusion 'AdjSemOfMath add calcsem mathsem'
          if($currentRule[1] != '=' && ! $assocArrayOfRules["$".$rule[0]])
            {
              return $rule;
            }
          //if the rule involves a numeric comparison then directly call
          //numeric evaluation, to see if the rule fits.
          if(is_numeric($condition[2]))
            { 
              //corrected order on 8/1
              $cmpRight = numericEvaluation($condition[1],
                                            $currentRule[2],
                                            $condition[2]);

            }
          //otherwise we need to convert the items being compared
          //to numbers by looking them up in nonNumericArray.
          else
            {
              //swapped the second and third argument, 8/1
              $cmpRight = numericEvaluation($condition[1],
                                            $nonNumericArray[$currentRule[2]],
                                            $nonNumericArray[$condition[2]]);
            }
          
          //if the rule's conclusion satisfies the condition, then
          // return rule.
          if($cmpRight)
            {
              if(! $assocArrayOfRules["$".$rule[0]])
                {
                  debugPrint("found rule: $rule[0]", false);
                  return $rule;
                }
            }
        }
    }
  return null;
}
?>
