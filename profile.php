<?php
require 'config.php';

//We need to use sessions, so you should always start sessions using the below code.
session_start();

//if not logged in redirect to login page
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

//query the database for the profile details
//We don't have the password or email info stored in sessions so instead
// we can get the results from the database.
$stmt = $con->prepare('SELECT password, email FROM accounts WHERE id = ?');
//In this case we can use the account ID to get the account info
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();
?>

<?= template_header('Profile') ?>
<?= template_nav() ?>

    <!-- START PAGE CONTENT -->
    <h1 class="title">Profile</h1>
    <p class = "subtitle">Your account details are below.</p>
    <table class = "table">
    <tr>
        <td>Username:</td>
        <td><?=$_SESSION['name']?></td>
    </tr>
    <tr>
        <td>Password:</td>
        <td><?= $password ?></td>
    </tr>
    <tr>
        <td>Email:</td>
        <td><?= $email ?></td>
    </tr>
    </table>
    <!-- END PAGE CONTENT -->

<?= template_footer() ?>