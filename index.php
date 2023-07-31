<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php

//login form submittion
if (isset($_POST['submit'])) {

    $errors = array(); //generated errors are stored in here

    //email is required
    if (!isset($_POST['email']) || strlen(trim($_POST['email'])) < 1) {
        $errors[] = 'Username is Missing / Invalid';
    }

    //password is required
    if (!isset($_POST['password']) || strlen(trim($_POST['password'])) < 1) {
        $errors[] = 'Password is Missing / Invalid';
    }

    if (empty($errors)) {
        $email = mysqli_real_escape_string($connection, $_POST['email']); //Avoid sql injection
        $password = mysqli_real_escape_string($connection, $_POST['password']);

        $hashed_password = sha1($password); //password hashing method

        $query = "SELECT * FROM user WHERE email = '{$email}' AND password = '{$hashed_password}' LIMIT 1";

        $result_set = mysqli_query($connection, $query); //execute query

        verify_query($result_set); //check executed sql query errors

        if (mysqli_num_rows($result_set) == 1) {
            //valid user found
            $user = mysqli_fetch_assoc($result_set);
            $_SESSION['user_id'] = $user['id']; //set session data
            $_SESSION['first_name'] = $user['first_name']; //set session data

            $query = "UPDATE user SET last_login = NOW() WHERE id = {$_SESSION['user_id']} LIMIT 1"; //update last login
            $result_set = mysqli_query($connection, $query);
            verify_query($result_set);

            header('Location: users.php');
        } else {
            $errors[] = "Invalid Username or Password";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - User Management System</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>

    <div class="login">

        <form action="index.php" method="post">

            <fieldset>
                <legend>
                    <h1>Log In</h1>
                </legend>

                <?php
                if (isset($errors) && !empty($errors)) {
                    echo '<p class="error">Invalid Username or Password</p>';
                }
                ?>

                <?php
                if (isset($_GET['logout'])) {
                    echo '<p class="info">You have successfully logged out from system</p>'; //logout successfully message display
                }
                ?>

                <p>
                    <label for="">Username:</label>
                    <input type="text" name="email" id="" placeholder="Email Address">
                </p>

                <p>
                    <label for="">Password:</label>
                    <input type="password" name="password" id="" placeholder="Password">
                </p>

                <p>
                    <button type="submit" name="submit">Log In</button>
                </p>
            </fieldset>

        </form>

    </div>

</body>

</html>

<?php mysqli_close($connection); ?>