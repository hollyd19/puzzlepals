<!-- this depends on jquery and jquery ui so make sure to include them in the head -->
<div class="content">
     <h5><a href= "landing-form.php">Go back to home</a></h5>

	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	<p>
		<input type="button" class="btn btn-primary" onclick="sendRequestToRecipients(); return false;" value="Send Request to Users Directly"/>
		<input type="text" value="User ID" name="user_ids" />
	</p>
	<p>
		<input type="button" class="btn btn-primary" onclick="Send Request to Many Users with MFS"/>
	</p>
	<script>';
		FB.init({
			appID : "311774552286219", 
			frictionlessRequests: true
		});
	</script>'
	 
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
                    echo '<div id="place-'.$index.$first.'" class="place" data-position="'.$index.$first.'"></div>';
               }
          }
     }
     ?>
    </div>
     
</div>