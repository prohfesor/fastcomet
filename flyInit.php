<?php 

	require_once('flyEnv.php');

	require_once('flySession.php');
	
	require_once('flyDb.php');
	$db = flyDb::getInstance();
	$db->connect("localhost", "root", "", "biedcenter");
?>