<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php
if (!isset($_SESSION['user_id'])) { //check session
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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    //checking required fields
    $req_fields = array('first_name', 'last_name', 'email', 'user_id');

    $errors = array_merge($errors, check_req_fields($req_fields));

    //checking max lengths
    $max_length_fields = array('first_name' => 50, 'last_name' => 100, 'email' => 100);

    $errors = array_merge($errors, check_max_len($max_length_fields));

    //check email is valid or not
    if (!is_email($_POST['email'])) {
        $errors[] = "Email address is invalid.";
    }

    //email already exist
    $email = mysqli_real_escape_string($connection, $_POST['email']);

    $query = "SELECT * FROM user WHERE email = '{$email}' AND id != {$user_id} LIMIT 1";

    $result_set = mysqli_query($connection, $query);

    if ($result_set) {
        if (mysqli_num_rows($result_set) == 1) {
            $errors[] = 'Email address already exist'; //insert error message to error array
        }
    }

    if (empty($errors)) {
        //no error => can add an user
        $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);

        $query = "UPDATE user SET first_name = '{$first_name}', last_name = '{$last_name}', email = '{$email}' WHERE id = {$user_id} LIMIT 1"; //update user details

        $result = mysqli_query($connection, $query);

        if ($result) {
            header('Location: users.php?user_modified=true');
        } else {
            $errors[] = "Faild to modify";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View / Modify user</title>
    <link rel="stylesheet" href="css/add-user.css">
</head>

<body>
    <header>
        <div class="appname">User Management System</div>
        <div class="loggedin">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Log Out</a></div>
    </header>
    <main>

        <div class="header-div">
            <h1>View / Modify User <span><a href="add-user.php">
                        < Back to User List</a></span></h1>
        </div>

        <?php
        if (!empty($errors)) {
            display_errors($errors);
        }
        ?>

        <div class="add-user-form">
            <form action="modify-user.php" method="post" class="userform">
                <input type="hidden" name="user_id" <?php echo 'value="' . $user_id . '"'; ?>>
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
                    <label for="">Password:</label>
                    <span>*****</span> | <a href="change-password.php?user_id=<?php echo $user_id; ?>">Change Password</a>
                </p>
                <p>
                    <label for="">&nbsp;</label>
                    <button type="submit" name="submit">Modify</button>
                </p>
            </form>
        </div>

    </main>
</body>

</html>