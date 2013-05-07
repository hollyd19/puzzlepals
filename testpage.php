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
 	/*add_puzzle_piece("this is the id", 0, 0, "images/imgs.jpeg");
 	$collection= $db->puzzle_piece;
 	$cursor= $collection->find();
	foreach($cursor as $document){
		echo '<p>$document["puzzle_id"]</p>';
		echo '<p>$document["x_currrent"]</p>';
		echo '<p>$document["x_final"]</p>';
		echo "<p>$document['img_url'] </p>";
	}*/
	
 	$collection = $db->command(array("create" => "Test"));
 	
 	$list = $db->listCollections();
	
	foreach ($list as $collection) {
		echo $collection;
	}
	
	$collection_test= $db->people;
	/*$document= array("title"=>"First element added", "attempting"=>"Taylor");
	$collection_test->insert($document);*/
	
	$cursor= $collection_test->find(array("title"=>"Taylor", "age"=>"21"));
	foreach($cursor as $document){
		echo $document["title"] ."\n";
		echo $document["age"]. "\n";
	}
 	
  } catch ( MongoConnectionException $e ) {
    die('Error connecting to MongoDB server');
  } catch ( MongoException $e ) {
    die('Mongo Error: ' . $e->getMessage());
  } catch ( Exception $e ) {
    die('Error: ' . $e->getMessage());
  }
?>

	<?php
		echo "<img src=\"images/babyandpup.jpg\" alt=\"hello\"/>"; 
		$image= new Imagick("images/babyandpup.jpg");
		$image->thumbnailImage(100, 0);

		echo $image;
		echo "hello";
	?>
  </body>
</html>