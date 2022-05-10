<?php

if (isset($_SESSION['start']) && (time() - $_SESSION['start'] > 10000)) {
    session_unset(); 
    session_destroy();
    header('Location: index.php');
}
$_SESSION['start'] = time();
?>
