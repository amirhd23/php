<?php
require 'includes/functions.php';
session_start();
if (!isset($_SESSION['loggedIn']) ||
    $_SESSION['loggedIn'] !== true ||
    !isset($_SESSION['admin']) ||
    $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit();
}
$message = '';
$successMessage = '';
if (count($_GET) > 0) {
    if (!isset($_GET['id']) || !isPostIdValid($_GET['id'])) {
        $message = 'please provide a valid ID for post.';
    } else {
        $id = $_GET['id'];
        $result = getPost($id);
        if ($result == false) {
            $message = 'Cannot find the post with the given ID.';
        }
        else {
            $result = deletePost($id);
            if ($result === false) {
                $message = 'There was an error deleting the post.';
            } else {
                
                $successMessage = 'Successfully deleted the post.';
            }
        }
    }
}


?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>COMP 3015</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <?php 
            if ($message !== '') {
                    echo '<div class="alert alert-danger">'. $message . '</div>';
            }
            if ($successMessage!== '') {
                echo '<div class="alert alert-success">'. $successMessage . '</div>';
                echo '<br />';
            }
            echo "<a href='search.php' class='btn btn-info'><i class='glyphicon glyphicon-search'> </i> Search</a>";
    ?>
    </body>

    </html>