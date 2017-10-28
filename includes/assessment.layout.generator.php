<?php
function displayQuestions($questionsResult, $formInfo, $idpName)
{
    $questTranslations = [];
    $questions = [];
    $languages = [];
    $itemStart = 0;
    ob_start();
    ?>
    <div class="col-md-12 accordion" id="myAccordion">
        <?php
        if(!empty($formInfo)) {                
            foreach($formInfo as $form) {
                foreach($questionsResult as $result) {
                    if($result["FORM_FormID"] == $form["FormID"]) {
                        $questions[] = $result;
                    }
                }
                if(!empty($questions[0]["Question"])) {
                    $arr = explode("[", $questions[0]["Question"]);
                    foreach($arr as $translation) {
                        if (!(strpos($translation, ']') !== false)) {
                            if(!in_array("Original", $languages, true)) {
                                array_push($languages, "Original");
                            }
                        } else {
                            $translation = explode("]", $translation, 2);
                            if(!in_array($translation[0], $languages, true)) {
                                array_push($languages, $translation[0]);
                            }
                        }
                    }

                    unset($arr);
                }
        ?>
        <div class="panel panel-info">
            <div class="panel-heading text-left">
                <div class="row">
                    <div class="col-md-9 accordion-toggle" data-toggle="collapse" data-parent="#myAccordion" href="#collapsible-<?php echo($form['FormID']) ?>">
                        <h5>
                            <?php
                            echo($form['FormType']);
                                if(isset($form['ItemStart'])) $itemStart = $form['ItemStart'];
                            ?>
                        </h5>
                        <p>
                            <?php if($idpName != '') { ?> Name: <b><?php echo($idpName); ?></b><br><?php } ?>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <label for="languageSelect">Translation:</label>
                        <div id="languageSelect">
                            <select id="languages" class="form-control" onchange='showDiv(this,<?php echo($form['FormID']) ?>,<?php echo(json_encode($languages)); ?>)'>
                            <?php
                            foreach($languages as $language) {
                            ?>
                                <option value='<?php echo($language); ?>'><?php echo($language); ?></option>
                            <?php
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body panel-collapse collapse in" id="collapsible-<?php echo($form['FormID']) ?>">
                <p><?php if(isset($form['Instructions'])) echo($form['Instructions']); ?></p>
                    <?php
                if(!empty($questions)) {
                    ?>
                    <?php
                    foreach ($questions as $result) {
                        $questTranslations = array_merge($questTranslations, array("id".$result['QuestionsID'] => array("translations" => array())));
                        $arr = explode("[", $result['Question']);
                        foreach($arr as $translation) {
                            if (!(strpos($translation, ']') !== false)) {
                                $questTranslations["id".$result['QuestionsID']]["translations"] = array_merge($questTranslations["id".$result['QuestionsID']]["translations"], array("Original" => $translation));
                            } else {
                                $translation = explode("]", $translation, 2);
                                $questTranslations["id".$result['QuestionsID']]["translations"] = array_merge($questTranslations["id".$result['QuestionsID']]["translations"], array($translation[0] => $translation[1]));
                            }
                        }
                    ?>
                    <div class="col-lg-12">
                        <div class="col-lg-10">
                            <?php
                            foreach($questTranslations["id".$result['QuestionsID']]["translations"] as $key => $translation) {
                                echo('<div name="'.$key.'-'.$form['FormID'].'"><p>'.nl2br(htmlspecialchars($translation)).'</p></div>');
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="col-lg-10" id="preview-wrapper<?php echo($result['QuestionsID']); ?>" >
                            <?php 
                            if(isset($result['FormType'])) {
                                echo '<fieldset id="q-a-'.$result['QuestionsID'].'">';
                                echo '<input type="hidden" name="q-'.$form['FormID'].'-1-'.$result['QuestionsID'].'" value="'.$result['QuestionsID'].'">';
                                if(isset($result['InputRange'])) {
                                    for($i = $itemStart; $i < $result['InputRange'] + $itemStart; $i++) {
                                        echo'<label class="'.$result['FormType'].'-inline"><input type="'.$result['FormType'].'" name="'.$form['FormID'].'-1-'.$result['QuestionsID'].'" value="'.$i.'">'.$i.'</label>';
                                    }
                                } else {
                                    if($result['FormType'] === "textarea") {
                                        echo '<textarea class="form-control" rows="5" id="comment" name="'.$form['FormID'].'-2-'.$result['QuestionsID'].'"></textarea>';
                                    } else if($result['FormType'] === "text") {
                                        echo '<input class="form-control" id="inputdefault" type="'.$result['FormType'].'" name="'.$form['FormID'].'-2-'.$result['QuestionsID'].'">';
                                    }
                                }
                                echo '</fieldset>';
                            }
                            ?>
                            <hr>
                        </div>
                    </div>
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
                <div class="row">
                    <br>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#collapsible-<?php echo($form['FormID']) ?>" data-parent="#myAccordion"><i class="fa fa-eye-slash"></i>&nbsp;Hide</button>
                    </div>   
                </div>
            </div>
            <?php   
                    unset($questions);
                    unset($questTranslations);
                    unset($languages);
                
                    $questions = [];
                    $questTranslations = [];
                    $languages = []; 
                ?>
            
        </div>
        <?php
            }

        }
        ?>
    </div>
<?php
    return ob_get_clean();
}
#---- assessment functions end ----

#---- edit tool functions ----
function generateEditInterface($questions, $form_info)
{
    $questTranslations = [];
    $languages = [];
    $qCount = count($questions);
    ob_start();
    ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <div class="col-md-12">
                                <h3><?php if(!empty($form_info)) { foreach ($form_info as $info) { echo($info['FormType']); }} ?></h3>
                            </div>
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
                        </div><!--header end-->
                        <div class="content">
                            <div class="form-group">
                                <table align="center" cellspacing="3" cellpadding="3" width="90%" class="table-responsive">
                                    <tr>
                                        <td align="left">
                                            <h3>
                                                <div class="col-md-9">
                                                    <?php
                                                    //checking whether to display 'Question' or 'Questions' depending on the number of available questions
                                                    echo($qCount === 1 ? "Question" : "Questions");
                                                    ?>
                                                    <button type="button" class="btn btn-success btn-fill btn-xs" data-toggle="modal" data-target="#addQuestion"><i class="fa fa-plus-circle "></i>&nbsp;Add Question</button>
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

                                                </div>
                                                <?php
                                                if(!empty($questions)) {
                                                ?>
                                                <div class="col-md-3" id="languageSelect">
                                                </div>
                                                <?php
                                                }
                                                ?>
                                            </h3>
                                        </td>
                                    </tr>
                                </table>

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
                                                    <button type="submit" onClick="ChangeTarget('new')" class="btn btn-secondary btn-fill btn-md" style="float: right; margin-right:5px;"><i class="fa fa-eye"></i>&nbsp;Preview</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </form>

                            </div><!--form-group end-->
                        </div><!--content end-->

                    </div><!--card end-->
                </div><!--col-md-12 end-->
            </div><!--row end-->
        </div><!--container fluid end-->
    </div><!--content end-->
<?php
    return ob_get_clean();
}
#---- edit tool functions end ----
?>