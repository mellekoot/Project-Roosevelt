<?php
require "includes/dbh.inc.php";

// Get the ID from the query parameter
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id || !is_numeric($id)) {
    header("Location: /errorsql.php");
    exit();
}


$query = "SELECT * FROM lectures WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$lecture = $stmt->fetch();
if (!$lecture) {
     header("Location: /errorsql.php");
    exit();
}


$SQL = "SELECT * FROM users WHERE id = ?";
$teacherStmt = $pdo->prepare($SQL);
$teacherStmt->execute([$lecture['teacher_id']]);
$teacher = $teacherStmt->fetch();

if (!$teacher) {
    $teacher = ['name' => 'Unknown', 'school_email' => 'Not Provided'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecture Details</title>
    <link rel="stylesheet" href="includes/css/navbar.css">
    <link rel="stylesheet" href="includes/css/lecture.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
 
    <script>
        window.onload = function () {
            window.scrollTo(0, 0); // Ensure data is on top for if the yapping is to much.
        };
    </script>
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

  
    <div class="lecture-container">
        <h1><?php echo html_entity_decode(htmlspecialchars($lecture['title'])); ?></h1>
        <h3>Class: <?php echo html_entity_decode(htmlspecialchars($lecture['class'])); ?> | Subject: <?php echo html_entity_decode(htmlspecialchars($lecture['subject'])); ?></h3>
        <div class="teacher-info">
            <p><strong>Uploaded by:</strong> <?php echo html_entity_decode(htmlspecialchars($teacher['name'])); ?> (<?php echo html_entity_decode(htmlspecialchars($teacher['school_email'])); ?>)</p>
        </div>
        <p><strong>Content:</strong><br> <?php echo html_entity_decode(nl2br(htmlspecialchars($lecture['message']))); ?></p>

        <?php if ($lecture['audio_url']): ?>
            <div class="audio-player">
                <audio controls>
                    <source src="<?php echo htmlspecialchars($lecture['audio_url']); ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>© Project-Roosevelt. A Melle-Koot personal project</p>
    </div>
</body>
</html>
