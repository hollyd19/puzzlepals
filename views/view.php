<!-- this depends on jquery and jquery ui so make sure to include them in the head -->
<div id="popup_box">
          <h1>CONGRATULATIONS!<br/><br/><br/>YOU WON THE GAME!</h1>
          <a id="popupBoxClose" href="index.php">Return Home</a>    
     </div>
<div class="content">
     <h5 id="back_home"><a class="btn btn-medium" href= "index.php">Return to Puzzle Center</a></h5>
     
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	
        <div id="other_players">
		<h3>Puzzle Collaborators</h3>
          <?php
               foreach($users as $index=>$user){
                    $user_name=json_decode(file_get_contents("http://graph.facebook.com/".$user))->name;
                    echo "<div class='other_user'><img src='http://graph.facebook.com/".$user."/picture?type=normal' alt='" . $user_name . "' title='" . $user_name . "'/>";
                    //echo "<p class='player_name'>".$user_name."</p>";
					echo"</div>";
               }
          ?>
        </div>
        
	 
    <div id="box">
     <?php
     if (isset($_POST['create'])){
          foreach($images as $index=>$array){
               foreach($array as $first=>$second){
                    echo '<div class="piece" data-number="'.$first.$index.'">';
                    echo '<img src="'.$second.'" alt="Puzzle Piece '.$first.$index.'" id="'.$first.$index.'"/>';
                    echo '</div>';
               }
          }
     ?>
     
     <div id="puzzle">
     <?php
          foreach($images as $index=>$array){
               foreach($array as $first=>$second){
                    echo '<div id="place-'.$index.$first.'" class="place" data-position="'.$index.$first.'"><p class="location" id="place'.$index.$first.'">'.$index.$first.'</p></div>';
               }
          }
     }
     if($_POST['in_prog_puzzle']!=""){
          foreach($images as $index=>$array){
               foreach($array as $first=>$second){
                    echo '<div class="piece" data-number="'.$first.$index.'">';
                    echo '<img src="'.$second.'" alt="Puzzle Piece '.$first.$index.'" id="'.$first.$index.'"/>';
                    echo '</div>';
               }
          }
     ?>
     
     <div id="puzzle">
     <?php
          foreach($images as $index=>$array){
               foreach($array as $first=>$second){
                    echo '<div id="place-'.$index.$first.'" class="place" data-position="'.$index.$first.'"><p class="location" id="place'.$index.$first.'">'.$index.$first.'</p></div>';
	       }
          }
     }
     ?>
    </div>
     
</div>