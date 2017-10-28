<?php
include($_SERVER['DOCUMENT_ROOT']."/includes/global.functions.php");
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?php setTitle("PSRMS - Login"); ?>

    </head>

    <body class="main">

        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                   <!--  <h1 class="text-center" id="logo"><i class="flaticon-users" aria-hidden="true"></i>PSRMS</h1> -->
                    <div class="panel panel-default" id="panel-login">
                        <div class="panel-body" id="panel-body-login">
                            <h3 class="text-center">Account Login</h3>
                            <p class="text-center" style="margin-bottom: 40px;">Sign In to your account</p>
                            <hr>
                            <form action="../includes/actions/global.login.php" method="post">
                                <div class="form-group">
                                    <!--<label for="email">Email address:</label>-->
                                    <input type="email" class="form-login form-control" id="email" name="email" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <!--<label for="pwd">Password:</label>-->
                                    <input type="password" class="form-login form-control" id="pwd" name="pwd" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <div class="checkbox" style="margin-left: 20px;">
                                        <input type="checkbox">
                                        <label class="form-inline" style="margin-left: -20px;">Remember me</label>
                                    </div>
                                </div>    
                                <input type="submit" class="btn btn-lg btn-primary btn-block" id="login-btn" value="Login">
                            </form>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>

</html>