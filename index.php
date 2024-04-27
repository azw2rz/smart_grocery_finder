<?php
if (!headers_sent() && '' == session_id()) {
    session_start();
}

$path = @parse_url($_SERVER['REQUEST_URI'])['path'];

// echo $path . '\n';

switch ($path) {
    case '/':
        require 'login.php';
        break;
    case '/grocery.php':
        require 'grocery.php';
        break;
    case '/login.php':
        require 'login.php';
        break;
    case '/signup.php':
        require 'signup.php';
        break;
    case '/admin.php':
        require 'admin.php';
        break;
    case '/profile.php':
        require 'profile.php';
        break;
    case '/favorites.php':
        require 'favorites.php';
        break;
    case '/request_change.php':
        require 'request_change.php';
        break;
    case '/changePassword.php':
        require 'changePassword.php';
        break;
    case '/admin_change_success.php':
        require 'admin_change_success.php';
        break;
    case '/request_change_success.php':
        require 'request_change_success.php';
        break;
    default:
        if (strpos($path, '/index.php') === 0) {
            require 'login.php';
        } else {
            http_response_code(404);
            exit('Not Found');
        }
        break;
}
?>