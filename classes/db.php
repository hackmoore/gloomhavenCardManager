<?php
	$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_TBLE) or die("Failed to connect to DB!");


	function db_getSessionPlayers(string $partyCode){
		global $db;

		$result = $db->query("SELECT pl.id, pl.name, cl.ClassName, pa.id as sid
			FROM classes cl
				LEFT JOIN players pl ON pl.classid = cl.id
				LEFT JOIN parties pa ON pl.partyid = pa.id
			WHERE pa.code = '". $db->real_escape_string($partyCode) ."'
			AND pl.retired = 0;");
		return $result->fetch_all(MYSQLI_ASSOC);
	}