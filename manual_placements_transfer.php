<?php
include ('mysql-connection.inc');
include ('simplePrintRecords.php');

//variable initializations
$fieldArray = array('reccsplace', 'recmathplace', 'recstatplace');

//Get initial studentid
print("Enter a studentID to Manually Update:\n");
$userid = trim(fgets(STDIN));
//Check id to see if it has any records associated with it
$sqlQuery = "SELECT *, students.studentID";
$sqlQuery .= " FROM students JOIN transfers";
$sqlQuery .= " ON students.studentID = transfers.studentID";
$sqlQuery .= " WHERE students.studentID = " ;
$sqlQuery .=   mysql_real_escape_string("".$userid);
$sqlResult = mysql_query($sqlQuery);

//print($sqlQuery);
//If there are no records, ask for new id
while((!$sqlResult) || (mysql_num_rows($sqlResult) == 0))  
  { 
    print("Query Result not valid. Student may not have transfer data.\n");
    print("Enter new ID:\n");
    $userid = trim(fgets(STDIN));
    $sqlQuery = "SELECT *, students.studentID";
    $sqlQuery .= " FROM students JOIN transfers";
    $sqlQuery .= " ON students.studentID = transfers.studentID";
    $sqlQuery .= " WHERE students.studentID = " ;
    $sqlQuery .=   mysql_real_escape_string("".$userid);
    $sqlResult = mysql_query($sqlQuery);
  }

//print student data
simplePrintRecords($sqlResult);

//print any transfer data for student
print("Transfer Data\n");

//loop throught all rows of students data
for($i = 0;$i < mysql_num_rows($sqlResult); $i++)
  {
    //check to see if what we are printing is null
    if(mysql_result($sqlResult,$i,"coursename") != null)
      {
        print(mysql_result($sqlResult,$i,"coursename"));
        if(mysql_result($sqlResult,$i,"instloc") != null)
          {
            print(", ".mysql_result($sqlResult,$i,"instloc").".\n");
          }
        else
          {
            print(".\n");
          }
      }
  }

$update_string = "UPDATE students SET ";

//initializing $input
$input = '';

print("\nEntering edit mode\n");
//prompt for change for each field in $fieldArray (initialized at start)
foreach($fieldArray as $field)
{
  while(strtolower(substr($input,0,1)) != 'n')
    {
      print("The current value of ".$field." is ".
            mysql_result($sqlResult,0,$field).".\n");
      print("Would you like to change this field?\n");
      $input = trim(fgets(STDIN));
      //check to see if input was affirmative
      if(strtolower(substr($input,0,1)) == 'y')
        {
          print("What value would you like to assign to ".$field."?\n");
          $value = trim(fgets(STDIN));
          //append to $update_string
          $update_string .= $field." = ".$value.", ";
          break;
        }
    }
  $input = '';
}

//check to see if any field and values were added
//if $update_string has not been modified, there is no update to run
if(strcmp($update_string,"UPDATE students SET ") != 0)
  {
    $update_string = substr_replace($update_string,"",- 2);
    $update_string .= " WHERE studentID = ".$userid;
    print($update_string."\n");
    $update_Result = mysql_query($update_string);
  }
?>