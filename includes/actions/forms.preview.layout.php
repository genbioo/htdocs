<?php
include("../../initialize.php");
includeCore();

$formID = $_POST['formID'];
$questTranslations = array();   //final associative array of tokenized questions and translations
$languages = array();           //array of available translation languages *updates in the question display foreach section
$db_handle = new DBController();
if(isset($_POST["isIntake"])) {
    $db_handle->prepareStatement("SELECT * FROM `intake` WHERE IntakeID = :formID");
    $db_handle->bindVar(':formID', $formID. PDO::PARAM_INT, 0);
    $form_info = $db_handle->runFetch();
    $itemStart = 0;
    $db_handle->prepareStatement("SELECT INTAKE_IntakeID, QuestionsID, Question, html_form.HTML_FORM_TYPE as FormType, html_form.HTML_FORM_INPUT_QUANTITY as InputRange, AnswerType FROM intake LEFT JOIN questions on questions.INTAKE_IntakeID = intake.IntakeID LEFT JOIN html_form on questions.HTML_FORM_HTML_FORM_ID = html_form.HTML_FORM_ID WHERE intake.IntakeID = :formID");
    $db_handle->bindVar(':formID', $formID, PDO::PARAM_INT, 0);
    $questions = $db_handle->runFetch();
} else {
    $db_handle->prepareStatement("SELECT FormID, FormType, Instructions, ItemStart FROM `form` WHERE FormID = :formID");
    $db_handle->bindVar(':formID', $formID, PDO::PARAM_INT,0);
    $form_info = $db_handle->runFetch();
    $itemStart = 0;
    $db_handle->prepareStatement("SELECT FORM_FormID, QuestionsID, Question, html_form.HTML_FORM_TYPE as FormType, html_form.HTML_FORM_INPUT_QUANTITY as InputRange, AnswerType FROM form LEFT JOIN questions on questions.FORM_FormID = form.FormID LEFT JOIN html_form on questions.HTML_FORM_HTML_FORM_ID = html_form.HTML_FORM_ID WHERE form.FormID = :formID");
    $db_handle->bindVar(':formID', $formID, PDO::PARAM_INT,0);
    $questions = $db_handle->runFetch();
}
?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary" style="margin-top: 50px;">
                    <div class="panel-heading text-center">
                        <h2>
                        <?php
                        if(!empty($form_info)) {
                            foreach ($form_info as $info) {
                                echo("(THIS IS A PREVIEW)<br>");
                                if(isset($_POST['isIntake'])) {
                                    foreach ($form_info as $info) {
                                        if($info['AgeGroup'] === '1') {
                                            echo ("Intake for adults");
                                        } else if ($intake['AgeGroup'] === '2') {
                                            echo ("Intake for children");
                                        }
                                    }
                                } else {
                                    echo($info["FormType"]);
                                }
                                if(isset($info['ItemStart'])) $itemStart = $info['ItemStart'];
                            }
                        } ?>
                        </h2></div>
                    <div class="panel-body" style="padding: 20px; 50px;">
                        <h3 style="margin: 10px 40px;">
                            Current IDP: <b>Tamad, Juan Pedro</b><br>
                        </h3>
                        <h4 style="margin: 20px 40px;">
                            <?php
                                if(!empty($form_info)) {
                                    foreach ($form_info as $info) {
                                        if(isset($_POST['isIntake'])) {
                                            foreach ($form_info as $info) {
                                                if($info['AgeGroup'] === '1') {
                                                    echo ("Intake for adults");
                                                } else if ($intake['AgeGroup'] === '2') {
                                                    echo ("Intake for children");
                                                }
                                            }
                                        } else {
                            ?>
                                            <b>Instructions: </b><?php if(!empty($form_info)) { foreach ($form_info as $info) { echo($info['Instructions']); }} ?>
                            <?php
                                        }
                                    }
                                } ?>
                        </h4>
                        <div class="col-md-9"><h3><b>Questions:</b></h3></div>
                        <div class="col-md-3" id="languageSelect">
                        </div>
                        <form action="submit_answers.php" method="post">
                            <?php
                            if(!empty($questions)) {
                            ?>
                            <?php
                                foreach ($questions as $result) {
                                    $qid = $result['QuestionsID'];
                                    //-----Tokenization of questions and translations------
                                    //use "id"+(questionID) as a key for the initial-nested-associative array for translation storage XD
                                    //each translation will be saved in $questTranslations["id".(questionID)]["translations"][(Language)]
                                    $questTranslations = array_merge($questTranslations, array("id".$qid => array("translations" => array())));
                                    //tokenize string, use '[' as the basis of division
                                    $arr = explode("[", $result['Question']);
                                    foreach($arr as $translation) {
                                        $questionSubGroup = array();
                                        //extract first token which will not have ']'
                                        if (!(strpos($translation, ']') !== false)) {
                                            //include it to our nested-associative array for questions and its translations, setting 'Original' as the first language
                                            $questTranslations["id".$qid]["translations"] = array_merge($questTranslations["id".$qid]["translations"], array("Original" => $translation));
                                            //to avoid duplicates, check if the language is already in the array
                                            if(!in_array("Original", $languages, true)) {
                                                //add "Original" as a language in $languages
                                                array_push($languages, "Original");
                                            }
                                        } else {
                                            //succeeding tokens will have a language followed by ']'. ie. "English]This is a sample"
                                            //tokenize string. ie "English]This is a sample" --> $translation[0] = "English"; $translation[1] = "This is a sample"
                                            $translation = explode("]", $translation, 2);
                                            ////include it to our nested-associative array setting $translation[0] as the language
                                            $questTranslations["id".$qid]["translations"] = array_merge($questTranslations["id".$qid]["translations"], array($translation[0] => $translation[1]));
                                            //to avoid duplicates, check if the language is already in the array
                                            if(!in_array($translation[0], $languages, true)) {
                                                //add $translation[0] as a language in $languages
                                                array_push($languages, $translation[0]);
                                            }
                                        }
                                    }
                                    //sample translation associative array entry:
                                    //$questTranslations["id25"]["translations"]["English"] = "This is a sample"
                                    //$questTranslations["id25"]["translations"]["Filipino"] = "Ito ay isang halimbawa"
                                    //-----Tokenization End------
                            ?>
                            <table align="center" cellspacing="3" cellpadding="3" width="90%" class=" table-responsive">
                                <tr>
                                    <td align="left" style="width:90%" name="no">
                                        <h4>
                                            <?php
                                    foreach($questTranslations["id".$qid]["translations"] as $key => $translation) {
                                        //echo($key.": ".$translation."<br>");
                                        echo('<div name="'.$key.'">'.$translation."</div>");
                                    }
                                            ?>
                                        </h4>
                                    </td>
                                </tr>
                                <tr name="preview-wrapper" style="margin-bottom: 30px;">
                                    <td id="preview-wrapper<?php echo($qid); ?>" >
                                        <?php
                                    if(isset($result['AnswerType'])) {
                                        echo '<fieldset id="q-a-'.$qid.'">';
                                        if($result['AnswerType'] == "1") {
                                            for($i = $itemStart; $i < $_POST['range-'.$qid] + $itemStart; $i++) {
                                                echo'<label class="'.$_POST['1-'.$qid].'-inline"><input type="'.$_POST['1-'.$qid].'" name="1-'.$qid.'" value="'.$i.'">'.$i.'</label>';
                                            }
                                        } else {
                                            if($_POST['2-'.$qid] === "textarea") {
                                                echo '<textarea class="form-control" rows="5" id="comment" name="2-'.$qid.'"></textarea>';
                                            } else if($_POST['2-'.$qid] === "text") {
                                                echo '<input class="form-control" id="inputdefault" type="'.$_POST['2-'.$qid].'" name="2-'.$qid.'">';
                                            }
                                        }
                                        echo '</fieldset>';
                                    }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <?php
                                }
                            } else { ?>
                            <table align="center" cellspacing="3" cellpadding="3" width="90%" class="table-responsive">
                                <tr>

                                    <td align="left">
                                        <h4>No questions for this form yet!</h4>
                                    </td>

                                </tr>
                            </table>
                            <?php
                            }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    //function for changing <form> target and action.
    //for preview and save buttons
    function ChangeTarget(loc) {
        if(loc=="new") {
            document.getElementById('form-layout').target="_blank";
            document.getElementById('form-layout').action="preview_form_layout.php";
        } else {
            document.getElementById('form-layout').target="";
            document.getElementById('form-layout').action="submit_form_layout.php";
        }
    }
    //instantiate languages array
    <?php echo("var languages = ".json_encode($languages). ";\n"); ?>
    //create dropdown list of languages
    var langList = document.createElement("select"); //input element, text
    langList.setAttribute('class',"form-control");
    langList.setAttribute('id',"languages");
    langList.setAttribute('onChange', "showDiv(this)")
    //append select
    document.getElementById("languageSelect").appendChild(langList);
    //create options based on the languages array
    $(function(){
        var options="";
        for(var i = 0; i < languages.length; i++) {
            options+="<option value='"+languages[i]+"'>"+languages[i]+"</option>";
        }       
        $("#languages").html(options);        
    });

    //show first translation as default
    $('div[name='+languages[0]+']').show().siblings().hide();
    //function for changing question display based on selected option
    function showDiv(elem){
        for(var i = 0; i < languages.length; i++) {
            //if selected option value is the same as language[i]
            if(elem.value == languages[i])
                //display <div> with name = languages[i] . Hide others
                $('div[name='+languages[i]+']').show().siblings().hide();
        }
    }
</script>