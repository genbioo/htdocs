<?php
include("../../initialize.php");
includeCore();

$db_handle = new DBController();
$editing = $_GET['editing'];
$id = $_GET['id'];
$questTranslations = array();   //final associative array of tokenized questions and translations
$languages = array();           //array of available translation languages *updates in the question display foreach section

if($editing === "title") {
    $db_handle->prepareStatement("SELECT form.FormType AS 'result' FROM `form` WHERE FormID = :id");
    $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
    $text = $db_handle->runFetch();
} else if ($editing === "instr") {
    $db_handle->prepareStatement("SELECT form.Instructions AS 'result' FROM `form` WHERE FormID = :id");
    $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
    $text = $db_handle->runFetch();
} else if ($editing === "quest") {
    $db_handle->prepareStatement("SELECT questions.Question AS 'result' FROM `questions` WHERE QuestionsID = :id");
    $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
    $text = $db_handle->runFetch();
} else if ($editing === "trans") {
    $db_handle->prepareStatement("SELECT QuestionsID, Question FROM `questions` WHERE FORM_FormID = :id");
    $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
    $text = $db_handle->runFetch();
} else {
    echo("ERROR!");
}
?>
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<style>
    .carousel-control {
        position:unset;
        font-size:12px;
        color:#000;
        text-align:left;
        text-shadow: unset;
    }
    .carousel-control:hover,
    .carousel-control:focus {
        color: #000;
    }
</style>

