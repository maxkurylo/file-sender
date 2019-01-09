<?php
    require "include/user_data.php";

    function json_response($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    $sign_in_user = new SignInUser($_POST["user_login"], $_POST["user_password"]);

    //if user data is not valid send error log
    if (!$sign_in_user->validate_user_data()) {
        json_response($sign_in_user->get_error_log());
        exit();
    }
    //trying to sign in
    if (!$sign_in_user->sign_in_user()) {
        //if sign in failed check if user exists
        if (!$sign_in_user->does_user_exists()) {
            //if exists - send request for registration
            json_response(array("registerRequired" => true));
            exit();
        }
        else {
            json_response($sign_in_user->get_error_log());
            exit();
        }
    }

    //session_start();
    json_response(array("signedIn" => true));
    exit();
?>