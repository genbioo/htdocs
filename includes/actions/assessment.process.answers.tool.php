<?php
include($_SERVER['DOCUMENT_ROOT']."/includes/global.functions.php");

includeCore();

$db_handle = new DBController();
$post = $_POST;
$query = "";
$question_ids = [];
$answeredQuestions = [];
$unansweredQuestion = [];
$formIDs = [];
$idp_form_answers_id = [];

$parameters = array(); //array of parameters for PDO binding
$paramNum = 0;
foreach($post as $key => $result) {
    //if $key has 'q-' prefix (which is for questions), add to $question_ids array
    if (strpos($key, 'q-') !== false) {
        $keys1 = explode('-',$key);
        //question key format => q-(formID)-(answerType)-(questionID) ex: q-17-1-397
        $question_ids[$keys1[1]."-".$keys1[3]] = $result; //set question id as array index for easier finding later
    } else {
        $keys2 = explode('-',$key);

        //add query for idp_form_answers insertion. The ID of idp_form_answers is an fk for answers_quali and answers_quanti
        if(!in_array($keys2[0], $formIDs, true)) {
            $formIDs[$keys2[0]] = $keys2[0];
            $db_handle->prepareStatement("INSERT INTO `form_answers` (`FORM_ANSWERS_ID`, `USER_UserID`, `FORM_FormID`, `DateTaken`, `IDP_IDP_ID`, `UnansweredItems`) VALUES (NULL, :userID, :formID, CURRENT_TIMESTAMP, :idpID, NULL);");
            $db_handle->bindVar(':userID', $_SESSION['UserID'], PDO::PARAM_INT,0);
            $db_handle->bindVar(':formID', $keys2[0], PDO::PARAM_INT,0);
            $db_handle->bindVar(':idpID', $_SESSION['idpID'], PDO::PARAM_INT,0);
            $db_handle->runUpdate();
            //store the auto-incremented id for use in update. Unanswered questions will be updated after this foreach
            $tempID = $db_handle->getLastInsertID();
            $idp_form_answers_id[$keys2[0]."-".$tempID] = $tempID;
        }
        //add query for answers_quali and answers_quanti
        $answeredQuestions[$keys2[2]] = $keys2[2]; //set question id as array index for easier finding later
        if(isset($result) && $keys2[1] == 1) {
            $paramNum++;
            $query .= "INSERT INTO `answers_quanti` (`ANSWERS_QUANTI_ID`, `Answer`, `QUESTIONS_QuestionsID`, `FORM_ANWERS_FORM_ANSWERS_ID`, `INTAKE_ANSWERS_INTAKE_ANSWERS_ID`) VALUES (NULL, :answer".$paramNum.", :qid".$paramNum.", :faid".$paramNum.", NULL);";
            $parameters[] = array('answer'.$paramNum => $result, 'qid'.$paramNum => $keys2[2], 'faid'.$paramNum => $idp_form_answers_id[$keys2[0]."-".$tempID], 'type'.$paramNum => 'quanti');
        } else if(isset($result)) {
            $paramNum++;
            $query .= "INSERT INTO `answers_quali` (`ANSWERS_QUALI_ID`, `Answer`, `QUESTIONS_QuestionsID`, `FORM_ANSWERS_FORM_ANSWERS_ID`, `INTAKE_ANSWERS_INTAKE_ANSWERS_ID`) VALUES (NULL, :answer".$paramNum.", :qid".$paramNum.", :faid".$paramNum.", NULL);";
            $parameters[] = array('answer'.$paramNum => $result, 'qid'.$paramNum => $keys2[2], 'faid'.$paramNum => $idp_form_answers_id[$keys2[0]."-".$tempID], 'type'.$paramNum => 'quanli');
        } else {
            $unansweredQuestion[$keys1[1]."-".$keys1[3]] = $keys1[3];
        }
    }
}

//-------- keeping track of unanswered questions --------
foreach($question_ids as $key => $qID) {
    $keys3 = explode('-',$key);
    if(!in_array($keys3[1], $answeredQuestions, true)) {
        $unansweredQuestion[$key] = $keys3[1];
    }
}
asort($unansweredQuestion);
foreach($idp_form_answers_id as $key1 => $item1) {
    $arr1 =  explode('-',$key1);
    $query .= "UPDATE `form_answers` SET `UnansweredItems` = '";
    foreach($unansweredQuestion as $key2 => $item2) {
        $arr2 = explode('-',$key2);
        if($arr1[0] == $arr2[0]) {
            $query .= $item2.",";
        }
    }
    $query .= "' WHERE `form_answers`.`FORM_ANSWERS_ID` = ".$arr1[1].";";
}
//-------------------------------------------------------

$db_handle->prepareStatement($query);
//pdo query parameter binding
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

$db_handle->runUpdate($query);
if($db_handle->getUpdateStatus()) {
    echo "<script type='text/javascript'>alert('Answers submitted!');
    location='/pages/assessment.php';
    </script>";
}
unset($_SESSION['idpID']);
?>