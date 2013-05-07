<!-- this depends on jquery and jquery ui so make sure to include them in the head -->
<div class="content">
     <h5><a href= "index.php">Go back to home</a></h5>

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