<?php
// logout.php
session_start();

// Check if logout was confirmed
if (isset($_POST['confirm_logout'])) {
    // Perform logout actions
    logoutUser();
    header("Location:../index.php");
    exit();
}

// If user accesses page directly without confirmation
if (isset($_SESSION['user_id'])) {
    // Show confirmation page
    showLogoutConfirmation();
} else {
    // User not logged in, redirect to login
    header("Location: ../index.php");
    exit();
}

function logoutUser() {
    // Clear session data
    $_SESSION = array();
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
    
    // Optional: Clear any other cookies
    setcookie('remember_me', '', time() - 3600, '/');
}

function showLogoutConfirmation() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Logout Confirmation</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
            .logout-container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                text-align: center;
                max-width: 400px;
                width: 100%;
            }
            .btn {
                padding: 10px 20px;
                margin: 10px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
            }
            .btn-logout {
                background-color: #dc3545;
                color: white;
            }
            .btn-cancel {
                background-color: #6c757d;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="logout-container">
            <h2>Logout Confirmation</h2>
            <p>Are you sure you want to logout?</p>
            
            <form method="POST" action="logout.php">
                <button type="submit" name="confirm_logout" value="1" class="btn btn-logout">
                    Yes, Logout
                </button>
                <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>