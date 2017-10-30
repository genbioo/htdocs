<?php
include("../initialize.php");
includeCore();

?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php
        includeHead("PSRMS - Manage Assessment Tools");
        includeDataTables();
        ?>

    </head>
    
    <body class="hold-transition skin-blue sidebar-mini fixed">

        <div class="wrapper">
            
           <?php includeNav(); ?>

           <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <!-- Main content -->
            <section class="content container-fluid">

              <div id="page-wrapper">
                <!-- /.row -->
                <div class="row">
                    <section class="content-header">
                      <h1>
                        Assessment Tools
                      </h1>
                    </section>
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <table width="100%" class="table table-bordered table-hover" id="table-tool-list">
                                    <thead>
                                        <tr>
                                            <th align="left"><b>Tool Name</b></th>
                                            <th align="left"><b>Action</b></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- page end-->
                        <div id="modal-container">
                        </div>
                        <button id="modalToggle" type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none"></button>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->

            </section>
            <!-- /.content -->
          </div>

        </div>
        <!-- /#wrapper -->


        <?php includeCommonJS(); ?>

    </body>

    <script>
        $(document).ready(function() {
            var dataTable = $('#table-tool-list').DataTable( {
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "order":[],
                "ajax":{
                    url :"/includes/actions/forms.generate.list.php",
                    method: "POST",
                },
                "columnDefs":[
                    {
                        "targets": [1],
                        "orderable":false
                    },
                ]
            } );
        } );
    </script>

</html>