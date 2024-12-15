<?php
require "includes/dbh.inc.php"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project-Roosevelt : Homepage</title>
    <link rel="stylesheet" href="includes/css/navbar.css">
	<link rel="stylesheet" href="includes/css/index.css">
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
        <form action="search.php" method="GET">
        <h1>Never Miss Anything In Class</h1>
        <h2>SUPREME COLLEGE NEDERLAND</h2>
        <div class="search-bar">
        <select name="class">
            <option value="">Class</option>
            <option value="1VMYP">1VMYP</option>
            <option value="2VMYP">2VMYP</option>
            <option value="3VMYP">3VMYP</option>
            <option value="4VMYP">4VMYP</option>
            <option value="5VWO">5VWO</option>
            <option value="6VWO">6VWO</option>
        </select>
        <select name="subject">
        <option value="">Subject</option>
            <option value="English">English</option>
            <option value="Wiskunde A">Wiskunde A</option>
            <option value="Wiskunde B">Wiskunde B</option>
            <option value="Wiskunde">Wiskunde</option>
            <option value="Natuurkunde">Natuurkunde</option>
            <option value="Biology">Biology</option>
            <option value="Scheikunde">Scheikunde</option>
            <option value="Beco">Beco</option>
            <option value="Nederlands">Nederlands</option>
            <option value="NT2">NT2</option>
            <option value="Spaans">Spaans</option>
            <option value="Duits">Duits</option>
            <option value="Geschiedenis">Geschiedenis</option>
            <option value="PHE">PHE</option>
            <option value="ECO">ECO</option>
            <option value="I&S">I&S</option>
        </select>
        <select name="chapter">
            <option value="">Chapter</option>
            <option value="1">Chapter 1</option>
            <option value="2">Chapter 2</option>
            <option value="3">Chapter 3</option>
            <option value="4">Chapter 4</option>
            <option value="5">Chapter 5</option>
            <option value="6">Chapter 6</option>
            <option value="7">Chapter 7</option>
            <option value="8">Chapter 8</option>
            <option value="9">Chapter 9</option>
        </select>
        <button>Search Now</button>
    </div>
</form>
<a href="advanced.php" class="advanced-link">Not finding what you are looking for? Try advanced search!</a>
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
</body>
</html>