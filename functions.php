<?php
	if(isset($_POST['function_name'])){
		$db_info=connect_to_db();
		$db="";
		if($db_info['connected']){
			$db=$db_info['db_name'];
		}
		else{
			return; 
		}
		switch ($_POST['function_name']){
			case "add_piece":
				$puzzle_id=$_POST['puzzle_name'];
				$piece_num=$_POST['piece_id'];
				$x= $_POST['x'];
				$y=$_POST['y'];
				$img_url="images/".$puzzle_id."/".$piece_num.".png";
				add_new_piece($puzzle_id, $piece_num, $img_url, $x, $y, $db);
				break;
			
			case "update_location":
				$puzzle_id=$_POST['puzzle_name'];
				$piece_num=$_POST['piece_id'];
				$piece_info=array("puzzle_id"=>$puzzle_id, "piece_num"=>$piece_num); 
				$x= $_POST['x'];
				$y=$_POST['y'];
				update_piece_location($piece_info, $db, $x, $y); 
				break;
			
			case "return_puzzle":
				$puzzle_name=$_POST['puzzle_name'];
				get_puzzle($puzzle_name, $db);
				echo $data; 
				break;
			
			case "delete_puzzle":
				$puzzle_name=$_POST['puzzle_name'];
				delete_puzzle($puzzle_name, $db);
				echo("success");
				break;
			
			case "drop_collection":
				drop_puzzles($db);
				echo "success";
				break;
			
			case "update_correct_location":
				$puzzle_id=$_POST['puzzle_name'];
				$piece_num=$_POST['piece_id'];
				$piece_info=array("puzzle_id"=>$puzzle_id, "piece_num"=>$piece_num); 
				$correct_location= $_POST['correct_location']; 
				update_correct_location($piece_info, $db, $correct_location);
				break;
			
			case "query_correct_location":
				$puzzle_id=$_POST['puzzle_name'];
				$array= query_correct_location($puzzle_id, $db);
				echo json_encode($array);
				break;
		}
	}

	function connect_to_db(){
		try{		
			$connection_url = getenv("MONGOHQ_URL");
			$m = new Mongo($connection_url);
			$url = parse_url($connection_url);
			$db_name = preg_replace('/\/(.*)/', '$1', $url['path']);
			$db= $m->selectDB($db_name); 
			$db_connect= array("connected"=>true, "db_name"=>$db);
			return $db_connect;
		}
		catch ( MongoConnectionException $e ) {
			die('Error connecting to MongoDB server');
		} catch ( MongoException $e ) {
			die('Mongo Error: ' . $e->getMessage());
		} catch ( Exception $e ) {
			 die('Error: ' . $e->getMessage());
		}	
	}

	function create_collection($collection_name, $db){
		$collection= $db->command(array("create"=>$collection_name));
	}
	

	function add_new_puzzle($users, $imageURL, $level, $db){
		$collection=$db->puzzle;
		$document= array("users"=>$users, "imageURL"=>$imageURL, "level"=>$level, "completed"=>"false");
		$collection->insert($document);
		return $document['_id']; 
	}
	
	function add_new_piece($puzzle_id, $piece_num, $img_url, $current_x, $current_y, $db){
		$collection_created= $db->piece;
		$puzzle_piece= array("puzzleID"=>$puzzle_id, "pieceNUMBER"=>$piece_num, "imgURL"=>$img_url, "x"=>$current_x, "y"=>$current_y, "correctLOCATION"=>"false", "status"=>"ready");
		$collection_created->insert($puzzle_piece);
		$piece_info=array("puzzle_id"=>$puzzle_id, "piece_num"=>$piece_num); 
		return $piece_info;
		
	}
	
	function query_piece($piece_info, $db){
		$collection= $db->piece;
		$p_id=$piece_info["puzzle_id"]."";
		$p_num=$piece_info["piece_num"]."";
		$cursor= $collection->find(array("puzzleID"=>$p_id, "pieceNUMBER"=>$p_num));
		return $cursor;	
		
	}
	
	function update_piece_location($piece_info, $db, $new_x, $new_y, $correct_location){
		$collection=$db->piece;
		$p_id=$piece_info["puzzle_id"]."";
		$p_num=$piece_info["piece_num"]."";
		$new_data = array('$set' => array("x" => $new_x, "y"=>$new_y));
		$cursor= $collection->update(array("puzzleID"=>$p_id, "pieceNUMBER"=>$p_num), $new_data);
		echo "<p> x: $new_x, y: $new_y </p>"; 
	}
	
	function update_correct_location($piece_info, $db, $correct_location){
		$collection=$db->piece;
		$p_id=$piece_info["puzzle_id"]."";
		$p_num=$piece_info["piece_num"]."";
		$new_data = array('$set' => array("correctLOCATION"=>$correct_location));
		$cursor= $collection->update(array("puzzleID"=>$p_id, "pieceNUMBER"=>$p_num), $new_data);
		echo "changed to ". $correct_location; 
	}
	
	function query_correct_location($puzzle_name, $db){
		$collection= $db->piece;
		$cursor=$collection->find(array("puzzleID"=>$puzzle_name));
		$result=array();
		foreach($cursor as $document){
			array_push($result, $document["correctLOCATION"]);
		}
		return $result;
	}
	
	function get_users_puzzle($user, $db){
		$collection=$db->puzzle;
		$query= array("users"=>array('$in'=>array($user))); 
		$cursor=$collection->find($query);
		$results= array();
		$a=0; 
		foreach($cursor as $document){
			$var=$document["users"];
			$user="";
			for($b=0; $b<sizeof($var); $b++){
				$url="http://graph.facebook.com/".$var[$b]; 
				$name= json_decode(file_get_contents($url))->name;
				if($b<sizeof($var)-1){
					$user+=$name . ", ";
				}
				else{
					$user+=$name; 
				}
			}
			
			$results[$a] = array("level"=>$document['level'], "id"=> $document['_id'], "name"=>$document['imageURL'], "users"=>$user);
			$a++; 
		}
		return $results; 
	}
	
	function get_puzzle($puzzle_name, $db){
		$collection= $db->piece;
		$cursor=$collection->find(array("puzzleID"=>$puzzle_name));
		foreach($cursor as $document){
			$var=explode("/", $document["imgURL"]);
			$a=$var[2];
			$var= explode(".", $a);
			
			echo "".$document["imgURL"]. "~!@#$%^&*";
			echo "".$document["x"]. "~!@#$%^&*";
			echo "".$document["y"]. "~!@#$%^&*";
			echo "". $var[0] ."~!@#$%^&*";
		}
	}
	
	function query_puzzles($user){
		$db_info=connect_to_db();
		$db=$db_info['db_name'];
		$result= get_users_puzzle($user, $db);
		return $result;
	}
	
	function drop_puzzles($db){
		$collection= $db->piece; 
		$response = $collection->drop();
		$collection=$db->puzzle;
		$response=$collection->drop(); 
	}
	
	function delete_puzzle($puzzle_id, $db){
		$collection = $db->piece;
		$collection->remove(array("puzzleID"=>$puzzle_id));
		$collection = $db->puzzle;
		$puzzle_id=new MongoId($puzzle_id); 
		$collection->remove(array("_id"=>$puzzle_id));
	}
	
	function update_puzzle_users($puzzle_id, $db, $new_user){
		$collection=$db->puzzle;
		$new_data = array('$addToSet' => array("users" => $new_user));
		$cursor= $collection->update(array("_id"=>$puzzle_id), $new_data);
	}


?>