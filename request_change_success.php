<?php session_start(); ?>

<?php include 'header.php'; ?>

<?php   // form handling
if (!$_SESSION["user_id"]) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Submitted</title>
</head>
<body>
    <div class="container">
        <div class="row g-3 mt-2">
            <div class="col">
                <h2>Request Submitted Successfully</h2>
                <p>Your change request has been submitted successfully.</p>
                <p><a href="request_change.php" class="btn btn-primary" style="margin-top:10px;">Go back to form</a></p>
            </div>
        </div>
    </div>
</body>
</html>