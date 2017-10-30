<?php
#---- dynamic root path function ----
function set_approot_dir($folder="")
    # Author: Rodolfo Villaruz
{
    preg_match('#' . $_SERVER['DOCUMENT_ROOT'] . '(.*)#', $folder, $matches);
    if ($matches) {
        define('ROOT', $matches[1] . '/');
    } else {
        define('ROOT', '/');
    }
}
#---- dynamic root path function end ----

function echoRoot()
{
    echo(ROOT);
}

#---- include functions ----
function includeCore()
{
    include($_SERVER['DOCUMENT_ROOT'].ROOT."includes/actions/global.check.credentials.php");
    include($_SERVER['DOCUMENT_ROOT'].ROOT."includes/global.dbcontrollerPDO.php");
}

function includeCommonJS()
{
    include($_SERVER['DOCUMENT_ROOT'].ROOT."includes/include.common.js.php");
}

function includeDataTables()
{
    include($_SERVER['DOCUMENT_ROOT'].ROOT."includes/include.datatables.php");
}

function includeNav()
{
    include($_SERVER['DOCUMENT_ROOT'].ROOT."includes/global.navigation.php");
}

function includeHead($pageTitle)
{
    include($_SERVER['DOCUMENT_ROOT'].ROOT."includes/include.common.head.php");
}

function includeLayoutGenerator()
{
    include($_SERVER['DOCUMENT_ROOT'].ROOT."includes/assessment.layout.generator.php");
}
#---- include functions end ----

#---- db fetch functions ----
function getIDPDetails($id) {
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT * FROM `idp` WHERE IDP_ID = :idpID");
    $db_handle->bindVar(':idpID', $id, PDO::PARAM_INT,0);
    $idp = $db_handle->runFetch();

    return $idp;
}

