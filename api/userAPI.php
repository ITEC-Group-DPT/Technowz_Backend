<?php
include './apiheader.php';
include '../classes/User.php';
$user = new User($conn);
$header = getallheaders();

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
    if (isset($header['Userid'])){
        $userID = $header['Userid'];
        if($_POST['command'] == 'updateUsername') {
            $newUsername = $_POST['newusername'];
            $res = $user->updateUsername($userID, $newUsername);
            if($res['isSuccess'])
                successApi("Username change successfully");
            else
                failApi("Username change unsuccessfully");
        }
        else if($_POST['command'] == 'updatePassword') {
            $password = $_POST['password'];
            $newPassword = $_POST['newpassword'];
            $res = $user->updatePassword($userID, $password, $newPassword);
            if($res['isSuccess'])
                successApi("Password change successfully");
            else
                failApi("Password change unsuccessfully");
        }
    }
    else if ($_POST['command'] == 'signUp') {
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
        $res = $user->checkSignIn($email, $password);
        if ($res['isSuccess'])
            successApi($res['data']);
        else
            failApi($res['data']);
    } else failApi("No command found!");
} else failApi("No command found!");
