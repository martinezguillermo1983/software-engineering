<?php
include_once('config.php');  
date_default_timezone_set ($timezone);
//  Global variables
$basename = __DIR__;
$baseUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'.$folder.'/';
$baseUrl = removeUrlParameters($baseUrl);

//  Global functions
function render($viewPath, $variables=[]) {
  include_once($GLOBALS['basename'] . '/views/layout/application.html');  
}

function getInputParameter($parameter) {
  switch ($GLOBALS['method']) {
    case 'GET':
      return isset($_GET[$parameter]) ? $_GET[$parameter] : null;
      break;
    case 'POST':
      return isset($_POST[$parameter]) ? $_POST[$parameter] : null;
      break;
    
    default:
      return null;
      break;
  }
}

function removeUrlParameters($url) {
  $url = explode('?', $url);
  return $url[0];
}

//  Include all the models
include_once($GLOBALS['basename'] . '/models.php');

//  Get the request route
$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = removeUrlParameters($requestUri);
$requestUriParts = explode('/', $requestUri);
array_shift($requestUriParts);
array_shift($requestUriParts);
$route = implode('/', $requestUriParts);
//  Get the request method
$method = $_SERVER['REQUEST_METHOD'];

//  Handle routes
include_once($GLOBALS['basename'] . '/routes.php');
