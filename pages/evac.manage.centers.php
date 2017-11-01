<?php
include("../initialize.php");
includeCore();

?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php
        includeHead("PSRMS - Evacuation Centers");
        includeDataTables();
        ?>

    </head>

    <body>

        <div id="wrapper">
            <?php includeNav(); ?>
            <div id="page-wrapper">
                <!-- /.row -->
                <div class="row">
                    <div class="header">
                        <h3 class="title">&nbsp;Evacuation Centers</h3>
                    </div>
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <table width="100%" class="table table-bordered table-hover" id="table-evac-list">
                                    <thead>
                                        <tr>
                                            <th>Evacuation Center No.</th>
                                            <th>Evacuation Center Name</th>
                                            <th>Evacuation Center Address</th>
                                            <th>Evacuation Center Type</th>
                                            <th>Evacuation Center Manager</th>
                                            <th>Evacuation Center Contact</th>
                                            <th>Evacuation Center Specific Address</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->

        <?php includeCommonJS(); ?>

    </body>
    <script>
        $(document).ready(function() {
            var dataTable = $('#table-evac-list').DataTable( {
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "order":[],
                "ajax":{
                    url :"/includes/actions/evac.generate.list.php",
                    method: "POST",
                },
                "columnDefs":[
                    {
                        "targets": [5],
                        "orderable":false
                    },
                ]
            } );
        } );
    </script>

</html>