<?php
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':                   // URL (without file name) to a default screen
        require 'login.php';
        break; 
    case '/login.php':     // if you plan to also allow a URL with the file name 
        require 'login.php';
        break;              
    case '/signup.php':
        require 'signup.php';
        break;
    case '/grocery.php':
        // $_GET = array_merge($_GET, @parse_url($_SERVER['REQUEST_URI'])['query']);
        // parse_str($_GET['query'], $_GET);
        require 'grocery.php';
        break;
//    case '/signup.php':
//       require 'signup.php';
//       break;
//    case '/signup.php':
//       require 'signup.php';
//       break;
//    case '/signup.php':
//       require 'signup.php';
//       break;
//    case '/signup.php':
//       require 'signup.php';
//       break;
   default:
      http_response_code(404);
      exit('Not Found');
}  
?>