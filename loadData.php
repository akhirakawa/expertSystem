<?php
//Script to automate moving of data to mysql database
//assumes first value of csv is studentID

include ('mysql-connection.inc');

//turn off error reporting for access to associative array
//  when key is not yet defined
error_reporting(E_ALL ^ E_NOTICE);

print("Enter the name of the table to load data into\n");
print("Pre-Condition: table must already exist in the database\n");
print("and first column of csv is studentID\n");
$table_name = trim(fgets(STDIN));

//assoc array where fields => offset
//provides easy reference while reading a csv file
$fields;

//array strictly for current field names of csv file
$keys;

//original keys for redo option
$ini_keys;

//assoc array where fields => bool
//check to see if field was updated for a student
$mod;

//assoc array where initialfields => changedfields
$mappings;

//array to store database field names
$database_fields;

//gets list of column names
$fields_query = "SHOW COLUMNS FROM ".$table_name;
$fields_result = mysql_query($fields_query);
$num_fields = mysql_num_rows($fields_result);

print("Listing of database field names\n");
//add column names to database_fields
for($i = 0; $i < $num_fields; $i++)
  {
    $database_fields[$i] = mysql_result($fields_result, $i, "Field");
    print("   field name:  " . $database_fields[$i] . "\n");
  }

print("Enter name of local file (in csv format): ");
$file_name = trim(fgets(STDIN));

//Check to make sure file is of csv type
$ext = substr($file_name, strrpos($file_name, '.') + 1);

//Loop until correct file input is given
while($ext != "csv" || !file_exists($file_name))
  {
    print("File was not in csv format or was not found, enter new file name: ");
    $file_name = trim(fgets(STDIN));
    
    $ext = substr($file_name, strrpos($file_name, '.') + 1);
  }
print("\n");

//open file for read only
$handle = fopen($file_name,"r");

$current_line = fgets($handle);

//read all empty space at top of file
while(empty($current_line))
  {
    $current_line = fgets($handle);
  }


$ini_fields = explode(",",$current_line);

//Make assoc array for getting offsets
//Remove any special characters from field names (ie "" around the field)
//Make all fields lower case for easy comparison in future
$index = 0;
foreach($ini_fields as $field)
{
  //fields must contain no special characters
  $field = strtolower(ereg_replace("[^A-Za-z0-9]", "", $field));
  $fields[$field] = $index;
  $mod[$field] = false;
  //increment index for next value
  $index++;
}

$keys = array_keys($fields);
$ini_keys = array_keys($fields);

//find which fields match in csv and database
foreach($keys as $key)
{
  foreach($database_fields as $field)
    {
      //fields are equal by strcmp
      if(strcmp($key, $field) == 0)
        {
          //set field in mod to true
          //you dont have to change this field
          $mod[$field] = true;
        }
    }
}
$input = "";

