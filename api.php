<?php
require 'connect.php';

$mode = $_GET['mode'];
if ($mode == 'query_leaked_password_by_userid') {
	$userid = $_GET['userid'];
	$results = query_leaked_password_by_userid($userid);
	echo json_encode($results);
} else if ($mode == 'query_leaked_password_by_domain') {
	$domain = $_GET['domain'];
	$results = query_leaked_password_by_domain($domain);
	echo json_encode($results);
} else if ($mode == 'get_db_stats') {
    $results = get_db_stats();
	echo json_encode($results);
}

?>
