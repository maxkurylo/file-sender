<?php

    function json_response($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    require "include/user_data.php";

    $register_user = new RegisterUser($_POST["user_login"], $_POST["user_password"]);

    if (!$register_user->validate_user_data()) {
        json_response($register_user->get_error_log());
        exit();
    }

    if(!$register_user->create_user()) {
        json_response($register_user->get_error_log());
        exit();
    }

    //session_start();
    json_response(array("registered" => true));
    exit();
?>