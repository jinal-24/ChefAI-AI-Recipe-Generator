document.addEventListener("DOMContentLoaded", () => {
    const createCookbookBtn = document.getElementById("createCookbookBtn");
    const createCookbookModal = document.getElementById("createCookbookModal");
    const closeModal = document.querySelector(".close");
    const saveCookbookBtn = document.getElementById("saveCookbookBtn");
    const cookbookContainer = document.getElementById("cookbookContainer");
    const cookbookNameInput = document.getElementById("cookbookName");
    const emptyMessage = document.querySelector(".empty-message");

    let userId = null; // Declare the userId globally

    // Fetch the logged-in user's ID dynamically
    fetch('get_user.php')
    .then(response => response.json())
    .then(data => {
        console.log(data); // Log the response
        if (data.error) {
            alert(data.error); // Handle user not logged in
            console.error("Error:", data.error);
        } else {
            userId = data.user_id;  // Store the user ID in the variable
            loadCookbooks();  // Once the user ID is fetched, load cookbooks
        }
    })
    .catch(error => {
        console.error('Error fetching user ID:', error);
    });

    // Open modal to create a cookbook
    createCookbookBtn.addEventListener("click", () => {
        createCookbookModal.style.display = "flex"; // Use flex for centering
    });

    // Close modal
    closeModal.addEventListener("click", () => {
        createCookbookModal.style.display = "none";
    });

    // Save a new cookbook
    saveCookbookBtn.addEventListener("click", () => {
        const cookbookName = cookbookNameInput.value.trim();
        if (!cookbookName) {
            alert("Cookbook name cannot be empty!");
            return;
        }

        // Ensure userId is fetched before allowing the user to save a cookbook
        if (!userId) {
            alert("User is not logged in.");
            return;
        }

        // Send the cookbook details to the backend (including the user_id)
        fetch("saveCookbook.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ name: cookbookName, user_id: userId }), // Send dynamic user_id
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to save cookbook, status: " + response.status);
                }
                return response.json();
            })
            .then((cookbook) => {
                if (cookbook.error) {
                    alert(cookbook.error);
                    console.error("Error saving cookbook:", cookbook.error);
                    return;
                }

                // If cookbook saved successfully, add it to the page
                createCookbookCard(cookbook);
                cookbookNameInput.value = "";
                createCookbookModal.style.display = "none";
                emptyMessage.style.display = "none";
                displaySuccessMessage("Cookbook saved successfully!");
            })
            .catch((error) => {
                alert('Failed to save cookbook. Check the console for details.');
                console.error("Error saving cookbook:", error);
            });
    });

    // Load cookbooks for the logged-in user
    function loadCookbooks() {

        console.log("Loading cookbooks...");
        if (!userId) {
            alert('User is not logged in.');
            return;
        }

        fetch("getCookbooks.php?user_id=" + userId) // Send user_id dynamically
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to load cookbooks, status: " + response.status);
                }
                return response.json();
            })
            .then((data) => {
                if (data.error) {
                    alert(data.error);
                    console.error("Error loading cookbooks:", data.error);
                    return;
                }

                cookbookContainer.innerHTML = "";

                if (data.length > 0) {
                    emptyMessage.style.display = "none"; // Hide the empty message if cookbooks exist
                    data.forEach((cookbook) => createCookbookCard(cookbook));
                } else {
                    emptyMessage.style.display = "block"; // Show the empty message if no cookbooks
                    console.log('No cookbooks found, displaying empty message.');
                }
            })
            .catch((error) => {
                alert('Failed to load cookbooks. Check the console for details.');
                console.error("Error loading cookbooks:", error);
            });
    }

    // Create a cookbook card
    function createCookbookCard(cookbook) {
        const card = document.createElement("div");
        card.className = "cookbook-card";
        card.innerHTML = `
          <h3>${cookbook.name}</h3>
          <a href="cookbookDetail.html?id=${cookbook.id}" class="view-cookbook">View Cookbook</a>
        `;
        cookbookContainer.appendChild(card);
    }

    // Display success message
    function displaySuccessMessage(message) {
        if (emptyMessage.style.display !== "none") emptyMessage.style.display = "none";
        const successMessage = document.createElement("div");
        successMessage.className = "success-message";
        successMessage.textContent = message;
        document.body.appendChild(successMessage);

        setTimeout(() => {
            successMessage.remove();
        }, 3000); // Remove after 3 seconds
    }
});
