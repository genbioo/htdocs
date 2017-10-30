<?php
include("../initialize.php");
includeCore();

includeLayoutGenerator();

$ageGroup = $_GET['ag'];
$idpID = $_GET['id'];
$userID = $_SESSION['UserID'];
$formID = getIntakeID($idpID, $ageGroup);

$questions = getAssessmentQuestions('Intake',$formID);
$formInfo = getIntakeInfo($formID);
$idpInfo = getIDPExtensiveDetails($idpID);
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php includeHead("PSRMS - Apply Intake"); ?>

    </head>
    
    <body class="hold-transition skin-blue sidebar-mini fixed">

        <div class="wrapper">
            
           <?php includeNav(); ?>

           <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <!-- Main content -->
            <section class="content container-fluid">

              <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="well" id="accordion">
                            <?php
                            foreach($idpInfo as $result)
                            {
                            ?>
                            <h4 class="panel-title">
                                <?php echo($result['IDPName']); ?>
                                <sup><a class="float-right" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><i class="fa fa-info-circle fa-fw"></i></a></sup>
                            </h4>
                            <div id="collapseOne" class="panel-collapse collapse">
                                <hr>
                                <div id="idp-info">
                                    <p class="field-label"><b>Date of Intake: </b><?php echo(date("l").', '.date("m-d-Y")); ?></p>
                                    <p class="field-label"><b>Age: </b><?php echo($result['Age']); ?></p>
                                    <p class="field-label"><b>Sex: </b><?php echo(($result['Gender'] == 1) ? 'Male' : 'Female'); ?></p>
                                    <p class="field-label"><b>Marital status: </b><?php echo(($result['MaritalStatus'] == 1) ? 'Single' : 'Married'); ?></p>
                                    <?php
                                if(isset($result['Occupation'])) {
                                    echo '<p class="field-label"><b>Employment/ Occupation: </b>'.$result['Occupation'].'</p>';
                                }
                                    ?>
                                    <p class="field-label"><b>Type of Relocation: </b><?php echo(($result['EvacType'] == 1) ? 'Government' : 'Home-based'); ?></p>
                                    <p class="field-label"><b>Address/Name of Evacuation Center: </b>
                                        <?php if(isset($result['EvacName']) && $result['EvacName'] != '') echo($result['EvacName'].'; '); echo($result['Evac_Address']); ?></p>
                                    <p class="field-label"><b>Address prior to evacuation: </b>
                                        <?php
                                if(isset($result['SpecificAddress']) && $result['SpecificAddress'] != '') {
                                    echo($result['SpecificAddress'].'; ');
                                }
                                echo($result['Origin_Address']);
                                        ?>
                                    </p>
                                    <p class="field-label"><b>Contact info: </b></p>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                        <form action="/includes/actions/assessment.process.answers.intake.php?id=<?php echo($idpID); ?>&ag=<?php echo($ageGroup); ?>" method="post">
                            <?php echo(displayQuestions($questions, $formInfo, '')); ?>
                        <div class="col-md-12">
                            <button id="btn-submit-form" class="btn btn-primary btn-md" type="submit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                        </div>
                        </form>
                    </div>
                </div>

            </section>
            <!-- /.content -->
          </div>

        </div>
        <!-- /#wrapper -->


        <?php includeCommonJS(); ?>

    </body>

    <script type="text/javascript">
        //show first translation as default
        $('div[name*="Original"]').show().siblings().hide();
        //function for changing question display based on selected option
        function showDiv(elem, fID, arr){
            var languages = arr;
            for(var i = 0; i < languages.length; i++) {
                //if selected option value is the same as language[i]
                if(elem.value == languages[i])
                    //display <div> with a name languages[i]-fID. Hide others
                    $('div[name='+languages[i]+'-'+fID+']').show().siblings().hide();
            }
        }
    </script>

</html>