<?php
if($editing === "trans") {
?>
<!-- ######################################################################## -->
<!-- #########            MODAL FOR ADDING TRANSLATIONS             ######### -->
<!-- ######################################################################## -->
<div id="myModal<?php echo($editing.$id); ?>" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="false">
                <div class="carousel-inner"> <!-- carousel container -->
                    <div class="item active"> <!-- IDP details container -->
                        <form action="/includes/actions/forms.process.add.translation.php?id=<?php echo($id); ?>" method="post">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <span style="font-size:20px;">Add Translations</span>
                                <span class="carousel-control" href="#carousel-example-generic" data-slide="next" role="button">
                                    Translation Edit History &gt;
                                </span>
                                <h5> Language: <input type="text" name="transLang" pattern="[a-zA-Z\s]+" placeholder="Letters and spaces only" required></h5>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <?php
                                    foreach($text as $question) {
                                    ?>
                                    <div class="form-group">
                                        <label for="question-<?php echo($question['QuestionsID']) ?>"><br>
                                        <?php
                                        $arr = explode("[", $question['Question']);
                                        echo($arr[0]);
                                        ?>
                                        </label>
                                        <textarea class="form-control mdltxt" rows="5" name="question-<?php echo($question['QuestionsID']) ?>"></textarea>
                                        <input type="hidden" name="oldQuestion-<?php echo($question['QuestionsID']) ?>" value="<?php echo($question['Question']) ?>">
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-fill btn-sm"><i class="fa fa-floppy-o"></i>&nbsp;Save</button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!--button type="button" class="btn btn-default" data-dismiss="modal">Close</button-->
                            </div>
                        </form>
                    </div>
                    <div class="item">  <!--Edit History-->
                        <?php
                          $db_handle->prepareStatement("SELECT EditHistoryID, CONCAT(Lname, ', ', Fname, ' ', Mname) as Name, LastEdit, QUESTIONS_QuestionsID, Remark FROM `edit_history` JOIN user ON user.UserID = edit_history.USER_UserID WHERE edit_history.FORM_FormID = :id AND SUBSTRING(Remark, 1, 19) = 'added translations' ORDER BY LastEdit DESC;");
                          $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
                          $editHistory = $db_handle->runFetch();
                        ?>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <span style="font-size:20px;">
                                History 
                            </span>
                            <span class="carousel-control" href="#carousel-example-generic" data-slide="prev" role="button">
                                &lt; Translation interface
                            </span>
                        </div>
                        <div class="modal-body">
                            <div>
                                <?php if(!empty($editHistory)) { ?>
                                <table align="left" cellspacing="3" cellpadding="3" width="75%" class="table table-hover">
                                    <tr>
                                        <th style="border-top: 0px solid black"><h6>Date</h6></th>
                                        <th style="border-top: 0px solid black"><h6>Edited by</h6></th>
                                        <th style="border-top: 0px solid black"><h6>Remarks</h6></th>
                                    </tr>
                                    <?php
                            foreach($editHistory as $entry) {
                                    ?>
                                    <tr>
                                        <td><h6>
                                            <?php
                                $phpdate = strtotime( $entry["LastEdit"] );
                                echo date( 'M d, Y <\b\r> h:i a', $phpdate );
                                            ?>
                                            </h6></td>
                                        <td><h6><?php echo($entry["Name"]); ?></h6></td>
                                        <td><h6><?php echo($entry["Remark"]); ?></h6></td>
                                    </tr>
                                    <?php
                            }
                                    ?>
                                </table>
                                <?php } else { ?>
                                <h4>No entries available.</h4>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--button type="button" class="btn btn-default" data-dismiss="modal">Close</button-->
                </div>
            </div>
        </div>

    </div>
</div><!-- Modal end -->
<?php
                         } else {
?>
<?php 
    $old = "";
    foreach($text as $result) { $old = $result['result']; }
?>
<!-- ######################################################################## -->
<!-- ######### MODAL FOR EDITING TITLE, INSTRUCTIONS, AND QUESTIONS ######### -->
<!-- ######################################################################## -->
<div id="myModal<?php echo($editing.$id); ?>" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="false">
                <div class="carousel-inner"> <!-- carousel container -->
                    <div class="item active"> <!-- IDP details container -->
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <span style="font-size:20px;">Edit
                                <?php
                                if($editing === "title") {
                                    echo("Title");
                                } else if ($editing === "instr") {
                                    echo("Instructions");
                                } else if ($editing === "quest") {
                                    echo("Question");
                                } else {
                                    echo("ERROR!");
                                }
                                ?></span>
                            <span class="carousel-control" href="#carousel-example-generic" data-slide="next" role="button">
                                Edit History &gt;
                            </span>
                        </div>
                        <div class="modal-body">
                            <form action="
                                <?php
                                if($editing === "title") {
                                    echo("/includes/actions/forms.process.edit.title.php");
                                } else if ($editing === "instr") {
                                    echo("/includes/actions/forms.process.edit.instruction.php");
                                } else if ($editing === "quest") {
                                    echo("/includes/actions/forms.process.edit.question.php");
                                } else {
                                    echo("#");
                                }
                                ?>" method="post">
                                <div>
                                    <div>
                                        <div class="form-group">
                                            <label><br>
                                            <?php
                                            if($editing === "title") {
                                                echo("Title");
                                            } else if ($editing === "instr") {
                                                echo("Instructions");
                                            } else if ($editing === "quest") {
                                                echo("Question");
                                            } else {
                                                echo("ERROR!");
                                            }
                                                ?>
                                            </label>
                                            <?php
                                            if($editing === "quest") {
                                            ?>
                                            <input type="hidden" name="oldItem" value="<?php echo(($old)) ?>">
                                            <?php
                                                //-----Tokenization of questions and translations------
                                                //use "id"+(questionID) as a key for the initial-nested-associative array for translation storage XD
                                                //each translation will be saved in $questTranslations["id".(questionID)]["translations"][(Language)]
                                                $questTranslations = array_merge($questTranslations, array("id".$id => array("translations" => array())));
                                                //tokenize string, use '[' as the basis of division
                                                $arr = explode("[", $old);
                                                foreach($arr as $translation) {
                                                    $questionSubGroup = array();
                                                    //extract first token which will not have ']'
                                                    if (!(strpos($translation, ']') !== false)) {
                                                        //include it to our nested-associative array for questions and its translations, setting 'Original' as the first language
                                                        $questTranslations["id".$id]["translations"] = array_merge($questTranslations["id".$id]["translations"], array("Original" => $translation));
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
                                                        $questTranslations["id".$id]["translations"] = array_merge($questTranslations["id".$id]["translations"], array($translation[0] => $translation[1]));
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
                                                foreach($questTranslations["id".$id]["translations"] as $key => $translation) {
                                                    echo("<p>".$key."</p>");
                                                    echo('<textarea class="form-control mdltxt" rows="5" name="'.$key.'">'.$translation.'</textarea>')
                                            ?>
                                            <?php
                                                }
                                            ?>
                                            <input type="hidden" name="itemID" value="<?php echo($id); ?>">
                                            <?php
                                            } else {
                                            ?>
                                            <input type="hidden" name="oldItem" value="<?php echo(($old)) ?>">
                                            <textarea class="form-control mdltxt" rows="5" name="textInput"><?php echo($old) ?></textarea>
                                            <input type="hidden" name="itemID" value="<?php echo($id); ?>">
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-fill btn-sm"><i class="fa fa-floppy-o"></i>&nbsp;Save</button>
                                </div>
                            </form>
                        </div>
                    </div><!---edit interface div(item)--->

                    <div class="item">  <!--Edit History-->
                        <?php
                        if($editing === "title") {
                            $db_handle->prepareStatement("SELECT EditHistoryID, CONCAT(Lname, ', ', Fname, ' ', Mname) as Name, LastEdit, FORM_FormID, Remark FROM `edit_history` JOIN user ON user.UserID = edit_history.USER_UserID WHERE FORM_FormID = :id AND Remark = 'edited the title' ORDER BY LastEdit DESC;");
                            $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
                            $editHistory = $db_handle->runFetch();
                        } else if ($editing === "instr") {
                            $db_handle->prepareStatement("SELECT EditHistoryID, CONCAT(Lname, ', ', Fname, ' ', Mname) as Name, LastEdit, FORM_FormID, Remark FROM `edit_history` JOIN user ON user.UserID = edit_history.USER_UserID WHERE FORM_FormID = :id AND Remark = 'edited the instructions' ORDER BY LastEdit DESC;");
                            $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
                            $editHistory = $db_handle->runFetch();
                        } else if ($editing === "quest") {
                            $db_handle->prepareStatement("SELECT EditHistoryID, CONCAT(Lname, ', ', Fname, ' ', Mname) as Name, LastEdit, QUESTIONS_QuestionsID, Remark FROM `edit_history` JOIN user ON user.UserID = edit_history.USER_UserID WHERE QUESTIONS_QuestionsID = :id AND Remark = 'edited this question' ORDER BY LastEdit DESC;");
                            $db_handle->bindVar(':id', $id, PDO::PARAM_INT,0);
                            $editHistory = $db_handle->runFetch();
                        } else {
                            echo("ERROR!");
                        }
                        ?>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <span style="font-size:20px;">
                                History 
                            </span>
                            <span class="carousel-control" href="#carousel-example-generic" data-slide="prev" role="button">
                                &lt; Edit interface
                            </span>
                        </div>
                        <div class="modal-body">
                            <div>
                                <?php if(!empty($editHistory)) { ?>
                                <table align="left" cellspacing="3" cellpadding="3" width="75%" class="table table-hover">
                                    <tr>
                                        <th style="border-top: 0px solid black"><h6>Date</h6></th>
                                        <th style="border-top: 0px solid black"><h6>Edited by</h6></th>
                                        <th style="border-top: 0px solid black"><h6>Remarks</h6></th>
                                    </tr>
                                    <?php
                            foreach($editHistory as $entry) {
                                    ?>
                                    <tr>
                                        <td><h6>
                                            <?php
                                $phpdate = strtotime( $entry["LastEdit"] );
                                echo date( 'M d, Y <\b\r> h:i a', $phpdate );
                                            ?>
                                            </h6></td>
                                        <td><h6><?php echo($entry["Name"]); ?></h6></td>
                                        <td><h6><?php echo($entry["Remark"]); ?></h6></td>
                                    </tr>
                                    <?php
                            }
                                    ?>
                                </table>
                                <?php } else { ?>
                                <h4>No entries available.</h4>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div> <!--carousel container-->
            </div>
            <div class="modal-footer">
                <!--button type="button" class="btn btn-default" data-dismiss="modal">Close</button-->
            </div>
        </div>

    </div>
</div><!-- Modal end -->
<?php
}
?>