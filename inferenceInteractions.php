<?php

include('yesNo.inc');
function inferenceInteractions()
{
  global $selector, $argv, $argc;
  if($argc != 2)
    {
      print("Select option:\n" .
            "1 - place class\n".
            "2 - place student\n".
            "3 - place all\n".
            "Option: ");
      $option = trim(fgets(STDIN));
      if($option == 1)
        {
          print("Please enter start year: ");
          $startyear =  trim(fgets(STDIN));
          $selector = " startyear = $startyear";
        }
      elseif($option == 2)
        {
          print("Enter studentID: ");
          $studentID =  trim(fgets(STDIN));
          $selector = " studentID = $studentID";
        }
      elseif($option == 3)
        {
          print("Are you sure you want to place ALL students in the data base?(y/n):");
          if(yesNo(trim(fgets(STDIN))))
            {
              $selector = " startyear != -1 || startyear = -1";
            }
        }
       
    }
  else
    {
      if($argv[1] == "previous")
        {
          $selector = " startyear != 2010 && startyear != -1";
        }
      else
        {
          $selector = " startyear = $argv[1]";
        }
    }
  return true;
}

