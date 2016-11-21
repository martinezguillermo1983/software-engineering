<?php

include_once($GLOBALS['basename'] . '/controllers/RootController.php');
include_once($GLOBALS['basename'] . '/controllers/NotFoundController.php');
include_once($GLOBALS['basename'] . '/controllers/FlightController.php');
include_once($GLOBALS['basename'] . '/controllers/ReservationController.php');
include_once($GLOBALS['basename'] . '/controllers/CityController.php');
include_once($GLOBALS['basename'] . '/controllers/ThankYouController.php');
include_once($GLOBALS['basename'] . '/controllers/AuthController.php');
include_once($GLOBALS['basename'] . '/controllers/MyAccountController.php');

// Routes
switch ($route) {
  case '':
  //  Root
    switch ($method) {
      case 'GET':
        getRoot();
        break;
    }
    break;
  case 'flight':
  //  Flight
    switch ($method) {
      case 'GET':
        getFlight();
        break;
    }
    break;
  case 'reservation':
  //  Reservation
    switch ($method) {
      case 'GET':
        getReservation();
        break;
      case 'POST':
        postReservation();
        break;
    }
    break;
  case 'my-account':
  //  MyAccount
    switch ($method) {
      case 'GET':
        getMyAccount();
        break;
      case 'POST':
        postMyAccount();
        break;
    }
    break;
  case 'city/search':
  //  Search Cities
    switch ($method) {
      case 'GET':
        searchCity();
        break;
    }
    break;
  case 'thank-you':
  //  Thank you page
    switch ($method) {
      case 'GET':
        getThankYou();
        break;
    }
    break;
  case 'login':
  //  Login
    switch ($method) {
      case 'POST':
        login();
        break;
    }
    break;
  case 'logout':
  //  Login
    switch ($method) {
      case 'GET':
      case 'POST':
        logout();
        break;
    }
    break;
  //  Not found if any route matches the url
  default:
    getNotFound();
    break;
}