<header>
    <nav class="navbar section-content" role="navigation" aria-label="Main navigation">
        <a href="index.php" class="nav-logo">
            <img src="images/logo.png" alt="Baker Best Logo">
            <h2 class="logo-text">🍰 Baker Best</h2>
        </a>

        <button id="menu-open-button" class="fas fa-bars" aria-label="Open menu"></button>

        <div class="nav-wrapper">
            <button id="menu-close-button" class="fas fa-times" aria-label="Close menu"></button>

            <ul class="nav-menu" id="nav-menu" aria-hidden="true">
                <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="about.php" class="nav-link">About Us</a></li>
                <li class="nav-item"><a href="menu.php" class="nav-link">Menu</a></li>
                <li class="nav-item"><a href="order.php" class="nav-link">Online Order</a></li>
                <li class="nav-item"><a href="gallery.php" class="nav-link">Gallery</a></li>
                <li class="nav-item"><a href="contact.php" class="nav-link">Contact Us</a></li>
            </ul>
        </div>

    </nav>

    <div id="mobile-overlay" class="mobile-overlay" aria-hidden="true"></div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

</header>
<style>
    /* Desktop nav */
    header {
        position: fixed;
        width: 100%;
        z-index: 999;
        background: #3b141c;
        top: 0;
        left: 0;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
    }

    .nav-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .nav-logo img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
    }

    .logo-text {
        color: #fff;
        font-size: 1.4rem;
        font-weight: 600;
        margin: 0;
    }

    .nav-wrapper {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 10px;
        margin: 0;
        padding: 0;
    }

    .nav-link {
        padding: 10px 18px;
        color: #fff;
        font-weight: 700;
        border-radius: 30px;
        text-decoration: none;
        transition: 0.25s ease;
    }

    .nav-link:hover,
    .nav-link.active {
        background: #f3961c;
        color: #3b141c;
    }

    #menu-open-button,
    #menu-close-button,
    .mobile-overlay {
        display: none;
    }

    /* MOBILE NAV  */

    @media screen and (max-width: 900px) {

        #menu-open-button {
            display: block;
            color: #fff;
            font-size: 1.5rem;
        }

        .nav-wrapper {
            display: block;
            position: fixed;
            top: 0;
            left: -300px;
            width: 250px;
            height: 100%;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
            transition: left 0.3s ease-in-out;
            z-index: 1000;
        }

        #menu-close-button {
            display: block;
            position: absolute;
            top: 20px;
            right: 20px;
            color: #3b141c;
            font-size: 1.5rem;
        }

        .nav-menu {
            flex-direction: column;
            align-items: flex-start;
            padding: 60px 20px 20px 20px;
            width: 100%;
        }

        .nav-item {
            width: 100%;
            margin-bottom: 5px;
        }

        .nav-link {
            color: #3b141c;
            width: 100%;
            display: block;
            padding: 10px 15px;
            text-align: left;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
        }

        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }


        body.menu-active .nav-wrapper {
            left: 0;
        }

        body.menu-active .mobile-overlay {
            opacity: 1;
            visibility: visible;
        }

        body.menu-active {
            overflow: hidden;
        }

        body.menu-active #nav-menu {
            aria-hidden: "false";
        }
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const menuOpen = document.querySelector("#menu-open-button");
        const menuClose = document.querySelector("#menu-close-button");
        const mobileOverlay = document.querySelector("#mobile-overlay");
        const navLinks = document.querySelectorAll(".nav-menu a");
        const body = document.body;
        const navMenu = document.querySelector("#nav-menu");

        const openMenu = () => {
            body.classList.add("menu-active");
            navMenu.setAttribute('aria-hidden', 'false');
            menuOpen.setAttribute('aria-expanded', 'true');
        };

        const closeMenu = () => {
            body.classList.remove("menu-active");
            navMenu.setAttribute('aria-hidden', 'true');
            menuOpen.setAttribute('aria-expanded', 'false');
        };

        // --- Event Listeners ---

        menuOpen.addEventListener("click", openMenu);

        menuClose.addEventListener("click", closeMenu);

        mobileOverlay.addEventListener("click", closeMenu);

        navLinks.forEach(link => {
            link.addEventListener("click", closeMenu);
        });

        const currentPage = window.location.pathname.split("/").pop() || "index.php";
        navLinks.forEach(link => {
            if (link.getAttribute("href") === currentPage) {
                link.classList.add("active");
            }
        });
    });
</script>