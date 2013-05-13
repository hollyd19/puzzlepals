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
				$correct_location=$_POST['correct_location']; 
				update_piece_location($piece_info, $db, $x, $y, $correct_location); 
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
			
			/*case "update_correct_location":
				$puzzle_id=$_POST['puzzle_name'];
				$piece_num=$_POST['piece_id'];
				$piece_info=array("puzzle_id"=>$puzzle_id, "piece_num"=>$piece_num); 
				$correct_location= $_POST['correct_location']; 
				update_correct_location($piece_info, $db, $correct_location);
				break;*/
			
			case "query_correct_location":
				$puzzle_id=$_POST['puzzle_name'];
				$array= query_correct_location($puzzle_id, $db);
				echo json_encode($array);
				break;
			
			case "check_pieces":
				$puzzle_id=$_POST['pID'];
				$array= get_updated_pieces($puzzle_id, $db);
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
		$time= time(); 
		$document= array("users"=>$users, "imageURL"=>$imageURL, "level"=>$level, "completed"=>"false", "time"=> $time, "havePLAYED"=>array());
		$collection->insert($document);
		return $document['_id']; 
	}
	
	function add_new_piece($puzzle_id, $piece_num, $img_url, $current_x, $current_y, $db){
		$collection_created= $db->piece;
		$puzzle_piece= array("puzzleID"=>$puzzle_id, "pieceNUMBER"=>$piece_num, "imgURL"=>$img_url, "x"=>$current_x, "y"=>$current_y, "correctLOCATION"=>"false", "status"=>"ready", "lastUPDATED"=>time());
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
		$new_data = array('$set' => array("x" => $new_x, "y"=>$new_y, "correctLOCATION"=>$correct_location, "lastUPDATED"=>time()));
		$cursor= $collection->update(array("puzzleID"=>$p_id, "pieceNUMBER"=>$p_num), $new_data);
		echo is_puzzle_completed($db, $p_id); 
	}
	
	function is_puzzle_completed($db, $puzzle_id){
		$collection=$db->piece;
		$number_right=$collection->find(array("puzzleID"=>$puzzle_id, "correctLOCATION"=>"true"));
		$total_number=$collection->find(array("puzzleID"=>$puzzle_id));
		if($number_right->count()==$total_number->count()){
			$new_data=array('$set'=>array("completed"=>"true", "time"=>time())); 
			$collection2=$db->puzzle;
			$puzzle_id=new MongoId($puzzle_id);
			$collection2->update(array("_id"=>$puzzle_id), $new_data);
			return true;
		}
		else return false;
	}
	
	
	function query_users($puzzle_id){
		$puzzle_id=new MongoId($puzzle_id);
		$db_info=connect_to_db();
		$db=$db_info['db_name'];
		$collection=$db->puzzle;
		$cursor=$collection->find(array("_id"=>$puzzle_id));
		$result=array();
		foreach($cursor as $document){
			array_push($result, $document["users"]);
		}
		return $result;
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
	
	function get_users_puzzle($user, $db, $state){
		$collection=$db->puzzle;
		$query= array("users"=>array('$in'=>array($user)), "completed"=>$state); 
		$cursor=$collection->find($query);
		$results= array();
		$a=0;
		$cursor->sort(array("time"=>-1));
		foreach($cursor as $document){
			$var=$document["users"];
			//var_dump($var);
			$user="";
			for($b=0; $b<sizeof($var); $b++){
				$user= $user . $var[$b] . ", ";
			}
			//echo $user;
			$results[$a] = array("level"=>$document['level'], "id"=> $document['_id'], "name"=>$document['imageURL'], "users"=>$user, "time"=>$document["time"], "havePLAYED"=>$document["havePLAYED"]);
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
	
	function query_puzzles($user, $state){
		$db_info=connect_to_db();
		$db=$db_info['db_name'];
		$result= get_users_puzzle($user, $db, $state);
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

	function time_elapsed($secs){
		$bit = array(
		'y' => $secs / 31556926 % 12,
		'w' => $secs / 604800 % 52,
		'd' => $secs / 86400 % 7,
		'h' => $secs / 3600 % 24,
		'm' => $secs / 60 % 60,
		);
        
		foreach($bit as $k => $v)
		    if($v > 0)$ret[] = $v . $k;
        
		return join(' ', $ret);
	}
	
	function get_location($puzzle_id, $peice_num, $db){
		$collection=$db->piece;
		$cursor= $collection->find(array("puzzleID"=>$puzzle_id, "pieceNUMBER"=>$peice_num));
		$location= $cursor["x"]."!@#$%".$cursor["y"];
		return $location; 
	}
	
	function get_updated_pieces($puzzle_id, $db){
		$collection=$db->piece;
		//$time= time();
		$new_time= time() - 2;
		//echo $puzzle_id;
		//echo " ". $new_time;
		$cursor= $collection->find(array("puzzleID"=>$puzzle_id, "lastUPDATED"=>array("\$gt"=>$new_time)));
		$result= array();
		foreach($cursor as $document){
			$doc_info= array();
			$doc_info['x']=$document['x'];
			$doc_info['y']=$document['y'];
			$doc_info['imgURL']=$document['imgURL'];
			$doc_info['lastUPDATED']=$document['lastUPDATED'];
			$doc_info['time']=$new_time;

			array_push($result, $doc_info);
		}
		//$return = $time . " " . $new_time;
		return $result;
	}
	
	function user_played($puzzle_id, $db, $user_id){
		$collection=$db->puzzle;
		$new_data = array('$addToSet' => array("havePLAYED" => $user_id));
		$puzzle_id=new MongoId($puzzle_id); 
		$collection->update(array("_id"=>$puzzle_id), $new_data);
	}
	

?>