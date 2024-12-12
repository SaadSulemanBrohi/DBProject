<?php
require 'config/database.php';

//get signup data only if signup button was clicked

if (isset($_POST['submit'])) {
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $avatar = $_FILES['avatar'];

    if (!$firstname) {
        $_SESSION['signup'] = "Please enter your First Name ";
    } elseif (!$lastname) {
        $_SESSION['signup'] = "Please enter your Last Name ";
    } elseif (!$username) {
        $_SESSION['signup'] = "Please enter your User Name ";
    } elseif (!$email) {
        $_SESSION['signup'] = "Please enter your valid email ";
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['signup'] = "Password should be 8+ characters";
    } elseif (!$avatar['name']) {
        $_SESSION['signup'] = "Please add avatar ";
    } else {
        if ($createpassword != $confirmpassword) {
            $_SESSION['signup'] = "Passwords donot match";
        } else {
            $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

            $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
            $user_check_result = mysqli_query($connection, $user_check_query);
            if (mysqli_num_rows($user_check_result) > 0) {
                $_SESSION['signup'] = "Username or Email already exists";
            } else {
                // rename avatar
                $time = time();
                $avatar_name = $time . $avatar['name'];
                $avatar_tmp_name = $avatar['tmp_name'];
                $avatar_destination_path = 'images/' . $avatar_name;

                //file is image confirmation
                $allowed_files = ['png', 'jpg', 'jpeg'];
                $extention = explode('.', $avatar_name);
                $extention = end($extention);
                if (in_array($extention, $allowed_files)) {
                    if ($avatar['size'] < 1000000) {
                        //upload avatar
                        move_uploaded_file($avatar_tmp_name, $avatar_destination_path);
                    } else {
                        $_SESSION['signup'] = 'File size too big, Should be less than 1MB';
                    }
                } else {
                    $_SESSION['signup'] = "File should be png,jpg or jpeg";
                }
            }
        }
    }

    if (isset($_SESSION['signup'])) {
        $_SESSION['signup-data'] = $POST;
        header('location: ' . ROOT_URL . 'signup.php');
        die();
    } else {
        $insert_user_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin)
        VALUES('$firstname','$lastname','$username','$email','$hashed_password','$avatar_name',0)";
        $insert_user_result = mysqli_query($connection, $insert_user_query);

        if (!mysqli_errno($connection)) {
            $_SESSION['signup-success'] = "Registration sucessful.Please login";
            header('location: ' . ROOT_URL . 'signin.php');
            die();
        }
    }
} else {
    header('location:' . ROOT_URL . 'signup.php');
    die();
}
