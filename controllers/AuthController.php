<?php

function login() {
  $variables = [];
  $email = getInputParameter('email');
  $password = getInputParameter('password');

  $customer = new Customer();
  $customer = $customer->where('email', trim($email))->first();
  if ($customer && password_verify($password, $customer->password)) {
    session_start();
    $_SESSION["customer"] = $customer;
    //  Redirect to homepage without errors
    header("Location: ".$GLOBALS['baseUrl']);
  } else {
    //  Redirect to homepage with login error
    header("Location: ".$GLOBALS['baseUrl']."?error=login");
  }

}

function logout() {
  session_start();

  // remove all session variables
  session_unset(); 

  // destroy the session 
  session_destroy(); 

  //  Redirect to homepage
  header("Location: ".$GLOBALS['baseUrl']);
}

