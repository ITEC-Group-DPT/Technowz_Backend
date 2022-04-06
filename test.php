<?php
    function substitution($char, $type) {
        $plain = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $cipher = ['5', 'T', '7', 'G', 'Z', '9', 'M', 'A', 'E', '4'];

        if($type == 'enc') {
            $index = array_search($char, $plain);
            return $cipher[$index];
        }

        if($type == 'dec') {
            $index = array_search($char, $cipher);
            return $plain[$index];
        }
    }

    function encrypt($orderID, $salt) {
        $encryptedID = "";
        $lenSalt = strlen($salt);

        if ($lenSalt < 2)
            $salt = "0" + $salt;

        else
            $salt = substr($salt, 0, 2);

        $addedSalt = $orderID . $salt;
        $chars = str_split($addedSalt);

        foreach ($chars as $char) {
            $encryptedID .= substitution($char, 'enc');
        }
        return $encryptedID;
    }

    function decrypt($encryptedID) {
        $orderID = "";
        $encryptedID = substr($encryptedID, 0, -2);

        $chars = str_split($encryptedID);

        foreach ($chars as $char) {
            $orderID .= substitution($char, 'dec');
        }

        return $orderID;
    }

    $orderID = '3726';
    $userID = '29';

    // $encryptedID = encrypt($orderID, $userID);
    // echo($encryptedID);

    // echo("\n\n\n");

    // $decryptedID = decrypt($encryptedID);
    $encryptedID = substr($orderID, 0, 1) . substr($orderID, 2);
    echo($encryptedID);
?>