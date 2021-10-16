<?php
    include '../database/db.php';
    include '../classes/User.php';
    $user = new User($conn);

    if ($_POST['command'] == 'signUp') {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        echo json_encode($user->checkSignUp($email, $username, $password));
    }
    else if ($_POST['command'] == 'signIn') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        echo json_encode($user->checkSignIn($email, $password));
    }
?>