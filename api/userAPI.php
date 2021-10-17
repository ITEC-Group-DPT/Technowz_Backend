<?php
    include './apiheader.php';
    include '../classes/User.php';
    $user = new User($conn);

    if(isset($_POST['command'])){
        if ($_POST['command'] == 'signUp') {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            if ($user->checkSignUp($email, $username, $password) != false)
                echo json_encode($user->checkSignUp($email, $username, $password));
            else errorAPI();
        }
        else if ($_POST['command'] == 'signIn') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            if ($user->checkSignIn($email, $password) != false)
                echo json_encode($user->checkSignIn($email, $password));
            else errorAPI();
        }
    }
?>