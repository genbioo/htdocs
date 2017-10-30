<?php
include("../initialize.php");
includeCore();

includeLayoutGenerator();

$toolIDs = json_decode($_POST["toolID"]);
$idpName = $_POST['idpName'];
$_SESSION['idpID'] = $_POST['idpID'];
$form_info = getMultipleAssessmentTools($toolIDs);
$questionsResult = getAssessmentQuestions('Tool',$toolIDs);
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php includeHead("PSRMS - Apply Assessment Tool"); ?>

    </head>
    
    <body class="hold-transition skin-blue sidebar-mini fixed">

        <div class="wrapper">
            
           <?php includeNav(); ?>

           <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <!-- Main content -->
            <section class="content container-fluid">

              <div id="exam-wrapper">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <form action="/includes/actions/assessment.process.answers.tool.php" method="post">
                            <?php echo(displayQuestions($questionsResult, $form_info, $idpName)); ?>
                        <div class="col-md-12">
                            <button id="btn-submit-form" class="btn btn-primary btn-md" type="submit"><i class="fa fa-check"></i>&nbsp;Submit</button>
                        </div>
                        </form>
                    </div>
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