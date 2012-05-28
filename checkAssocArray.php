<?php
error_reporting(E_ALL ^ E_NOTICE);

$var = array();

$var["abcd"] = "fred";
$var["efgh"] = "susan";

foreach ($var as $item => $value)
  {
    print ("key:  $item ==> value:  $value \n");
  }
$val = $var["empty"];
print ("key: empty  ==> value $val\n");

?>