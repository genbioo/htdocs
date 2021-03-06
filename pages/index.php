<?php
include("../initialize.php");
includeCore();
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php includeHead("PSRMS - Dashboard"); ?>

    </head>

    <body>
        
        <div id="wrapper">
            <?php includeNav(); ?>
            <div id="page-wrapper">
                <div class="row">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <div class="row">
                    <div class="header">
                        <h3 class="title">&nbsp;Dashboard</h3>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-comments fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge">#</div>
                                        <div>Under construction</div>
                                    </div>
                                </div>
                            </div>
                            <a href="#">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
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

</html>