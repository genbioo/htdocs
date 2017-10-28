<?php
include($_SERVER['DOCUMENT_ROOT']."/includes/global.functions.php");
includeCore();
$db_handle = new DBController();
//echo(json_encode($_POST));
if($_POST["pwd1"] != $_POST["pwd2"]) {
    echo "<script type='text/javascript'>alert('Passwords do not match!');
    location='".$_SESSION['loc']."';
    </script>";
} else {
    if($_POST["Agency"] != "specify") {
        //Register User
        $query = "INSERT INTO `user` (`UserID`, `Lname`, `Fname`, `Mname`, `Sex`, `PhoneNum`, `Position`, `DateAdded`, `AGENCY_AgencyID`) VALUES (NULL, :Lname, :Fname, :Mname, :Sex, :PhoneNum, NULL, NOW(), :AgencyID)";
        $db_handle->prepareStatement($query);
        $db_handle->bindVar(':Lname', $_POST['Lname'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':Fname', $_POST['Fname'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':Mname', $_POST['Mname'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':Sex', $_POST['Gender'], PDO::PARAM_INT,0);
        $db_handle->bindVar(':PhoneNum', $_POST['PhoneNum'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':AgencyID', $_POST['Agency'], PDO::PARAM_INT,0);
        $db_handle->runUpdate();
        $newUserID = $db_handle->getLastInsertID();
        
        //Register account
        $query = "INSERT INTO `account` (`AccountID`, `USER_UserID`, `Username`, `Password`, `Type`, `DateEnrolled`, `USING_ORGANIZATION_UsingOrganizationID`) VALUES (NULL, :uID, :uName, :pass, '2', NOW(), :uOrg)";
        $db_handle->prepareStatement($query);
        $db_handle->bindVar(':uID', $newUserID, PDO::PARAM_INT,0);
        $db_handle->bindVar(':uName', $_POST['Email'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':pass', password_hash($_POST['pwd1'], PASSWORD_DEFAULT), PDO::PARAM_STR,0);
        $db_handle->bindVar(':uOrg', $_POST['UserGroup'], PDO::PARAM_INT,0);
        $db_handle->runUpdate();
        
        echo "<script type='text/javascript'>alert('Success!');
        location='".$_SESSION['loc']."';
        </script>";
    } else {
        //Register specified agency
        $query = "INSERT INTO `agency` (`AgencyID`, `AgencyName`) VALUES (NULL, :agency)";
        $db_handle->prepareStatement($query);
        $db_handle->bindVar(':agency', $_POST['specAgency'], PDO::PARAM_STR,0);
        $db_handle->runUpdate();
        $newAgencyID = $db_handle->getLastInsertID();
        
        //Register User
        $query = "INSERT INTO `user` (`UserID`, `Lname`, `Fname`, `Mname`, `Sex`, `PhoneNum`, `Position`, `DateAdded`, `AGENCY_AgencyID`) VALUES (NULL, :Lname, :Fname, :Mname, :Sex, :PhoneNum, NULL, NOW(), :AgencyID)";
        $db_handle->prepareStatement($query);
        $db_handle->bindVar(':Lname', $_POST['Lname'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':Fname', $_POST['Fname'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':Mname', $_POST['Mname'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':Sex', $_POST['Gender'], PDO::PARAM_INT,0);
        $db_handle->bindVar(':PhoneNum', $_POST['PhoneNum'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':AgencyID', $newAgencyID, PDO::PARAM_INT,0);
        $db_handle->runUpdate();
        $newUserID = $db_handle->getLastInsertID();
        
        //Register account
        $query = "INSERT INTO `account` (`AccountID`, `USER_UserID`, `Username`, `Password`, `Type`, `DateEnrolled`, `USING_ORGANIZATION_UsingOrganizationID`) VALUES (NULL, :uID, :uName, :pass, '2', NOW(), :uOrg)";
        $db_handle->prepareStatement($query);
        $db_handle->bindVar(':uID', $newUserID, PDO::PARAM_INT,0);
        $db_handle->bindVar(':uName', $_POST['Email'], PDO::PARAM_STR,0);
        $db_handle->bindVar(':pass', password_hash($_POST['pwd1'], PASSWORD_DEFAULT), PDO::PARAM_STR,0);
        $db_handle->bindVar(':uOrg', $_POST['UserGroup'], PDO::PARAM_INT,0);
        $db_handle->runUpdate();
        
        echo "<script type='text/javascript'>alert('Success!');
        location='".$_SESSION['loc']."';
        </script>";
    }
}
?>