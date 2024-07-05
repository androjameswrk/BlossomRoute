<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BlossomRoute - Find your roses</title>

    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../cs/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <header class="header" data-header>
        <div style=" background-color: white; border-bottom: 15px solid #623672; /* Add a purple line at the bottom */" class="header-bottom">
            <div class="container">

                <a href="../php/index.php" class="logo"  style="margin-top: 110px; margin-bottom: -95px;">
                    <img src="../img/homelogo.png" alt="Image Failed to Load" width="240" height="240" >
                </a>

                <nav class="navbar" data-navbar>

                    <div class="navbar-top">
                        <a href="#" class="logo">
                            <img src="./images/logo.png" alt="Image Failed to Load">
                        </a>
                        <button class="nav-close-btn" data-nav-close-btn aria-label="Close Menu">
                            <ion-icon name="close-outline"></ion-icon>
                        </button>
                    </div>

                    <div class="navbar-bottom">
                        <ul class="navbar-list home">
                            <li>
                                <a href="index.php" class="navbar-link" data-nav-link>Home</a>
                            </li>
                            <li>
                                <a href="#about" class="navbar-link" data-nav-link>About</a>
                            </li>
                            <li>
                                <a href="#service" class="navbar-link" data-nav-link>Features</a>
                            </li>
                            <li>
                                <a href="#property" class="navbar-link" data-nav-link>Products</a>
                            </li>                   
                            <li>
                                <a href="#contact" class="navbar-link" data-nav-link>Contact</a>
                            </li>

                        </ul>
                    </div>

                </nav>

                <div class="header-bottom-actions home icon-container custom-icon ">
                    <button aria-label="Search">
                        <ion-icon name="search-outline" class="icongap" ></ion-icon>
                        <span></span>
                    </button>

                    <button aria-label="Profile">
                        <ion-icon name="person-outline" class="icongap"></ion-icon>
                        <span></span>
                    </button>
                    
                    <button id="cart-button" aria-label="Cart">
                        <ion-icon name="cart-outline" class="icongap"></ion-icon>
                        <span></span>
                        
                    </button>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                // Find the "Cart" button by its id
                                const cartButton = document.getElementById("cart-button");

                                // Add a click event listener to the "Cart" button
                                cartButton.addEventListener("click", function () {
                                    // Redirect to customer_cart.php when the button is clicked
                                    window.location.href = "customer_cart.php";
                                });
                            });
                        </script>

                    <button cdata-nav-open-btn aria-label="Open Menu">
                        <ion-icon name="menu-outline" class="icongap"></ion-icon>
                        <span></span>
                    </button>
                </div>

            </div>
        </div>

    </header>

    <main>
        <article>

        <!-- - #Hero -->

            

        <!-- - #About -->

           

            <!--- #Product -->

            
            <!-- 
        - #PROPERTY
      -->
      

      <section>

<style>
    /* Container style for the card */
    .card-container {
        background-color: White;
      
        border-radius: 10px; /* Rounded corners for the card */
        padding: 20px;
        margin: 20px; /* Adjust margin for spacing */
    }

    /* Styles for the existing card elements */
    .order-card {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 10px;
    }

    .order-image {
        max-width: 250px; /* Adjust the image size to make it a little bigger */
    }

    .order-content {
        flex: 1;
    }

    .order-title {
        font-size: 18px;
    }

    .order-description {
        font-size: 14px;
    }

    .order-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .add-to-cart-btn,
    .remove-from-cart-btn,
    .heart-btn {
        background-color: #623672;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>
<style>
.card-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: left;
}

.card {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 0 10px #623672; /* Purple glow effect */
  max-width: 300px;
  margin: 10px;
  text-align: center;
  flex: 0 0 calc(33.33% - 20px); /* Set width for three cards in a row with some spacing */
  box-sizing: border-box;
}

.price {
  color: grey;
  font-size: 20px;
  color: #623672;
}

.card button {
  border: none;
  outline: 0;
  padding: 12px;
  color: white;
  background-color: #623672;
  text-align: center;
  cursor: pointer;
  width: 100%;
  font-size: 18px;
}

.card button:hover {
  opacity: 0.7;
}

</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Retrieve wishlist data from localStorage
        const wishlistData = JSON.parse(localStorage.getItem("wishlist")) || [];

        // Select the wishlist container
        const wishlistContainer = document.querySelector(".card-container");

        // Loop through the cart data and create card elements
        wishlistData.forEach((product) => {
            const card = document.createElement("div");
            card.classList.add("order-card");

            // Create card content using the product information
            card.innerHTML = `
                <img src="${product.image}" alt="Product Image" class="order-image"> <!-- Use product.image for the image source -->
                <div class="order-content">
                    <h3 class="order-title">${product.name}</h3>
                    <p class="order-description">${product.description}</p>
                    <br>
                    <div class="order-buttons">
                        <button class="remove-from-cart-btn" data-product-id="${product.id}">Remove from Wishlist</button>
                       
                    </div>
                </div>
            `;

            // Append the card to the cart container
            wishlistContainer.appendChild(card);
        });
    });


      // Add an event listener for removing items from the wishlist
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("remove-from-cart-btn")) {
            const productIndex = event.target.getAttribute("data-product-index");
            removeProductFromCart(productIndex);
        }
    });

    // Function to remove a product from the wishlist
    function removeProductFromCart(productIndex) {
        let wishlistData = JSON.parse(localStorage.getItem("wishlist")) || [];
        wishlistData.splice(productIndex, 1);
        localStorage.setItem("wishlist", JSON.stringify(wishlistData));
        location.reload(); // Refresh the page to update the cart display
    }
</script>

<div class="card-container" id="wishlist-items">
                    <!-- Cart items will be dynamically added here -->
                </div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const wishlistItems = document.getElementById("wishlist-items");
        const addToWishlistButtons = document.querySelectorAll(".heart-btn");

        addToWishlistButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const productId = button.getAttribute("data-product-id");
                const productName = button.parentElement.previousElementSibling.querySelector(".order-title").textContent;
                const productDescription = button.parentElement.previousElementSibling.querySelector(".order-description").textContent;

                // Create a list item for the cart
                const wishlistItem = document.createElement("li");
                wishlistItem.innerHTML = `${productName} - ${productDescription}`;

                wishlistItems.appendChild(wishlistItem);
            });
        });
    });
</script>


</section>

          
        </article>
    </main>





    <!-- 
    - #FOOTER
  -->

    





    <!-- 
    - custom js link
  -->
    <script src="./assets/js/script.js"></script>

    <!-- 
    - ionicon link
  -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>