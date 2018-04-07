<?php
require('includes/functions.php');

$phoneNumber = '';
$errorMessage = '';

if(isset($_POST['remember']) && $_POST['remember'] == 1)
{
    setcookie('phoneNumber', $_POST['phoneNumber'], time() + 60 * 60 * 24 * 20);    // 60 seconds, 60 minutes, 24 hours, 20 days
    $phoneNumber = $_POST['phoneNumber'];
}
elseif(isset($_COOKIE['phoneNumber']))
{
    $phoneNumber = $_COOKIE['phoneNumber'];
}

if(isset($_POST['phoneNumber']) && !isset($_POST['remember']))
{
    setcookie('phoneNumber', null, time() - 3600);
    $phoneNumber = '';
}
if (count($_POST) > 0) {
    if (isset($_POST['phoneNumber']) 
        && isset($_POST['password'])
        && trim($_POST['phoneNumber']) != "" 
        && trim($_POST['password']) != "") {
        if (!isPhoneNumberValid($_POST['phoneNumber'])) {
            $errorMessage = "<div style='margin-top: 20px;' class='alert alert-danger'>Phone number is not valid</div>";
        } else {
            $user = isThisValidUser($_POST['phoneNumber'], $_POST['password']);
            if ($user) {
                session_start();
                $_SESSION['loggedIn'] = true;
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['lastName'] = $user['lastName'];
                if (isset($user['admin']) && $user['admin'] === '1') {
                    $_SESSION['admin'] = true;
                }
                header("Location: index.php");
            } else {
                $errorMessage = "<div style='margin-top: 20px;' class='alert alert-danger'>User not found</div>";
            }
        }
    } else {
        $errorMessage = "<div style='margin-top: 20px;' class='alert alert-danger'>Please provide phone number and password.</div>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>COMP 3015</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div id="wrapper">

    <div class="container">

        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h1 class="login-panel text-center text-muted">COMP 3015</h1>
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                        <?php if(isset($errorMessage)) echo $errorMessage;?>
                    </div>
                    <div class="panel-body">
                        <form name="login" role="form" action="login.php" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control"
                                           value="<?php echo $phoneNumber;?>"
                                           name="phoneNumber"
                                           placeholder="Phone Number"
                                           type="text"
                                        <?php echo empty($phoneNumber) ? 'autofocus' : ''; ?>
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
                                           name="password"
                                           placeholder="Password"
                                           type="password"
                                        <?php echo empty($phoneNumber) ? '' : 'autofocus'; ?>
                                    />
                                </div>
                                <div class="form-group">
                                    <input type="checkbox"
                                           value="1"
                                           name="remember"
                                        <?php echo empty($phoneNumber) ? '' : 'checked'; ?>
                                    />
                                    Remember Me
                                </div>
                                <input type="submit" class="btn btn-lg btn-success btn-block" value="Login"/>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <a class="btn btn-sm btn-default" href="signup.php">Sign Up</a>
            </div>
        </div>

    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
