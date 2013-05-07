<?php
require("functions.php");

function sort_puzzles($user){
	$in_progress_puzzles= query_puzzles($user);
	$easy=array();
	$medium=array();
	$hard=array();
	foreach ($in_progress_puzzles as $in_progress_puzzle=>$info){
		$puzzle= explode(".", $in_progress_puzzle);
		$images_name= $puzzle[0];
		$puzzle_size= $info(0);
		if ($puzzle_size=="9"){
			array_push($easy, array($images_name, $info[1]));
		} elseif($puzzle_size=="25"){
			array_push($medium, array($images_name, $info[1]));
		} elseif($puzzle_size=="49"){
			array_push($hard, array($images_name, $info[1]));
		}
	}
	return array($easy, $medium, $hard);
}

list($easy, $medium, $hard)= sort_puzzles($user);

require("views/landing_header.php");
require("views/landing_form_view.php");
require("views/footer.php");

?>