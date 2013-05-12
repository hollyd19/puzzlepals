<?php

/**
 * This sample app is provided to kickstart your experience using Facebook's
 * resources for developers.  This sample app provides examples of several
 * key concepts, including authentication, the Graph API, and FQL (Facebook
 * Query Language). Please visit the docs at 'developers.facebook.com/docs'
 * to learn more about the resources available to you
 */

/**
 * TO DO:
 * -- Make it so someone can invite their friend
 * -- Make it so when someone is invited, they are notified and given a link to the puzzle.
 * -- display who can view puzzle on the puzzle page itself (maybe with profile pics and online status?)
 * -- use AJAX (every x miliseconds) to show movements made by other players
 * -- declare the puzzle completed
 * -- keep track of time taken to complete a puzzle
 * -- Make a list of completed puzzles
 * 
 */

// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');

// Enforce https on production
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');


/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

require_once('sdk/src/facebook.php');

$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
  'sharedSession' => true,
  'trustForwarded' => true,
));

$user_id = $facebook->getUser();
if ($user_id) {
  try {
    // Fetch the viewer's basic information
    $basic = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    // If the call fails we check if we still have a user. The user will be
    // cleared if the error is because of an invalid accesstoken
    if (!$facebook->getUser()) {
      header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
      exit();
    }
  }

  // This fetches some things that you like . 'limit=*" only returns * values.
  // To see the format of the data you are retrieving, use the "Graph API
  // Explorer" which is at https://developers.facebook.com/tools/explorer/
  $likes = idx($facebook->api('/me/likes?limit=4'), 'data', array());

  // This fetches 4 of your friends.
  $friends = idx($facebook->api('/me/friends?limit=4'), 'data', array());

  // And this returns 16 of your photos.
  $photos = idx($facebook->api('/me/photos?limit=16'), 'data', array());

  // Here is an example of a FQL call that fetches all of your friends that are
  // using this app
  $app_using_friends = $facebook->api(array(
    'method' => 'fql.query',
    'query' => 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
  ));
}

// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());

$app_name = idx($app_info, 'name', '');

?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title><?php echo he($app_name); ?></title>
    <link rel="stylesheet" href="stylesheets/bootstrap.min.css" media="Screen" type="text/css" />
	<!-- <script type="text/javascript" src="javascript/bootstrap.min.js"></script>--> 
    <link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />

    <!--[if IEMobile]>
    <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
    <![endif]-->

    <!-- These are Open Graph tags.  They add meta data to your  -->
    <!-- site that facebook uses when your content is shared     -->
    <!-- over facebook.  You should fill these tags in with      -->
    <!-- your data.  To learn more about Open Graph, visit       -->
    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
    <meta property="og:title" content="<?php echo he($app_name); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
    <meta property="og:image" content="<?php echo AppInfo::getUrl('/logo.png'); ?>" />
    <meta property="og:site_name" content="<?php echo he($app_name); ?>" />
    <meta property="og:description" content="My first app" />
    <meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />

    <script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>

    <script type="text/javascript">
      function logResponse(response) {
        if (console && console.log) {
          console.log('The response was', response);
        }
      }

      $(function(){
        // Set up so we handle click on the buttons
        $('#postToWall').click(function() {
          FB.ui(
            {
              method : 'feed',
              link   : $(this).attr('data-url')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });

        $('#sendToFriends').click(function() {
          FB.ui(
            {
              method : 'send',
              link   : $(this).attr('data-url')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });

        $('#sendRequest').click(function() {
          FB.ui(
            {
              method  : 'apprequests',
              message : $(this).attr('data-message')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                $('input#invited_users_id').val(response.to + "");
                logResponse(response.to + "");
              }
            }
          );
        });
      });
    </script>

    <!--[if IE]>
      <script type="text/javascript">
        var tags = ['header', 'section'];
        while(tags.length)
          document.createElement(tags.pop());
      </script>
    <![endif]-->
    
    <link href="../stylesheets/bootstrap.min.css" rel="stylesheet" media="screen">
	
	<link href="stylesheets/basic-css.css" rel="stylesheet" type="text/css"/>
	
    <script type="text/javascript" src="javascript/bootstrap.min.js"></script>
    <script type="text/javascript" src="javascript/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui.js"></script>
    
	<script type="text/javascript" src="javascript/jquery.slimscroll.min.js"></script>

	<script type="text/javascript" src="javascript/javascript-dragndrop.js"></script>
  </head>
  <body onload="enable_scrollbar();">
    <div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          window.location = window.location;
        });

        FB.Canvas.setAutoGrow();
      };

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>

    <header class="clearfix">
      <?php if (isset($basic)) { ?>
      <p id="picture" style="background-image: url(https://graph.facebook.com/<?php echo he($user_id); ?>/picture?type=normal)"></p>

      <div>
        <h3 id="welcome_header">Welcome to Puzzle Pals, <?php echo he(idx($basic, 'name')); ?></h3>
      </div>
      </header>
      
      <?php
      
require("functions.php");

function sort_puzzles($user){
	$in_progress_puzzles= query_puzzles($user);
        //var_dump($in_progress_puzzles);
	$easy=array();
	$medium=array();
	$hard=array();
	foreach ($in_progress_puzzles as $item){
                $item["users"]= explode(", ", $item["users"]);
                //var_dump($item["users"]);
		$puzzle= explode(".", $item["name"]);
		$images_name= $puzzle[0];
		$puzzle_size= $item["level"];

                $players=""; 
                foreach($item["users"] as $player){
                  //echo $player . " = " . $user ."<br/>";
                  if (/*$player != $user*/true){
                    $var="/".$player."/?fields=name";
                      $friends = idx($facebook->api($var), 'data', array());
                                  foreach ($friends as $friend) {
                                    $players+=idx($friend, 'name'). ", ";
                                  }

                    //echo '<a href="'.'http://graph.facebook.com/'.$player.'">link</a><br/>';
                   // array_push($players, json_decode(file_get_contents("http://graph.facebook.com/".$player))->name);
                  }
                }
                
                //var_dump($players);

		if ($puzzle_size=="9"){
                        $array=array("name"=>$images_name, "id"=> $item['id'], "users"=>$players, "time"=>$item["time"]); 
			array_push($easy, $array);
		} elseif($puzzle_size=="25"){
                        $array=array("name"=>$images_name, "id"=> $item['id'], "users"=>$item['users'], "time"=>$item["time"]); 
			array_push($medium, $array);
		} elseif($puzzle_size=="49"){
                        $array=array("name"=>$images_name, "id"=> $item['id'], "users"=>$item['users'], "time"=>$item["time"]); 
			array_push($hard, $array);
		}
	}
	return array($easy, $medium, $hard);
}
list($easy, $medium, $hard)= sort_puzzles($user_id);
//var_dump($medium);
require("views/landing_form_view.php");


?>
      
    
<?php } else { ?>

      <div>
        <h1>Welcome</h1>
        <div class="fb-login-button" data-scope="user_likes,user_photos"></div>
      </div>
      </header>
      <?php }
      ?>
      
      
  </body>
</html>
