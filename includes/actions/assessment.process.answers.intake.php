<?php
include("../../initialize.php");
includeCore();

$db_handle = new DBController();
$idp_id = $_GET['id'];
$age_group = $_GET['ag'];
$post = $_POST; //$key for this array is in the format x-y where x = answertype & y = questionID
$temp_answertype = "";
$temp_qid = "";
$query = "";
$paramNum = 0;

$db_handle->prepareStatement("SELECT * FROM `intake` WHERE DISASTER_DisasterID = :disasterID AND AgeGroup = :ageGroup");
$db_handle->bindVar(':disasterID', $_SESSION['disaster_id'], PDO::PARAM_INT,0);
$db_handle->bindVar(':ageGroup', $age_group, PDO::PARAM_INT,0);
$intakes = $db_handle->runFetch();

foreach($intakes as $info) {
    $intakeID = $info['IntakeID'];
}

$db_handle->prepareStatement("INSERT INTO `intake_answers` (`INTAKE_ANSWERS_ID`, `INTAKE_IntakeID`, `IDP_IDP_ID`, `USER_UserID`, `Date_taken`) VALUES (NULL, :intakeID, :idpID, :userID, CURRENT_TIMESTAMP)");
$db_handle->bindVar(':intakeID', $intakeID, PDO::PARAM_INT,0);
$db_handle->bindVar(':idpID', $idp_id, PDO::PARAM_INT,0);
$db_handle->bindVar(':userID', $_SESSION['UserID'], PDO::PARAM_INT,0);
$db_handle->runUpdate();

$idp_form_answers_id = $db_handle->getLastInsertID();

foreach($post as $key => $answers) {
    if(substr($key, 0, 1) != 'q')
    {
        $temp_answertype = substr($key, 2, 1);
        $temp_qid = substr($key, 4);
        if($temp_answertype == '1') {
            $paramNum++;
            $query .= "INSERT INTO `answers_quanti` (`ANSWERS_QUANTI_ID`, `Answer`, `QUESTIONS_QuestionsID`, `FORM_ANWERS_FORM_ANSWERS_ID`, `INTAKE_ANSWERS_INTAKE_ANSWERS_ID`) VALUES ( NULL, :answer".$paramNum.", :qid".$paramNum.", NULL, :faid".$paramNum.");";
            $parameters[] = array('answer'.$paramNum => $answers, 'qid'.$paramNum => $temp_qid, 'faid'.$paramNum => $idp_form_answers_id, 'type'.$paramNum => 'quanti');
        } else if($temp_answertype == '2') {
            $paramNum++;
            if($answers == '') {
                $query .= "INSERT INTO `answers_quali` (`ANSWERS_QUALI_ID`, `Answer`, `QUESTIONS_QuestionsID`, `FORM_ANSWERS_FORM_ANSWERS_ID`, `INTAKE_ANSWERS_INTAKE_ANSWERS_ID`) VALUES ( NULL, :answer".$paramNum.", :qid".$paramNum.", NULL, :faid".$paramNum.");";
                $parameters[] = array('answer'.$paramNum => "(blank)", 'qid'.$paramNum => $temp_qid, 'faid'.$paramNum => $idp_form_answers_id, 'type'.$paramNum => 'quali');
            } else {
                $query .= "INSERT INTO `answers_quali` (`ANSWERS_QUALI_ID`, `Answer`, `QUESTIONS_QuestionsID`, `FORM_ANSWERS_FORM_ANSWERS_ID`, `INTAKE_ANSWERS_INTAKE_ANSWERS_ID`) VALUES ( NULL, :answer".$paramNum.", :qid".$paramNum.", NULL, :faid".$paramNum.");";
                $parameters[] = array('answer'.$paramNum => $answers, 'qid'.$paramNum => $temp_qid, 'faid'.$paramNum => $idp_form_answers_id, 'type'.$paramNum => 'quali');
            }
        }
    }

    $temp_answertype = "";
    $temp_qid = "";

}
$db_handle->prepareStatement($query);
$paramNum = 0;
foreach($parameters as $param) {

    $paramNum++;

    if($param['type'.$paramNum] == 'quanti') {
        $db_handle->bindVar(':answer'.$paramNum, $param['answer'.$paramNum], PDO::PARAM_INT,0);
    } else {
        $db_handle->bindVar(':answer'.$paramNum, $param['answer'.$paramNum], PDO::PARAM_STR,0);
    }

    $db_handle->bindVar(':qid'.$paramNum, $param['qid'.$paramNum], PDO::PARAM_INT,0);
    $db_handle->bindVar(':faid'.$paramNum, $param['faid'.$paramNum], PDO::PARAM_INT,0);
}

$db_handle->runUpdate();

if($db_handle->getUpdateStatus()) {
    header("location: /pages/idp.assessment.history.php?id=".$idp_id."&ag=".$age_group."&status=intakesuccess");
}
?>