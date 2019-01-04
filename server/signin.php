<?php

    require "include/user_data.php";

    function json_response($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    $user = new SignInUser();
    
    if (!$user->check_sign_in_data()) {
        //json_response($user->get_all_properties());
        json_response($user->get_error_log());
        exit();
    }

    json_response(array(true));
    exit();
?>