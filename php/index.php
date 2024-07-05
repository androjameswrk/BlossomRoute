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
        <div class="header-bottom">
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

                    <button  aria-label="Cart">
                        <ion-icon name="cart-outline" class="icongap"></ion-icon>
                        <span></span>
                    </button>

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

            <section class="hero background" id="home">
                <div class="container" >

                    <div class="hero-content">

                        <p class="hero-subtitle">
                            <ion-icon name="home"></ion-icon>
                            <span>Where Nature Blooms, Our Journey Begins.</span>
                        </p>

                        <h2 class="h1 hero-title">Fresh Flowers <span class="homecolor">Natural & Beautiful Flowers</span></h2>

                        <p class="hero-text">
                            Let us help you discover your perfect rose with our expertise.
                        </p>
                        
                        <a href="../login.php">
                        <button class="btn">Order Now!</button>
                        </a>

                    </div>

                    <figure class="hero-banner">
                        <img src="../img/empty.png" alt="Image Failed to Load" class="w-100 whiteroses ">

                    </figure>

                </div>
            </section>

        <!-- - #About -->

            <section class="about" id="about">
                <div class="container">

                    <figure class="about-banner whiteroseabout">
                        <img src="../img/whiteroses.png" alt="Image Failed to Load">
                       
                    </figure>

                    <div class="about-content">

                        <p class="section-subtitle">About Us</p>

                        <h2 class="h2 section-title">Leading Rose Online Ordering Site.</h2>

                        <p class="about-text">
                        Your one-stop destination for an exceptional online ordering
                        experience for all things floral!
                        </p>

                        <ul class="about-list">

                            <li class="about-item">
                                <div class="about-item-icon">
                                    <ion-icon name="home-outline"></ion-icon>
                                </div>

                                <p class="about-item-text">Exquisite Selection of Roses</p>
                            </li>

                            <li class="about-item">
                                <div class="about-item-icon">
                                    <ion-icon name="leaf-outline"></ion-icon>
                                </div>

                                <p class="about-item-text">Convenient and Secure Ordering</p>
                            </li>

                            <li class="about-item">
                                <div class="about-item-icon">
                                    <ion-icon name="wine-outline"></ion-icon>
                                </div>

                                <p class="about-item-text">Prompt Delivery Services</p>
                            </li>

                            <li class="about-item">
                                <div class="about-item-icon">
                                    <ion-icon name="shield-checkmark-outline"></ion-icon>
                                </div>

                                <p class="about-item-text">Satisfaction Guaranteed</p>
                            </li>

                        </ul>

                        <p class="callout">
                            With our user-friendly interface, seamless navigation, and extensive collection, we
                            strive to make ordering flowers a delightful and stress-free 
                            experience for our valued customers.
                        </p>

                        <a href="#service" class="btn">Our Products</a>

                    </div>

                </div>
            </section>

            <!--- #Product -->

            <section class="service" id="service">
                <div class="container">

                    <p class="section-subtitle">Our Products</p>

                    <h2 class="h2 section-title">Our Main Focus</h2>

                    <ul class="service-list">

                        <li>
                            <div class="service-card">

                                <div class="card-icon">
                                <img src="../img/card.png" alt="Image Failed to Load" width="100" height="100">
                                </div>

                                <h3 class="h3 card-title">
                                    <a href="#">Exquisite Floral Collection</a>
                                </h3>

                                <p class="card-text">
                                    Blossom Route takes pride in curating an exquisite and diverse collection of fresh flowers. 
                                </p>

                                <a href="#" class="card-link">
                                    <span>Order A Rose</span>

                                    <ion-icon name="arrow-forward-outline"></ion-icon>
                                </a>

                            </div>
                        </li>

                        <li>
                            <div class="service-card">

                                <div class="card-icon">
                                    <img src="../img/card.png" alt="Image Failed to Load" width="100" height="100">
                                </div>

                                <h3 class="h3 card-title">
                                    <a href="#">Artistry and Customization</a>
                                </h3>

                                <p class="card-text">
                                    Our user-friendly website and intuitive interface ensure 
                                    a stress-free navigation and smooth checkout process. 
                                </p>

                                <a href="#" class="card-link">
                                    <span>Order A Rose</span>

                                    <ion-icon name="arrow-forward-outline"></ion-icon>
                                </a>

                            </div>
                        </li>

                        <li>
                            <div class="service-card">

                                <div class="card-icon">
                                <img src="../img/card.png" alt="Image Failed to Load" width="100" height="100">
                                </div>

                                <h3 class="h3 card-title">
                                    <a href="#">Seamless Online Ordering</a>
                                </h3>

                                <p class="card-text">
                                    Blossom Route is committed to providing a seamless 
                                    and convenient online ordering experience. 
                                </p>

                                <a href="#" class="card-link">
                                    <span>Order A Rose</span>

                                    <ion-icon name="arrow-forward-outline"></ion-icon>
                                </a>

                            </div>
                        </li>

                    </ul>

                </div>
            </section>

            <!-- 
        - #PROPERTY
      -->

            <section class="property" id="property">
                <div class="container">

                    <p class="section-subtitle">Product Offers</p>

                    <h2 class="h2 section-title">Flower Listings</h2>

                    <ul class="property-list has-scrollbar">

                        <li>
                            <div class="property-card">

                                <figure class="card-banner">

                                    <a href="#">
                                        <img src="../img/yellow.jpg" alt="New Apartment Nice View" class="w-100">
                                    </a>

                                    <div class="card-badge purple">For Sale</div>

                                    <div class="banner-actions">
                                    <style>
                                            .banner-actions-btn:hover,
                                            .banner-actions-btn:focus {
                                                color: #C1AEFF;
                                                background-color: transparent;
                                                border-color: red;
                                            }
                                            </style>
                                        <button class="banner-actions-btn">
                                            <ion-icon name="location"></ion-icon>

                                            <address>Marahan, Marilog District, Davao City</address>
                                        </button>

                                        
                                    </div>

                                </figure>

                                <div class="card-content">

                                    <div class="card-price" style="color:#623672;">
                                        <strong>₱50.00-150.00</strong>/Dozen
                                    </div>

                                    <h3 class="h3 card-title">
                                    <style>
                                        .h3.card-title a:hover,
                                        .h3.card-title a:focus {
                                            color: #C1AEFF;
                                        }
                                    </style>
                                        <a href="#">Sunshine Yellow Rose</a>
                                    </h3>

                                    <p class="card-text">
                                    A variety of rose with bright yellow petals, 
                                    evoking feelings of joy, happiness, and warmth.
                                    </p>

                                    <ul class="card-list">

                                        <li class="card-item">
                                            <strong>S</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Small</span>
                                        </li>

                                        <li class="card-item">
                                            <strong>M</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Medium</span>
                                        </li>

                                        <li class="card-item">
                                            <strong>L</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Large</span>
                                        </li>

                                    </ul>

                                </div>

                           

                            </div>
                        </li>

                        <li>
                            <div class="property-card">

                                <figure class="card-banner">

                                    <a href="#">
                                        <img src="../img/rosered.jpg" alt="Modern Apartments" class="w-100">
                                    </a>

                                    <div class="card-badge purple">For Sale</div>

                                    <div class="banner-actions">

                                        <button class="banner-actions-btn">
                                            <ion-icon name="location"></ion-icon>

                                            <address>Marahan, Marilog District, Davao City</address>
                                        </button>

                                    </div>

                                </figure>

                                <div class="card-content">

                                <div class="card-price" style="color: #623672;">
                                <strong>₱50.00-150.00</strong>/Dozen
                                    </div>


                                    <h3 class="h3 card-title">
                                        <a href="#">Scarlet Red Rose</a>
                                    </h3>

                                    <p class="card-text">
                                    A type of rose distinguished by its rich, deep red hue, symbolizing love, passion, and romance.
                                    </p>

                                   <ul class="card-list">

                                        <li class="card-item">
                                            <strong>S</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Small</span>
                                        </li>

                                        <li class="card-item">
                                            <strong>M</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Medium</span>
                                        </li>

                                        <li class="card-item">
                                            <strong>L</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Large</span>
                                        </li>

                                    </ul>

                                </div>

                            </div>
                        </li>

                        <li>
                            <div class="property-card">

                                <figure class="card-banner">

                                    <a href="#">
                                        <img src="../img/rosepeach.jpg" alt="Comfortable Apartment" class="w-100">
                                    </a>

                                    <div class="card-badge purple">For Sale</div>

                                    <div class="banner-actions">

                                        <button class="banner-actions-btn">
                                            <ion-icon name="location"></ion-icon>

                                            <address>Marahan, Marilog District, Davao City</address>
                                        </button>

                                       

                                    </div>

                                </figure>

                                <div class="card-content">

                                    <div class="card-price" style="color: #623672">
                                        <strong>₱50.00-150.00</strong>/Dozen
                                    </div>

                                    <h3 class="h3 card-title">
                                        <a href="#">Tender Peach Rose</a>
                                    </h3>

                                    <p class="card-text">
                                    A variety of rose characterized by its soft and delicate peach-colored petals.
                                    </p>

                                    <ul class="card-list">

                                        <li class="card-item">
                                            <strong>S</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Small</span>
                                        </li>

                                        <li class="card-item">
                                            <strong>M</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Medium</span>
                                        </li>

                                        <li class="card-item">
                                            <strong>L</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Large</span>
                                        </li>

                                    </ul>

                                </div>


                            </div>
                        </li>

                        <li>
                            <div class="property-card">

                                <figure class="card-banner">

                                    <a href="#">
                                        <img src="../img/rosepink.jpg" alt="Luxury villa in Rego Park"
                                            class="w-100">
                                    </a>

                                    <div class="card-badge purple">For Sale</div>

                                    <div class="banner-actions">

                                        <button class="banner-actions-btn">
                                            <ion-icon name="location"></ion-icon>

                                            <address>Marahan, Marilog District, Davao City</address>
                                        </button>

                                        

                                    </div>

                                </figure>

                                <div class="card-content">

                                    <div class="card-price" style="color: #623672">
                                        <strong>₱50.00-150.00</strong>/Dozen
                                    </div>

                                    <h3 class="h3 card-title">
                                        <a href="#">Blushing Pink Rose</a>
                                    </h3>

                                    <p class="card-text">
                                    A type of rose displaying a delicate shade of pink that evokes a sense of innocence, grace, and sweetness. 
                                    </p>

                                    <ul class="card-list">

                                        <li class="card-item">
                                            <strong>S</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Small</span>
                                        </li>

                                        <li class="card-item">
                                            <strong>M</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Medium</span>
                                        </li>

                                        <li class="card-item">
                                            <strong>L</strong>

                                            <ion-icon name="rose-outline"></ion-icon>

                                            <span>Large</span>
                                        </li>

                                    </ul>
                                </div>

                       

                            </div>
                        </li>

                    </ul>

                </div>
            </section>


            <section class="cta">
                
                <div class="container">
                <style>
  /* Apply the green background color to the cta-card div */
  .cta-card {
    background-color: #623672;
  }
