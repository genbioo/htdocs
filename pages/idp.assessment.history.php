<?php
include("../initialize.php");
includeCore();

$id = $_GET['id'];
$ag = $_GET['ag'];

$idp = getIDPExtensiveDetails($id);
$intakeCount = getIntakeCount($id);
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php 
        includeHead("PSRMS - Assessment History"); 
        includeDataTables();
        ?>

    </head>

    <body>

        <div id="wrapper">
            <?php includeNav(); ?>

            <div id="page-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header"><?php echo($idp[0]['IDPName']); ?></h1>
                    </div>
                </div>
                <?php
                if(isset($_GET['status']) && $_GET['status'] == 'intakesuccess')
                {
                ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Intake answers are saved successfully!
                </div>
                <?php
                } else if (isset($_GET['status']) && $_GET['status'] == 'toolsuccess')
                {
                ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Assessment tool answers are saved successfully!
                </div>
                <?php
                } else if (isset($_GET['status']) && $_GET['status'] == 'err1')
                {
                ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    An error occured during the process. If this issue persists, please contact the system admin.
                </div>
                <?php
                }
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="title">&nbsp;Intakes taken&nbsp;
                                    <a href="assessment.informed.consent.php?id=<?php echo($id); ?>&ag=<?php echo($ag); ?>&from=intake" class="btn btn-success btn-xs btn-fill">
                                        <i class="icon_check_alt"></i>Apply Intake
                                    </a>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <table width="100%" class="table table-bordered table-hover" id="table-intake-list">
                                    <thead>
                                        <tr>
                                            <th align="left"><b>Date Taken</b></th>
                                            <th align="left"><b>Previously Interviewed?</b></th>
                                            <th align="left"><b>Knew the organization?</b></th>
                                            <th align="left"><b>If yes, name of the organization</b></th>
                                            <th align="left"><b>Psychosocial Report Rating Improvement</b></th>
                                            <th align="left"><b>Agent</b></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="title">&nbsp;Assessment tools taken&nbsp;
                                    <?php
                                    if($intakeCount[0]['count'] !== '0')
                                    {
                                    ?>
                                    <a href="assessment.select.forms.php?id=<?php echo($id); ?>" class="btn btn-success btn-xs btn-fill" id="assessmentButton">
                                        <i class="icon_check_alt"></i>Apply Assessment tool
                                    </a>
                                    <?php
                                    }
                                    ?>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <table width="100%" class="table table-bordered table-hover" id="table-assessment-list">
                                    <thead>
                                        <tr>
                                            <th align="left"><b>Date Taken</b></th>
                                            <th align="left"><b>Assessment Tool</b></th>
                                            <th align="left"><b>Score</b></th>
                                            <th align="left"><b>Provisionary Assessment</b></th>
                                            <th align="left"><b>Agent</b></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php includeCommonJS(); ?>

    </body>
    <script>
        $(document).ready(function() {
            var intakeDataTable = $('#table-intake-list').DataTable( {
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "order":[],
                "ajax":{
                    url :"/includes/actions/assessment.generate.taken.intakes.php?id=<?php echo($id); ?>",
                    method: "POST",
                },
                "columnDefs":[
                    {
                        "targets": [0,1,2,3,4,5],
                        "orderable":false
                    },
                ]
            } );
        } );
        $(document).ready(function() {
            var assessmentDataTable = $('#table-assessment-list').DataTable( {
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "order":[],
                "ajax":{
                    url :"/includes/actions/assessment.generate.taken.tools.php?id=<?php echo($id); ?>",
                    method: "POST",
                },
                "columnDefs":[
                    {
                        "targets": [3],
                        "orderable":false
                    },
                ]
            } );
        } );
    </script>

</html>