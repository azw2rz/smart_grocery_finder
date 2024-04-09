<?php 
require_once("connect-db.php");
require_once("request-db.php");
?>

<?php

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout-btn'])) {
    echo "<p>Logging out</p>";

    $_SESSION = array();
    session_destroy();

    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Smart Grocery Finder</title>

    <!-- header.php is the only file that links to CSS sheets -->

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
    <link rel="stylesheet" href="grocery.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Smart Grocery Finder</h1>
            <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
                <input type="submit" class="logout-btn" name="logout-btn" value="Logout">
            </form> 
        </div>
    </header>

    <!-- Rest of the page content goes here -->
</body>
</html>