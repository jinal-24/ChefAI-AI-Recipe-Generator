<?php
session_start();

// Check if the user is logged in before displaying the recipe generation form
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php"); // Redirect to login page if not logged in
    exit;
}

include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChefAI</title>
    <link rel="stylesheet" href="style_gen.css">
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
            <li><a href="D:\Porject_ChefAI\ChefAI\login_register.php">Login / Register</a>                                    
        </ul>                        
        </div>
                          
    </nav>                    
    </div>
    </header>
<!-- ##### Header Area End ##### --> 
    
    <div class="input-container">
    <div class="content">
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!Meet Your Personal AI-Powered Kitchen Assistant</h1>
    <h2> Simply type a recipe idea or some ingredients you have on hand and DishGen's AI will instantly generate an all-new recipe on demand...</h2>
    <form action="generate.php" method="POST">
        <label for="ingredients">Enter Ingredients (separated by commas):</label>
        <br>
        <input type="text" id="ingredients" placeholder="Create a recipe... " name="ingredients" value="<?php echo isset($_POST['ingredients']) ? htmlspecialchars($_POST['ingredients']) : ''; ?>" required>
        <br>
        <button type="submit">Generate Recipe</button>
        
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
                    $line = rtrim($line, '***');
                    
                     // Removes leading bullets or hyphens
        
                    if (stripos($line, 'Title:') === 0) {
                        $current_section = "Title";
                        $sections[$current_section] = substr($line, 6);
                    } elseif (stripos($line, 'Description:') === 0) {
                        $current_section = "Description";
                        $sections[$current_section] = substr($line, 12);
                    } elseif (stripos($line, 'Ingredients:') === 0) {
                        $current_section = "Ingredients";
                    } elseif (stripos($line, 'Steps:') === 0) {
                        $current_section = "Steps";
                    } elseif (stripos($line, 'Serving:') === 0 || stripos($line, 'Servings:') === 0) {
                        $current_section = "Serving";
                        $sections[$current_section] = substr($line, 8);
                    } elseif (stripos($line, 'Tips:') === 0) {
                        $current_section = "Tips";
                    } elseif ($current_section && in_array($current_section, ["Ingredients", "Steps", "Tips"])) {
                        $sections[$current_section][] = $line;
                    }
                }
                file_put_contents('parsed_output.txt', print_r($sections, true));
                file_put_contents('rendered_html.txt', ob_get_contents());
        
                echo "<div class='recipe-container'>";
                if (!empty($sections["Title"])) {
                    echo "<h1 class='title'>";
                    echo "<p>". htmlspecialchars($sections["Title"]) . "</p>";
                }

                // Render Description
                if (!empty($sections["Description"])) {
                    echo "<h2>Description:</h2>"; 
                    echo "<p>". htmlspecialchars($sections["Description"]) . "</p>";
                }

                // Render Ingredients
                if (!empty($sections["Ingredients"])) {
                    echo "<h2>Ingredients:</h2><ul>";
                    foreach ($sections["Ingredients"] as $ingredient) echo "<li>" . htmlspecialchars($ingredient) . "</li>";
                    echo "</ul>";
                }

                if (!empty($sections["Serving"])) {
                    echo "<h2>Serving:</h2>";
                    echo "<p>" . htmlspecialchars($sections["Serving"]) . "</p>";
                }
                                        
                
                // Render Steps
                if (!empty($sections["Steps"])) {
                    echo "<h2>Steps:</h2><ol>";
                    foreach ($sections["Steps"] as $step) {
                        $step = preg_replace('/^\d+\.\s*/', '', $step); // Removes manual numbering like "1. Step"
                        echo "<li>" . htmlspecialchars($step) . "</li>";
                    }
                    echo "</ol>";
                }


                  // Render Tips
                if (!empty($sections["Tips"])) {
                    echo "<h2>Tips</h2><ul>";
                    foreach ($sections["Tips"] as $tip) {
                        echo "<li>" . htmlspecialchars($tip) . "</li>";
                    }
                    echo "</ul>";
                }

                echo "</div>";
            }
        }
    ?>
</div>
</div>
    
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



