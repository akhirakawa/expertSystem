//Script to update manually change student records
<?php
include ('mysql-connection.inc');

//variable initializations
$userid = '';
$field = '';
$rec = '';
$update = '';
$fieldArray = array();
$optionsPerLine = 3;

//Get initial studentid
print("Enter a studentID to Manually Update:\n");
$userid = trim(fgets(STDIN));
//Check id to see if it has any records associated with it
$sqlQuery = "SELECT * FROM students WHERE studentID = " ;
$sqlQuery .=   mysql_real_escape_string("".$userid);
$sqlResult = mysql_query($sqlQuery);

//print($sqlQuery);
//If there are no records, ask for new id
while((!$sqlResult) || (mysql_num_rows($sqlResult) == 0))  
  { 
    print("ID not found. Enter new ID:\n");
    $userid = trim(fgets(STDIN));
    $sqlQuery = "SELECT * FROM students WHERE studentID = ";
    $sqlQuery .=    mysql_real_escape_string("".$userid);
    $sqlResult = mysql_query($sqlQuery);
    //print($sqlQuery);
  }

//Find field names
$fieldsQuery = "SHOW COLUMNS FROM students";
$fieldsResult = mysql_query($fieldsQuery);
$numRows = mysql_num_rows($fieldsResult);
for( $offset = 0 ; $offset < $numRows ; $offset++)
  {
    $fieldArray[$offset] = mysql_result($fieldsResult, $offset, "Field");
  }


//print options for updating
for($offset = 0 ; $offset < $numRows ; $offset++)
  {
    print($offset . " - " . $fieldArray[$offset] . " ");
    if( $offset % $optionsPerLine == $optionsPerLine - 1)
      {
        print("\n");
      }
  }

print("\nEnter done to finish editing...\n");
print("What placement do you want to change:\n");
$field = trim(fgets(STDIN));


//Loop to keep updating fields until done
while($field != 'done')
  {
    print("Enter recomendation: ");
    $rec = trim(fgets(STDIN));

    //Set up and execute query
    $update = "UPDATE students ";
    $update .= "SET ". $fieldArray[$field] ." = ";
    $update .=  mysql_real_escape_string($rec);
    $update .= " Where studentID = " . mysql_real_escape_string($userid);
    $updateResult = mysql_query($update);

    //Check the changed field
    $checkQuery = "SELECT " . $fieldArray[$field] ." FROM students";
    $checkQuery .= " WHERE studentID = " . $userid;
    $checkResult = mysql_query($checkQuery);
    print("Changed " . $fieldArray[$field] . " to ");
    print(mysql_result($checkResult,0) . "\n\n");
    //printing options
    for($offset = 0 ; $offset < $numRows ; $offset++)
      {
      print($offset . " - " . $fieldArray[$offset] . " ");
	if( $offset % $optionsPerLine == $optionsPerLine - 1 )
	  {
	    print("\n");
	  }
      }

    //Select new field to update
    print("\nEnter done to finish editing...\n");
    print("What placement do you want to change:\n");
    $field = trim(fgets(STDIN));
  }
?>
