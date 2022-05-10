<?php

//index.php

//Include Configuration File
include('config.php');

// Include database configuration file 
require_once 'dbConfig.php';

// Include Google calendar api handler class 
require_once 'GoogleCalendarApi.class.php'; 

require_once('sessionVerif.php');

$login_button = '';

// This $_GET["code"] variable value received after user has login into their Google Account redirct to PHP script then this variable value has been received
if(isset($_GET["code"]))
{
 // It will Attempt to exchange a code for an valid authentication token.
 $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

 // This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
 if(!isset($token['error']))
 {
  // Set the access token used for requests
  $google_client->setAccessToken($token['access_token']);

  // Stockage de l'access token dans une variable pour une future utilisation
  $_SESSION['access_token'] = $token['access_token'];
  $access_token = $_SESSION['access_token'];
  echo ($access_token);

  // Créer l'objet de la classe Google Oauth 2
  $google_service = new Google_Service_Oauth2($google_client);

  // Obtention des données de l'utilisteur depuis Google
  $data = $google_service->userinfo->get();

  //Below you can find Get profile data and store into $_SESSION variable
  if(!empty($data['given_name']))
  {
   $_SESSION['user_first_name'] = $data['given_name'];
  }

  if(!empty($data['family_name']))
  {
   $_SESSION['user_last_name'] = $data['family_name'];
  }

  if(!empty($data['email']))
  {
   $_SESSION['user_email_address'] = $data['email'];
  }

  if(!empty($data['gender']))
  {
   $_SESSION['user_gender'] = $data['gender'];
  }

  if(!empty($data['picture']))
  {
   $_SESSION['user_image'] = $data['picture'];
  }
 }
}

// Vérifie si l'utilisateur est déja connecté, si non, exécution du bloc de code ci-contre
if(!isset($_SESSION['access_token']))
{
 // Création de l'URL pour autorisation
 $login_button = '<a href="'.$google_client->createAuthUrl().'"><img src="sign-in-with-google.png" /></a>';
}

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PHP Login using Google Account</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
    <br />
    <h2 align="center">Connexion Google en PHP</h2>
    <br />
    <div class="panel panel-default">
    <?php
    if($login_button == '')
    {
        echo '<div class="panel-heading">Bienvenue '.$_SESSION['user_first_name'].'</div><div class="panel-body">';
        echo '<img src="'.$_SESSION["user_image"].'" class="img-responsive img-circle img-thumbnail" />';
        echo '<h3><b>Nom complet:</b> '.$_SESSION['user_first_name'].' '.$_SESSION['user_last_name'].'</h3>';
        echo '<h3><b>Email :</b> '.$_SESSION['user_email_address'].'</h3>';
        echo '<h3><a href="logout.php">Déconnexion</a></h3></div>';
    ?>
    <div class="container">
    <h1>AJOUTER UN EVENEMENT AU CALENDRIER GOOGLE</h1>
    <!-- Status message -->
    <?php if(!empty($statusMsg)){ ?>
        <div class="alert alert-<?php echo $status; ?>"><?php echo $statusMsg; ?></div>
    <?php } ?>


    <div class="col-md-11">
        <form method="post" class="form">
            <div class="form-group">
                <label>Titre</label>
                <input type="text" class="form-control" name="title" value="<?php echo !empty($postData['title'])?$postData['title']:''; ?>" required="">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo !empty($postData['description'])?$postData['description']:''; ?></textarea>
            </div>
            <div class="form-group">
                <label>Lieu</label>
                <input type="text" name="location" class="form-control" value="<?php echo !empty($postData['location'])?$postData['location']:''; ?>">
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="<?php echo !empty($postData['date'])?$postData['date']:''; ?>" required="">
            </div>
            <div class="form-group time">
                <label>Durée de l'intervention</label>
                <input type="time" name="time_from" class="form-control" value="<?php echo !empty($postData['time_from'])?$postData['time_from']:''; ?>">
                <span>JUSQU'À</span>
                <input type="time" name="time_to" class="form-control" value="<?php echo !empty($postData['time_to'])?$postData['time_to']:''; ?>">
            </div>
            <div class="form-group">
                <input type="submit" class="form-control btn-primary" name="submit" value="Ajouter l'évenement"/>
                <br>
            </div>
        </form>
    </div>
</div>
    <?php
    }
    else
    {
        echo '<div align="center">'.$login_button . '</div>';
    }
   ?>
</div>
</div>
</body>
</html>

<?php
$statusMsg = ''; 
$status = 'danger'; 
$emailEnzo = 'enzo@azurinfo.fr';

