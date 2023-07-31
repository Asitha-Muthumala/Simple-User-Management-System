<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
}

$errors = array();

$user_id = '';
$first_name = '';
$last_name = '';
$email = '';

if (isset($_GET['user_id'])) {
    //getting user information
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);

    $query = "SELECT * FROM user WHERE id = {$user_id} LIMIT 1";

    $result_set = mysqli_query($connection, $query);

    if ($result_set) {
        if (mysqli_num_rows($result_set) == 1) {
            //user found
            $result = mysqli_fetch_assoc($result_set);
            $first_name = $result['first_name'];
            $last_name = $result['last_name'];
            $email = $result['email'];
        } else {
            //user not found
            header('Location: users.php?err=user_not_found');
        }
    } else {
        //query unsuccessful
        header('Location: users.php?err=query_failure');
    }
}

if (isset($_POST['submit'])) {

    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    //checking required fields
    $req_fields = array('user_id', 'password');

    $errors = array_merge($errors, check_req_fields($req_fields));

    //checking max length
    $max_length_fields = array('password' => 40);

    $errors = array_merge($errors, check_max_len($max_length_fields));

    if (empty($errors)) {
        //no error => change password
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        $hashed_password = sha1($password);

        $query = "UPDATE user SET password = '{$hashed_password}' WHERE id = {$user_id} LIMIT 1";

        $result = mysqli_query($connection, $query);

        if ($result) {
            header('Location: users.php?password_updated=true');
        } else {
            $errors[] = "Faild to update password";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/add-user.css">
</head>

<body>
    <header>
        <div class="appname">User Management System</div>
        <div class="loggedin">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Log Out</a></div>
    </header>
    <main>

        <div class="header-div">
            <h1>Change Password <span><a href="users.php">
                        < Back to User List</a></span></h1>
        </div>

        <?php
        if (!empty($errors)) {
            display_errors($errors);
        }
        ?>

        <div class="add-user-form">
            <form action="change-password.php" method="post" class="userform">
                <input type="hidden" name="user_id" <?php echo 'value="' . $user_id . '"'; ?>>
                <p>
                    <label for="">First Name:</label>
                    <input type="text" name="first_name" <?php echo 'value="' . $first_name . '"'; ?> disabled>
                </p>
                <p>
                    <label for="">Last Name:</label>
                    <input type="text" name="last_name" <?php echo 'value="' . $last_name . '"'; ?> disabled>
                </p>
                <p>
                    <label for="">Email Address:</label>
                    <input type="text" name="email" <?php echo 'value="' . $email . '"'; ?> disabled>
                </p>
                <p>
                    <label for="">New Password:</label>
                    <input type="password" name="password" id="password">
                </p>
                <p>
                    <label for="">Show Password:</label>
                    <input type="checkbox" name="showpassword" id="showpassword">
                </p>
                <p>
                    <label for="">&nbsp;</label>
                    <button type="submit" name="submit">Change Password</button>
                </p>
            </form>
        </div>

    </main>

    <script>
        //show password when clicked check box
        const toggleCheckbox = document.getElementById('showpassword');
        const inputField = document.getElementById('password');

        toggleCheckbox.addEventListener('click', function() {
            if (toggleCheckbox.checked) {
                inputField.type = 'text';
            } else {
                inputField.type = 'password';
            }
        });
    </script>

</body>

</html>