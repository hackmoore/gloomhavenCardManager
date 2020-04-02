<?php
	require 'config.php';
	$cards = db::getPlayerCards($_SESSION['player']['id']);

	if( count($cards) === 0 ){
		header("Location: selectDeck.php");
		exit();
	}
?>
<!DOCTYPE html> 
<html> 

<head> 
	<meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
	<title>Gloomhaven Hand Manager</title> 

	<!-- Stylesheets -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha256-rByPlHULObEjJ6XQxW/flG2r+22R5dKiAoef+aXWfik=" crossorigin="anonymous" />
	<style>
		.bootbox button[data-dismiss="modal"]{
			display: none;
		}
	</style>

	<!-- Scripts -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
</head>

<body>
	<div class="container h-100"> 
		<div class="d-flex h-100"> 
			<div class="align-self-center mx-auto"> 
				<button type="button" class="btn btn-success">Create Party</button>
				<button type="button" class="btn btn-primary" id="joinParty">Join Party</button>
			</div>
		</div> 
	</div> 

	<script>
	    
	</script>
</body> 

</html> 