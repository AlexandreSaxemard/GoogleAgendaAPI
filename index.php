<?php

//index.php

//Include Configuration File
include('config.php');

// Include Google calendar api handler class 
require_once 'GoogleCalendarApi.class.php'; 

require_once('sessionVerif.php');

$login_button = '';


// Si la valeur de variable $_GET["code"] a été reçue après que l'utilisateur s'est connecté à son compte Google, alors on exécute la suite du bloc de code en dessous
if(isset($_GET["code"]))
{
    // Echange d'un code contre un jeton d'authentification valide.
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

    // Cette condition vérifiera qu'une erreur s'est produite lors de l'obtention du jeton d'authentification. S'il n'y a pas d'erreur, il s'exécutera le bloc de code en dessous
    if(!isset($token['error']))
    {
        // Définit le jeton d'accès utilisé pour les requêtes
        $google_client->setAccessToken($token['access_token']);

        // Stockage de l'access token dans une variable pour une future utilisation
        $_SESSION['access_token'] = $token['access_token'];
        $access_token = $_SESSION['access_token'];
        echo ($access_token);

        // Créer l'objet de la classe Google Oauth 2
        $google_service = new Google_Service_Oauth2($google_client);

        // Obtention des données de l'utilisteur depuis Google
        $data = $google_service->userinfo->get();

        // Ci-dessous, vous pouvez obtenir les données de profil et les stocker dans la variable $_SESSION
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

    // Initialize Google Calendar API class
    $GoogleCalendarApi = new GoogleCalendarApi();
    
    // Si tout les champs requis ne sont pas remplis
    if(empty($title)){ 
        $valErr .= 'Please enter event title.<br/>'; 
    } 
    if(empty($date)){ 
        $valErr .= 'Please enter event date.<br/>'; 
    } 
    $eventData = $_SESSION['postData'];
    
    // Si la variable $eventData n'est pas vide on passe les données en string
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
        var_dump($eventData);
        // On récupère l'access_token qui va être utilisé pour créer l'évenement
        $access_token_sess = $_SESSION['access_token'];
        if(!empty($access_token_sess)){
            $access_token = $access_token_sess;
        }else{
            $data = $GoogleCalendarApi -> GetAccessToken($clientID, 'http://localhost/GoogleAPI/index.php', $clientSecret, $_GET['code']);
            $access_token = $data['access_token'];
            $_SESSION['access_token'] = $access_token;
        }
        var_dump($access_token);
    }
    
    if(!empty($access_token)){
        // Obtention du fuseau horaire de l'utilisateur 
        $user_timezone = $GoogleCalendarApi -> GetUserCalendarTimezone($access_token);
        // Creation de l'évènement sur le calendrier primaire 
        $google_event_id = $GoogleCalendarApi -> CreateCalendarEvent($access_token, 'primary', $calendar_event, 1, $event_datetime, $user_timezone); 

        if($google_event_id){    
            $status = 'success'; 
        }
    }   

    $_SESSION['status_response'] = array('status' => $status, 'status_msg' => $statusMsg); 
    exit();
}

?>