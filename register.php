<?php
require 'config.php';

//additional php code for this page goes here
if (isset($_POST['username'], $_POST['password'], $_POST['email'])){
    // We need to check if an account with that username exists
    if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
        //Bind parameters (s = string, i = int, b = blob, etc. hash the password using the PHP password_hash function)
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();
        //Store the result so we can check if the account exists in the database.
        if ($stmt->num_rows > 0){
            //username already exists
            echo 'Username already exists, please choose another!';
        }else {
            //Username doesn't exist, create new account
            if($stmt = $con->prepare('INSERT INTO accounts (username, password, email, activation_code) VALUES (?, ?, ?, ?)')) {
                //We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $uniqid = uniqid();
                //Bind parameters (s = string, i = int, b = blob, etc. hash the password using the PHP password_hash function)
                $stmt->bind_param('ssss', $_POST['username'], $password, $_POST['email'], $uniqid);
                $stmt->execute();
                //send confirmation email
                $from = 'andrewdecker1@mail.weber.edu';
                $subject = 'Account activation required';
                $header = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" 
                         . 'X-Mailer: PHP/' . phpversion() . "\r\n"
                         . 'MIME-VERSION: 1.0' . "\r\n"
                         . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                $activate_link = 'http://icarus.cs.weber.edu/~ad82769/web3400/project3/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
                $message = '<p>Please click the following link to activate your account: <a href=' . $activate_link . '">' . $activate_link . '</a></p>';
                echo "<a href='$activate_link'>$activate_link</a>";
            } else {
                //something went wrong with the sql statement, check to make sure accounts table exists with all 3 fields
                echo ('Could not prepare statement!');

            }
        }
        $stmt->close();
    } else {
        echo ('Could not prepare statement!');
    }
    $con->close();

}
?>

<?= template_header('Register') ?>
<?= template_nav() ?>

    <!-- START PAGE CONTENT -->
    <h1 class="title">Register</h1>
    <form action="register.php" method="post">
        <div class="field">
            <label class="label">Username</label>
            <p class="control has-icons-left">
                <input name="username" class="input" type="text" placeholder="Username" required>
                <span class="icon is-small is-left">
                    <i class="fas fa-user"></i>
                </span>
            </p>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <p class="control has-icons-left">
                <input name="password" class="input" type="password" placeholder="Password" required>
                <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                </span>
            </p>
        </div>
        <div class="field">
            <label class="label">Email</label>
            <div class="control has-icons-left has-icons-right">
            <input name="email" class="input" type="email" placeholder="Email" required>
            <span class="icon is-small is-left">
                <i class="fas fa-envelope"></i>
            </span>
        </div>
        <div class="field">
            <p class="control">
                <button class="button is-success">
                    Register
                </button>
            </p>
        </div>
    </form>
    <!-- END PAGE CONTENT -->

<?= template_footer() ?>