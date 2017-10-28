<?php
include($_SERVER['DOCUMENT_ROOT']."/includes/global.functions.php");

includeCore();

$_SESSION['loc'] = $_SERVER['PHP_SELF'];
$_SESSION['disaster_id'] = 1;

?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php
        setTitle("PSRMS - IDP Assessment");
        
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
                        <h3 class="title">&nbsp;Enrolled IDPs&nbsp;<a href="idp.enroll.php" type="button" class="btn btn-success btn-md btn-fill">Add New IDP</a></h3>
                    </div>
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            
                            <div class="panel-body">
                                <table width="100%" class="table table-bordered table-hover" id="table-idp-list">
                                    <thead>
                                        <tr>
                                            <th align="left"><b>Name</b></th>
                                            <th align="left"><b>Family ID</b></th>
                                            <th align="left"><b>IDP ID</b></th>
                                            <th align="left"><b>Gender</b></th>
                                            <th align="left"><b>Age</b></th>
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
        </div>
        <!-- /#wrapper -->


        <?php includeCommonJS(); ?>

    </body>
    <script>
        window.load_modal = function(clicked_id) {
            $("#modal-container").load("/includes/fragments/idp_assessment_modal.php?id="+clicked_id, function() {
                $("#modalToggle").attr('data-target', '#myModal'+clicked_id);
                $("#modalToggle").click();
            });
        }
        function printDiv(clicked_id) {
            var divToPrint=document.getElementById('myModal' + clicked_id);
            var newWin=window.open('','Print-Window');
            newWin.document.open();
            newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
            newWin.document.close();
            setTimeout(function(){newWin.close();},10);
        }
        $(document).ready(function() {
            var dataTable = $('#table-idp-list').DataTable( {
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "order":[],
                "ajax":{
                    url :"/includes/actions/assessment.generate.list.php",
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
        $('#table-idp-list').on('click', 'tbody tr', function() {
            console.log('TD cell textContent : ', this.id);
        })
    </script>

</html>