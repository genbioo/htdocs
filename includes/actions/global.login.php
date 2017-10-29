<?php
include("../../initialize.php");
include($_SERVER['DOCUMENT_ROOT'].ROOT."includes/global.dbcontrollerPDO.php");

$db_handle = new DBController();

if(isset($_SESSION["UserID"])) {
    session_unset(); 
    session_destroy();
    header("location: /pages/login.php?err=3");
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
        header("location: /pages/login.php?err=2");
    }
}
?>