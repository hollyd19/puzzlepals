<!DOCTYPE html>
<head>
	<title>ATTEMPTING TO CONNECT TO DATABASES</title>
</head>
<body>
<h1>Where Dreams Come to Die</h1>
<?php  
try {
    // connect to MongoHQ assuming your MONGOHQ_URL environment
    // variable contains the connection string
    $connection_url = getenv("MONGOHQ_URL");
 	$m = new Mongo($connection_url);
 	$url = parse_url($connection_url);
    $db_name = preg_replace('/\/(.*)/', '$1', $url['path']);
 	
 	$db = $m->selectDB($db_name);
 	 $collection= $db->command(array("create" => "puzzle_piece"));

 	function add_puzzle_piece($puzzle_id, $x_final, $y_final, $img_url){
 		$x_current=rand(0, 600);
 		$y_current=rand(0, 400);
 		$document= array("puzzle_id"=>$puzzle_id, "x_current"=>$_current, 
 		"y_current"=>$y_current, "x_final"=>$x_final, "y_final"=>$y_final, "img_url"=>$img_url);
 		$collection= $db->puzzle_piece;
 		$collection->insert($document);
 	}
 	add_puzzle_piece("this is the id", 0, 0, "images/imgs.jpeg");
 	$collection= $db->puzzle_piece;
 	$cursor= $collection->find();
	/*foreach($cursor as $document){
		echo '<p>$document["puzzle_id"]</p>';
		echo '<p>$document["x_currrent"]</p>';
		echo '<p>$document["x_final"]</p>';
		echo "<p>$document['img_url'] </p>";
	}
	*/
 	$collection = $db->command(array("create" => "puzzle"));
 	include("functions.php");
	/*$one= array("taylor", "nicole");
	$two= array("holly", "nicole");
	$three= array("nicole", "bobby");
	$four = array("bobby", "taylor", "nicole");
	$five = array("bobby");
	$six= array("nicole");
	add_new_puzzle($one, "pizzal", $db);
	add_new_puzzle($two, "pizzal", $db);
	add_new_puzzle($three, "pizzal", $db);
	add_new_puzzle($four, "pizzal", $db);
	add_new_puzzle($five, "pizzal", $db);
	add_new_puzzle($six, "pizzal", $db);
	
<<<<<<< HEAD
	get_users_puzzle("", $db);
	*/
 	/*$list = $db->listCollections();
=======
	get_users_puzzle("nicole", $db);
	
 	$list = $db->listCollections();
>>>>>>> 043fc360bb62e012ad16022e656020bf4ce9ec6a
	
	foreach ($list as $collection) {
		echo $collection;
	}*/
	
	$collection_test= $db->puzzle;
	$cursor=$collection_test->find();
	foreach($cursor as $document){
		echo  "<ul>";
		$doc=$document['users'];
		foreach($doc as $item){
			echo "<li>$item</li>";
		}
		echo "</ul>"; 
	}

	
	$collection_test= $db->people;

	/*$document= array("title"=>"First element added", "attempting"=>"Taylor");
	$collection_test->insert($document);*/
	
	/*$cursor= $collection_test->find(array("title"=>"Taylor", "age"=>"21"));
	foreach($cursor as $document){
		echo $document["title"] ."\n";
		echo $document["age"]. "\n";
	}*/
	
	
 	
  } catch ( MongoConnectionException $e ) {
    die('Error connecting to MongoDB server');
  } catch ( MongoException $e ) {
    die('Mongo Error: ' . $e->getMessage());
  } catch ( Exception $e ) {
    die('Error: ' . $e->getMessage());
  }
?>

	
  </body>
</html>