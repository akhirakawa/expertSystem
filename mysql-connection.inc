<?php
// set certain php.ini variables to exclude server-side session error messages.
ini_set('session.bug_compat_42', 1);
ini_set('session.bug_compat_warn', 0);


 $db_connection = mysql_pconnect('', '', '');
 $db = mysql_select_db ('placement');
?>
