   <div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          window.location = window.location;
        });

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
      }
   </script>

<div class="row-fluid">
	<div class="span12 container" id="encapsulating_div">
	<h1 id="puzzle_center_header">Puzzle Center</h1>
	
	<div id="frozen_div">
		<div class="span3 landing_section" id="create_puz_div">
		<h3 id="new_puzzle_header">Create A New Puzzle</h3>
			<div class="row-fluid">
			<form action="testpuzzle.php" method="post">

			<script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>

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
					echo '<input type="hidden" name="invited_users_id" id="invited_users_id"/>';
					echo '<br/>';
					echo '</div>';
					
					echo '<div class="span5" id="invite_friends_div"><br/>';
					echo '<a href="#" class="apprequests" id="sendRequest" data-message="Come play Puzzle Pals!">';
					echo '<span class="btn btn-primary apprequests">3. Invite Friends</span></a></div>'; 
				
				echo '<div class="span5" id="create_button_div"><br/><input class="btn btn-primary" type="submit" value="4. Create & Go!" name="create"></div></div>';
				?>
		
		</form>
	</div>
	
	<div class="span6 landing_section" id="ongoing_puz_div">
	<h3 id="existing_puzzles">Ongoing Puzzles</h3>

		<div class="row-fluid">
		<form action="testpuzzle.php" method="post">
			<input type="hidden" name="in_prog_puzzle"/>

		<div class="span12">
		<div class="span3 scrollable_div" id="easy_section">
		<h4 class="instructions">Easy</h4>
		<?php
			for($a=0; $a<sizeof($easy); $a++){
				$var= $easy[$a];
				$string_of_players="";
				foreach($var['users'] as $player1){
					$string_of_players= $string_of_players + $player1 + "<br/>";
				}
				$now = time(); // or your date as well
				$your_date = $var["time"];
				$datediff = $now - $your_date;
				$datediff= floor($datediff/(60*60*24));
				echo '<br/><img src ="'.$var["name"].'.png " class="exist_puz_photos img-polariod" alt="' . $var["name"] . '" /><br/><p class="time_elapsed">Time Elapsed: '.$datediff.' days</p><p class="participating_friends">Puzzle Players: '.$string_of_players.'</p><button class="resume_puzzle btn btn-mini" type="submit" name="' . $var["id"] .'_9_'.$var["name"].'"><i class="icon-repeat"></i> Resume</button><button class="start_puzzle_over btn btn-mini" type="submit" name="' . $var["id"] . '"/><i class="icon-remove"></i> Give Up</button><br/>';
	
			}
			echo '</div>';
			
		echo '<div class="span3 scrollable_div" id="medium_section"><h4 class="instructions">Medium</h4>';
			for($a=0; $a<sizeof($medium); $a++){
				$var= $medium[$a];
				$string_of_players="";
				foreach($var['users'] as $player1){
					$string_of_players= $string_of_players + $player1 + "<br/>";
				}
				$now = time(); // or your date as well
				$your_date = $var["time"];
				$datediff = $now - $your_date;
				$datediff= floor($datediff/(60*60*24));
				echo '<br/><img src ="'.$var["name"].'.png " class="exist_puz_photos img-polariod" alt="' . $var["name"] . '" /><br/><p class="time_elapsed">Time Elapsed: '.$datediff.' days</p><p class="participating_friends">Puzzle Players: '.$string_of_players.'</p><button class="resume_puzzle btn btn-mini" type="submit" name="' . $var["id"] .'_25_'.$var["name"].'""><i class="icon-repeat"></i> Resume</button><button class="start_puzzle_over btn btn-mini" type="submit" name="' . $var["id"] . '"/><i class="icon-remove"></i> Give Up</button><br/>';

			}
		echo '</div>';
		
		echo '<div class="span3 scrollable_div" id="hard_section"><h4 class="instructions">Hard</h4>';
			for($a=0; $a<sizeof($hard); $a++){
				$var= $hard[$a];
				$string_of_players="";
				foreach($var['users'] as $player1){
					$string_of_players= $string_of_players + $player1 + "<br/>";
				}
				$now = time(); // or your date as well
				$your_date = $var["time"];
				$datediff = $now - $your_date;
				$datediff= floor($datediff/(60*60*24));
				echo '<br/><img src ="'.$var["name"].'.png " class="exist_puz_photos img-polariod" alt="' . $var["name"] . '" /><br/><p class="time_elapsed">Time Elapsed: '.$datediff.' days</p><p class="participating_friends">Puzzle Players: '.$string_of_players.'</p><button class="resume_puzzle btn btn-mini" type="submit" name="' . $var["id"] .'_49_'.$var["name"].'"><i class="icon-repeat"></i> Resume</button><button class="start_puzzle_over btn btn-mini" type="submit" name="' . $var["id"] . '"/><i class="icon-remove"></i> Give Up</button><br/>';

			}
		echo '</div></form></div>';
		echo '<div class="row-fluid"><div class="span12"><input id="delete_button" class="btn btn-danger" type="submit" name="delete_all" value="Delete All Puzzles"/></div></div>';
			?>
</div>
</div>	
</div>