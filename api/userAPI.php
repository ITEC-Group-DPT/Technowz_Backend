<?php
include './apiheader.php';
include '../classes/User.php';
$user = new User($conn);

if (isset($_POST['command'])) {
    if ($_POST['command'] == 'signUp') {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        if ($user->checkSignUp($email, $username, $password))
            successApi($user->createUser());
        else failApi("Email is already taken");
    } else if ($_POST['command'] == 'signIn') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $res = $user->checkSignIn($email, $password);
        if ($res['isSuccess'])
            successApi($res['data']);
        else failApi($res['data']);
    } else failApi("No command found!");
} else failApi("No command found!");
