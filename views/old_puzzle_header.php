<!DOCTYPE html>
<html>
  <head>
    <title>Puzzle Pals</title>
	
	<!-- Bootstrap -->
    <!-- <link href="../stylesheets/bootstrap.min.css" rel="stylesheet" media="screen"> -->
	
	<link href="stylesheets/basic-css.css" rel="stylesheet" type="text/css"/> 
	<!-- <script src="../javascript/bootstrap.min.js"></script> -->
    <script type="text/javascript" src="javascript/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui.js"></script>
	<!--<script type="text/javascript" src="javascript/jquery.slimscroll.min.js"></script>-->
    <script type="text/javascript" src="javascript/javascript-dragndrop.js"></script>
	
	
  </head>
  <body onload="get_pieces('<?php echo $puzzle_id; ?>'); size_places(<?php echo $width.", ". $height.", ".sqrt($num_pieces);?>); setInterval(function(){check_pieces();}, 1000);">
