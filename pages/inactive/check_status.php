<?php
session_start();

header('Content-Type: application/json');

// Check if session variable 'active' is set
if (isset($_SESSION['active']) && $_SESSION['active'] == 0) {
    $response = [
        'status' => 'inactive',
        'message' => ' <br> <p> <h2> Currently your account is inactive. </br> <br> To become an active user, you need to deposit at least $20. </h2></br> 
        Meanwhile, as an inactive user, you will only be able to preview the first page of each section without navigating through the website. 
        Once you deposit the minimum amount, your account will become active, and you will be able to access all the functionalities of the website. 
        </p>' .
                     '<p>Please note that if a user does not make any deposit for more than 15 days, their account will be automatically deleted.</p>',
    ];
} else {
    $response = ['status' => 'active'];
}

echo json_encode($response);