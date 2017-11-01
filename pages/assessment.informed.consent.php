<?php
include("../initialize.php");
includeCore();

if($_GET['from'] == 'intake')
{
    $id = $_GET['id'];
    $ag = $_GET['ag'];
} else
{
    $idpID = $_POST['idpID'];
    $idpName = $_POST['idp_name'];

    foreach($_POST as $key => $value) {
        if(substr($key,0,5) === "form-") {
            $ids[] = intval($value);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php includeHead("PSRMS - Informed Consent"); ?>

    </head>

    <body>

        <div id="wrapper">

            <div id="exam-wrapper">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="well">
                            <form id="formInput"
                                  <?php
                                  if($_GET['from'] == 'intake')
                                  {
                                      echo('action="assessment.apply.intake.php?id='.$id.'&ag='.$ag.'"');
                                  } else {
                                      echo('action="assessment.apply.tool.php"');
                                  }
                                  ?> 
                                  method="post">
                                <?php
                                if($_GET['from'] == 'tools')
                                {
                                ?>
                                    <input type="hidden" name="idpID" value="<?php echo($idpID); ?>">
                                    <input type="hidden" name="idpName" value="<?php echo($idpName); ?>">
                                    <input type="hidden" name="toolID" value='<?php echo(json_encode($ids)); ?>'>
                                <?php
                                }
                                ?>
                                <h1>Informed consent</h1>
                                <br>
                                <p>Our aim is to learn from your knowledge and experience so that we will be better able to provide support. We cannot promise to give you support in exchange for this interview. We are here only to ask questions and learn from your experiences. You are free to take part or not. We will use this information to decide how best to support people in similar situations. If you do choose to be interviewed, I can assure you that your information will remain anonymous so no-one will know what you have said. We cannot give you anything for taking part, but we greatly value your time and responses.</p>
                                <br>
                                <ul class="list-unstyled">
                                    <li><h4>Do you wish to continue?</h4></li>
                                    <li>
                                        <input type="checkbox" id="consent">&nbsp;<label for="consent">&nbsp;Yes, please continue.</label>
                                        <br>
                                    </li>
                                    <button class="btn btn-success btn-fill btn-sm" type="submit" id="submitButton" style="display:none">Continue</button>
                                    <a href="javascript:history.go(-1)" class="btn btn-success btn-fill btn-sm" id="returnButton">Back</a>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php includeCommonJS(); ?>

    </body>
    <script type="text/javascript">
        $('#consent').click(function(){
            if($(this).is(':checked')){
                $('#submitButton').show();
                $('#returnButton').hide();
            } else {
                $('#submitButton').hide();
                $('#returnButton').show();
            }
        });
    </script>
</html>