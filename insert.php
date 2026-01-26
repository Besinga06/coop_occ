<?php
$message = "";

// Check if form is submitted
if (isset($_POST['submit_data'])) {
    // Include database connection
    include "db_connect.php";

    // Get data from POST
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $fullname = trim($_POST['fullname']);
    $usertype = (int)$_POST['usertype']; // e.g., 1=admin, 2=cashier, 3=treasurer

    // Validate required fields
    if (empty($username) || empty($password) || empty($fullname) || empty($usertype)) {
        $message = "All fields are required.";
    } else {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare MySQL query to prevent SQL injection
        $stmt = $db->prepare("INSERT INTO tbl_users (username, password, usertype, fullname, field_status, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
        $stmt->bind_param("ssis", $username, $hashed_password, $usertype, $fullname);

        // Execute the query and check result
        if ($stmt->execute()) {
            $message = "User inserted successfully.";
        } else {
            $message = "Error: Could not insert user. " . $stmt->error;
        }

        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert User</title>
</head>
<body>
<div style="width: 500px; margin: 20px auto;">

    <!-- Show message -->
    <div><?php echo $message; ?></div>

    <form action="" method="post">
        <table width="100%" cellpadding="5" cellspacing="1" border="1">
            <tr>
                <td>Username:</td>
                <td><input name="username" type="text" required></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input name="password" type="password" required></td>
            </tr>
            <tr>
                <td>Full Name:</td>
                <td><input name="fullname" type="text" required></td>
            </tr>
            <tr>
                <td>User Type:</td>
                <td>
                    <select name="usertype" required>
                        <option value="">Select type</option>
                        <option value="1">Admin</option>
                        <option value="2">Cashier</option>
                        <option value="3">Treasurer</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><a href="list.php">See Users</a></td>
                <td><input name="submit_data" type="submit" value="Insert User"></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>

