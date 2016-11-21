<?php

function getMyAccount($error=null) {
  $variables = [];
  //  Render view
  render("my-account/my-account.html", $variables);
}

function postMyAccount() {
  session_start();
  $variables = [];
  //  Get parameters
  $customerInput = getInputParameter('customer');
  // Validate form
  $errors = null;

  if (!empty($errors)) {
    //  Render view
    return getMyAccount($errors[0]);
  }

  // Save customer
  $address = new Address();
  $addressId = $address->where('id',$_SESSION['customer']->address_id)->update([
    'address' => $customerInput['address'],
    'postal_code' => $customerInput['postal_code'],
    'city_id' => $customerInput['city_id']
  ]);
  $customer = new Customer();
  $customerFields = [
    'first_name' => $customerInput['first_name'],
    'last_name' => $customerInput['last_name'],
    'email' => $customerInput['email'],
    'phone' => $customerInput['phone'],
  ];
  if (isset($customerInput['password']) && !empty($customerInput['password'])) {
    $pass = password_hash($customerInput['password'], PASSWORD_DEFAULT);
    $customerFields['password'] = $pass;
  }
  $customer->where('id',$_SESSION['customer']->id)->update($customerFields);

  // Reload customer and update session
  $customer = new Customer();
  $_SESSION['customer'] = $customer->find($_SESSION['customer']->id);
  //  Redirect to my-account
  header("Location: ".$GLOBALS['baseUrl'].'my-account');

}

