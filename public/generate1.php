<?php
session_start();

// Check if the user is logged in before displaying the recipe generation form
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ChefAI</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
   
</head>
<body>      
        
    <header class="header-area">

                <!-- Logo Area -->
        <div class="logo-area" >
            <a href="index.html"><img src="logo_chefAI.png" alt="" width="150px" height="100px"></a>
        </div>
        
                <!-- Navbar Area -->
                
        <div class="container">
                            <!-- Menu -->
           <nav>                    
                <!-- Nav Start -->
                <div class="classynav">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="recipe.html">Recipes</a></li>
                    <li><a href="cookbook.html">Cookbooks</a></li>                                                                           
                    <li><a href="#">Meal Planner</a></li>                     
                    <li><a href="profile.php">Profile</a></li>                                    
                </ul>                        
                </div>
                                            
            </nav>                    
        </div>
                  
    </header>
            <!-- ##### Header Area End ##### -->
           
    
    <div class="content">
    <h1>Welcome, <?php echo $_SESSION['username']; ?>! Meet Your Personal AI-Powered Kitchen Assistant</h1>
    <h2> Simply type a recipe idea or some ingredients you have on hand and 
         <br>ChefAI's AI will instantly generate an all-new recipe on demand...</h2>
    <form action="generate1.php" method="POST">
        <label for="ingredients">Enter Ingredients (separated by commas):</label>
        <br>
        <input type="text" id="ingredients" placeholder="Create a recipe... " name="ingredients" value="<?php echo isset($_POST['ingredients']) ? htmlspecialchars($_POST['ingredients']) : ''; ?>" required>
        <br>
        <button id="gen" type="submit">Generate Recipe</button>
        
    </form>
    
    <?php       
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            
            // Capture the ingredients from the form
            $ingredients = $_POST['ingredients'];
            $escaped_ingredients = escapeshellarg($ingredients);  // Sanitize input

            // Paths to Python executable and the script
            $python_path = 'C:\Users\Jinal\AppData\Local\Programs\Python\Python312\python.exe';
            $script_path = 'C:/xampp/cgi-bin/main.py';

            // Command to execute the Python script
            $command = escapeshellcmd("$python_path $script_path $escaped_ingredients 2>&1");

            // Execute and capture the output
            $output = shell_exec($command);

            // Log the output to a debug file
            file_put_contents('debug_output.txt', $output);
            
            

            // Check if output was generated
            if (empty($output)) {
                echo "No output received from the Python script.";
            } else {
                $lines = explode("\n", $output);
        
                $sections = [
                    "Title" => "",
                    "Description" => "",
                    "Ingredients" => [],
                    "Steps" => [],
                    "Serving" => "",
                    "Tips" => []
                ];
        
                $current_section = "";
                foreach ($lines as $line) {
                    $line = trim($line);  // Remove any extra spaces or line breaks
                    $line = ltrim($line, '*- ');  // Remove any leading bullets or hyphens
                    $line = rtrim($line, '**');
                    $line = ltrim($line, '**');
                    $line = rtrim($line, '***');
                    
                     // Removes leading bullets or hyphens
        
                     if (stripos($line, 'Title:') === 0) {
                        $current_section = "Title";
                        $sections[$current_section] = htmlspecialchars(substr($line, 6)); // Sanitize title
                    } elseif (stripos($line, 'Description:') === 0) {
                        $current_section = "Description";
                        $sections[$current_section] = htmlspecialchars(substr($line, 12)); // Sanitize description
                    } elseif (stripos($line, 'Ingredients:') === 0) {
                        $current_section = "Ingredients";
                    } elseif (stripos($line, 'Steps:') === 0) {
                        $current_section = "Steps";
                    } elseif (stripos($line, 'Serving:') === 0 || stripos($line, 'Servings:') === 0) {
                        $current_section = "Serving";
                        $sections[$current_section] = htmlspecialchars(substr($line, 8)); // Sanitize servings
                    } elseif (stripos($line, 'Tips:') === 0) {
                        $current_section = "Tips";
                    } elseif ($current_section && in_array($current_section, ["Ingredients", "Steps", "Tips"])) {
                        $sections[$current_section][] = htmlspecialchars($line); // Sanitize other sections
                    }
                }

                file_put_contents('parsed_output.txt', print_r($sections, true));
                file_put_contents('rendered_html.txt', ob_get_contents());
        
                echo "<div class='recipe-container'>";
                
                // Display the recipe in plain text
                echo "<pre>";
                if (!empty($sections["Title"])) {
                    echo "Title: " . $sections["Title"] . "\n\n";
                }
                if (!empty($sections["Description"])) {
                    echo "Description: " . $sections["Description"] . "\n\n";
                }
                if (!empty($sections["Ingredients"])) {
                    echo "Ingredients:\n";
                    foreach ($sections["Ingredients"] as $ingredient) {
                        echo "- " . $ingredient . "\n";
                    }
                }
                if (!empty($sections["Serving"])) {
                    echo "Serving: " . $sections["Serving"] . "\n\n";
                }
                if (!empty($sections["Steps"])) {
                    echo "Steps:\n";
                    foreach ($sections["Steps"] as $step) {
                        echo $step . "\n";
                    }
                }
                if (!empty($sections["Tips"])) {
                    echo "Tips:\n";
                    foreach ($sections["Tips"] as $tip) {
                        echo "- " . $tip . "\n";
                    }
                }
                echo "</pre>";
                echo "</div>";
            }


            if ($output) {
                // Render the AI-generated recipe (your existing logic)
                
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='recipename' value='" . htmlspecialchars($sections['Title']) . "'>";
                echo "<input type='hidden' name='ingredients' value='" . htmlspecialchars(implode(', ', $sections['Ingredients'])) . "'>";
                echo "<input type='hidden' name='instructions' value='" . htmlspecialchars(implode('. ', $sections['Steps'])) . "'>";
                echo "<input type='hidden' name='tips' value='" . htmlspecialchars(implode('. ', $sections['Tips'])) . "'>";
                echo "<input type='hidden' name='serving' value='" . htmlspecialchars($sections['Serving']) . "'>";
                echo "<button type='submit' name='save_recipe' id='save_recipe'>Save Recipe</button>";
                echo "</form>";
            }
        }
        
        // Handle recipe saving
            if (isset($_POST['save_recipe'])) {
                $user_id = $_SESSION['user_id'];
                $recipename = $_POST['recipename'];
                $ingredients = $_POST['ingredients'];
                $instructions = $_POST['instructions'];
                $tips = $_POST['tips'];
                $serving = $_POST['serving'];
        
                if (!isset($_SESSION['user_id'])) {
                    die('User not logged in. Please log in to save recipes.');
                }
                
                $userid = $_SESSION['user_id']; // Retrieve user ID from the session
                $query = "INSERT INTO ai_gen (user_id, recipename, ingredients, instructions, tips, serving) 
                          VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);

                if ($stmt === false) {
                    die('SQL prepare error: ' . $conn->error); // Debugging help if query fails
                }
                
                // Bind parameters to the query
                $stmt->bind_param('isssss', $userid, $recipename, $ingredients, $instructions, $tips, $serving);
                
                // Execute and check for errors
                if ($stmt->execute()) {
                    echo "Recipe saved successfully!";
                } else {
                    die('SQL execution error: ' . $stmt->error);        
                }       
        }
    ?>
