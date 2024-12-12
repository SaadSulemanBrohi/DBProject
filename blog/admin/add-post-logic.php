<?php
session_start();
require 'config/database.php';

if (isset($_POST['submit'])) {
    $author_id = $_SESSION['user-id'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    $is_featured = ($is_featured == 1) ? 1 : 0;

    if (!$title) {
        $_SESSION['add-post'] = "Enter post title";
    } elseif (!$category_id) {
        $_SESSION['add-post'] = "Select post category";
    } elseif (!$body) {
        $_SESSION['add-post'] = "Enter post body";
    } elseif (!$thumbnail['name']) {
        $_SESSION['add-post'] = "Choose post thumbnail";
    } else {
        $time = time();
        $thumbnail_name = $time . $thumbnail['name'];
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $thumbnail_destination_path = '../images/' . $thumbnail_name;

        // Check if the file is an image and within allowed size
        $allowed_files = ['png', 'jpg', 'jpeg'];
        $extension = pathinfo($thumbnail_name, PATHINFO_EXTENSION);

        if (in_array($extension, $allowed_files)) {
            if ($thumbnail['size'] < 2_000_000) {
                if (!move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path)) {
                    $_SESSION['add-post'] = "Failed to upload thumbnail.";
                }
            } else {
                $_SESSION['add-post'] = "File size too big. Reduce to less than 2MB.";
            }
        } else {
            $_SESSION['add-post'] = "File type should be png, jpg, or jpeg.";
        }
    }

    if (isset($_SESSION['add-post'])) {
        $_SESSION['add-post-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/add-post.php');
        die();
    } else {
        if ($is_featured == 1) {
            $zero_all_is_featured_query = "UPDATE posts SET is_featured=0";
            $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
        }

        $query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'sssiis', $title, $body, $thumbnail_name, $category_id, $author_id, $is_featured);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['add-post-success'] = "New post added successfully";
            header('location: ' . ROOT_URL . 'admin/');
            die();
        } else {
            $_SESSION['add-post'] = "Failed to add post.";
            header('location: ' . ROOT_URL . 'admin/add-post.php');
            die();
        }
    }
}

header('location: ' . ROOT_URL . 'admin/add-post.php');
die();