</style>
                    <div class="cta-card">
                        <div class="card-content">
                            
                            <h2 class="h2 card-title">Looking for a fresh rose?</h2>

                            <p class="card-text">Embrace Nature's Beauty with Our Fresh Roses!</p>
                        </div>

                        <button class="btn cta-btn">
                            <span>Find Best Roses</span>

                            <ion-icon name="arrow-forward-outline"></ion-icon>
                        </button>
                    </div>

                </div>
            </section>

        </article>
    </main>





    <!-- 
    - #FOOTER
  -->

    <footer class="footer">
    <style>
  /* Apply the green color to the footer-list items on hover */
  .footer-list li:hover a {
    color: #623672;
  }
  .contact-link:hover,
  .contact-link:hover ion-icon,
  .contact-link:hover span {
    color: #623672;
  }
  
</style>


        <div class="footer-top" id ="contact">
            <div class="container">

                <div class="footer-brand">

                    <a href="#" class="logo">
                        <img src="../img/footer.png" alt="Hellohomes logo" width="315" height="80" >
                    </a>

                    <p class="section-text">
                    Indulge in the Floral Splendor - BlossomRoute, your gateway to enchanting roses.
                    </p>

                    <ul class="contact-list">

                        <li>
                            <a href="#" class="contact-link">
                                <ion-icon name="location-outline"></ion-icon>

                                <address>Marahan, Marilog District, Davao City, 8000 Davao del Sur</address>
                            </a>
                        </li>

                        <li>
                            <a href="tel:0935 106 2763" class="contact-link">
                                <ion-icon name="call-outline"></ion-icon>

                                <span>09204111793</span>
                            </a>
                        </li>

                        <li>
                            <a href="mailto:hellohomes@gmail.com" class="contact-link">
                                <ion-icon name="mail-outline"></ion-icon>

                                <span>blossomroute@gmail.com</span>
                            </a>
                        </li>

                    </ul>

                    <ul class="social-list">

                        <li>
                            <a href="#" class="social-link">
                                <ion-icon name="logo-facebook"></ion-icon>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="social-link">
                                <ion-icon name="logo-twitter"></ion-icon>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="social-link">
                                <ion-icon name="logo-linkedin"></ion-icon>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="social-link">
                                <ion-icon name="logo-youtube"></ion-icon>
                            </a>
                        </li>

                    </ul>

                </div>

                <div class="footer-link-box">

                    <ul class="footer-list">

                        <li>
                            <p class="footer-list-title">Company</p>
                        </li>

                        <li>
                            <a href="#" class="footer-link">About</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Blog</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">All Products</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Locations Map</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">FAQ</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Contact us</a>
                        </li>

                    </ul>

                    <ul class="footer-list">

                        <li>
                            <p class="footer-list-title">Services</p>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Order tracking</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Wish List</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Login</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">My account</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Terms & Conditions</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Promotional Offers</a>
                        </li>

                    </ul>

                    <ul class="footer-list">

                        <li>
                            <p class="footer-list-title">Customer Care</p>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Login</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">My account</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Wish List</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Order tracking</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">FAQ</a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">Contact us</a>
                        </li>

                    </ul>

                </div>

            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">

            </div>
        </div>

    </footer>





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