// Si le formulaire a été envoyé
if(isset($_POST['submit'])){
    // Obtention des infos de l'évenement
    $_SESSION['postData'] = $_POST;
    $title = !empty($_POST['title'])?trim($_POST['title']):''; 
    $description = !empty($_POST['description'])?trim($_POST['description']):''; 
    $location = !empty($_POST['location'])?trim($_POST['location']):''; 
    $date = !empty($_POST['date'])?trim($_POST['date']):''; 
    $time_from = !empty($_POST['time_from'])?trim($_POST['time_from']):''; 
    $time_to = !empty($_POST['time_to'])?trim($_POST['time_to']):'';

    // Si tout les champs requis ne sont pas remplis
    if(empty($title)){ 
        $valErr .= 'Please enter event title.<br/>'; 
    } 
    if(empty($date)){ 
        $valErr .= 'Please enter event date.<br/>'; 
    } 

    // Si tout les champs requis sont remplis
    if(empty($valErr)){
        // Insert data into the database 
        $sqlQ = "INSERT INTO events (title,description,location,date,time_from,time_to,created) VALUES (?,?,?,?,?,?,NOW())"; 
        $stmt = $db->prepare($sqlQ); 
        $stmt->bind_param("ssssss", $db_title, $db_description, $db_location, $db_date, $db_time_from, $db_time_to); 
        $db_title = $title; 
        $db_description = $description; 
        $db_location = $location; 
        $db_date = $date; 
        $db_time_from = $time_from; 
        $db_time_to = $time_to; 
        $insert = $stmt->execute();

        if($insert){
            $event_id = $stmt->insert_id; 
                
            unset($_SESSION['postData']); 
                
            // Store event ID in session 
            $_SESSION['last_event_id'] = $event_id;
        }
    }

    // Initialize Google Calendar API class
    $GoogleCalendarApi = new GoogleCalendarApi();

    // Get event ID from session 
    $event_id = $_SESSION['last_event_id'];

    if(!empty($event_id)){
        // Fetch event details from database 
        $sqlQ = "SELECT * FROM events WHERE id = ?"; 
        $stmt = $db->prepare($sqlQ);  
        $stmt->bind_param("i", $db_event_id); 
        $db_event_id = $event_id; 
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $eventData = $result->fetch_assoc();
        var_dump($eventData);

        if(!empty($eventData)){
            $calendar_event = array( 
                'summary' => $eventData['title'], 
                'location' => $eventData['location'], 
                'description' => $eventData['description'] 
            ); 
             
            $event_datetime = array( 
                'event_date' => $eventData['date'], 
                'start_time' => $eventData['time_from'], 
                'end_time' => $eventData['time_to'] 
            );
            $access_token_sess = $_SESSION['access_token'];
            if(!empty($access_token_sess)){
                $access_token = $access_token_sess;
            }else{
                $data = $GoogleCalendarApi -> GetAccessToken('205163721970-0ncl7bn7eb5qh3den6oreenkd2lvno8j.apps.googleusercontent.com', 'http://localhost/GoogleOAuth/index.php', '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', $_GET['code']);
                $access_token = $data['access_token'];
                $_SESSION['access_token'] = $access_token;
                var_dump($access_token);
            }

            if(!empty($access_token)){
                try{
                    // Get the user's calendar timezone 
                    $user_timezone = $GoogleCalendarApi -> GetUserCalendarTimezone($access_token);
                    // Create an event on the primary calendar 
                    $google_event_id = $GoogleCalendarApi -> CreateCalendarEvent($access_token, 'primary', $calendar_event, 0, $event_datetime, $user_timezone); 

                    if($google_event_id){ 
                        // Update google event reference in the database 
                        $sqlQ = "UPDATE events SET google_calendar_event_id=? WHERE id=?"; 
                        $stmt = $db->prepare($sqlQ); 
                        $stmt->bind_param("si", $db_google_event_id, $db_event_id); 
                        $db_google_event_id = $google_event_id; 
                        $db_event_id = $event_id; 
                        $update = $stmt->execute(); 
                         
                        unset($_SESSION['last_event_id']); 
                        unset($_SESSION['google_access_token']); 
                         
                        $status = 'success'; 
                        $statusMsg = '<p>Event #'.$event_id.' has been added to Google Calendar successfully!</p>'; 
                        $statusMsg .= '<p><a href="https://calendar.google.com/calendar/" target="_blank">Open Calendar</a>'; 
                    } 
                } catch(Exception $e){
                    $statusMsg = $e->getMessage(); 
                }
            }else{
                $statusMsg = 'Failed to fetch access token!';  
            }
        }else{
            $statusMsg = 'Event data not found!';
        }
    }else{
        $statusMsg = 'Event reference not found!';
    }
    $_SESSION['status_response'] = array('status' => $status, 'status_msg' => $statusMsg); 
    exit();
}

?>