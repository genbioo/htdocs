<?php
include("../initialize.php");
includeCore();

includeLayoutGenerator();

$id = $_GET['form_id'];

$db_handle = new DBController();

$questions = getEditToolQuestions($id);
$form_info = getAssessmentTool($id);
$q_has_null_form = false;
$nullCount = 0;
$questTranslations = [];
$languages = [];
$qCount = count($questions);
if(!empty($questions)) {
    foreach($questions as $question) {
        if(array_key_exists('HTML_FORM_HTML_FORM_ID', $question) && is_null($question['HTML_FORM_HTML_FORM_ID'])) {
            $q_has_null_form = true;
            $nullCount++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php includeHead("PSRMS - Edit Tool"); ?>

    </head>

    <body>

        <div id="wrapper">
            <?php includeNav(); ?>
            <div id="page-wrapper">
                <div class="row">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/pages/forms.manage.tools.php">Assessment Tools</a></li>
                        <li class="breadcrumb-item active">Edit Assessment Tool</li>
                    </ol>
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3><?php if(!empty($form_info)) { foreach ($form_info as $info) { echo($info['FormType']); }} ?></h3>
                                </div>
                            <div class="panel-body">
                                    <div class="col-md-12">
                                        <h5>Instructions: <?php if(!empty($form_info)) { foreach ($form_info as $info) { echo($info['Instructions']); }} ?></h5>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-info btn-fill btn-xs" onClick ="load_modal('title<?php echo($id); ?>')"><span class="fa fa-pencil"></span>Edit Title</button>
                                        <!-- this button is for a workaround in triggering the edit modal -->
                                        <!-- without this workaround, button needs to be clicked twice before modal shows -->
                                        <button class="btn btn-primary btn-lg" id="titleTrigger<?php echo($id); ?>" data-toggle="modal" data-target="#myModaltitle<?php echo($id); ?>" style="display:none"></button>

                                        <button type="button" class="btn btn-info btn-fill btn-xs" onClick ="load_modal('instr<?php echo($id); ?>')"><span class="fa fa-pencil"></span>Edit Instructions</button>
                                        <!-- this button is for a workaround in triggering the edit modal -->
                                        <!-- without this workaround, button needs to be clicked twice before modal shows -->
                                        <button class="btn btn-primary btn-lg" id="instrTrigger<?php echo($id); ?>" data-toggle="modal" data-target="#myModalinstr<?php echo($id); ?>" style="display:none"></button>
                                    </div>   
                                </div>    
                            </div>
                        </div>
                    </div><!-- row end -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3> 
                                            <?php
                                            //checking whether to display 'Question' or 'Questions' depending on the number of available questions
                                            echo($qCount === 1 ? "Question" : "Questions");
                                            ?>
                                            <?php
                                            if(!empty($questions)) {
                                            ?>
                                            <button type="button" class="btn btn-warning btn-fill btn-xs" onClick ="load_modal('trans<?php echo($id); ?>')"><i class="fa fa-plus-circle "></i>&nbsp;Add Translations</button>
                                            <!-- this button is for a workaround in triggering the add translation modal -->
                                            <!-- without this workaround, button needs to be clicked twice before modal shows -->
                                            <button type="button" class="btn btn-primary btn-lg" id="transTrigger<?php echo($id); ?>" data-toggle="modal" data-target="#myModaltrans<?php echo($id); ?>" style="display:none"></button>

                                            <?php
                                            }
                                            ?>

                                        <?php
                                        if(!empty($questions)) {
                                        ?>
                                        <div class="pull-right" id="languageSelect">
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </h3> 
                                </div> <!-- panel heading -->
                                <div class="panel-body">
                                    <div class="form-group">
                                        <form id="form-layout" target="" action="" onsubmit="return validate_forms()" method="post">
                                            <?php
                                            if(!empty($questions)) {
                                                foreach ($questions as $question) {
                                                    //-----Tokenization of questions and translations------
                                                    //use "id"+(questionID) as a key for the initial-nested-associative array for translation storage XD
                                                    //each translation will be saved in $questTranslations["id".(questionID)]["translations"][(Language)]
                                                    $questTranslations = array_merge($questTranslations, array("id".$question['QuestionsID'] => array("translations" => array())));
                                                    //tokenize string, use '[' as the basis of division
                                                    $arr = explode("[", $question['Question']);
                                                    foreach($arr as $translation) {
                                                        $questionSubGroup = array();
                                                        //extract first token which will not have ']'
                                                        if (!(strpos($translation, ']') !== false)) {
                                                            //include it to our nested-associative array for questions and its translations, setting 'Original' as the first language
                                                            $questTranslations["id".$question['QuestionsID']]["translations"] = array_merge($questTranslations["id".$question['QuestionsID']]["translations"], array("Original" => $translation));
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
                                                            $questTranslations["id".$question['QuestionsID']]["translations"] = array_merge($questTranslations["id".$question['QuestionsID']]["translations"], array($translation[0] => $translation[1]));
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
                                            <table align="center" cellspacing="3" cellpadding="3" width="90%" class="table-hover table-responsive  table-striped">
                                                <div class="col-md-8">
                                                    <tr>
                                                        <td align="left" style="width:90%">
                                                            <p style="margin-bottom: 20px; margin-top: 20px;">
                                                                <?php
                                                    foreach($questTranslations["id".$question['QuestionsID']]["translations"] as $key => $translation) {
                                                        //echo($key.": ".$translation."<br>");
                                                        echo('<div name="'.$key.'">'.$translation);
                                                                ?>
                                                                <button type="button" class="btn btn-info btn-fill btn-xs" onClick ="load_modal('quest<?php echo($question['QuestionsID']); ?>')"><span class="fa fa-pencil"></span>Edit Question</button>
                                                                <!-- this button is for a workaround in triggering the edit modal -->
                                                                <!-- without this workaround, button needs to be clicked twice before modal shows -->
                                                                <button type="button" class="btn btn-primary btn-lg" id="questTrigger<?php echo($question['QuestionsID']); ?>" data-toggle="modal" data-target="#myModalquest<?php echo($question['QuestionsID']); ?>" style="display:none"></button>
                                                                <?php echo'</div>';
                                                    }
                                                                ?>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </div>
                                                <tr>
                                                    <?php
                                                    if($question['AnswerType'] == 1) {
                                                    ?>
                                                    <td>
                                                        <div class="col-md-4">
                                                            <label for="formType">Input Type:</label>
                                                            <?php
                                                        //the name of the select form below starts with 1 (1-qid)
                                                        //1 denotes the selection value is for a quantitative question
                                                            ?>
                                                            <select class="form-control" id="formType" name="1-<?php echo($question['QuestionsID']); ?>">
                                                                <option value="radio"<?php echo($question['HTML_FORM_TYPE'] == "radio") ? " selected='selected'":""; ?>>Radio Buttons</option>
                                                                <option value="checkbox"<?php echo($question['HTML_FORM_TYPE'] == "checkbox") ? " selected='selected'":""; ?>>Checkboxes</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="formRange">Max Range:</label>
                                                            <select class="form-control" id="formRange" name="range-<?php echo($question['QuestionsID']); ?>">
                                                                <?php
                                                        for($i = 2; $i < 10; ++$i) {
                                                            echo("<option value='".$i."'");
                                                            if($i == $question['HTML_FORM_INPUT_QUANTITY']) {
                                                                echo(" selected='selected'");
                                                            }
                                                            echo(">".$i."</option>");
                                                        }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <?php
                                                    } else {
                                                    ?>
                                                    <td>
                                                        <div class="col-md-4">
                                                            <label for="formType">Input Type:</label>
                                                            <?php
                                                        //the name of the select form below starts with 2 (2-qid)
                                                        //2 denotes the selection value is for a qualitative question
                                                            ?>
                                                            <select class="form-control" id="formType" name="2-<?php echo($question['QuestionsID']); ?>">
                                                                <option value="text"<?php echo($question['HTML_FORM_TYPE'] == "text") ? " selected='selected'":""; ?>>Text (Single Line)</option>
                                                                <option value="textarea"<?php echo($question['HTML_FORM_TYPE'] == "textarea") ? " selected='selected'":""; ?>>Textbox (Multiple Lines)</option>
                                                                <input type="hidden" name="range-<?php echo($question['QuestionsID']); ?>" value="null">
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <?php
                                                    }
                                                    ?>
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
                                            <input type="hidden" name="formID" value="<?php echo($id); ?>">
                                            <br>
                                            <table align="center" cellspacing="3" cellpadding="3" width="90%" class="table-responsive">
                                                <tr>
                                                    <td class="align-bottom">
                                                        <div class="col-md-12">
                                                            <button type="submit" onClick="ChangeTarget('sme')" class="btn btn-primary btn-fill btn-md" style="float: right;"><i class="fa fa-floppy-o"></i>&nbsp;Save Layout</button>
                                                            <!--<button type="submit" onClick="ChangeTarget('new')" class="btn btn-secondary btn-fill btn-md" style="float: right; margin-right:5px;" disabled><i class="fa fa-eye"></i>&nbsp;Preview</button>-->
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>

                                    </div><!--form-group end--> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /#page-wrapper -->

        </div>

        <!-- container for edit title/instructions/questions modal -->
        <div id="modal-container">
        </div>

        <?php includeCommonJS(); ?>

    </body>
    <script type="text/javascript">
        //script for adding more questions in add question modal
        var i = 0;
        function add_more_questions() {
            var $div = $('div[id^="question-field-container-"]:last');

            var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;

            var $klon = $div.clone().prop('id', 'question-field-container-'+num );

            $($klon.find("[name='question_add"+(num-1)+"']")).attr("name","question_add"+num);
            $($klon.find("[name='answerType"+(num-1)+"']")[0]).attr("name","answerType"+num);
            $($klon.find("[name='answerType"+(num-1)+"']")[0]).attr("name","answerType"+num);
            $div.after( $klon );
        }
        function check_empty() {
            var flag_radio = true;
            var flag_textarea = true;

            $('input:radio.mdlrad').each(function () {
                name = $(this).attr('name');
                if (flag_radio && !$(':radio[name="' + name + '"]:checked').length) {
                    flag_radio = false;
                }
            });

            $('textarea.mdltxt').each(function() {
                if (!$.trim($(this).val())) {
                    flag_textarea = false; 
                }
            });

            if(flag_radio && flag_textarea) {
                return true;
            } else {
                alert("Please do not leave any field empty before submitting.");
                return false;
            }
        }

        window.load_modal = function(clicked_id) {
            $("#modal-container").load("/includes/fragments/forms.modal.edit.php?editing="+clicked_id.substring(0,5)+"&id="+clicked_id.substring(5), function() {
                if(clicked_id.substring(0,5) === "title") {
                    $('#titleTrigger'+clicked_id.substring(5)).click();
                } else if(clicked_id.substring(0,5) === "instr") {
                    $('#instrTrigger'+clicked_id.substring(5)).click();
                } else if(clicked_id.substring(0,5) === "quest") {
                    $('#questTrigger'+clicked_id.substring(5)).click();
                } else if(clicked_id.substring(0,5) === "trans") {
                    $('#transTrigger'+clicked_id.substring(5)).click();
                }
            });
        }
        function ChangeTarget(loc) {
            if(loc=="new") {
                document.getElementById('form-layout').target="_blank";
                document.getElementById('form-layout').action="/includes/actions/preview_form_layout.php";
            } else {
                document.getElementById('form-layout').target="";
                document.getElementById('form-layout').action="/includes/actions/forms.process.layout.php";
            }
        }
        <?php echo("var languages = ".json_encode($languages). ";\n"); ?>
        var langList = document.createElement("select"); //input element, text
        langList.setAttribute('class',"form-control");
        langList.setAttribute('id',"languages");
        langList.setAttribute('onChange', "showDiv(this)")
        document.getElementById("languageSelect").appendChild(langList);
        $(function(){
            var options="<option disabled selected>Translations</option>";
            for(var i = 0; i < languages.length; i++) {
                options+="<option value='"+languages[i]+"'>"+languages[i]+"</option>";
            }       
            $("#languages").html(options);        
        });

        $('div[name='+languages[0]+']').show().siblings().hide();
        function showDiv(elem){
            for(var i = 0; i < languages.length; i++) {
                if(elem.value == languages[i])
                    $('div[name='+languages[i]+']').show().siblings().hide();
            }
        }
    </script>

</html>