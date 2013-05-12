<!-- this depends on jquery and jquery ui so make sure to include them in the head -->
<div class="content">
     <h5><a href= "index.php">Return to Puzzle Center</a></h5>

	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	
        <div id="other_players">
          <?php
               foreach($users as $user){
                    $user_name=json_decode(file_get_contents("http://graph.facebook.com/".$user))->name;
                    echo "<div class='other_user'><img src='http://graph.facebook.com/".$user."/picture?type=normal' alt='picture'/><br/>";
                    echo "<p>".$user_name."</p></div>";
               }
          ?>
        </div>
        
	 
    <div id="box">
     <?php
     if (isset($_POST['create'])){
          foreach($images as $index=>$array){
               foreach($array as $first=>$second){
                    echo '<div class="piece" data-number="'.$first.$index.'">';
                    echo '<img src="'.$second.'" alt="Puzzle Piece '.$first.$index.'"/>';
                    echo '</div>';
               }
          }
     ?>
     
     <div id="puzzle">
     <?php
          foreach($images as $index=>$array){
               foreach($array as $first=>$second){
                    echo '<div id="place-'.$index.$first.'" class="place" data-position="'.$index.$first.'"></div>';
               }
          }
     }
     if($_POST['in_prog_puzzle']!=""){
          foreach($images as $index=>$array){
               foreach($array as $first=>$second){
                    echo '<div class="piece" data-number="'.$first.$index.'">';
                    echo '<img src="'.$second.'" alt="Puzzle Piece '.$first.$index.'"/>';
                    echo '</div>';
               }
          }
     ?>
     
     <div id="puzzle">
     <?php
          foreach($images as $index=>$array){
               foreach($array as $first=>$second){
                    echo '<div id="place-'.$index.$first.'" class="place" data-position="'.$index.$first.'"><p id="'.$index.$first.'">'.$index.$first.'</p></div>';
	       }
          }
     }
     ?>
    </div>
     
</div>