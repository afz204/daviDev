<?php
define('URL', 'http://localhost/bungdav/');
define('CORPORATE', 'http://localhost/bungdav/corporate/');
session_start();
require 'config/api.php';

$cekLogin = new Login();
$config = new Admin();

if($cekLogin->is_loggedin()!="")
{
    
    $cekLogin->redirect('index');
}

if(isset($_POST['btn-login']))
{
    $username = strip_tags($_POST['txt_username']);
    $password = strip_tags($_POST['txt_password']);

    if ($cekLogin->runLogin($username, $password)) {
        # code...
//        echo 'has login';
       $cekLogin->redirect('index');
       $logs = $config->saveLogs('0', $_SESSION['user_session'], 'f', 'login users');
    } else{
        $error = $_SESSION['error'];
    }
}
$pass = password_verify('hello', 'makan');
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="<?=URL?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=URL?>assets/vendors/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="<?=URL?>assets/css/login.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body class="bg-light">

<div class="container">
    <div class="form-signin" autocomplete="off">
        <div class="text-center mb-4">
            <img class="mb-4" src="<?=URL?>assets/images/roses-bouquet.png" alt="" width="90" height="90">
            <h1 class="h3 mb-3 font-weight-normal"><i class="fa fa-fw fa-tencent-weibo"> </i>BungaDavi</h1>

        </div>
        <?php
      if(isset($error))
      {
        ?>
        <div class="alert alert-danger" style="font-size: 15px">
            <i class="fa fa-fw fa-exclamation-triangle"></i> &nbsp; <?php echo $error; ?> !
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        }
        ?>

        <div class="card text-white bg-info mb-6">
            <div class="card-header">Login Box</div>
            <div class="card-body">
                <form id="form-login" method="post" data-parsley-validate="" autocomplete="off">
                    <div class="form-label-group">
                        <input name="txt_username" type="text" class="form-control" placeholder="Username" autofocus="autofocus" required>
                    </div>

                    <div class="form-label-group">
                        <input name="txt_password" type="password" class="form-control" placeholder="Password" required />
                    </div>
                    <button type="submit" class="btn btn-lg btn-outline-light btn-block" name="btn-login">Sign in</button>
                </form>
            </div>
        </div>

        <p class="text-muted text-center">
            Please contact your admin if you don't have any username!
        </p>
        <p class="mt-5 mb-3 text-muted text-center">Made with <i class="fa fa-fw fa-heart" style="color: #b9161d;"></i> using <i class="fa fa-fw fa-html5"></i></p>
        <p class="mt-5 mb-3 text-muted text-center">&copy; 2018</p>
    </div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?=URL?>assets/vendors/dll/jquery.min.js"></script>
<script src="<?=URL?>assets/vendors/parsley/parsley.min.js"></script>
<script src="<?=URL?>assets/vendors/dll/popper.min.js"></script>
<script src="<?=URL?>assets/js/bootstrap.min.js"></script>
<script src="<?=URL?>assets/vendors/dll/holder.min.js"></script>
</body>
</html>