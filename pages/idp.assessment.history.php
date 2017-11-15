<?php
include("../initialize.php");
includeCore();

$_SESSION['idpID'] = $_GET['id'];
$id = $_GET['id'];
$ag = getAgeGroup($id)[0]['AgeGroup'];

$idp = getIDPExtensiveDetails($id);
$intakeCount = getIntakeCount($id);
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php 
        includeHead("PSRMS - ".$idp[0]['IDPName']." Assessment History"); 
        includeDataTables();
        ?>
        <?php
        if($_SESSION['account_type'] == '77')
        {
        ?>
        <style>
            #table-assessment-list, #table-intake-list  tbody tr {
                cursor: pointer;
            }
        </style>
        <?php
        }
        ?>
    </head>

    <body>

        <div id="wrapper">
            <?php includeNav(); ?>

            <div id="page-wrapper">
                <div class="row">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/pages/idp.list.php">IDPs</a></li>
                        <li class="breadcrumb-item active">IDP Assessment History</li>
                    </ol>
                </div>
                <div class="row">
                    <div class="header">
                        <h3 class="title"><?php echo($idp[0]['IDPName']); ?>&nbsp;
                            <?php
                            if($_SESSION['account_type'] == '77')
                            {
                            ?>
                            <sup>
                                <button type="button" class="btn btn-success btn-xs" onClick ="load_modal()">
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </sup>
                            <!-- this button is for a workaround in triggering the edit modal -->
                            <!-- without this workaround, button needs to be clicked twice before modal shows -->
                            <button class="btn btn-primary" id="dtailTrigger" data-toggle="modal" data-target="#myModal" style="display:none"></button>
                            <?php
                            }
                            ?>
                        </h3>
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
        
        <div id="modal-container">
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
                ],
                "dom": 'Blfrtip',
                "buttons": [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
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
                ],
                "dom": 'Blfrtip',
                "buttons": [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                "createdRow": function( row, data, dataIndex ) {
                    if ( data[3] != "No auto-assessment available for this tool." && data[3] != "Below cutoff") {        
                        $(row).addClass('danger');
                    }
                }
            } );
        } );
        <?php
        if($_SESSION['account_type'] == '77')
        {
        ?>
        $('#table-intake-list').on('click', 'tbody tr', function() {
            console.log('TD cell textContent : ', this.id);
            window.location.href = "/pages/assessment.view.answers.intake.php?id="+this.id;
        })
        $('#table-assessment-list').on('click', 'tbody tr', function() {
            console.log('TD cell textContent : ', this.id);
            window.location.href = "/pages/assessment.view.answers.tool.php?id="+this.id;
        })
        <?php
        }
        ?>
        window.load_modal = function(clicked_id) {
            $("#modal-container").load("/includes/fragments/idp.modal.details.php?id=<?php echo($id); ?>", function() {
                $('#dtailTrigger').click();
            });
        }
    </script>

</html>