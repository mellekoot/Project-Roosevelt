<?php
require "includes/dbh.inc.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /unauthorized.php");
    exit; 
}
if (!isset($_SESSION['user_id'])) {
    header ('Location: login');
 }
$userId = $_SESSION['user_id'];
$userQuery = $pdo->prepare("SELECT id, subject FROM users WHERE id = ?");
$userQuery->execute([$userId]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$teacherId = $user['id'];
$teacherSubject = $user['subject'];
/* DATA COPIED FROM HUGGINGFACE API INFORMATION */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['audioBlob'])) {
        $file = $_FILES['audioBlob'];

        if ($file['error'] === UPLOAD_ERR_OK && strpos($file['type'], 'audio') !== false) {
            $apiUrl = "https://api-inference.huggingface.co/models/openai/whisper-large-v3-turbo";
            $headers = [
                "Authorization: Bearer YOURAPIKEY",
                "Content-Type: application/octet-stream"
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $apiUrl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, file_get_contents($file['tmp_name']));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                $error = curl_error($curl);
                die("Error with transcription API: $error");
            } else {
                $responseDecoded = json_decode($response, true);
                $transcribedText = isset($responseDecoded['text']) ? $responseDecoded['text'] : 'Error processing transcription.';
                $_SESSION['transcribed_text'] = htmlspecialchars($transcribedText);
            }
            curl_close($curl);
        } else {
            die("Error processing the uploaded audio. Please try again.");
        }

        // Store information in session so ya can refresh whilst still maintaining info
        $_SESSION['class'] = $_POST['class'];
        $_SESSION['title'] = $_POST['title'];
        $_SESSION['chapter'] = $_POST['chapter'];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['confirm'])) {
        // i have no clue how this works but it does
        if (!isset($_SESSION['transcribed_text'], $_SESSION['class'], $_SESSION['title'], $_SESSION['chapter'])) {
            error_log("Session data missing at save point: " . print_r($_SESSION, true));
            die("Error: Missing session data for saving.");
        }

        try {
            $transcribedText = $_SESSION['transcribed_text'];
            $class = $_SESSION['class'];
            $title = $_SESSION['title'];
            $chapter = $_SESSION['chapter'];
            $audioUrl = $_SESSION['audio_url'] ?? null; // audio URL for future refrence!

       
            $sql = "INSERT INTO lectures (message, audio_url, teacher_id, title, chapter, class, subject) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $transcribedText,
                $audioUrl,
                $teacherId,
                $title,
                $chapter,
                $class,
                $teacherSubject
            ]);
            $lectureId = $pdo->lastInsertId();
            unset($_SESSION['transcribed_text'], $_SESSION['class'], $_SESSION['title'], $_SESSION['chapter']); //CLEAR SESSION SO NEW LECTURES CNA BE MADE
            header("Location: success?id=" . $lectureId);
            exit;
        } catch (PDOException $e) {
            die("Error saving lecture: " . $e->getMessage());
        }
    }

    if (isset($_POST['cancel'])) {
        unset($_SESSION['transcribed_text'], $_SESSION['class'], $_SESSION['title'], $_SESSION['chapter']); //When cancelled refresh and remove all data
        header("Location: /record");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record : Project-Roosevevelt</title>
    <link rel="stylesheet" href="includes/css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="includes/css/record.css">
</head>
<body> <nav>
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
        <li class="leaveTheMobile"><a href="/logout.php">Log Out</a></li> 
    <?php else: ?>
             <li class="leaveTheMobile"><a href="/login">Login</a></li>
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
<div class="container" id="container">
<h1>Hello, <span><?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES); ?>!</span></h1>
    <p>Record your classes.</p>
    <?php if (!isset($_SESSION['transcribed_text'])): ?>
        <!-- MEANING IF NOT SET SHOW THIS -->
        <form id="audioForm" enctype="multipart/form-data" method="POST">
            <div class="form-group">
                <label for="class">Class:</label>
                <select id="class" name="class" required>
                    <option value="" disabled selected>Select your class</option>
                    <option value="1VMYP">1VMYP</option>
                    <option value="2VMYP">2VMYP</option>
                    <option value="3VMYP">3VMYP</option>
                    <option value="4VMYP">4VMYP</option>
                    <option value="5VWO">5VWO</option>
                    <option value="6VWO">6VWO</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="chapter">Chapter:</label>
                <input type="number" id="chapter" name="chapter" required>
            </div>

            <button type="button" id="recordButton" class="record-button">Start Recording</button>
            <div class="timer" id="timer"></div>
            <div class="status" id="status"></div>
            <div class="alert" id="alert" style="display:none;"></div>
        </form>
    <?php else: ?>
        <!-- IF IT IS SET MAKE IT REVIEW IF AI DID A NORMAL JOB -->
        <h2>Review Your Transcription</h2>
        <p><strong>Class:</strong> <?php echo htmlspecialchars($_SESSION['class'], ENT_QUOTES); ?></p>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($_SESSION['title'], ENT_QUOTES); ?></p>
        <p><strong>Chapter:</strong> <?php echo htmlspecialchars($_SESSION['chapter'], ENT_QUOTES); ?></p>
        
        <h3>Transcribed Text:</h3>
<form method="POST">
    <textarea name="transcribed_text" rows="10" required><?php echo htmlspecialchars(html_entity_decode($_SESSION['transcribed_text'], ENT_QUOTES, 'UTF-8')); ?></textarea>
    <br><br>
    <button type="submit" name="confirm" class="btn">Confirm and Save</button>
</form>
        <form method="POST">
            <button type="submit" name="cancel" class="btn-cancel">Cancel</button>
        </form>
    <?php endif; ?>
    </div>
   
    <div class="loading-bar" id="loadingBar">
        <div class="loading-text">Loading...</div>
        <div class="progress">
            <div class="progress-inner" id="progress"></div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Project-Roosevelt. All Rights Reserved.</p>
    </div>

  <script src="includes/js/record.js"></script>
</body>
</html>
