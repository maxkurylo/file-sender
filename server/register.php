<?php

    require "include/user_data.php";

    function json_response($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    $user = new RegisterUser();
    
    if (!$user->validate_register_data()) {
        json_response($user->get_error_log());
        exit();
    }

    $user->hash_password();

     if (!$user->create_user()) {
        json_response($user->get_error_log());
        exit();
    }

    json_response(array(true));
    exit();
?>