function getIDPExtensiveDetails($id) {
    $db_handle = new DBController();
    $db_handle->prepareStatement(
        "SELECT idp.IDP_ID,
                 CONCAT(Lname, ', ', Fname, ' ', Mname) AS IDPName, idp.Age,
                 idp.Gender, idp.Education, idp.MaritalStatus,
                 idp.PhoneNum, Origin_Address, EvacTable.EvacName, Evac_Address,
                 EvacTable.EvacType, idp.Email, idp.Occupation, 
                 idp.Remarks, idp.SpecificAddress FROM idp
        LEFT JOIN
        evacuation_centers ON evacuation_centers.EvacuationCentersID = idp.EvacuationCenters_EvacuationCentersID
        LEFT JOIN
            (Select idp.IDP_ID, idp.Origin_Barangay,
                    CONCAT(barangay.BarangayName, ', ', city_mun.City_Mun_Name, ', ', province.ProvinceName) AS Origin_Address
                    FROM barangay
            LEFT JOIN city_mun ON city_mun.City_Mun_ID = barangay.City_CityID
            LEFT JOIN province ON city_mun.PROVINCE_ProvinceID = province.ProvinceID
            LEFT JOIN idp ON idp.Origin_Barangay = barangay.BarangayID
            WHERE barangay.BarangayID = idp.Origin_Barangay)
        AS OriginTable
        ON OriginTable.IDP_ID = idp.IDP_ID
        LEFT JOIN
            (Select idp.IDP_ID, idp.EvacuationCenters_EvacuationCentersID, evacuation_centers.EvacType, evacuation_centers.EvacName, evacuation_centers.EvacAddress AS EvacAddressID,
                CONCAT(barangay.BarangayName, ', ', city_mun.City_Mun_Name, ', ', province.ProvinceName) as Evac_Address
             FROM idp LEFT JOIN evacuation_centers ON idp.EvacuationCenters_EvacuationCentersID = evacuation_centers.EvacuationCentersID LEFT JOIN barangay ON barangay.BarangayID = evacuation_centers.EvacAddress LEFT JOIN city_mun ON city_mun.City_Mun_ID = barangay.City_CityID LEFT JOIN province ON city_mun.PROVINCE_ProvinceID = province.ProvinceID where barangay.BarangayID = evacuation_centers.EvacAddress)
        AS EvacTable
        ON EvacTable.IDP_ID = idp.IDP_ID
        WHERE idp.IDP_ID = :idpID");

    $db_handle->bindVar(':idpID', $id, PDO::PARAM_INT, 0);
    $idpInfo = $db_handle->runFetch();

    return $idpInfo;
}

function getProvinces()
{
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT * FROM `province` ORDER BY ProvinceName");
    $provinces = $db_handle->runFetch();

    return $provinces;
}

function getCities()
{
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT * FROM city_mun JOIN province ON city_mun.PROVINCE_ProvinceID = province.ProvinceID ORDER BY `City_Mun_Name`");
    $cities = $db_handle->runFetch();

    return $cities;
}

function getBarangays()
{
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT * FROM `barangay` JOIN city_mun ON barangay.City_CityID = city_mun.City_Mun_ID ORDER BY `BarangayName`");
    $barangays = $db_handle->runFetch();

    return $barangays;
}

function getEvacuationCenters()
{
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT * FROM evacuation_centers");
    $evac_centers = $db_handle->runFetch();

    return $evac_centers;
}

function getAgencies()
{
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT * FROM `agency` ORDER BY AgencyName");
    $agencies = $db_handle->runFetch();

    return $agencies;
}

function getAllAssessmentTools() {
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT * FROM `form` ORDER BY FormType");
    $forms = $db_handle->runFetch();

    return $forms;
}

function getMultipleAssessmentTools($qIDs = '')
{
    if(!isset($qIDs)) $qIDs = ['z']; //safeguard for tampered $qIDs
    $db_handle = new DBController();
    $inQuery = implode(',', array_fill(0, count($qIDs), '?'));
    $db_handle->prepareStatement(
        'SELECT * FROM `form` WHERE FormID IN (' . $inQuery . ')'
    );
    $formInfo = $db_handle->fetchWithIn($qIDs);

    return $formInfo;
}

function getAssessmentTool($qID)
{
    if(!isset($qIDs)) $qIDs = ['z']; //safeguard for tampered $qIDs
    $db_handle = new DBController();

    $db_handle->prepareStatement("SELECT * FROM `form` WHERE FormID = :id");
    $db_handle->bindVar(':id', $qID, PDO::PARAM_INT,0);
    $formInfo = $db_handle->runFetch();
    if(!isset($formInfo))$formInfo = '';
    return $formInfo;
}

function getAssessmentQuestions($type = '',$qIDs = '') {
    if(!isset($qIDs)) $qIDs = ['z']; //safeguard for tampered $qIDs
    $db_handle = new DBController();
    if($type == 'Tool')
    {
        $toolIDs = implode(',',$qIDs);
        $inQuery = implode(',', array_fill(0, count($qIDs), '?'));
        $db_handle->prepareStatement(
            "SELECT FORM_FormID, QuestionsID, Question,
                    html_form.HTML_FORM_TYPE AS FormType,
                    html_form.HTML_FORM_INPUT_QUANTITY AS InputRange
            FROM `questions`
            JOIN html_form
                ON questions.HTML_FORM_HTML_FORM_ID = html_form.HTML_FORM_ID
            WHERE FORM_FormID IN (".$inQuery.") ORDER BY FIELD(FORM_FormID, ?)");

        $qIDs[] = $toolIDs;
        $questionsResult = $db_handle->fetchWithIn($qIDs);
    }
    else if($type == 'Intake')
    {
        $db_handle->prepareStatement(
            "SELECT INTAKE_IntakeID AS FORM_FormID, QuestionsID, Question,
                    html_form.HTML_FORM_TYPE AS FormType,
                    html_form.HTML_FORM_INPUT_QUANTITY AS InputRange
            FROM questions
            JOIN html_form
                ON questions.HTML_FORM_HTML_FORM_ID = html_form.HTML_FORM_ID
            WHERE INTAKE_IntakeID = :formID");
        $db_handle->bindVar(':formID', $qIDs, PDO::PARAM_INT,0);
        $questionsResult = $db_handle->runFetch();
    }
    if(!isset($questionsResult))$questionsResult = '';
    return $questionsResult;
}

function getEditToolQuestions($qID = '')
{
    if(!isset($qIDs)) $qIDs = ['z']; //safeguard for tampered $qIDs
    $db_handle = new DBController();

    $db_handle->prepareStatement(
        "SELECT * FROM `questions`
        LEFT JOIN html_form
            ON html_form.HTML_FORM_ID = questions.HTML_FORM_HTML_FORM_ID
        WHERE FORM_FormID = :id");
    $db_handle->bindVar(':id', $qID, PDO::PARAM_INT,0);
    $questionsResult = $db_handle->runFetch();

    if(!isset($questionsResult))$questionsResult = '';
    return $questionsResult;
}

function getIntakeInfo($id = '')
{
    $db_handle = new DBController();
    $db_handle->prepareStatement(
        "SELECT IntakeID as FormID,
            (CASE WHEN (AgeGroup = 2) THEN 'Intake for Adults' ELSE 'Intake for Children' END) AS FormType,
            DISASTER_DisasterID, AgeGroup
        FROM `intake` WHERE IntakeID = :id"
    );
    $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
    $intakeInfo = $db_handle->runFetch();

    return $intakeInfo;
}

function getAgeGroup($id = '')
{
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT (CASE WHEN (Age > 18) THEN '2' ELSE '1' END) AS AgeGroup FROM `idp` WHERE idp.IDP_ID = :id");
    $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
    $ageGroup = $db_handle->runFetch();

    return $ageGroup;
}

function getIntakeCount($idpID = '')
{
    $db_handle = new DBController();
    $db_handle->prepareStatement("SELECT COUNT(*) AS count FROM intake_answers WHERE IDP_IDP_ID = :id");
    $db_handle->bindVar(':id', $idpID, PDO::PARAM_INT,0);
    $intakeCount = $db_handle->runFetch();
    
    return $intakeCount;
}

function getIntakeID($idpID = '', $ag = '')
{
    $formID = 0;
    if(!filter_var($idpID, FILTER_VALIDATE_INT) === false) {
        $ag = $_GET['ag'];
    } else {
        $ag = 0;
    }
    if($ag == 1) {
        //children
        $formID = 1;
    } else if($ag == 2) {
        //adults
        $formID = 2;
    } else {
        $formID = 0;
    }

    return $formID;
}
#---- db fetch functions end ----

#---- assessment functions ----
function getList($data, $listType = 'IDP', $listTarget = '') 
{
    global $db_handle;
    $keyword = '';
    $order = '';
    $length = '';
    $query = '';
    $output = [];
    $tmp = [];
    $subArray = [];
    $draw = 0;
    $rowCount = 0;
    $recordsFiltered = 0;
    $type = $listType;
    $idpID = $listTarget;

    if(isset($_POST["draw"])) $draw = $data["draw"];
    if(isset($data["search"]["value"])) $keyword = $data["search"]["value"];
    if(isset($data["order"])) $order = $data["order"];
    if(isset($data["length"])) $length = $data["length"];

    if($listType === 'IDP')
    {
        $query .= "SELECT
                        CONCAT(Lname, ', ', Fname, ' ', Mname) AS IDPName, IDP_ID,
                        (CASE WHEN (Gender = 1) THEN 'Male' ELSE 'Female' END) AS Gender,
                        Age, COALESCE(MIN(j.INTAKE_ANSWERS_ID), 0) AS intake_answersID,
                        (CASE WHEN (Age > 18) THEN 2 ELSE 1 END) AS age_group 
                FROM `idp` i 
                LEFT JOIN intake_answers j ON i.IDP_ID = j.IDP_IDP_ID ";

        if($keyword != '')
        {
            $query .= " WHERE Lname LIKE :keyword OR Fname LIKE :keyword OR Mname LIKE :keyword  OR Fname LIKE :keyword OR IDP_ID LIKE :keyword OR DAFAC_DAFAC_SN LIKE :keyword ";
        }

        if($order != '')
        {
            if($order['0']['dir'] == 'asc')
            {
                $query .= 'GROUP BY i.IDP_ID, IDPName ORDER BY :orderColumn ASC ';
            }
            else
            {
                $query .= 'GROUP BY i.IDP_ID, IDPName ORDER BY :orderColumn DESC ';
            }

        }
        else
        {
            $query .= 'GROUP BY i.IDP_ID, IDPName ORDER BY 1 ASC ';
        }
    }
    else if($listType === 'Evac')
    {
        $query .= "SELECT * FROM `evacuation_centers`  ";

        if($keyword != '')
        {
            $query .= " WHERE EvacuationCentersID LIKE :keyword OR EvacName LIKE :keyword OR EvacAddress LIKE :keyword OR EvacType LIKE :keyword OR EvacManager LIKE :keyword OR SpecificAddress LIKE :keyword ";
        }

        if($order != '')
        {
            if($order['0']['dir'] == 'asc')
            {
                $query .= 'ORDER BY :orderColumn ASC ';
            }
            else
            {
                $query .= 'ORDER BY :orderColumn DESC ';
            }
        }
        else
        {
            $query .= 'ORDER BY 1 ASC ';
        }
    }
    else if($listType === 'Tool')
    {
        $query .= "SELECT FormType, FormID FROM `form`  ";

        if($keyword != '')
        {
            $query .= " WHERE FormID LIKE :keyword OR FormType LIKE :keyword ";
        }

        if($order != '')
        {
            if($order['0']['dir'] == 'asc')
            {
                $query .= 'ORDER BY :orderColumn ASC ';
            }
            else
            {
                $query .= 'ORDER BY :orderColumn DESC ';
            }
        }
        else
        {
            $query .= 'ORDER BY 1 ASC ';
        }
    }
    else if($listType === 'Assessment_taken')
    {
        $query .= "SELECT form_answers.DateTaken,
                          form.FormType AS FormID,
                          form_answers.Score,
                          auto_assmt.Assessment,
                          form_answers.IDP_IDP_ID AS IDP,
                          form_answers.FORM_ANSWERS_ID,
                          CONCAT(user.Lname, ', ', user.Fname, ' ', user.Mname) as User,
                          form_answers.UnansweredItems,
                          auto_assmt.Cutoff,
                          form_answers.FORM_FormID
                    FROM form_answers
                    LEFT JOIN form ON form_answers.FORM_FormID = form.FormID
                    LEFT JOIN auto_assmt ON form_answers.FORM_FormID = auto_assmt.FORM_FormID
                    LEFT JOIN user ON form_answers.USER_UserID = user.UserID
                    WHERE form_answers.IDP_IDP_ID = :idpID ";

        if($keyword != '')
        {
            $query .= " AND FormType LIKE :keyword OR A.User LIKE :keyword ";
        }

        if($order != '')
        {
            if($order['0']['dir'] == 'asc')
            {
                $query .= 'ORDER BY :orderColumn ASC ';
            }
            else
            {
                $query .= 'ORDER BY :orderColumn DESC ';
            }
        }
        else
        {
            $query .= 'ORDER BY DateTaken ASC, FormType ASC ';
        }
    }
    else if($listType === 'Intake')
    {
        $query = "SELECT intake_answers.IDP_IDP_ID AS IDP, intake_answers.INTAKE_ANSWERS_ID,
                            IF(intake_answers.INTAKE_IntakeID = 2, 'Intake for Adults', 'Intake for Children') as FormID,
                            CONCAT(user.Lname, ', ', user.Fname, ' ', user.Mname) AS User,
                            intake_answers.Date_taken AS DateTaken, 'N/A' AS Score
                    FROM intake_answers
                    JOIN intake
                        ON intake_answers.INTAKE_IntakeID = intake.IntakeID
                    JOIN user
                        ON user.UserID = intake_answers.USER_UserID
                    WHERE intake_answers.IDP_IDP_ID = :id  ";

        $db_handle->prepareStatement($query);
        $db_handle->bindVar(':id', $listTarget, PDO::PARAM_STR,0);
        $firstResult = $db_handle->runFetch();

        $query = '';
        $rowCount = 0;

        if(!empty($firstResult)){
            foreach($firstResult as $forms) {
                if($forms["FormID"] == "Intake for Adults") {
                    $query = "SELECT Date_taken AS DateTaken,

                                    (SELECT Answer FROM answers_quanti WHERE INTAKE_ANSWERS_INTAKE_ANSWERS_ID = :intakeID AND QUESTIONS_QuestionsID = 220) as Result1,

                                    (SELECT Answer FROM answers_quanti WHERE INTAKE_ANSWERS_INTAKE_ANSWERS_ID = :intakeID AND QUESTIONS_QuestionsID = 221) as Result2,

                                    (SELECT Answer FROM answers_quali WHERE INTAKE_ANSWERS_INTAKE_ANSWERS_ID = :intakeID AND QUESTIONS_QuestionsID = 222) as Result3,

                                    (SELECT Answer FROM answers_quanti WHERE INTAKE_ANSWERS_INTAKE_ANSWERS_ID = :intakeID AND QUESTIONS_QuestionsID = 223) as Result4,

                                    CONCAT(user.Lname, ', ', user.Fname, ' ', user.Mname) AS User,
                                    INTAKE_IntakeID

                    FROM intake_answers
                    JOIN user
                        ON intake_answers.USER_UserID = user.UserID
                    WHERE INTAKE_ANSWERS_ID = :intakeID ";
                } else {
                    $query = "SELECT Date_taken AS DateTaken,

                                    (SELECT Answer FROM answers_quanti WHERE INTAKE_ANSWERS_INTAKE_ANSWERS_ID = :intakeID AND QUESTIONS_QuestionsID = 216) as Result1,

                                    (SELECT Answer FROM answers_quanti WHERE INTAKE_ANSWERS_INTAKE_ANSWERS_ID = :intakeID AND QUESTIONS_QuestionsID = 217) as Result2,

                                    (SELECT Answer FROM answers_quali WHERE INTAKE_ANSWERS_INTAKE_ANSWERS_ID = :intakeID AND QUESTIONS_QuestionsID = 218) as Result3,

                                    (SELECT Answer FROM answers_quanti WHERE INTAKE_ANSWERS_INTAKE_ANSWERS_ID = :intakeID AND QUESTIONS_QuestionsID = 219) as Result4,

                                    CONCAT(user.Lname, ', ', user.Fname, ' ', user.Mname) AS User,
                                    INTAKE_IntakeID

                 FROM intake_answers
                 JOIN user
                    ON intake_answers.USER_UserID = user.UserID
                 WHERE INTAKE_ANSWERS_ID = :intakeID ";
                }
                
                $db_handle->prepareStatement($query);
                
                $db_handle->bindVar(':idpID', $listTarget, PDO::PARAM_INT,0);
                $db_handle->bindVar(':intakeID', $forms["INTAKE_ANSWERS_ID"], PDO::PARAM_INT,0);
                
                $result = $db_handle->runFetch();
                $rowCount += $db_handle->getFetchCount();

                if($rowCount != 0)
                {
                    foreach($result as $row)
                    {
                        $recordsFiltered = get_total_all_records('Intake', $listTarget);

                        $subArray["DT_RowId"] = $forms["INTAKE_ANSWERS_ID"];
                        $phpdate = strtotime($row['DateTaken']);
                        $subArray[] = date('M d, Y <\b\r> h:i a', $phpdate);
                        if(isset($row['Result1'])) {
                            $subArray[] = ($row['Result1' ]== '0' ? 'Yes' : 'No');
                        } else {
                            $subArray[] = '(blank)';
                        }
                        if(isset($row['Result2'])) {
                            $subArray[] = ($row['Result2' ]== '0' ? 'Yes' : 'No');
                        } else {
                            $subArray[] = '(blank)';
                        }
                        if(isset($row['Result3'])) {
                            $subArray[] = $row['Result3'];
                        } else {
                            $subArray[] = '(blank)';
                        }
                        if(isset($row['Result4'])) {
                            if($row['Result4'] == '0') {
                                $subArray[] = 'No changes';
                            } else if($row['Result4'] == '1') {
                                $subArray[] = 'Slightly Improved (less than 20%)';
                            } else if($row['Result4'] == '2') {
                                $subArray[] = 'Moderately Improved (20%-60%)';
                            } else if($row['Result4'] == '3') {
                                $subArray[] = 'Much improved (60%-80%)';
                            } else if($row['Result4'] == '4') {
                                $subArray[] = 'Very much improved (more than 80%)';
                            }
                        } else {
                            $subArray[] = '(blank)';
                        }

                        $subArray[] = $forms["User"];
                    }
                    $tmp[] = $subArray;
                    $subArray = [];
                } 
                else
                {
                    $tmp = [];
                }
            }
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => $rowCount,
            "recordsFiltered" => $recordsFiltered,
            "data" => $tmp
        );

        return $output;
    }

    if($length != '')
    {
        $query .= 'LIMIT '.$data['start'].', '.$length;
    }

    $statement = $db_handle->prepareStatement($query);

    if($keyword != '')
    {
        $db_handle->bindVar(':keyword', '%'.$keyword.'%', PDO::PARAM_STR,0);
    }

    if($order != '')
    {
        $db_handle->bindVar(':orderColumn', ($order['0']['column'] + 1), PDO::PARAM_INT, 0);
    }

    if($listType === 'Assessment_taken')
    {
        $db_handle->bindVar(':idpID', $listTarget, PDO::PARAM_INT,0);
    }

    $result = $db_handle->runFetch();
    $rowCount = $db_handle->getFetchCount();

    if($rowCount != 0)
    {
        foreach($result as $row)
        {
            if($listType === 'IDP')
            {
                $recordsFiltered = get_total_all_records('IDP', 0);

                $subArray["DT_RowId"] = $row["IDP_ID"];
                $subArray[] = $row["IDPName"];
                $subArray[] = $row["IDP_ID"];
                $subArray[] = $row["Gender"];
                $subArray[] = $row["Age"];

                if($row['intake_answersID'] == 0) {
                    $subArray[] = 
                        '<a class="btn btn-info btn-sm btn-block" href="idp.assessment.history.php?id='.$row["IDP_ID"].'">
                            <i class="pe-7s-info"></i>Assessment History
                        </a>
                        <a href="assessment.informed.consent.php?id='.$row["IDP_ID"].'&ag='.$row["age_group"].'&from=intake" class="btn btn-success btn-xs btn-block">
                            <i class="icon_check_alt"></i>Apply Intake
                        </a>';
                } 
                else
                {
                    $subArray[] = 
                        '<a class="btn btn-info btn-sm btn-block" href="idp.assessment.history.php?id='.$row["IDP_ID"].'">
                            <i class="pe-7s-info"></i>Assessment History
                         </a>
                         <a href="assessment.informed.consent.php?id='.$row["IDP_ID"].'&ag='.$row["age_group"].'&from=intake" class="btn btn-success btn-xs btn-block">
                            <i class="icon_check_alt"></i>Apply Intake
                         </a>
                         <a href="assessment.select.forms.php?id='.$row["IDP_ID"].'" class="btn btn-primary btn-xs btn-block">
                                Apply Assessment Tool
                         </a>';
                }
            }
            else if($listType === 'Tool')
            {
                $recordsFiltered = get_total_all_records('Tool', 0);

                $subArray["DT_RowId"] = $row["FormID"];
                $subArray[] = $row["FormType"];
                $subArray[] = 
                    '<a class="btn btn-info btn-xs center-block" href="forms.edit.tool.php?form_id='.$row["FormID"].'">
                        <i class="fa fa-pencil-square-o"></i>Edit Tool
                     </a>';
            }
            else if($listType === 'Evac')
            {
                $recordsFiltered = get_total_all_records('Evac', 0);
                $subArray["DT_RowId"] = $row["EvacuationCentersID"];
                $subArray[] = $row["EvacuationCentersID"];
                $subArray[] = $row["EvacName"];
                $subArray[] = $row["EvacAddress"];
                $subArray[] = $row["EvacType"];
                $subArray[] = $row["EvacManager"];
                $subArray[] = $row["EvacManagerContact"];
                $subArray[] = $row["SpecificAddress"];
            }
            else if($listType === 'Assessment_taken')
            {
                $recordsFiltered = get_total_all_records('Assessment_taken', $listTarget);

                $subArray["DT_RowId"] = $row["FORM_ANSWERS_ID"];

                $phpdate = strtotime($row['DateTaken']);
                $subArray[] = date('M d, Y <\b\r> h:i a', $phpdate);
                $subArray[] = $row["FormID"];

                if(!isset($row['UnansweredItems']) || $row['UnansweredItems'] == '') {
                    $subArray[] = $row["Score"];
                } else {
                    $subArray[] = 'partial: '.$row["Score"];
                }

                if(isset($row['Assessment'])) {
                    if($row['Score'] >= $row['Cutoff']) {
                        $subArray[] = $row['Assessment'];
                    } else {
                        $subArray[] = 'Below cutoff';
                    }
                } else {
                    $subArray[] = 'No auto-assessment available for this tool.';
                }
                $subArray[] = $row["User"];
            }

            $tmp[] = $subArray;
            $subArray = [];

        }
    } 
    else
    {
        $tmp = [];
    }


    $output = array(
        "draw" => intval($draw),
        "recordsTotal" => $rowCount,
        "recordsFiltered" => $recordsFiltered,
        "data" => $tmp
    );

    return $output;
}

function get_total_all_records($type, $target = '')
{
    $db_handle = new DBController();

    if($type === 'IDP')
    {
        $db_handle->prepareStatement("SELECT COUNT(*) AS total FROM `idp`");
        $result = $db_handle->runFetch();
    }
    else if($type === 'Tool')
    {
        $db_handle->prepareStatement("SELECT COUNT(*) AS total FROM `form`");
        $result = $db_handle->runFetch();
    }
    else if($type === 'Evac')
    {
        $db_handle->prepareStatement("SELECT COUNT(*) AS total FROM `evacuation_centers`");
        $result = $db_handle->runFetch();
    }
    else if($type === 'Intake')
    {
        $db_handle->prepareStatement("SELECT COUNT(intake_answers.INTAKE_ANSWERS_ID) AS total FROM intake_answers WHERE intake_answers.IDP_IDP_ID = :id");
        $db_handle->bindVar(':id', $target, PDO::PARAM_INT, 0);
        $result = $db_handle->runFetch();
    }
    else if($type === 'Assessment_taken')
    {
        $db_handle->prepareStatement("SELECT COUNT(FormType) AS total
                   FROM
                        (SELECT form_answers.IDP_IDP_ID, form_answers.FORM_ANSWERS_ID, FormType, form_answers.USER_UserID, form_answers.DateTaken, form_answers.UnansweredItems,
                            CONCAT(user.Lname, ', ', user.Fname, ' ', user.Mname) as User,
                            auto_assmt.Assessment, auto_assmt.Cutoff,auto_assmt.FORM_FormID
                        FROM form_answers
                        JOIN form ON form.FormID = form_answers.FORM_FormID
                        JOIN user ON user.UserID = form_answers.USER_UserID
                        LEFT JOIN auto_assmt ON auto_assmt.FORM_FormID = form_answers.FORM_FormID
                        WHERE form_answers.IDP_IDP_ID = :id) A
                   RIGHT JOIN
                        (SELECT DISTINCT(IDP_IDP_ID), FORM_ANSWERS_ID,
                            COALESCE(SUM(answers_quanti.Answer),0) as Score
                        FROM answers_quanti
                        RIGHT JOIN form_answers ON form_answers.FORM_ANSWERS_ID = answers_quanti.FORM_ANWERS_FORM_ANSWERS_ID
                        WHERE form_answers.IDP_IDP_ID = :id
                        GROUP BY FORM_ANWERS_FORM_ANSWERS_ID) B
                   ON A.FORM_ANSWERS_ID = B.FORM_ANSWERS_ID");
        $db_handle->bindVar(':id', $target, PDO::PARAM_INT, 0);
        $result = $db_handle->runFetch();
    }


    return $result[0]['total'];
}
?>