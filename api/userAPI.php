<?php
include '../database/db.php';
include '../classes/User.php';
$user = new User($conn);

if ($_POST['command']=='signup') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password1 = $_POST['password'];
    echo json_encode($user->checkCreate($email, $username, $password1));
   

}else if ($_POST['command']=='signin') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    echo json_encode($user->checkSignIn($email, $password));
}
?>