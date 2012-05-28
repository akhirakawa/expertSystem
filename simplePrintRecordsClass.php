<?php
//Planned Include file to print records
function simplePrintRecordsClass($result,$offset)
{
  print("***************************\n");
  print("INFORMATION FOR: " . 
        mysql_result($result, $offset,"name") . ".\n");
  
  if(mysql_result($result, $offset, "studentID"))
    {
      print("\tStudent-ID: " . 
            mysql_result($result, $offset, "studentID") . "\n");
    }
  if(mysql_result($result, $offset, "startyear"))
    {
      print("\tClass: " . 
            mysql_result($result, $offset, "startyear") . "\n");
    }
  
  //Only print High School data is both high school name
  //and graduation date are non-null
  if(mysql_result($result, $offset, "hsname") 
     && mysql_result($result, $offset, "hsgraddate"))
    {
      //Print High School data
      print("***************************\n");
      print("High School Data\n");
      
      if(mysql_result($result, $offset, "totalyears"))
        {
          print("\tTotal Years in High School: " . 
                mysql_result($result, $offset, "totalyears") . "\n");
        }
      
      print("***************************\n");
    }
  //End of non-transcript high school data
  
  //Check CS semesters and grades
  if(mysql_result($result, $offset, "cssem"))
    {
      print("Took " .
            mysql_result($result, $offset, "cssem") .
            " semesters of Computer Science");
      if(mysql_result($result, $offset, "csgrade"))
        {
          print("and received a " .
                mysql_result($result, $offset, "csgrade") .
                ".\n");
        }
      else
        {
          print(".\n");
        }
    }
  
  //Total semesters of math
  if(mysql_result($result, $offset, "mathsem"))
    {
      print("Total semesters of math: " .
            mysql_result($result, $offset,"mathsem")."\n");
    }
  
  //Check Precalc semesters and grades
  if(mysql_result($result, $offset, "precalcsem"))
    {
      print("Took " .
            mysql_result($result, $offset, "precalcsem") .
            " semesters of Pre-Calculus");
      if(mysql_result($result, $offset, "precalcgrade"))
        {
          print(" and received a " .
                mysql_result($result, $offset, "precalcgrade") .
                ".\n");
        }
      else
        {
          print(".\n");
        }
    }
  //Check Calc semesters and grades
  if(mysql_result($result, $offset, "calcsem"))
    {
      print("Took " .
            mysql_result($result, $offset, "calcsem") .
            " semesters of Calculus");
      if(mysql_result($result, $offset, "calgrade"))
        {
          print(" and received a " .
                mysql_result($result, $offset, "calgrade") .
                ".\n");
        }
      else
        {
          print(".\n");
        }
    }
  //Check stat semesters and grades
  if(mysql_result($result, $offset, "statsem"))
    {
      print("Took " .
            mysql_result($result, $offset, "statsem") .
            " semesters of Statistics");
      if(mysql_result($result, $offset, "statgrade"))
        {
          print(" and received a " .
                mysql_result($result, $offset, "statgrade") .
                ".\n");
        }
      else
        {
          print(".\n");
        }
    }
  
  //AP Scores
  print("***************************\n");
  print("AP/IB Scores\n");
  $cflag = "No Records...\n";
  if(mysql_result($result, $offset, "apcalcab"))
    {
      $cflag = " ";
      print("\tAP Calculus AB: " . 
            mysql_result($result, $offset, "apcalcab") . "\n");
    }
  if(mysql_result($result, $offset, "apcalcbc"))
    {
      $cflag = " ";
      print("\tAP Calculus BC: " . 
            mysql_result($result, $offset, "apcalcbc") . "\n");
    }
  if(mysql_result($result, $offset, "apcsa"))
    {
      $cflag = " ";
      print("\tAP Computer Science A: " . 
            mysql_result($result, $offset, "apcsa") . "\n");
    }
  if(mysql_result($result, $offset, "apcsab"))
    {
      $cflag = " ";
      print("\tAP Computer Science AB: " . 
            mysql_result($result, $offset, "apcsab") . "\n");
    }
  if(mysql_result($result, $offset, "apstat"))
    {
      $flag = " ";
      print("\tAP Statistics: " . 
            mysql_result($result, $offset, "apstat") . "\n");
    }
  if(mysql_result($result, $offset, "ibmat"))
    {
      $cflag = " ";
      print("\tIB Math: " . 
            mysql_result($result, $offset, "ibmat") . "\n");
    }
  if(mysql_result($result, $offset, "ibcs"))
    {
      $cflag = " ";
      print("\IB Computer Science: " . 
            mysql_result($result, $offset, "ibcs") . "\n");
    }
  if(mysql_result($result, $offset, "alevelf"))
    {
      $cflag = " ";
      print("\tA Level F: " . 
            mysql_result($result, $offset, "alevelf") . "\n");
    }
  if(mysql_result($result, $offset, "alevelp"))
    {
      $cflag = " ";
      print("\A Level P: " . 
            mysql_result($result, $offset, "alevelp") . "\n");
    }
  print($cflag);
  print("***************************\n");
  if(mysql_result($result, $offset, "actcomp")
     || mysql_result($result, $offset, "actmath") 
     || mysql_result($result, $offset, "satmath"))
    {
      print("SAT/ACT Scores\n");
      if(mysql_result($result, $offset, "actcomp"))
        {
          print("\tACT Composite: " . 
                mysql_result($result, $offset, "actcomp") . "\n");
        }
      if(mysql_result($result, $offset, "actmath"))
        {
          print("\tACT Math: " . 
                mysql_result($result, $offset, "actmath") . "\n");
        }
      if(mysql_result($result, $offset, "satmath"))
        {
          print("\tSAT Math: " . 
                mysql_result($result, $offset, "satmath") . "\n");
        }
    }
  print("***************************\n");
  
  //Check first CS course
  print("First Courses\n");
  if(mysql_result($result, $offset, "firstcscourse"))
    {
      print("\tTook CSC " .
            mysql_result($result, $offset, "firstcscourse"));
      if(mysql_result($result, $offset, "firstcsgrade"))
        {
          print(" and received a " .
                mysql_result($result, $offset, "firstcsgrade") .
                ".\n");
        }
      else
        {
          print(".\n");
        }
    }
  //Check first math course
  if(mysql_result($result, $offset, "firstmathcourse"))
    {
      print("\tTook MAT " .
            mysql_result($result, $offset, "firstmathcourse"));
      if(mysql_result($result, $offset, "firstmathgrade"))
        {
          print(" and received a " .
                mysql_result($result, $offset, "firstmathgrade") .
                ".\n");
        }
      else
        {
          print(".\n");
        }
    }
  //Check first stat course
  if(mysql_result($result, $offset, "firststatcourse"))
    {
      print("\tTook statistics level " .
            mysql_result($result, $offset, "firststatcourse"));
      if(mysql_result($result, $offset, "firststatgrade"))
        {
          print(" and received a " .
                mysql_result($result, $offset, "firststatgrade") .
                ".\n");
        }
      else
        {
          print(".\n");
        }
    }
  print("***************************\n");
  print("Current Placements\n");
  if(mysql_result($result, $offset, "reccsplace"))
    {
      print("\tCS Placement: " . 
            mysql_result($result, $offset, "reccsplace") . "\n");
    }
  if(mysql_result($result, $offset, "recmathplace"))
    {
      print("\tMath Placement: " . 
            mysql_result($result, $offset, "recmathplace") . "\n");
    }
  if(mysql_result($result, $offset, "recstatplace"))
    {
      print("\tStatistics Placement: " . 
            mysql_result($result, $offset, "recstatplace") . "\n");
    } 
  print("END OF " . 
        mysql_result($result, $offset, "name") .
        "'s record.\n");
  print("*************************************\n");
  
    }

?>