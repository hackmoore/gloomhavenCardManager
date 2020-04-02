<?php
	// DEBUGGING
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	// DB Params
	define("DB_HOST", "localhost");
	define("DB_USER", "gloomhaven");
	define("DB_PASS", "VvnKKy8YBdKyoa7c");
	define("DB_TBLE", "gloomhaven");



	// Not config stuff
	session_start();
	foreach (glob("classes/*.php") as $filename)
		require_once($filename);