<?php

//display errors: http://stackoverflow.com/questions/1053424/how-do-i-get-php-errors-to-display
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$link = null;

function db_connect() {
	GLOBAL $link;

	if ($link != null) $link->close();
	// Connecting, selecting database
	$link = new mysqli('127.0.0.1', 'root', '', 'vericlouds_replica');
	if ($link->connect_errno) {
		echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
		exit;
	}
	
    return $link;
}
db_connect();

function query_leaked_password_by_userid($userid) {
	GLOBAL $link;
	
	$clean_userid = strtolower(mysqli_real_escape_string($link,$userid));
	$link->query('SET CHARACTER SET utf8');  //solve the bug which return null when calling json_encode. more details see http://stackoverflow.com/questions/1972006/json-encode-is-returning-null

	$passwords = array();
	$query = 'SELECT * FROM vericlouds_replica.leaked_accounts_hashed WHERE email_reverse=REVERSE("' . $clean_userid . '");';
	//echo $query;
	$result = $link->query($query);
	while ($line = $result->fetch_assoc()) {
		$passwords[] = array('email'=>$clean_userid,'pass_b'=>$line['pass_b'],'pass_bm'=>$line['pass_bm']);
    }
	return array('result'=>'succeeded','records'=>$passwords);
}

function query_leaked_password_by_domain($domain) {
	GLOBAL $link;
	
	$clean_domain = strtolower(mysqli_real_escape_string($link,$domain));
	$clean_domain_rev = strrev($clean_domain);
	$link->query('SET CHARACTER SET utf8');  //solve the bug which return null when calling json_encode. more details see http://stackoverflow.com/questions/1972006/json-encode-is-returning-null

	$passwords = array();
	$query = 'SELECT * FROM vericlouds_replica.leaked_accounts_hashed WHERE email_reverse LIKE "' . $clean_domain_rev . '@%";';
	//echo $query;
	$result = $link->query($query);
	while ($line = $result->fetch_assoc()) {
        $email = strrev($line['email_reverse']);
        $passwords[] = array('email'=>$email,'pass_b'=>$line['pass_b'],'pass_bm'=>$line['pass_bm']);
	}
	
	return array('result'=>'succeeded','records'=>$passwords);
}

function get_db_stats() {
	GLOBAL $link;

    $query = 'SELECT COUNT(*) AS total FROM vericlouds_replica.leaked_accounts_hashed;';
	//echo $query;
	$result = $link->query($query);
    $stats = array();
	if ($line = $result->fetch_assoc()) {
		$stats['total'] = $line['total'];
    } else {
        $stats['total'] = -1;
    }
	return array('result'=>'succeeded','stats'=>$stats);
}

?>

