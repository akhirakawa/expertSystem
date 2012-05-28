<?php
include ('mysql-connection.inc');
include ('simplePrintRecordsClass.php');

//variable initializations
$fieldArray = array('reccsplace', 'recmathplace', 'recstatplace');
$toEng = array('reccsplace' => 'CS Placement',
               'recmathplace' => 'Math Placement',
               'recstatplace' => 'Statistics Placement');

print("This script will allow manual edits of recommended placements\n");
print("on the basis of transfer credits\n");
//Get initial studentid
print("Enter a class year to Manually Update:\n");
$year = trim(fgets(STDIN));

$sqlQuery = "SELECT *, students.studentID";
$sqlQuery .= " FROM students JOIN transfers";
$sqlQuery .= " ON students.studentID = transfers.studentID";
$sqlQuery .= " WHERE students.startyear = " ;
$sqlQuery .=   mysql_real_escape_string("".$year);
$sqlQuery .= " ORDER BY students.name";
$sqlResult = mysql_query($sqlQuery);

$numRows = mysql_num_rows($sqlResult);

//process each line of query result
for($i = 0; $i < $numRows; $i++)
  {
    $studentID = mysql_result($sqlResult,$i,'studentID');
    
    //new student so print out
    simplePrintRecordsClass($sqlResult,$i);

    print("Transfer Data\n");

    //continue through rows while we are processing the same student
    while($i < $numRows && 
          $studentID == mysql_result($sqlResult,$i,"studentID"))
      {
        //if we have transfer data, print i
        if(mysql_result($sqlResult,$i,"dept") != null)
          {
            print(mysql_result($sqlResult,$i,"dept").", ");
          }
        if(mysql_result($sqlResult,$i,"coursename") != null)
          {
            print(mysql_result($sqlResult,$i,"coursename").", ");
          }
        if(mysql_result($sqlResult,$i,"credits") != null)
          {
            print(mysql_result($sqlResult,$i,"credits").", ");
          }
        if(mysql_result($sqlResult,$i,"instname") != null)
          {
            print(mysql_result($sqlResult,$i,"instname").", ");
          }
        if(mysql_result($sqlResult,$i,"instloc") != null)
          {
            print(mysql_result($sqlResult,$i,"instloc")."\n");
          }
        
        //increment $i to get next row of data
        $i++;
      }

    //$i is now one row ahead of current student
    //move $i back to current student
    $i--;
    
    //student data is now printed
    //query for updates
    
    $update_string = "UPDATE students SET ";
    $changes = '';

    //initializing $input
    $input = '';
    
    print("\nEntering edit mode\n");
    //process each element in $fieldArray to see if
    //user wants to update
    foreach($fieldArray as $field)
      {
        print("The current value of ".$toEng[$field]." is ".
                  mysql_result($sqlResult,$i,$field).".\n");
        print("Enter new value to change current placement\n");
        print("or press the Enter Key to keep placement.\n");
        $input = trim(fgets(STDIN));
        //loop until field is changed or
        //user gives negative response
        if(strlen($input) < 3 || $input == '')
          {
            continue;
          }
        else
          {
            $changes .= $field." = ".$input.", ";
          }
      }
    $update_string .= $changes;
    //check to see if any field and values were added
    //if $update_string has not been modified, there is no update to run
    if(strcmp($update_string,"UPDATE students SET ") != 0)
      {
        $update_string = substr_replace($update_string,"",- 2);
        print("Changing ".$changes." for ".
              mysql_result($sqlResult,$i,'name')."\n");
        $update_string .= " WHERE studentID = ".$studentID;
        $update_Result = mysql_query($update_string);
      }
  }