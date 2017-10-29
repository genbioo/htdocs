<?php
if(!isset($_SESSION["UserID"])) {
    session_destroy();
    header( "Location: login.php" );
}
?>