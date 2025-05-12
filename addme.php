<?php
// add_siddhant.php
include 'config.php';

// Define user details.
$full_name = "Siddhant_Shukla";
$username  = "Siddhant_Shukla";
$password  = "Sshukla@2005";  // Plain password; will be hashed.
$role      = "admin";        // Change to "admin" if needed.

// Create a hashed version of the password.
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute the query to insert the user.
$stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)");
if ($stmt->execute([$full_name, $username, $hashedPassword, $role])) {
    echo "User '$username' added successfully.";
} else {
    echo "Failed to add user.";
}
?>
