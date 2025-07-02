<?php
// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "newcontact");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only process form on POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // You can start a session here if needed
        // session_start();
        // $_SESSION['email'] = $email;

        header("Location: ../index.php");
        exit();
    } else {
        echo "Invalid login credentials.";
    }

    $stmt->close();
}

$conn->close();
?>
