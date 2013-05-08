<div class="row-fluid">
	<div class="span12 container" id="encapsulating_div">
	<h1 id="puzzle_center_header">Puzzle Center</h1>
	<div id="frozen_div">
		<div class="span3 landing_section" id="create_puz_div">
		<h3 id="new_puzzle_header">Create A New Puzzle</h3>
			<div class="row-fluid">
			<form action="testpuzzle.php" method="post">
				
				<div class="span4" id="pick_a_photo">
				<h4 class="instructions">1. Pick a Photo</h4>
					<?php
						$img = null; 
						
						foreach(glob('images/puzzle-photos/*.png') as $img) {
							$temp_img= explode("/", $img);
							$name= explode(".", $temp_img[2]);
							if (!in_array($name[0], $easy) && !in_array($name[0], $medium) && !in_array($name[0], $hard)){
								echo '<input type="radio" name="picture" value="' . $img . '" checked><img src ="' . $img .' " class="puz_photo_choices img-polaroid" alt="' . $img . '" /><br/>';
							}
						}
					echo '</div>';
					echo '<div class="span4" id="select_difficulty">';
					echo '<h4 class="instructions">2. Select Difficulty</h4>';
					echo '<input type="radio" name="puzzle_size" value="9" checked>Easy<br/>';
					echo '<input type="radio" name="puzzle_size" value="25">Medium<br/>';
					echo '<input type="radio" name="puzzle_size" value="49">Hard<br/>';
					echo '<input type="hidden" name="id" value="'.$user_id.'"/>';
					echo '<br/>';
					echo '</div>';
				
				echo '<div class="span5" id="create_button_div"></br><input class="btn btn-primary" type="submit" value="3. Create" name="create"></div></div>';
				?>
		
		</form>
	</div>
	
	<div class="span6 landing_section" id="ongoing_puz_div">
	<h3 id="existing_puzzles">Ongoing Puzzles</h3>

		<div class="row-fluid">
		<form action="testpuzzle.php" method="post">
			<input type="hidden" name="in_prog_puzzle"/>
			<input type="hidden" name="id"/>
		<div class="span12">
		<div class="span3 scrollable_div" id="easy_section">
		<h4 class="instructions">Easy</h4>
		<?php
			echo "easy".$easy;
			for($a=0; $a<sizeof($easy); $a++){
				echo $easy[a];
			}
			foreach($easy as $easy_puzzle){
				echo $easy_puzzle[0]. "......";
				echo $easy_puzzle[1]; 
			
				echo '<br/><img src ="images/puzzle-photos/' . $easy_puzzle.'.png " class="exist_puz_photos img-polariod" alt="' . $easy_puzzle . '" /><br/><p class="time_elapsed">Time Elapsed: xxx days</p><p class="participating_friends">Friends: Nicole, Taylor</p><button class="resume_puzzle btn btn-mini" type="submit" name="' . $easy_puzzle . '_9--'.$easy_puzzle[1].'"><i class="icon-repeat"></i> Resume</button><button class="start_puzzle_over btn btn-mini" type="submit" name="' . $easy_puzzle . '_9"/><i class="icon-remove"></i> Give Up</button><br/>';
				
			}
			echo '</div>';
			
		echo '<div class="span3 scrollable_div" id="medium_section"><h4 class="instructions">Medium</h4>';
			foreach($medium as $medium_puzzle){
				echo '<br/><img src ="images/puzzle-photos/' . $medium_puzzle .'.png " class="exist_puz_photos img-polariod" alt="' . $medium_puzzle . '" /><br/><p class="time_elapsed">Time Elapsed: xxx days</p><p class="participating_friends">Friends: Nicole, Taylor</p><button class="resume_puzzle btn btn-mini" type="submit" name="' . $medium_puzzle . '_25--'.$easy_puzzle[1].'"><i class="icon-repeat"></i> Resume</button><button class="start_puzzle_over btn btn-mini" type="submit" name="' . $medium_puzzle . '_25"/><i class="icon-remove"></i> Give Up</button><br/>';
			}
		echo '</div>';
		
		echo '<div class="span3 scrollable_div" id="hard_section"><h4 class="instructions">Hard</h4>';
			foreach($hard as $hard_puzzle){
				echo '<br/><img src ="images/puzzle-photos/' . $hard_puzzle .'.png " class="exist_puz_photos img-polariod" alt="' . $hard_puzzle. '" /><br/><p class="time_elapsed">Time Elapsed: xxx days</p><p class="participating_friends">Friends: Nicole, Taylor</p><button class="resume_puzzle btn btn-mini" type="submit" name="' . $hard_puzzle . '_49--'.$easy_puzzle[1].'"><i class="icon-repeat"></i> Resume</button><button class="start_puzzle_over btn btn-mini" type="submit" name="' . $hard_puzzle . '_49"/><i class="icon-remove"></i> Give Up</button><br/><br/>';
			}
		echo '</div></form></div>';
		echo '<div class="row-fluid"><div class="span12"><input id="delete_button" class="btn btn-danger" type="submit" name="delete_all" value="Delete All Puzzles"/></div></div>';
			?>
</div>
</div>	
</div>