</div>

    <!-- Feature Recipe Section Begin -->
    <section class="feature-recipe">
        <div class="section-title">
            <h5>How does it work?</h5>
        </div>
        <div class="container po-relative">
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="fr-item">
                        
                        <div class="fr-item-text">
                            <h4>1. Enter a prompt</h4>
                            <p>Enter a recipe idea or available ingredients you've always wanted to try 
                                <br>and ChefAI will create a recipe for you. 
                                 
                                
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="fr-item">
                        
                        <div class="fr-item-text">
                            <h4>2. Generate a recipe</h4>
                            <p>An artificial intelligence is used to create the recipe for you. 
                               <br> It only takes a few seconds. 
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="fr-item">
                        
                        <div class="fr-item-text">
                            <h4>3. Enjoy your meal!</h4>
                            <p>You can then cook the recipe and enjoy your meal.                                  
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Feature Recipe Section End -->

    <!-- Footer Section Begin -->
    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="fs-left">
                        <div class="logo">
                            <a href="./index.html">
                                <img src="logo_chefAI.png" alt="">
                            </a>
                        </div>
                        <p>Cooking made effortless, flavors made unforgettable — ChefAI, your partner in the kitchen.</p>
                    </div>
                </div>
                
                <div class="col-lg-6 offset-lg-1">
                    <form action="#" class="subscribe-form">
                        <h3>Subscribe to our newsletter</h3>
                        <input type="email" placeholder="Your e-mail">
                        <button type="submit">Subscribe</button>
                    </form> 
                    <div class="social-links">
                        <a href="#"><i class="fa fa-instagram"></i><span>Instagram</span></a>
                        <a href="#"><i class="fa fa-pinterest"></i><span>Pinterest</span></a>
                        <a href="#"><i class="fa fa-facebook"></i><span>Facebook</span></a>
                        <a href="#"><i class="fa fa-twitter"></i><span>Twitter</span></a>
                        <a href="#"><i class="fa fa-youtube"></i><span>Youtube</span></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="copyright-text">
                        <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | 
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

        <script src="js/jquery-3.3.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.slicknav.js"></script>
        <script src="js/jquery.nice-select.min.js"></script>
        <script src="js/mixitup.min.js"></script>
        <script src="js/main.js"></script>
        
</body>
</html>