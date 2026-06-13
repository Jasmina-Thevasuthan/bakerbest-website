<footer class="footer-section">
    <div class="footer-content section-content">

        <div class="footer-logo-title">
            <h2 class="footer-title">🍰 Baker Best</h2>
        </div>

        <div class="footer-links">
            <a href="index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="menu.php">Menu</a>
            <a href="order.php">Order Online</a>
            <a href="gallery.php">Gallery</a>
            <a href="contact.php">Contact Us</a>
        </div>

        <div class="footer-social">
            <div class="footer-icons">
                <a href="https://www.instagram.com/" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="https://www.facebook.com/" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.tiktok.com/" class="social-link"><i class="fab fa-tiktok"></i></a>
            </div>

            <p>No.12, Main Street, Colombo, <br> Sri Lanka</p>
            <p>+94 77 123 4567</p>
            <p>bakerbest@gmail.com</p>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> Baker Best Bakery. All Rights Reserved.
    </div>

    <div id="backToTop" aria-label="Back to top">↑</div>
</footer>
<style>
    .footer-section {
        background: #3b141c;
        color: white;
        padding: 20px 10px;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-title {
        font-family: "Brush Script MT", cursive;
        font-size: 2rem;
        margin: 5px 0;
    }

    /* Footer Links */
    .footer-links {
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
    }

    .footer-links a {
        color: white;
        text-decoration: none;
        transition: 0.3s ease;
    }

    .footer-links a:hover {
        color: #f3961c;
    }

    /* Social + Contact */
    .footer-social {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-width: 250px;
    }

    .footer-icons {
        display: flex;
        gap: 15px;
    }

    .social-link {
        font-size: 1.5rem;
        transition: 0.3s ease;
        color: white;
    }

    .social-link:hover {
        color: #f3961c;
        transform: scale(1.2);
    }

    .footer-bottom {
        text-align: center;
        padding: 10px 0;
        font-size: 0.9rem;
        border-top: 1px solid rgba(255, 255, 255, 0.3);
        margin-top: 15px;
    }

    /* Back to top */
    #backToTop {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #a15959;
        color: white;
        padding: 12px 15px;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* ================================
   RESPONSIVE FOOTER
================================= */
    @media (max-width: 900px) {
        .footer-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .footer-links {
            justify-content: center;
            gap: 15px;
        }

        .footer-icons {
            justify-content: center;
        }
    }
</style>