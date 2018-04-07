<?php
require("Bcrypt.php");

function moments($seconds)
{
    if($seconds < 60 * 60 * 24 * 30)
    {
        return "within the month";
    }

    return "a while ago";
}

function getUsers() {
    $dbLink = connectDatabase();
    if (!$dbLink) {return false;}
    $sql = 'select firstname, lastname, dob from logins';
    $results = mysqli_query($dbLink, $sql);
    if (!$results) {return false;}
    $users = [];
    while ($row = mysqli_fetch_array($results)) {
        $users[] = array('firstname' => $row['firstname'],
                        'lastname' => $row['lastname'],
                        'dob' => $row['dob']);
    }
    mysqli_close($dbLink);
    return $users; 
}

/*
    formats date of birth. 
    $dob should be of format similar to: JAN-01-1985
    returns date in the format Jan 1st, 1990
*/
function getFormattedDob($dob) {
    if ($dob == null) {return null;}
    $parts = preg_split('/-/', $dob);
    $month = getMonthNumber($parts[0]);
    $time = mktime(0, 0, 0, $month, $parts[1], $parts[2]);
    $formattedDob = date('M jS, Y', $time);
    return $formattedDob;
}

/*
    $month: a 3-letter month string such as JAN, FEB, MAR etc.
    returns numbers from 1 to 12 based on valid months. 1 for JAN, 2 for FEB, etc.
*/
function getMonthNumber($month) {
    $result = 0;
    switch ($month) {
        case 'JAN':
            $result = 1;
            break;
        case 'FEB':
            $result = 2;
            break;
        case 'MAR':
            $result = 3;
            break;
        case 'APR':
            $result = 4;
            break;
        case 'MAY':
            $result = 5;
            break;
        case 'JUN':
            $result = 6;
            break;
        case 'JUL':
            $result = 7;
            break;
        case 'AUG':
            $result = 8;
            break;
        case 'SEP':
            $result = 9;
            break;
        case 'OCT':
            $result = 10;
            break;
        case 'NOV':
            $result = 11;
            break;
        case 'DEC':
            $result = 12;
            break;
    }
        return $result;
}

/*
    validates the post id for edit.php page.
*/
function isPostIdValid($postID) {
    if ($postID == null || trim($postID) == '') {
        return false;
    }
    if (!preg_match('/^[0-9]+$/', $postID)) {
        return false;
    }
    return true;
} 

function getPost($id) {
    if (!isPostIdValid($id)) {return null;}
    $dbLink = connectDatabase();
    if (!$dbLink) {return null;}
    $sql = 'select * from posts where id=' . $id;
    $result = mysqli_query($dbLink, $sql);
    if ($result == false || $result == null) {return null;}
    $post = null;
    while ($row = mysqli_fetch_array($result)) {
        $post = array('id' => $row['id'],
                        'firstname' => $row['firstname'],
                        'lastname' => $row['lastname'],
                        'title' => $row['title'],
                        'comment' => $row['comment'],
                        'priority' => $row['priority'],
                        'filename' => $row['filename'],
                        'time' => $row['time']);
    }
    mysqli_close($dbLink);
    return $post;
}

function getPosts()
{
    $posts = [];
    $dbLink = connectDatabase();
    if (!$dbLink) {return false;}
    $sql = "select * from posts";
    $result = mysqli_query($dbLink, $sql);
    if (!$result) {return false;}
    $importantPriority = [];
    $highPriority = [];
    $normalPriority = [];
    while ($row = mysqli_fetch_array($result)) {
        $post = validatePost($row);
        if ($post != false) {
            switch($post['priority']) {
                case 3;
                    $normalPriority[] = $post;
                    break;
                case 2;
                    $highPriority[] = $post;
                    break;
                case 1;
                    $importantPriority[] = $post;
                    break; 
            }
        }
    }
    $posts = array_merge($importantPriority, $highPriority, $normalPriority);
    mysqli_close($dbLink);
    return $posts;
}

function searchPosts($term)
{
    $posts = [];
    $dbLink = connectDatabase();
    if (!$dbLink) {return false;}
    $sql = "select * from posts where comment like '%$term%'";
    $result = mysqli_query($dbLink, $sql);
    if (!$result) {return $posts;}
    $importantPriority = [];
    $highPriority   = [];
    $normalPriority = [];
    while ($row = mysqli_fetch_array($result)) {
        $post = validatePost($row);
        if (!$post) {
            continue;
        }
        switch($row['priority']) {
            case 3;
                $normalPriority[] = $post;
                break;
            case 2;
                $highPriority[] = $post;
                break;
            case 1;
                $importantPriority[] = $post;
                break;
        }
    }
    $posts = array_merge($importantPriority, $highPriority, $normalPriority);
    mysqli_close($dbLink);
    return $posts;
}