//keep looping until user input == done
while(strtolower($input) != "done")
  {
    $default = null;
    //check if there are any fields to change
    $print_fields = false;
    foreach($mod as $field)
      {
        //if a mod field is set to false (ie not changed)
        if(!$field)
          {
            $print_fields = true;
            break;
          }
      }
    //print_fields must be true so there are names to mod
    if($print_fields)
      {
        //print unfound names
        print("\nThe following fields were not found in the database: \n");
        $tabs = 1;
        for($i = 0; $i < sizeof($keys); $i++)
          {
            if(!$mod[$keys[$i]])
              {
                //set default value to first field that needs to be changed
                if($default == null)
                  {
                    $default = $keys[$i];
                  }
                print(" ".$keys[$i]);
                if($i == sizeof($keys) - 1 || ($tabs % 4 == 0))
                  {
                    print("\n");
                  }
                else
                  {
                    print(", ");
                  }
                $tabs++;
              }
          }
        //print field name possibilities from database
        print("\nThe following fields are found in the database and have not been used: \n");
        $tabs = 1;
        for($i = 0; $i < sizeof($database_fields); $i++)
          {
            if(!$mod[$database_fields[$i]])
              {
                print(" ".$database_fields[$i]);
                if($i == sizeof($database_fields) - 1 || ($tabs % 4 == 0))
                  {
                    print("\n");
                  }
                else
                  {
                    print(", ");
                  }
                $tabs++;
              }
          }

        print("\nWould you like to change a field name?\n");
        print("   Press Enter Key to use default field: ".$default.".\n");
        print("   Enter 'done' to skip, \n");
        print("         'insert' to add a column to the table\n");
        print("         'redo' to undo a change, \n");
        print("         'see' to check mappings\n");
        print("   else input current field name that you would like to change.\n");
        $input = fgets(STDIN);
        //check to see if input is just '\n'
        if(strlen($input) == 1 && strcmp($input,"\n") == 0)
          {
            $input = $default;
          }
        else
          {
            //do not trim until here because trim will 
            //kill newline characters and we will not be able to test
            //if user wanted default
            $input = trim($input);
          }
        if(strtolower($input) == "done")
          {
            //user is done changing fields
            continue;
          }
        elseif(strtolower($input) == "insert")
          {
            $commit = '';
            while($commit != "yes")
              {
                print("Enter the column name to insert\n");
                print("or 'stop' to quit insertion\n");
                $column_name = trim(fgets(STDIN));
                if(strtolower($column_name) == "stop")
                  {
                    //user does not want to insert column
                    break;
                  }
                print("Enter data-type for column (ie varchar(255) or bigint(20))\n");
                $data_type = trim(fgets(STDIN));
                print("\nInserting column named: ".$column_name."\n");
                print("with data-type: ".$data_type."\n");
                $insert_query = "ALTER TABLE ".$table_name." ";
                $insert_query .= "ADD ".$column_name." ".$data_type;
                print("Current insert query is: \n");
                print($insert_query."\n");
                print("Commit insertion into table? (yes/no)\n");
                $commit = trim(fgets(STDIN));
                if(strtolower($commit) != "yes")
                  {
                    //for safety the only way to commit insertion is 'yes'
                    //any other input will return user to input menu
                    continue;
                  }
                else
                  {
                    //execute insert query
                    $insert_result = mysql_query($insert_query);
                    //modify arrays to contain new column
                    print("Which field of the csv does this column represent?\n");
                    $old = trim(fgets(STDIN));
                    while(!in_array($old,$keys,true) || 
                          $mod[$old] == true)
                      {
                        print("\nThe input is either not a field or this field has\n");
                        print("already been updated. Please enter another field\n");
                        print("or 'done' to finish\n");
                        $old = trim(fgets(STDIN));
                      }
                    $database_fields[sizeof($database_fields)] = $column_name;
                    $fields[$column_name] = $fields[$old];
                    unset($fields[$old]);
                    //change mod array
                    $mod[$column_name] = $mod[$old];
                    unset($mod[$old]);
                    $mod[$column_name] = true;
                    //change key array
                    for($i = 0; $i < sizeof($keys); $i++)
                      {
                        if($keys[$i] == $old)
                          {
                            $keys[$i] = $column_name;
                          }
                      }
                    $mappings[$old] = $column_name;
                  }
              }
          }
        elseif(strtolower($input) == "redo")
          {
            //print mappings
            print("\n");
            $original = array_keys($mappings);
            $i=0;
            foreach($mappings as $value)
              {
                if($value != null)
                  {
                    print($original[$i]." => ".$value."\n");
                  }
                $i++;
              }
            print("\nWhich field would you like to un-map?\n");
            print("Enter 'finish' to quit\n");
            print("Note: cannot unmap inserted columns\n");
            $unmap = trim(fgets(STDIN));
            if(strtolower($unmap) == "finish")
              {
                continue;
              }
            while(!in_array($unmap,$ini_keys))
              {
                print("\nOriginal field not found. Enter new field\n");
                print("Note: cannot unmap inserted columns\n");
                $unmap = trim(fgets(STDIN));
              }
            $current_mapping = $mappings[$unmap];
            $fields[$unmap] = $fields[$current_mapping];
            unset($fields[$current_mapping]);
            //change mod array
            $mod[$unmap] = $mod[$current_mapping];
            unset($mod[$current_mapping]);
            $mod[$unmap] = false;
            //change key array
            for($i = 0; $i < sizeof($keys); $i++)
              {
                if($keys[$i] == $current_mapping)
                  {
                    $keys[$i] = $unmap;
                  }
              }
            $mappings[$unmap] = null;
          }
        elseif(strtolower($input) == "see")
          {
            print("\n");
            $original = array_keys($mappings);
            $i=0;
            foreach($mappings as $value)
              {
                if($value != null)
                  {
                    print($original[$i]." => ".$value."\n");
                  }
                $i++;
              }
            print("Enter anything to finish looking at mappings\n");
            $finish = trim(fgets(STDIN));
          }
        else
          {
            if($input != $default)
              {
                $commit = '';
                while(strtolower($input) != "done" &&
                      strtolower($input) != "quit" &&
                      !in_array($input,$keys,true) || 
                      $mod[$input] == true)
                  {
                    print("\nThe input is either not a field or this field has\n");
                    print("already been updated. Please enter another field\n");
                    print("'quit' to exit back to menu or 'done' to finish updating\n");
                    $input = trim(fgets(STDIN));
                  }
                if(strtolower($input) == "done" || strtolower($input) == "quit")
                  {
                    continue;
                  }
                //change field in arrays
                print("\nYour input was ".$input.". Which field in the\n");
                print("database would you like to use instead?\n");
                $correction = trim(fgets(STDIN));
              }
            else
              {
                print("Used default field: ".$input.". Which field in the\n");
                print("database would you like to change ".$input." to?\n");
                $correction = trim(fgets(STDIN));
              }
            //get input until input is a real field name
            while(!in_array($correction,$database_fields,true))
              {
                print("\nInput was not a field name in the database\n");
                print("Enter another field\n");
                $correction = trim(fgets(STDIN));
              }
            print("Old field name: ".$input."\n");
            print("Corrected field name: ".$correction."\n");
            print("Commit change of field name using current input? (yes/no)\n");
            $commit = trim(fgets(STDIN));
            if(strtolower($commit) != "yes")
              {
                continue;
              }
            else
              {
                //change arrays to use new field name instead
                $fields[$correction] = $fields[$input];
                unset($fields[$input]);
                //change mod array
                $mod[$correction] = $mod[$input];
                unset($mod[$input]);
                $mod[$correction] = true;
                //change key array
                for($i = 0; $i < sizeof($keys); $i++)
                  {
                    if($keys[$i] == $input)
                      {
                        $keys[$i] = $correction;
                      }
                  }
                //show mapping
                $mappings[$input] = $correction;
              }
          }

      }
    else
      {
        //above if statement failed so there are no
        //mismatched fields
        $input = "done";
      }
  }
