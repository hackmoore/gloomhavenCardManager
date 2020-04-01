<?php
	// DEBUGGING
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);



	// Starter stuff
	require 'config.php';
	session_start();
	foreach (glob("classes/*.php") as $filename)
    	include $filename;
	

	// Check for stupid
	if( !isset($_GET['action']) ){
		die("Invalid action");
	}

	function returnJson($data, bool $success = true){
		header("Content-type: text/json");

		if( is_string($success) )
			print(json_encode(array('success'=>$success,'message'=>$data)));
		else
			print(json_encode(array_merge(array('success'=>$success), array('data'=>$data))));
		exit();
	}



	if( $_GET['action'] == "getSession" ){
		if( !isset($_GET['partyCode']) )
			returnJson("Invalid party code", false);

		returnJson(db_getSessionPlayers($_GET['partyCode']));


	}else if( $_GET['action'] == "join" ){
		if( !isset($_POST['partyCode']) )
			returnJson("Invalid party code", false);
		if( !isset($_POST['playerid']) )
			returnJson("Invalid player id", false);

		$players = db_getSessionPlayers($_POST['partyCode']);
		$player = false;
		foreach($players as $p){
			if( $p['id'] == $_POST['playerid'] ){
				$player = $p;
				break;
			}
		}
		if( !$player )
			returnJson("Player not in party or retired.", false);

		// Record the user to join the session
		$_SESSION['partyid'] = $player['sid'];
		$_SESSION['playerid'] = $player['id'];
		returnJson("Loading Session");
	}