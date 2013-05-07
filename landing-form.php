<?php
require("functions.php");

function sort_puzzles(){
	$in_progress_puzzles= query_puzzles();
	$easy=array();
	$medium=array();
	$hard=array();
	foreach ($in_progress_puzzles as $in_progress_puzzle=>$level){
		$puzzle= explode(".", $in_progress_puzzle);
		$images_name= $puzzle[0];
		$puzzle_size= $level;
		if ($puzzle_size=="9"){
			array_push($easy, $images_name);
		} elseif($puzzle_size=="25"){
			array_push($medium, $images_name);
		} elseif($puzzle_size=="49"){
			array_push($hard, $images_name);
		}
	}
	return array($easy, $medium, $hard);
}

list($easy, $medium, $hard)= sort_puzzles();

require("views/landing_header.php");
require("views/landing_form_view.php");
require("views/footer.php");

?>