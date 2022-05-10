<?php 
// Inclut le fichier de configuration 
require_once  'config.php' ; 
 
// Créer une connexion à la base de données 
$db  = new  mysqli ( DB_HOST ,  DB_USERNAME ,  DB_PASSWORD ,  DB_NAME ); 
 
// Vérifie la connexion 
if ( $db -> connect_error ) { 
    die( "Échec de la connexion : "  .  $db -> connect_error ); 
}