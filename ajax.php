<?php
	// Starter stuff
	require 'config.php';	

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

		$players = db::getSessionPlayers($_GET['partyCode']);
		returnJson($players, (count($players) > 0));


	}else if( $_GET['action'] == "join" ){
		if( !isset($_POST['partyCode']) )
			returnJson("Invalid party code", false);
		if( !isset($_POST['playerid']) )
			returnJson("Invalid player id", false);

		$players = db::getSessionPlayers($_POST['partyCode']);
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
		$_SESSION['player'] = db::getPlayer($_POST['playerid'])[0];
		$_SESSION['class'] = db::getClass($_SESSION['player']['classid'])[0];
		returnJson("Loading Session");

	}else if( $_GET['action'] == "getClassCards"){
		if( !isset($_GET['classid']) )
			returnJson("Invalid class id", false);

		$cards = db::getClassCards($_GET['classid']);
		returnJson($cards);
	}