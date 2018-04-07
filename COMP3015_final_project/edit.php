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
$post = null;
if (count($_GET) > 0) {
    if (!isset($_GET['id']) || !isPostIdValid($_GET['id'])) {
        $message = 'please provide a valid ID for post.';
    } else {
        $id = $_GET['id'];
        $post = getPost($id);
        if ($post == null) {
            $message = 'Post not found with the given ID';
        }
    }
}

if(count($_POST) > 0)
{
    $fieldInput = validateFields($_POST);
    $fileInput  = isValidFile($_FILES['file']);
    $postIdValid = isPostIdValid($_POST['id']);
    if($fieldInput != false && $fileInput != false && $postIdValid != false)
    {
        $fieldInput['file'] = $_FILES['file']['tmp_name'];
        $fieldInput['id'] = $_POST['id'];
        $result = updatePost($fieldInput);
        if ($result === false) {
            $message = 'There was an error updating the post.';
        } else {
            $successMessage = 'Successfully updated the post.';
        }
    }
    else
    {
        $message = 'Invalid input!';
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
        <style>

            #newPost {
                width: 50%;
                margin-left: 100px;
                margin-right: 800px;
                margin-top: 50px;

            }

            #thumbnail {
                width: 400px;
                position: absolute;
                top: 50px;
                right: 100px;
            }
        </style>
    </head>

    <body>
            <div id="newPost">
                <?php 
                if ($message !== '') {
                        echo '<div class="alert alert-danger">'. $message . '</div>';
                }
                if ($successMessage!== '') {
                    echo '<div class="alert alert-success">'. $successMessage . '</div>';
                    echo '<br />';
                    echo "<a href='search.php' class='btn btn-info'><i class='glyphicon glyphicon-search'> </i> Search</a>";
                    exit();
                }
                ?>
                <form role="form" method="post" action="edit.php" enctype="multipart/form-data">
                    <div>
                        <div class="form-group">
                        <input name="id" type="hidden" readonly <?php if (isset($post)) { echo 'value="' . $post['id'] . '"' ;} ?> />
                            <input class="form-control" placeholder="First Name" name="firstName" <?php if (isset($post)) {echo 'value="'. $post[
                                'firstname'] . '"';} ?> />
                        </div>
                        <div class="form-group">
                            <input class="form-control" placeholder="Last Name" name="lastName" <?php if (isset($post)) {echo 'value="'. $post[
                                'lastname'] . '"';} ?> />
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input class="form-control" placeholder="" name="title" <?php if (isset($post)) {echo 'value="'. $post[ 'title'] . '"';}
                                ?> />
                        </div>
                        <div class="form-group">
                            <label>Comment</label>
                            <textarea class="form-control" rows="8" name="comment"><?php if (isset($post)) {echo $post['comment'];} ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Priority</label>
                            <select class="form-control" name="priority">
                                <option <?php if (isset($post) && $post[ 'priority']==1 ) {echo 'selected=true';} ?> value="1">Important</option>
                                <option <?php if (isset($post) && $post[ 'priority']==2 ) {echo 'selected=true';} ?> value="2">High</option>
                                <option <?php if (isset($post) && $post[ 'priority']==3 ) {echo 'selected=true';} ?> value="3">Normal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Image</label>

                            <input type="file" name="file" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="Update" />
                    </div>
                </form>
            </div>
            <?php
            if (isset($post)) {
                $src = '';
                $filename = $post['filename'];
                if ($filename != null && file_exists('uploads/' . trim($filename))) {
                    $src = 'uploads/' . trim($filename);
                } else {
                    $src = 'img/not_found.png';
                }
                echo '<img id="thumbnail" src=' . $src;
            }           
            ?>
    </body>

    </html>