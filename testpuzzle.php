<?php
function make_puzzle_from_pic($image_url, $puzzle_size, $puzzle_name){
        $image_size= getimagesize($image_url);
	
        $width_of_piece= $image_size[0]/sqrt($puzzle_size);
        $height_of_piece= $image_size[1]/sqrt($puzzle_size);
        $org_img = imagecreatefrompng($image_url);
        
        for ($i=0; $i<sqrt($puzzle_size);$i++) {
            for ($k=0; $k<sqrt($puzzle_size); $k++){
                $img = imagecreatetruecolor(''.$width_of_piece,''.$height_of_piece);
                mkdir("images/".$puzzle_name);
                $dest_image= "images/".$puzzle_name.'/'.$k.$i.".png";
                imagecopy($img,$org_img, 0, 0, $i*$width_of_piece, $k*$height_of_piece, $image_size[0], $image_size[1]);
                imagepng($img,$dest_image);
                imagedestroy($img);
                $images[$i][$k]= $dest_image;
            }
        }
        $return= array($images, $width_of_piece, $height_of_piece);
        return $return;
}

if (isset($_POST['create'])){
	$image_url = $_POST["picture"];
	$puzzle_size = $_POST["puzzle_size"];
	
	$imgexp = explode("/", $image_url);
	$img_w_ext = explode(".", $imgexp[2]);
	$puzzle_name = $img_w_ext[0]. "_" . $_POST['puzzle_size'];
	list($images, $width, $height)= make_puzzle_from_pic($image_url, $puzzle_size, $puzzle_name);
	
    
    require("views/header.php");
    
    $puzzle_string=""; 
      include("functions.php");
    $db_info=connect_to_db();
    if($db_info['connected']){
	$db=$db_info['db_name'];
	create_collection("puzzle", $db);
	$users = explode (',', $_POST['invited_users_id']);
	array_push($users, $_POST['id']);
	//var_dump($users);
	$puzzle_id=add_new_puzzle($users, $image_url, $puzzle_size, $db);
	user_played($puzzle_id, $db, $_POST['id']);
	$puzzle_string=$puzzle_id."";
	?>
	
	<input type="hidden" name="p-id" value="<?php echo $puzzle_string; ?>"/>
<?php    }
  
}

if($_POST['in_prog_puzzle']!=""){
    require("functions.php");
    $user_id=$_POST['user_id'];
    $puzzle_id= $_POST['in_prog_puzzle'];

	/*$db_info=connect_to_db();
    if($db_info['connected']){
	$db=$db_info['db_name'];
	echo $user_id; 
	user_played($puzzle_id, $db, $user_id);
    }*/
    $temp= explode('_', $puzzle_id);
    $image_url= $temp[2].".png";
    $puzzle_id=$temp[0];
    //echo $puzzle_id;
    $users= query_users($puzzle_id);
    $users=$users[0];
    $num_pieces= $temp[1];
    
     list($images, $width, $height)= make_puzzle_from_pic($image_url, $num_pieces, $puzzle_id);
    
    require("views/old_puzzle_header.php");
    
    	echo '<input type="hidden" name="p-id" value="'.$puzzle_id.'"/>';

    
}

    
    //var_dump($users);
    require("views/view.php");
    require("views/footer.php");
    
?>

