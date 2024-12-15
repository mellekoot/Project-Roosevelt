<?php

$lifetimenotreally = 30 * 24 * 60 * 60; // 30 days
session_set_cookie_params($lifetimenotreally);
require 'includes/dbh.inc.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($name && $email && $password) {
        //   email has to  ends with @supremecollege.nl
        if (!preg_match('/^[^@]+@supremecollege\.nl$/', $email)) {
            $error_message = "Email must be a valid @supremecollege.nl address.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE school_email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $error_message = "Email is already in use. Please choose another one.";
            } else {
                // Determine the role based on the email 
                $email_prefix = explode('@', $email)[0];   
                if (preg_match('/^[a-zA-Z.]+$/', $email_prefix)) {
                    $role = 'teacher';
                } else {
                    $role = 'student';
                }
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                try {
                    $stmt = $pdo->prepare("INSERT INTO users (name, school_email, password, role) VALUES (:name, :email, :password, :role)");
                    $stmt->execute([
                        'name' => $name,
                        'email' => $email,
                        'password' => $hashed_password,
                        'role' => $role
                    ]);
                    $user_id = $pdo->lastInsertId();
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $name;
                    $_SESSION['role'] = $role;
                    header("Location: index.php");
                    exit;
                } catch (PDOException $e) {
                    $error_message = "An error occurred. Please try again later.";
                }
            }
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project-Roosevelt</title>
    <link rel="stylesheet" href="includes/css/navbarlogin.css">
    <link rel="stylesheet" href="includes/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<nav>
        <ul class="sidebar">
            <li onclick="phoneGone()"><a href=""><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
            <li><a href="/">Project-Roosevelt</a></li>
            <li><a href="/">Homepage</a></li>
            <li><a href="/about">About Us</a></li>
            <li><a href="/advanced">Search</a></li>
            <li><a href="/Signin">Signin</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'teacher'): ?>
            <li><a href="/record" style="color: red;">Record your lecture</a></li>
        <?php endif; ?>
        </ul>
        <ul>
            <li class=""><a href="/">Project-Roosevelt</a></li>
            <li class="leaveTheMobile"><a href="/">Homepage</a></li>
            <li class="leaveTheMobile"><a href="/about">About Us</a></li>
              <li class="leaveTheMobile"><a href="/advanced">Search</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
        <li class="leaveTheMobile"><a href="/logout.php">Log Out</a></li> <!-- Log out link if user is logged in -->
    <?php else: ?>
             <li class="leaveTheMobile"><a href="/login">Login</a></li> <!-- Sign in link if user is not logged in -->
    <?php endif; ?>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'teacher'): ?>
            <li class="leaveTheMobile"><a href="/record" style="color: red;">Record</a></li>
        <?php endif; ?>
            <li class="leaveTheComputer" onclick="whyAPhone()">
                <a href="javascript:void(0);">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368">
                        <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                    </svg>
                </a>
            </li>      
        </ul>
    </nav>



    <div class="alert-box <?php echo $error_message || $success_message ? 'show' : ''; ?>">
        <p class="alert"><?php echo $error_message ? htmlspecialchars($error_message) : htmlspecialchars($success_message); ?></p>
    </div>

    <div class="form">
        <h1 class="heading">Register</h1>
        <form method="POST" action="signup">
            <input type="text" name="name" placeholder="Full Name" autocomplete="off" class="name" required>
            <input type="email" name="email" placeholder="Email" autocomplete="off" class="email" required>
            <input type="password" name="password" placeholder="Password" autocomplete="off" class="password" required>
            <button type="submit" class="submit">Register</button>
        </form>
        <a href="/login" class="link">Already got an account? Log in</a>
    </div>


    
    <div class="footer">
  <p>© Project-Roosevelt. A Melle-Koot personal project</p>
</div>
	<script>
		function whyAPhone(){
			const sidebar  = document.querySelector('.sidebar')
			sidebar.style.display	= 'flex';
		}

		function phoneGone(){
			const sidebar  = document.querySelector('.sidebar')
			sidebar.style.display	= 'none';
		}
	</script>
<script src="includes/js/login.js"></script>
</body>
</html>