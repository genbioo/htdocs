<?php
include($_SERVER['DOCUMENT_ROOT']."/includes/global.functions.php");

includeCore();
$db_handle = new DBController();
if(isset($_SESSION["userID"])) {
    session_unset(); 
    session_destroy();
    echo "<script type='text/javascript'>alert('Two logins detected! Please sign in again.'); 
    location='login.php';
    </script>";
} else {
    $db_handle->prepareStatement("SELECT * FROM `account` WHERE `Username` = :uName");
    $db_handle->bindVar(':uName', $_POST['email'], PDO::PARAM_STR,0);
    $result = $db_handle->runFetch();

    if (password_verify($_POST['pwd'], $result[0]['Password'])) {
        $_SESSION["UserID"] = $result[0]['USER_UserID'];
        $_SESSION["UserGroup"] = $result[0]['USING_ORGANIZATION_UsingOrganizationID'];
        $_SESSION["account_type"] = $result[0]['Type'];
        header("Location: /pages/index.php");
    } else {
        echo "<script type='text/javascript'>alert('Invalid credentials.'); 
        location='/pages/login.php';
        </script>";
    }
}
?>