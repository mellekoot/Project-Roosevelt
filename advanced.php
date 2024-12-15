<?php
require_once 'includes/dbh.inc.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    
    
    $sql = "SELECT * FROM lectures 
            WHERE title LIKE :query OR 
                  message LIKE :query OR 
                  class LIKE :query OR 
                  subject LIKE :query OR 
                  chapter LIKE :query";
    
    // to make sure I dont get killed with SQL INEJECTION
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':query', '%'.$query.'%');
    $stmt->execute();

    $lectures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $lectures = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project-Roosevelt : Advanced Search</title>
    <link rel="stylesheet" href="includes/css/navbar.css">
    <link rel="stylesheet" href="includes/css/index.css">
   <link rel="stylesheet" href="includes/css/advanced.css">
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

    <!-- HOMEPAGE -->
    <div class="home">
        <form action="advanced_search.php" method="GET">
            <h1>Advanced Search</h1>
            <h2>Enter a keyword to search across classes, subjects, chapters, or titles</h2>
            <div class="search-bar">
                <div class="search-container">
                    <input type="text" name="query" placeholder="Enter your search..." value="<?= isset($query) ? htmlspecialchars($query) : '' ?>" required>
                </div>
                
                <div class="optional-filters">
                    <select name="class">
                        <option value="">Class (Optional)</option>
                        <option value="1VMYP" <?= isset($_GET['class']) && $_GET['class'] == '1VMYP' ? 'selected' : '' ?>>1VMYP</option>
                        <option value="2VMYP" <?= isset($_GET['class']) && $_GET['class'] == '2VMYP' ? 'selected' : '' ?>>2VMYP</option>
                        <option value="3VMYP" <?= isset($_GET['class']) && $_GET['class'] == '3VMYP' ? 'selected' : '' ?>>3VMYP</option>
                        <option value="4VMYP" <?= isset($_GET['class']) && $_GET['class'] == '4VMYP' ? 'selected' : '' ?>>4VMYP</option>
                        <option value="5VWO" <?= isset($_GET['class']) && $_GET['class'] == '5VWO' ? 'selected' : '' ?>>5VWO</option>
                        <option value="6VWO" <?= isset($_GET['class']) && $_GET['class'] == '6VWO' ? 'selected' : '' ?>>6VWO</option>
                    </select>

                    <select name="subject">
                        <option value="">Subject (Optional)</option>
                        <option value="English" <?= isset($_GET['subject']) && $_GET['subject'] == 'English' ? 'selected' : '' ?>>English</option>
                        <option value="Wiskunde A" <?= isset($_GET['subject']) && $_GET['subject'] == 'Wiskunde A' ? 'selected' : '' ?>>Wiskunde A</option>
                        <option value="Wiskunde B" <?= isset($_GET['subject']) && $_GET['subject'] == 'Wiskunde B' ? 'selected' : '' ?>>Wiskunde B</option>
                        <option value="Natuurkunde" <?= isset($_GET['subject']) && $_GET['subject'] == 'Natuurkunde' ? 'selected' : '' ?>>Natuurkunde</option>
                        <option value="Biology" <?= isset($_GET['subject']) && $_GET['subject'] == 'Biology' ? 'selected' : '' ?>>Biology</option>
                    </select>

                    <select name="chapter">
                        <option value="">Chapter (Optional)</option>
                        <option value="1" <?= isset($_GET['chapter']) && $_GET['chapter'] == '1' ? 'selected' : '' ?>>Chapter 1</option>
                        <option value="2" <?= isset($_GET['chapter']) && $_GET['chapter'] == '2' ? 'selected' : '' ?>>Chapter 2</option>
                        <option value="3" <?= isset($_GET['chapter']) && $_GET['chapter'] == '3' ? 'selected' : '' ?>>Chapter 3</option>
                    </select>
                </div>

                <button type="submit">Search Now</button>
            </div>
        </form>
    </div>

    <div class="footer">
        <p>© Project-Roosevelt. A Melle-Koot personal project</p>
    </div>

    <script>
        function whyAPhone(){
            const sidebar  = document.querySelector('.sidebar')
            sidebar.style.display = 'flex';
        }

        function phoneGone(){
            const sidebar  = document.querySelector('.sidebar')
            sidebar.style.display = 'none';
        }
    </script>
</body>
</html>
        