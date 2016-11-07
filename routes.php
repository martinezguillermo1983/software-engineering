<?php

include_once($GLOBALS['basename'] . '/controllers/RootController.php');
include_once($GLOBALS['basename'] . '/controllers/NotFoundController.php');
include_once($GLOBALS['basename'] . '/controllers/FlightController.php');

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
  //  Not found if any route matches the url
  default:
    getNotFound();
    break;
}