/*$post is an array of one post*/
function validatePost($post)
{
    $valid = [];
    $postID = $post['id'];
    $firstName  = trim($post['firstname']);
    $lastName   = trim($post['lastname']);
    $title      = trim($post['title']);
    $comment    = trim($post['comment']);
    $priority   = trim($post['priority']);
    $filename   = trim($post['filename']);
    $time       = trim($post['time']);

    if($firstName == '' ||
        $lastName == '' ||
        $title    == '' ||
        $comment  == '' ||
        $priority == '' ||
        $filename == '' ||
        $time     == '')
    {
        $valid = false;
    }
    elseif(!file_exists('uploads/'.$filename))
    {
        $valid = false;
    }
    else
    {
        $valid['id'] = $postID;
        $valid['firstName'] = $firstName;
        $valid['lastName']  = $lastName;
        $valid['title']     = $title;
        $valid['comment']   = $comment;
        $valid['priority']  = $priority;
        $valid['filename']  = $filename;
        $valid['time']      = $time;
    }
    return $valid;
}

function filterPost($post)
{
    $author     = trim($post['firstName']) . ' ' . trim($post['lastName']);
    $title      = trim($post['title']);
    $comment    = trim($post['comment']);
    $priority   = trim($post['priority']);
    $filename   = trim($post['filename']);
    $postedTime = trim($post['time']);

    $filteredPost['id'] = $post['id']; 
    $filteredPost['author']     = ucwords(strtolower($author));
    $filteredPost['moment']     = moments(time() - $postedTime);
    $filteredPost['title']      = trim($title);
    $filteredPost['comment']    = trim($comment);
    $filteredPost['priority']   = trim($priority);
    $filteredPost['filename']   = trim($filename);
    $filteredPost['postedTime'] = date('l F \t\h\e dS, Y', $postedTime);
    $filteredPost['searchResultsPostedTime'] = date('M d, \'y', $postedTime);

    return $filteredPost;
}

function validateFields($input)
{
    $valid = [];

    $firstName  = trim($input['firstName']);
    $lastName   = trim($input['lastName']);
    $title      = trim($input['title']);
    $comment    = trim($input['comment']);
    $priority   = trim($input['priority']);

    if($firstName == '' ||
        $lastName == '' ||
        $title    == '' ||
        $comment  == '' ||
        $priority == '' )
    {
        $valid = false;
    }
    elseif(!preg_match("/^[A-Z]+$/i", $firstName) || !preg_match("/^[A-Z]+$/i", $lastName) || !preg_match("/^[A-Z ]+$/i", $title))
    {
        $valid = false;
    }
    elseif(preg_match("/<|>/", $comment))
    {
        $valid = false;
    }
    elseif(!preg_match("/^[0-9]{1}$/i", $priority))
    {
        $valid = false;
    }
    else
    {
        $valid['firstName'] = $firstName;
        $valid['lastName'] = $lastName;
        $valid['title'] = $title;
        $valid['comment'] = $comment;
        $valid['priority'] = $priority;
    }

    return $valid;
}

function isValidFile($fileInfo)
{
    if($fileInfo['type'] == 'image/jpeg')
    {
        return true;
    }

    return false;
}

function isValidSearchTerm($term)
{
    if(preg_match("/^[A-Z]+$/i", $term))
    {
        return true;
    }

    return false;
}

function insertPost($data)
{
    // md5 is a hashing function http://php.net/manual/en/function.md5.php
    $fileName = md5(time().$data['firstName'].$data['lastName']) . '.jpg';
    move_uploaded_file($data['file'], 'uploads/'.$fileName);
    $dbLink = connectDatabase();
    if (!$dbLink) {return false;}
    $firstname = $data['firstName'];
    $lastname = $data['lastName'];
    $title = $data['title'];
    $comment = $data['comment'];
    $priority = $data['priority'];
    $time = time();
    $sql = "insert into posts (firstname, lastname, title, comment, priority, filename, time)
            values ('$firstname', '$lastname', '$title', '$comment', $priority, '$fileName', '$time')";
    mysqli_query($dbLink, $sql);
    mysqli_close($dbLink);
}

