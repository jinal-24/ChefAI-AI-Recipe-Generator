<?php
session_start(); // Start the session to manage user login state

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

include 'db_connect.php'; // Ensure you have the DB connection setup here

// Fetch saved recipes for the logged-in user
$user_id = $_SESSION['user_id'];

// SQL query to fetch saved recipes for the user (including the tips)
$query = "SELECT * FROM ai_gen WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id); // Bind the user ID parameter
$stmt->execute();
$result = $stmt->get_result();

// Check if recipes are found for the logged-in user
$saved_recipes = [];
if ($result->num_rows > 0) {
    $saved_recipes = $result->fetch_all(MYSQLI_ASSOC); // Fetch recipes as an associative array
} else {
    $error_message = "No saved recipes found for this user.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="icon" href="favicon.png" type="image/x-icon">

    <style>
        body{
    background-color: white;
    font-family: "PT Sans", sans-serif;
    width: 100%;
    -webkit-font-smoothing: antialiased;
	min-height: 100vh;
    overflow-x: hidden;
}

        .container {
            text-align: center;
            max-width: 90%;
            margin: 50px auto;            
            padding: 10px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333333;
            margin-bottom: 20px;
        }

        

    .header-area {
    position: sticky;
    top: 0; /* Sticks to the top of the viewport */
    z-index: 1000;
    width: 100%; 
    height: 40px;
    display: flex;
    background-color: white; 
    
    justify-items: space-between;
    padding: 35px 20px ;
    align-items: center;
  }
  
.logo-area{
    float: left;
    padding: 10px 15px;
    padding-bottom: 10px;
    margin-bottom:25px;
    
  }
  
.classynav {
    position: absolute;
      right: 70px;
      top: 50%;
      transform: translateY(-50%);
  }

  
.classynav li {
    color: #885202;
      margin: 5px;
      padding: 10px;
      font-size: 1.2rem;
  }
  
.classynav a {
    text-decoration: none;
    transition: color 0.3s ease;
    color: #885202; /* Adjust the color for links */
  }

.classynav a:hover{
    border-bottom: 2px solid #cc9c54;
    text-decoration: none;
    color: #885202;
  }

.classynav a:focus{
    color: #885202; /* Same as normal text color */
    text-decoration: none;
}
.classynav a:active{
    color: #885202;   
}
        .saved-recipes {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
            margin-top: 30px;
        }

        .recipe-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            height: auto;
            padding: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: height 0.3s ease;
        }

        .recipe-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .recipe-card .recipe-header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .recipe-card .recipe-body {
            font-size: 14px;
            height: 120px;  /* Set initial height for the truncated content */
            overflow: hidden; /* Hide the overflow */
            margin-bottom: 10px;
            transition: height 0.3s ease; /* Transition for smooth expansion */
        }

        .recipe-card.open .recipe-body {
            height: auto; /* Expand to show full content when clicked */
            overflow: visible; /* Allow full content to be displayed */
            max-height: none;
            padding-bottom: 10px;
        }

        .recipe-card .recipe-footer {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
            color: #999;
        }

        .read-more {
            color: #007bff;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }

        .ingredients ul {
            padding-left: 20px;
            list-style-type: disc;
        }

        .tips {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
            padding: 10px;
            background-color: #f4f4f4;
            border-radius: 5px;
        }

        

        /* Add responsive adjustments */
        @media (max-width: 768px) {
            .recipe-card {
                width: 100%;
                margin: 10px 0;
            }
        }

        /* Make the navigation items inline */
.classynav ul {
    list-style: none;
    display: flex;
    justify-content: flex-start;
    gap: 20px; /* Space between menu items */
    margin-right: 20px;
    margin-bottom: 20px;
    padding-bottom: 10px;
}

/* Style for the profile link */
.profile-link {
    position: relative; /* Required for absolute positioning of Sign Out */
}

/* Hide the Sign Out button by default */
.sign-out-form {
    display: none; /* Hide the form initially */
    position: absolute; /* Position it absolutely relative to the profile link */
    top: 100%; /* Place it below the username */
    left: 0;
    
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
    
}

/* Show the Sign Out button when hovering over the profile link */
.profile-link:hover .sign-out-form {
    display: block; /* Show the Sign Out button on hover */
}

/* Style the Sign Out button */
.sign-out-btn {
    background-color: #cc9c54;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 16px;
    cursor: pointer;
    
}

.sign-out-btn:hover {
    background-color: #b17f3c;
}

.profile-link a {
    color: #885202;
    
    text-decoration: none;
    cursor: pointer;
}

.profile-link a:hover {
    text-decoration: none;
}


    </style>
</head>
<body>
<header class="header-area">
    <!-- Logo Area -->
    <div class="logo-area">
        <a href="index.html"><img src="logo_chefAI.png" alt="" width="150px" height="100px"></a>
    </div>

    <!-- Navbar Area -->
    <div class="container1">
        <!-- Menu -->
        <nav>
            <!-- Nav Start -->
            <div class="classynav">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="recipe.html">Recipes</a></li>
                    <li><a href="cookbook.html">Cookbooks</a></li>
                    <li><a href="#">Meal Planner</a></li>
                    <!-- Profile / Sign In/Register -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="profile-link">
                            <a href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                            <form method="POST" action="logout.php" class="sign-out-form">
                                <button type="submit" class="sign-out-btn">Sign Out</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="login.php">Sign In</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</header><!-- ##### Header Area End ##### -->

    <div class="container">
        

        <h2>Here are your saved recipes:</h2>
        <?php if (!empty($saved_recipes)): ?>
            <div class="saved-recipes">
                <?php foreach ($saved_recipes as $recipe): ?>
                    <div class="recipe-card">
                        <div class="recipe-header">
                            <h3><?php echo htmlspecialchars($recipe['recipename']); ?></h3>
                        </div>
                        <div class="recipe-body">
                            <p><strong>Ingredients:</strong></p>
                            <div class="ingredients">
                                <ul>
                                    <?php 
                                    $ingredients = explode(",", $recipe['ingredients']);
                                    foreach ($ingredients as $ingredient): ?>
                                        <li><?php echo htmlspecialchars(trim($ingredient)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- Full Recipe content: Instructions -->
                            <p><strong>Instructions:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
                        </div>
                        <div class="recipe-footer">
                            <p><strong>Serving:</strong> <?php echo htmlspecialchars($recipe['serving']); ?></p>
                        </div>
                        
                        <!-- Display Tips -->
                        <?php if (!empty($recipe['tips'])): ?>
                            <div class="tips">
                                <strong>Tips:</strong> <?php echo nl2br(htmlspecialchars($recipe['tips'])); ?>
                            </div>
                        <?php endif; ?>

                        <div class="read-more">Read More</div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p><?php echo isset($error_message) ? $error_message : "You have no saved recipes yet."; ?></p>
        <?php endif; ?>
    </div>
    <script>
        // JavaScript to toggle full recipe visibility on click
        document.querySelectorAll('.recipe-card').forEach(function(card) {
            card.addEventListener('click', function() {
                this.classList.toggle('open');
                const readMoreButton = this.querySelector('.read-more');
                if (this.classList.contains('open')) {
                    readMoreButton.innerHTML = 'Read Less'; // Change button text to "Read Less" when expanded
                } else {
                    readMoreButton.innerHTML = 'Read More'; // Reset button text when collapsed
                }
            });
        });
    </script>
</body>
</html>
