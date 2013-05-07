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
                //need to send info to database to save puzzle pieces and their destinations
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
    
}

if($_POST['in_prog_puzzle']!=""){
    require("functions.php");
    
    $puzzle_id= $_POST['in_prog_puzzle'];
    $temp= explode('_', $puzzle_id);
    $image_url= "images/puzzle-photos/".$temp[0].".png";
    
    $num_pieces= $temp[1];
    
     list($images, $width, $height)= make_puzzle_from_pic($image_url, $num_pieces, $puzzle_id);
    
    require("views/old_puzzle_header.php");
    
}

    
    
    require("views/view.php");
    require("views/footer.php");
    
?>
