<?php
// Test script to check profile picture functionality
session_start();
include_once('database/db_connection.php');

echo "<h2>Profile Picture Test</h2>";

// Check if images/profile_pictures directory exists
if (is_dir("images/profile_pictures")) {
    echo "<p style='color: green;'>✓ images/profile_pictures directory exists</p>";
    
    // List files in the directory
    $files = scandir("images/profile_pictures");
    echo "<h3>Files in profile_pictures directory:</h3>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ images/profile_pictures directory does not exist</p>";
}

// Check session data
echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test default avatar
echo "<h3>Default Avatar Test:</h3>";
echo "<img src='default_avatar.php' alt='Default Avatar' width='100' height='100'>";

echo "<p><a href='register.php'>Go to Registration</a> | <a href='login.php'>Go to Login</a></p>";
?>
