<?php
require ("includes/functions.php");

session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit();
}

$isUserAdmin = false;
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    $isUserAdmin = true;
}

$results = [];
$term = '';
$message = '';
$count = 0;

if(isset($_GET['search']) && isValidSearchTerm($_GET['search']))
{
    $term = $_GET['search'];
    $results = searchPosts($term);
    $count = count($results);
}
elseif(isset($_GET['search']))
{
    $message = '<div class="alert alert-warning alert-dismissable text-center">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            Invalid input!
                    </div>';
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
        span {
            margin-right: 5px;
        }

        button {
            margin-right: 10px;
        }

        #users_link {
            float: right;
        }
    </style>

</head>
<body>

<div id="wrapper">

    <div class="container">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3 class="login-panel text-center text-muted">Search</h3>
                <?php echo $message; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <a href="/" class="btn btn-default"><i class="fa fa-arrow-circle-left"> </i> Back</a>
                <?php
                    if ($isUserAdmin) {
                        echo "<a id='users_link' href='users.php' class='btn btn-info'><i class='glyphicon glyphicon-user'> </i> Users</a>";
                    }
                ?>
                <hr/>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form action="search.php" method="get">
                    <div class="form-group input-group">
                        <input type="text" value="<?php echo $term; ?>" placeholder="Search term" class="form-control" name="search" autofocus>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </form>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Results
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Time</th>
                                    <?php
                                    if ($isUserAdmin) {
                                        echo '<th>Options</th>';
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    if(count($results) > 0)
                                    {
                                        foreach($results as $result)
                                        {
                                            $filteredPost = filterPost($result);
                                            $panelTag = '';
                                            if($filteredPost['priority'] == 1)
                                            {
                                                $panelTag = 'danger';
                                            }
                                            elseif($filteredPost['priority'] == 2)
                                            {
                                                $panelTag = 'warning';
                                            }
                                            else
                                            {
                                                $panelTag = 'info';
                                            }
                                            $options = '';
                                            if ($isUserAdmin) {
                                                $options = '<a href=edit.php?id='.$filteredPost['id'].'><button type="button" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span>Edit</button></a>
                                                            <a href=delete.php?id='.$filteredPost['id'].'><button type="button" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>Delete</button></a>';
                                            }
                                            echo '<tr class="'.$panelTag.'">
                                                    <td>' . $filteredPost['author']     . '</td>
                                                    <td>' . $filteredPost['title']      . '</td>
                                                    <td>' . $filteredPost['searchResultsPostedTime'] . '</td>
                                                    <td>'.$options. '</td>
                                                </tr>';
                                        }
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->

            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <p class="text-center text-muted">
                    Total results: <?php echo $count; ?>.
                </p>
            </div>
        </div>

    </div>
</div>

</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
