<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
}

if (isset($_GET['user_id'])) {
    //getting user information
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);

    if ($_SESSION['user_id'] == $user_id) {
        //cant delete current logged user
        header('Location: users.php?err=cannot_delete_current_user');
    } else {
        //delete user (change is_deleted status as 1)
        $query = "UPDATE user SET is_deleted = 1 WHERE id = '{$user_id}' LIMIT 1";
        $result = mysqli_query($connection, $query);

        if ($result) {
            header('Location: users.php?msg=user_deleted');
        } else {
            header('Location: users.php?msg=delete_faild');
        }
    }
} else {
    header('Location: users.php');
}

?>
