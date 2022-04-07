<?php
include './apiheader.php';
include '../classes/User.php';
$user = new User($conn);

if (isset($_GET['command'])) {
    if ($_GET['command'] == "checkToken") {
        $header = getallheaders();

        if (isset($header['Userid'])) {
            if ($user->getUser("userID", $header['Userid'])) {
                successApi("validate user success");
                return;
            }
        }
        failApi("validate user fail");
    }
}
else if (isset($_POST['command'])) {
    if ($_POST['command'] == 'signUp') {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        if ($user->checkSignUp($email, $username, $password))
            successApi($user->createUser());
        else
            failApi("Email is already taken");
    } else if ($_POST['command'] == 'signIn') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $isAdmin = false;

        if (isset($_POST['isAdmin'])) $isAdmin = boolval($_POST['isAdmin']);

        $res = $user->checkSignIn($email, $password, $isAdmin);
        if ($res['isSuccess'])
            successApi($res['data']);
        else
            failApi($res['data']);
    } else failApi("No command found!");
} else failApi("No command found!");