//get start year
print("\nBefore we start processing students,\n");
print("enter start year for current student file\n");
$start_year = trim(fgets(STDIN));

print("\nIs this transfer data?(ie you can have multiple rows for one student) (yes/no)\n");
$transfer = trim(fgets(STDIN));

//get first student
$current_line = fgets($handle);
$current_line = process_line($current_line);

//process students until EOF has been reached
while(!feof($handle))
{
  //reset $field_value_str
  $field_value_str = '';
  //query to update current student
  //check to see if current student already exists in the database
  $exist_check = "SELECT * FROM ".$table_name." ";
  $exist_check .= "WHERE studentID = " . $current_line[$fields['studentID']];
  print($exist_check."\n");
  $exist_result = mysql_query($exist_check);
  
  //check offset for mysql_result
  $offset = 0;
  
  //if data has multiple rows per student
  if(mysql_num_rows($exist_result) > 0)
    {
      //use the latest row for update
      $offset = mysql_num_rows($exist_result) - 1;
    }

  if(empty($exist_result))
    {
      print("YAYAYAY!\n");
    }
  
  //build $update_query string
  foreach($keys as $key)
    {
      //checks if there are no rows for current student OR
      //if this is transfer data OR
      //if this is not transfer data and there is no value in
      //current field for student
      //All of the following must be true: value is non-empty
      //field must be a legit
      //value must not be "  " spaces thus the strlen(trim) condition
      if((mysql_num_rows($exist_result) == 0 ||
          $transfer == 'yes' || 
          ($transfer != 'yes' && 
           mysql_result($exist_result, $offset, $key) == null)) &&
         !empty($current_line[$fields[$key]]) &&
         in_array($key,$database_fields) &&
         strlen(trim($current_line[$fields[$key]])) != 0)
        {
          $field_value_str .= $key . " = ";
          $field_value_str .= "'".trim($current_line[$fields[$key]])."', ";
        }
      //same conditions above except we want the fields with no values
      //value is set to 0
      elseif((mysql_num_rows($exist_result) == 0 ||
              $transfer == 'yes' || 
              ($transfer != 'yes' &&
               mysql_result($exist_result, $offset, $key) == null)) &&
             (empty($current_line[$fields[$key]]) ||
              strlen(trim($current_line[$fields[$key]])) == 0) &&
             in_array($key,$database_fields))        
        {
          $field_value_str .= $key . " = 0, ";
        }
    }
  //if start year needs to be added
  if(in_array("startyear",$database_fields))
    {
      //add start year to query
      $field_value_str .= "startyear = ".$start_year.", ";
    }
  
  //check to see if current student already exists in the database
  /*$exist_check = "SELECT * FROM ".$table_name." ";
  $exist_check .= "WHERE studentID = " . $current_line[$fields['studentID']];
  $exist_result = mysql_query($exist_check);*/
  
  //there will be no rows for query if student does not exist
  
  if(mysql_num_rows($exist_result) == 0 || $transfer == 'yes')
    {
      //student does not exist so create a row for him
      $create_row = "INSERT INTO ".$table_name." SET ";
      $create_row .= $field_value_str;
      if(!contains("studentID",$create_row,false))
        $create_row .= "studentID = ".$current_line[$fields['studentID']].", ";
      $create_row = substr_replace($create_row,"",- 2);
      $create_result = mysql_query($create_row);
      print("Updated student with id: ".$current_line[$fields['studentID']]."\n");
    }
  else
    {
      //mysql_num_rows != 0 so we must update the row
      $update_query = "UPDATE ".$table_name." SET ";
      
      //add previously formed string of 'field = value'
      $update_query .= $field_value_str;
      
      //take our last ', '
      $update_query = substr_replace($update_query,"",-2);
      
      $update_query .= " WHERE studentID = ".
        $current_line[$fields['studentID']];
      $update_result = mysql_query($update_query);
      print("Updated student with id: ".$current_line[$fields['studentID']]."\n");
    }
  //get next student
  $current_line = fgets($handle);
  $current_line = process_line($current_line);
 }

function process_line($string)
{
  //get out ", " after first name
  //fixes extra column error
  $string = str_replace(", ","!@#",$string);

  //remove ' from field values
  $string = str_replace("'","",$string);
  
  //remove double " from string
  $string = str_replace('"','',$string);

  $string = explode(",",$string);

  //find instances of !@# to replace

  for($i=0;$i<sizeof($string);$i++)
    {
      if(contains("!@#",$string[$i],false))
        {
          $string[$i] = str_replace("!@#",", ",$string[$i]);
        }
    }

  return $string;
}



function contains($str, $content, $ignorecase = true)
{
    if ($ignorecase){
        $str = strtolower($str);
        $content = strtolower($content);
    }  
    return strpos($content,$str) ? true : false;
}
?>