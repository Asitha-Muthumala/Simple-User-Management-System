<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
}

$errors = array();

$first_name = '';
$last_name = '';
$email = '';
$password = '';

if (isset($_POST['submit'])) {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    //checking required fields
    $req_fields = array('first_name', 'last_name', 'email', 'password');

    $errors = array_merge($errors, check_req_fields($req_fields));

    //checking max length
    $max_length_fields = array('first_name' => 50, 'last_name' => 100, 'email' => 100, 'password' => 40);

    $errors = array_merge($errors, check_max_len($max_length_fields));

    //check email address
    if (!is_email($_POST['email'])) {
        $errors[] = "Email address is invalid.";
    }

    //email already exist
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $query = "SELECT * FROM user WHERE email = '{$email}' LIMIT 1";

    $result_set = mysqli_query($connection, $query);

    if ($result_set) {
        if (mysqli_num_rows($result_set) == 1) {
            $errors[] = 'Email address already exist';
        }
    }

    if (empty($errors)) {
        //no error => can add user
        $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);

        $hashed_password = sha1($password);

        $query = "INSERT INTO user (";
        $query .= "first_name, last_name, email, password, is_deleted";
        $query .= ") VALUES ('{$first_name}', '{$last_name}', '{$email}', '{$hashed_password}', 0)";

        $result = mysqli_query($connection, $query);

        if ($result) {
            header('Location: users.php?user_added=true');
        } else {
            $errors[] = "Faild to add new record";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New user</title>
    <link rel="stylesheet" href="css/add-user.css">
</head>

<body>
    <header>
        <div class="appname">User Management System</div>
        <div class="loggedin">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Log Out</a></div>
    </header>
    <main>

        <div class="header-div">
            <h1>Add New User <span><a href="add-user.php">
                        < Back to User List</a></span></h1>
        </div>

        <?php
        if (!empty($errors)) {
            display_errors($errors);
        }
        ?>

        <div class="add-user-form">
            <form action="add-user.php" method="post" class="userform">
                <p>
                    <label for="">First Name:</label>
                    <input type="text" name="first_name" <?php echo 'value="' . $first_name . '"'; ?>>
                </p>
                <p>
                    <label for="">Last Name:</label>
                    <input type="text" name="last_name" <?php echo 'value="' . $last_name . '"'; ?>>
                </p>
                <p>
                    <label for="">Email Address:</label>
                    <input type="text" name="email" <?php echo 'value="' . $email . '"'; ?>>
                </p>
                <p>
                    <label for="">New Password:</label>
                    <input type="password" name="password">
                </p>
                <p>
                    <label for="">&nbsp;</label>
                    <button type="submit" name="submit">Save</button>
                </p>
            </form>
        </div>

    </main>
</body>

</html>