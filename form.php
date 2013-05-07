<!DOCTYPE html>
<?php
	$db="";
	function connect_to_db(){
		try{
			$connection_url = getenv("MONGOHQ_URL");
			$m = new Mongo($connection_url);
			$url = parse_url($connection_url);
			$db_name = preg_replace('/\/(.*)/', '$1', $url['path']);
			$db= $m->selectDB($db_name); 

			echo '<p> Connection Achieved </p>';
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
	
	//test functions
	function create_collection($collection_name, $db){
		$collection= $db->command(array("create"=>$collection_name));
		echo "<p>Collection Created!</p>";
	}
	
	
	function create_document($title, $age, $collection, $db){
		$collection_created= $db->$collection;
		$document= array("title"=>$title, "age"=>$age);
		$collection_created->insert($document);
		echo "<p>Document Created!</p>";
	}
	
	function print_collection_items($collection, $db){
		$collection_queried=$db->$collection; 
		$cursor= $collection_queried->find();
		foreach($cursor as $document){
			echo "<p>title: ".$document["title"]."</p>";
			echo "<p>age: ".$document["age"]."</p>";
		}
		
	}
	
	/*puzzle functions*/

	function query_puzzles(){
		$db_info=connect_to_db();
		$collection=$db_info['db_name']->piece;
		$cursor=$collection->find();
		$result=array();
		foreach($cursor as $doc){
			if (!in_array($doc['puzzleID'], $result) && preg_match('/_\d/', $doc['puzzleID'])) {
				$result[]=$doc['puzzleID'];
			}
		}
		return $result;
	}
	
	function add_new_piece($puzzle_id, $piece_num, $img_url, $current_x, $current_y, $user_one, $user_two, $db){
		$collection_created= $db->piece;
		$puzzle_piece= array("puzzleID"=>$puzzle_id, "pieceNUMBER"=>$piece_num, "imgURL"=>$img_url, "x"=>$current_x, "y"=>$current_y, "users"=>array($user_one, $user_two));
		$collection_created->insert($puzzle_piece);
		echo "<p>Piece Created!</p>";
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
	
	function update_piece_location($piece_info, $db, $new_x, $new_y){
		$collection=$db->piece;
		$p_id=$piece_info["puzzle_id"]."";
		$p_num=$piece_info["piece_num"]."";
		$new_data = array('$set' => array("x" => $new_x, "y"=>$new_y));
		$cursor= $collection->update(array("puzzleID"=>$p_id, "pieceNUMBER"=>$p_num), $new_data);
		
	}
	
	function update_piece_users($piece_info, $db, $new_user){
		$collection=$db->piece;
		$p_id=$piece_info["puzzle_id"]."";
		$p_num=$piece_info["piece_num"]."";
		$new_data = array('$addToSet' => array("users" => $new_user));
		$cursor= $collection->update(array("puzzleID"=>$p_id, "pieceNUMBER"=>$p_num), $new_data);
	}
	
	
?>
<head>
	<title>ATTEMPTING TO WRITE USABLE FUNCTIONS</title>
</head>
<body>
	<h1>I HOPE THESE FUNCTIONS ARE USEABLE</h1>
	<p><a href="index.php">Back to Homepage</a></p>
	<form method="post" action="form.php">
		<label>Collection Name: </label><input type="text" name="collectionName"/>
		<label>Title: </label><input type="text" name="title"/>
		<label>Age: </label><input type="text" name="age"/>
		<input type="submit" value="Add Document" name="add"/>
	</form>
	<?php
		echo "var_dump of all the (valid) puzzles in the database so far.";
		$result= query_puzzles();
		var_dump($result);
	
	?>
	<h2> Create Puzzle Piece</h2>
	<form method="post" action="form.php">
		<label>Piece Number: </label><input type="text" name="p_num"/>
		<label>Puzzle Id:</label><input type="text" name="puzzle_id"/>
		<label>User One: </label><input type="text" name="user_one"/>
		<label>User Two:</label><input type="text" name="user_two"/>
		<label>Current X Location: </label><input type="text" name="x_current"/>
		<label>Current Y Location: </label><input type="text" name="y_current"/>
		<label>Image Url</label><input type="text" name="img_url"/>
		<input type="submit" value="Add Puzzle" name="add_puzzle"/>
	</form>
	
	<form method="post" action="form.php">
		<?php $db_info=connect_to_db();
			$collection=$db_info['db_name']->piece;
			$cursor=$collection->find();
		?>
		<label>Puzzle ID: </label><select name="puzzle"><?php foreach($cursor as $document){ echo "<option>".$document["puzzleID"]."</option>";} ?></select>
		<label>Piece Number: </label><select name="number"><?php foreach($cursor as $document){ echo "<option>".$document["pieceNUMBER"]."</option>";} ?></select>
		<label>New X</label><input type="text" name="x"/>
		<label>New Y</label><input type="text" name="y"/>
		<input type="submit" value="update location" name="location"/>
	</form>
	
	<form method="post" action="form.php">
		<?php $db_info=connect_to_db();
			$collection=$db_info['db_name']->piece;
			$cursor=$collection->find();
		?>
		<label>Puzzle ID: </label><select name="puzzle"><?php foreach($cursor as $document){ echo "<option>".$document["puzzleID"]."</option>";} ?></select>
		<label>Piece Number: </label><select name="number"><?php foreach($cursor as $document){ echo "<option>".$document["pieceNUMBER"]."</option>";} ?></select>
		<label>New User</label><input type="text" name="new_user"/>
		<input type="submit" value="add user" name="user"/>
	</form>
	
	
	<?php
		if(isset($_POST['add_puzzle'])){
			$piece_num=$_POST['p_num'];
			$puzzle_id=$_POST['puzzle_id'];
			$user_one=$_POST['user_one'];
			$user_two= $_POST['user_two'];
			$current_x=$_POST['x_current'];
			$current_y=$_POST['y_current'];
			$img_url=$_POST['img_url'];
			
			
			$db_info= connect_to_db();
			if($db_info['connected']){
				$db= $db_info['db_name'];
				create_collection("piece", $db);
				$piece_info=add_new_piece($puzzle_id, $piece_num, $img_url, $current_x, $current_y, $user_one, $user_two, $db); 
				$cursor= query_piece($piece_info, $db);
				echo "<ul>";
				foreach($cursor as $document){
					$var= $document["users"];
					echo "<li> ".$document["imgURL"]. "</li>";
					echo "<li> $var[0] and $var[1] </li>";
					echo "<li> ".$document["pieceNUMBER"]. "</li>";
					echo "<li> ".$document["puzzleID"]. "</li>";


				}
				echo "</ul>";
			}	
			
		}
	?>
	<?php
	if(isset($_POST['location'])){
			$piece_num=$_POST['number'];
			$puzzle_id=$_POST['puzzle'];
			$current_x=$_POST['x'];
			$current_y=$_POST['y'];
			
			$db_info= connect_to_db();
			if($db_info['connected']){
				$db= $db_info['db_name'];
				$piece_info=array("puzzle_id"=>$puzzle_id, "piece_num"=>$piece_num); 
				$cursor= query_piece($piece_info, $db);
				echo "<h3>New Location </h3>"; 
				echo "<ul>";
				foreach($cursor as $document){
					$var= $document["users"];
					echo "<li> ".$document["imgURL"]. "</li>";
					echo "<li> $var[0] and $var[1] </li>";
					echo "<li> ".$document["pieceNUMBER"]. "</li>";
					echo "<li> ".$document["puzzleID"]. "</li>";
					echo "<li> X: ".$document["x"]. "</li>"; 
					echo "<li> Y: ".$document["y"]. "</li>"; 
				}
				echo "</ul>";
				update_piece_location($piece_info, $db, $current_x, $current_y);
				echo "<h3>New Location</h3>"; 
				$cursor= query_piece($piece_info, $db);
				echo "<ul>";
				foreach($cursor as $document){
					$var= $document["users"];
					echo "<li> ".$document["imgURL"]. "</li>";
					echo "<li> $var[0] and $var[1] </li>";
					echo "<li> ".$document["pieceNUMBER"]. "</li>";
					echo "<li> ".$document["puzzleID"]. "</li>";
					echo "<li> X: ".$document["x"]. "</li>"; 
					echo "<li> Y: ".$document["y"]. "</li>"; 
				}
				echo "</ul>";
			}	
			
		}
	?>
	
	<?php
	if(isset($_POST['user'])){
			$piece_num=$_POST['number'];
			$puzzle_id=$_POST['puzzle'];
			$new_user=$_POST['new_user'];
			
			$db_info= connect_to_db();
			if($db_info['connected']){
				$db= $db_info['db_name'];
				$piece_info=array("puzzle_id"=>$puzzle_id, "piece_num"=>$piece_num); 
				$cursor= query_piece($piece_info, $db);
				echo "<h3>New Location </h3>"; 
				echo "<ul>";
				foreach($cursor as $document){
					$var= $document["users"];
					echo "<li> ".$document["imgURL"]. "</li>";
					echo "<li> ";
					foreach($var as $item){
						echo $item;
						echo ", "; 
					}
					echo "</li>";
					echo "<li> ".$document["pieceNUMBER"]. "</li>";
					echo "<li> ".$document["puzzleID"]. "</li>";
					echo "<li> X: ".$document["x"]. "</li>"; 
					echo "<li> Y: ".$document["y"]. "</li>"; 
				}
				echo "</ul>";
				update_piece_users($piece_info, $db, $new_user);
				echo "<h3>New Location</h3>"; 
				$cursor= query_piece($piece_info, $db);
				echo "<ul>";
				foreach($cursor as $document){
					$var= $document["users"];
					echo "<li> ".$document["imgURL"]. "</li>";
					echo "<li> ";
					foreach($var as $item){
						echo $item;
						echo ", "; 
					}
					echo "</li>";
					echo "<li> ".$document["pieceNUMBER"]. "</li>";
					echo "<li> ".$document["puzzleID"]. "</li>";
					echo "<li> X: ".$document["x"]. "</li>"; 
					echo "<li> Y: ".$document["y"]. "</li>"; 
				}
				echo "</ul>";
			}	
			
		}
	?>
	
	<?php
		if(isset($_POST['add'])){
			$new_collection=$_POST['collectionName'];
			$new_title=$_POST['title'];
			$new_age=$_POST['age'];
			$db_info=connect_to_db();
			
			if($db_info['connected']){
				$db= $db_info['db_name'];
				echo "<p>Database: $db</p>";
				create_collection($new_collection, $db);
				create_document($new_title, $new_age, $new_collection, $db);
				print_collection_items($new_collection, $db); 				
			}
		}
	?>
</body>
</html>