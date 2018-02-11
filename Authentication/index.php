<?php
// Fill these out with the values you got from Github
$githubClientID = '';
$githubClientSecret = '';


$authorizeURL = 'https://github.com/login/oauth/authorize'; // This is the URL we'll send the user to first to get their authorization
$tokenURL = 'https://github.com/login/oauth/access_token'; // This is the endpoint our server will request an access token from
$apiURLBase = 'https://api.github.com/'; // This is the Github base URL we can use to make authenticated API requests
$baseURL = ''; // The full path to this script. Note that for production sites, you should use an https URL.

session_start(); // Start a session so we have a place to store things between redirects

if(get('action') == 'logout') {
    unset($_SESSION['access_token']);
    header('Location: ' . $baseURL);
    die();
}

// Start the login process by sending the user to Github's authorization page
if(get('action') == 'login') {
  // Generate a random hash and store in the session for security
  $_SESSION['state'] = hash('sha256', microtime(1).rand().$_SERVER['REMOTE_ADDR']);
  unset($_SESSION['access_token']);

  $params = array(
    'client_id' => $githubClientID,
    'redirect_uri' => $baseURL,
    'scope' => 'user',
    'state' => $_SESSION['state']
  );

  // Redirect the user to Github's authorization page
  header('Location: ' . $authorizeURL . '?' . http_build_query($params));
  die();
}

// When Github redirects the user back here, there will be a "code" and "state" 
// parameter in the query string
if(get('code')) {
    // Verify the state matches our stored state
    if(!get('state') || $_SESSION['state'] != get('state')) {
      header('Location: ' . $baseURL . '?error=invalid_state');
      die();
    }
  
    // Exchange the auth code for a token
    $token = apiRequest($tokenURL, array(
      'client_id' => $githubClientID,
      'client_secret' => $githubClientSecret,
      'redirect_uri' => $baseURL,
      'code' => get('code')
    ));
    $_SESSION['access_token'] = $token->access_token;
  
    header('Location: ' . $baseURL);
    die();
  }
  
  // If there is an access token in the session the user is logged in
  if(session('access_token')) {
    // Make an API request to Github to fetch basic profile information
    $user = apiRequest($apiURLBase . 'user');
  
    $access = true;
    if(get('action') != 'show'){
        $message = "Welcome ".$user->name."!";
    }else{
        $message = "Your access Token is: ".session('access_token');
        $email = apiRequest($apiURLBase . '/user/emails');
    }
  } else {
    $access = false;
    $message = "Welcome! Please identify yourself in order to update your bracalet credentials!.";
  }
  
  
  function apiRequest($url, $post=FALSE, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  
    if($post)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  
    $headers[] = 'Accept: application/json';
  
    if(session('access_token'))
      $headers[] = 'Authorization: Bearer ' . session('access_token');
  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
    $response = curl_exec($ch);
    return json_decode($response);
  }
  
  function get($key, $default=NULL) {
    return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
  }
  
  function session($key, $default=NULL) {
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
  }

?>

<!DOCTYPE html>
<html>
<head>
    <title>SLEM Authentication</title>

    <style type="text/css">
        @font-face { font-family: font; src: url('res/font.otf'); }
        body{
            font-family: font;
            color: rgba(0, 0, 0, 1);
            -webkit-font-smoothing: antialiased;
            background: #24292e;
            height: 100%;
            width: 100%;
            padding: 0px;
            margin: 0px;
        }
        .image {
            position: relative;
            z-index: 0;
            width: 250px;
            height: 250px;
            margin: 0px auto;
            -webkit-animation: spin 120s linear infinite;
            -moz-animation: spin 120s linear infinite;
            animation: spin 120s linear infinite;
        }
        @-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }
        @-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }
        @keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }

        .content{
            z-index: 1;
            text-align: center;
            left: 50px;
            top: 150px;
            height: 500px;
            right: 50px;
            position: absolute;
            background: #fafafa;
        }
        #logo{
            width: 250px;
            height: 250px;
            margin: 50px auto 50px auto;
        }
        .button a, .button button{ 
            background-color: #24292e;
            border: none;
            color: white;
            padding: 15px 32px;
            font-size: 16px;
            bottom: 50px;
            text-decoration: none;
            margin-left: 10px;
        }
        .button{ 
            margin: 0px auto;
            width: 100%;
        }
        .Message{
            color: #24292e;
            text-align: center;
            font-size: 16px;
            position: absolute;
            bottom: 130px;
            left: 0px;
            right: 0px;
        }
    </style>
    <script>
        function save(tokenID){
            localStorage.setItem("tokenID", tokenID);
        }
    </script>
</head>
<body>
    <div class="content">
        <h1 style="text-align: center;">SLEM Authentication</h1>
        <div id="logo">
            <img  class="image" src="/Authentication/res/logo.png">
        </div>
        <div class="Message"><?php echo $message ?></div>
        <?php
            if(!$access){
                $buttonToDisplay = '<a href="?action=login">PLEASE IDENTIFY YOURSELF</a>';
            }else if(get('action') == 'show'){
                $buttonToDisplay =  '<button onclick="javascript:localStorage.setItem(\'tokenID\', \''.session('access_token').'\')">SAVE MY ACCESS TOKEN</button><a href="?action=logout">LOGOUT</a>';
            }else{
                $buttonToDisplay =  '<a href="?action=show">SHOW MY ACCESS TOKEN</a><a href="?action=logout">LOGOUT</a>';
            }
            echo '<div class="button">'.$buttonToDisplay.'</div>';
        ?>
    </div>
</body>
</html>