function updatePost($data)
{
    $fileName = md5(time().$data['firstName'].$data['lastName']) . '.jpg';
    move_uploaded_file($data['file'], 'uploads/'.$fileName);
    $dbLink = connectDatabase();
    if (!$dbLink) {return false;}
    $id = $data['id'];
    $firstname = $data['firstName'];
    $lastname = $data['lastName'];
    $title = $data['title'];
    $comment = $data['comment'];
    $priority = $data['priority'];
    $time = time();
    $sql = "update posts set firstname='$firstname', lastname='$lastname', title='$title', comment='$comment', priority=$priority, filename='$fileName', time='$time' where id=$id";
    $result = mysqli_query($dbLink, $sql);
    mysqli_close($dbLink);
    return $result;
}

function deletePost($id) {
    if (!isPostIdValid($id)) {return false;}
    $dbLink = connectDatabase();
    if (!$dbLink) {return false;}
    $sql = "delete from posts where id=$id";
    $result = mysqli_query($dbLink, $sql);
    mysqli_close($dbLink);
    return $result;
}

function checkSignUp($data)
{
    $valid = false;

    // if any of the fields are missing, return an error
    if(trim($data['firstName']) == '' ||
        trim($data['lastName']) == '' ||
        trim($data['password'])  == '' ||
        trim($data['phoneNumber'])    == '' ||
        trim($data['dob']) == '')
    {
        $valid = "All inputs are required.";
    }
    elseif(!preg_match("/^[A-Z]+$/i", trim($data['firstName'])))
    {
        $valid = 'First Name needs to be alphabetical only.';
    }
    elseif(!preg_match("/^[A-Z]+$/i", trim($data['lastName'])))
    {
        $valid = 'Last Name needs to be alphabetical only';
    }
    elseif(!preg_match("/^.*([0-9]+.*[A-Z])|([A-Z]+.*[0-9]+).*$/i", trim($data['password'])))
    {
        $valid = 'Password must contain at least a number and a letter.';
    }
    elseif(!isPhoneNumberValid(trim($data['phoneNumber'])))
    {
        $valid = 'Phone Number must be in the format of (000) 000 0000.';
    }
    elseif(!preg_match("/^(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)-[0-9]{2}-[0-9]{4}$/i", trim($data['dob'])))
    {
        $valid = 'Date of Birth must be in the format of MMM-DD-YYYY.';
    }
    else
    {
        $valid = true;
    }

    return $valid;
}

function insertUserIntoDatabase($data) {
    $firstName = $data['firstName'];
    $lastName = $data['lastName'];
    $password = Bcrypt::hashPassword($data["password"], 12);
    $dob = $data['dob'];
    $phoneNumber = formatPhoneNumber($data['phoneNumber']);
    $sql = "insert into logins (firstname, lastname, password, dob, phoneNumber) 
            values ('$firstName', '$lastName', '$password', '$dob', '$phoneNumber')";
    
    $dbLink = connectDatabase();
    if (!$dbLink) {
        return false;
    }
    $success = mysqli_query($dbLink, $sql);
    if (!$success) {
        return false;
    }
    mysqli_close($dbLink);
    return true;
}

function connectDatabase() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $database = 'homework2';
    $link = mysqli_connect($host, $user, $pass, $database);
    return $link;
}

function isPhoneNumberValid($phoneNumber) {
    return preg_match("/^((\([0-9]{3}\))|([0-9]{3}))?( |-)?[0-9]{3}( |-)?[0-9]{4}$/", $phoneNumber);
}

function formatPhoneNumber($rawPhoneNumber) {
    $formattedPhoneNumber = preg_replace('/[^0-9]/', '', $rawPhoneNumber);
    return $formattedPhoneNumber;
}

function isThisValidUser($phoneNumber, $password) {
    if (trim($phoneNumber) == '' || 
        trim($password) == '') {
            return false;
        }
    $dbLink = connectDatabase();
    if (!$dbLink) {return false;}
    $formattedPhoneNumber = formatPhoneNumber($phoneNumber);
    $sql = "select * from logins where phoneNumber='$formattedPhoneNumber'";
    $result = mysqli_query($dbLink, $sql);
    while ($row = mysqli_fetch_array($result)) {
        if (Bcrypt::checkPassword($password, $row['password'])) {
            $user = array('firstName' => $row['firstname'], 
                        'lastName' => $row['lastname'],
                        'admin' => $row['admin']);
            mysqli_close($dbLink);
            return $user;
        }
    }
    mysqli_close($dbLink);
    return false;
}