**ChefAI 🍳  
AI-Powered Recipe Generator & Cookbook Management System**

ChefAI is a full-stack web application that generates personalized recipes using AI based on user preferences and ingredients.  
The platform also allows users to manage cookbooks, save recipes, and explore meal ideas efficiently.

This project was developed as an **academic and learning-based project** with a focus on AI integration, backend logic, and full-stack development concepts.
## 🚀 Features

- 🔐 User Authentication (Register / Login / Logout)
- 🤖 AI-based Recipe Generation
- 🔍 Recipe Search and Filtering
- 📚 Personal Cookbook Management
- 💾 Save AI-generated recipes
- 🎨 Responsive and user-friendly interface


## 🛠 Tech Stack

**Frontend**
- HTML
- CSS
- JavaScript

**Backend**
- PHP

**AI Integration**
- Gemini API (for recipe generation)

**Database**
- MySQL

**Tools & Environment**
- Composer
- XAMPP / WAMP (Local development)
- Git & GitHub

---

## 📂 Project Structure
ChefAI/
├── public/ 
│ ├── index.html
│ ├── login.php
│ ├── register.php
│ ├── generate.php
│ ├── generate1.php
│ ├── search.php
│ ├── logout.php
│ ├── css/
│ ├── js/
│ ├── img/
│ └── fonts/
│
├── app/ # Application logic
│ └── controllers/
│
├── config/ # Configuration files
│ └── db_connect.php
│
├── composer.json
├── composer.lock
├── README.md


## ⚙️ Setup Instructions (Local)

1. **Clone the repository**
    git clone https://github.com/USERNAME/ChefAI-AI-Recipe-Generator.git
**2. Move the project**
   -Place the folder inside:
      -htdocs (XAMPP) or www (WAMP)
**3. Install dependencies**
   -composer install
**4. Configure**
   -Add database credentials in config/db_connect.php
   -Add your Gemini API key securely
**5. Run the project**
    http://localhost/ChefAI/public

