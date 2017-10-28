<?php
include($_SERVER['DOCUMENT_ROOT']."/includes/global.functions.php");

includeCore();

$id = $_GET['id'];

$idp = getIDPExtensiveDetails($id);
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php 
        setTitle("PSRMS - Assessment History"); 
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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="title">&nbsp;Intakes taken</h4>
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
                                <h4 class="title">&nbsp;Assessment tools taken</h4>
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
            var dataTable = $('#table-intake-list').DataTable( {
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
            var dataTable = $('#table-assessment-list').DataTable( {
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