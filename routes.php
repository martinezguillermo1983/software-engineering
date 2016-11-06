<?php

include_once($GLOBALS['basename'] . '/controllers/RootController.php');
include_once($GLOBALS['basename'] . '/controllers/NotFoundController.php');

// Routes
switch ($route) {
  case '':
  //  Root
    switch ($method) {
      case 'GET':
        getRoot();
        break;
      case 'POST':
        postRoot();
        break;
    }
    break;
  //  Not found if any route matches the url
  default:
    getNotFound();
    break;
}