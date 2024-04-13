<?php 
require_once("connect-db.php");
require_once("request-db.php");
?>

<?php
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout-btn'])) {
    // echo "<p>Logging out</p>";

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
                <div class="header-buttons">
                    <?php if ($_SESSION): ?>
                        <div class="profile-dropdown">
                            <button class="profile-btn" onclick="toggleProfileMenu(event)"></button>
                            <div class="profile-menu" id="profileMenu">
                                <a href="profile.php">Edit Profile</a>
                                <a href="favorites.php">My Favorites</a>
                                <a href="watchlist.php">My Watchlist</a>
                                <form method="post" class="logout-form" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
                                    <input type="submit" class="logout-btn" name="logout-btn" value="Logout">
                                </form> 
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <script>
            function toggleProfileMenu() {
                event.stopPropagation();
                var menu = document.getElementById("profileMenu");
                menu.style.display = menu.style.display === "block" ? "none" : "block";
                console.log("toggleProfileMenu function called");
            }

            // called whenever a click
            window.onclick = function(event) {
                var menu = document.getElementById("profileMenu");
                if (event.target !== menu && event.target.parentNode !== menu) {
                    menu.style.display = "none";
                }
            }
        </script>
    </body>
</html>