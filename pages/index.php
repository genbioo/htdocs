<?php
include("../initialize.php");
includeCore();
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php includeHead("PSRMS - Dashboard"); ?>

    </head>

    <body class="hold-transition skin-blue sidebar-mini fixed">

        <div class="wrapper">
            
           <?php includeNav(); ?>

           <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <!-- Main content -->
            <section class="content container-fluid">

              <div id="page-wrapper">
                <div class="row">
                    <section class="content-header">
                      <h1>
                        Dashboard
                        <small>Control panel</small>
                      </h1>
                    </section>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                      <!-- small box -->
                      <div class="small-box bg-aqua">
                        <div class="inner">
                          <h3>150</h3>

                          <p>New Orders</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                      <!-- small box -->
                      <div class="small-box bg-green">
                        <div class="inner">
                          <h3>53<sup style="font-size: 20px">%</sup></h3>

                          <p>Bounce Rate</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                      <!-- small box -->
                      <div class="small-box bg-yellow">
                        <div class="inner">
                          <h3>44</h3>

                          <p>User Registrations</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-person-add"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                      <!-- small box -->
                      <div class="small-box bg-red">
                        <div class="inner">
                          <h3>65</h3>

                          <p>Unique Visitors</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <!-- ./col -->
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

</html>