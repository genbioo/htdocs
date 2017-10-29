<?php
include("../../initialize.php");
includeCore();

$db_handle = new DBController();
$query = "";
$query .= "INSERT INTO `dafac_no` (`DAFAC_SN`, `Name`, `serial_no`) VALUES (NULL, :name, NULL);";
$db_handle->prepareStatement($query);
$db_handle->bindVar(':name', $_POST['Lname'].", ".$_POST['Fname']." ".$_POST['Mname'], PDO::PARAM_STR,0);
$db_handle->runUpdate();
$dafacID = $db_handle->getLastInsertID();
$query = "";
$query .= "INSERT INTO `idp` (`IDP_ID`, `DAFAC_DAFAC_SN`, `RelationToHead`, `Lname`, `Fname`, `Mname`, `Bdate`, `Age`, `Gender`, `Education`, `MaritalStatus`, `PhoneNum`, `Origin_Barangay`, `Email`, `EvacuationCenters_EvacuationCentersID`, `DateTaken`, `USER_UserID`, `Occupation`, `Remarks`, `OtherContact`, `SpecificAddress`, `IDP_status`, `MonthlyNetIncome`, `Religion`, `Ethnicity`, `School`) VALUES (NULL, :dafacID, NULL, :Lname, :Fname, :Mname, :Bdate, :Age, :Gender, :Education, :MaritalStatus, :PhoneNum, :Origin, :Email, :EvacID, NOW(), :UserID, :Occupation, :Remarks, :OtherContact, :SpecificAddress, NULL, :MNetIncome, :Religion, :Ethnicity, :School);";
$db_handle->prepareStatement($query);
$db_handle->bindVar(':dafacID', $dafacID, PDO::PARAM_INT,0);
if(isset($_POST['Lname'])) $db_handle->bindVar(':Lname', $_POST['Lname'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Lname');
if(isset($_POST['Fname'])) $db_handle->bindVar(':Fname', $_POST['Fname'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Fname');
if(isset($_POST['Mname'])) $db_handle->bindVar(':Mname', $_POST['Mname'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Mname');
if(isset($_POST['Bdate'])) $db_handle->bindVar(':Bdate', $_POST['Bdate'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Bdate');
if(isset($_POST['Age'])) $db_handle->bindVar(':Age', $_POST['Age'], PDO::PARAM_INT,0); else $db_handle->bindNull(':Age');
if(isset($_POST['Gender'])) $db_handle->bindVar(':Gender', $_POST['Gender'], PDO::PARAM_INT,0); else $db_handle->bindNull(':Gender');
if(isset($_POST['education'])) $db_handle->bindVar(':Education', $_POST['education'], PDO::PARAM_INT,0); else $db_handle->bindNull(':Education');
if(isset($_POST['MaritalStatus'])) $db_handle->bindVar(':MaritalStatus', $_POST['MaritalStatus'], PDO::PARAM_INT,0); else $db_handle->bindNull(':MaritalStatus');
if(isset($_POST['PhoneNum'])) $db_handle->bindVar(':PhoneNum', $_POST['PhoneNum'], PDO::PARAM_STR,0); else $db_handle->bindNull(':PhoneNum');
if(isset($_POST['barangay1'])) $db_handle->bindVar(':Origin', $_POST['barangay1'], PDO::PARAM_INT,0); else $db_handle->bindNull(':Origin');
if(isset($_POST['Email'])) $db_handle->bindVar(':Email', $_POST['Email'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Email');
if($_POST["EvacType"] == 1) $db_handle->bindVar(':EvacID', $_POST['EvacName'], PDO::PARAM_INT,0); else $db_handle->bindNull(':EvacID');
$db_handle->bindVar(':UserID', $_SESSION['UserID'], PDO::PARAM_INT,0);
if(isset($_POST['Occupation'])) $db_handle->bindVar(':Occupation', $_POST['Occupation'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Occupation');
if(isset($_POST['Remarks'])) $db_handle->bindVar(':Remarks', $_POST['Remarks'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Remarks');
if(isset($_POST['OtherContact'])) $db_handle->bindVar(':OtherContact', $_POST['OtherContact'], PDO::PARAM_STR,0); else $db_handle->bindNull(':OtherContact');
if(isset($_POST['SpecificAddress'])) $db_handle->bindVar(':SpecificAddress', $_POST['SpecificAddress'], PDO::PARAM_STR,0); else $db_handle->bindNull(':SpecificAddress');
if(isset($_POST['net_income'])) $db_handle->bindVar(':MNetIncome', $_POST['net_income'], PDO::PARAM_STR,0); else $db_handle->bindNull(':MNetIncome');
if(isset($_POST['Religion'])) $db_handle->bindVar(':Religion', $_POST['Religion'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Religion');
if(isset($_POST['Ethnicity'])) $db_handle->bindVar(':Ethnicity', $_POST['Ethnicity'], PDO::PARAM_STR,0); else $db_handle->bindNull(':Ethnicity');
if(isset($_POST['School'])) $db_handle->bindVar(':School', $_POST['School'], PDO::PARAM_STR,0); else $db_handle->bindNull(':School');
$db_handle->runUpdate();

if($db_handle->getUpdateStatus())
{
    header("location: ".$_SESSION['loc']."?status=success");
} else {
    header("location: ".$_SESSION['loc']."?status=err1");
}
?>