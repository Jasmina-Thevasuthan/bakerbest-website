<?php
require_once 'admin/db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="Baker Best - Freshly baked cakes, pastries, breads and artisan bakery items. Order online or visit our shop.">
  <meta name="keywords" content="bakery, cakes, pastries, bread, order online bakery Sri Lanka">
  <meta name="author" content="Baker Best Bakery">

  <title>The Baker Best</title>
  <link rel="stylesheet" href="assets/css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="icon" href="images/logo.png" type="image/png">

</head>

<body>

  <?php include 'header.php'; ?>

  <main>
    <!-- HERO SECTION -->
    <section class="hero-section">
      <div class="section-content">
        <div class="hero-details">
          <h2 class="title">Every Bite Brings Comfort, Joy, And Sweetness!</h2>
          <p class="description">Welcome to BakerBest! Where every treat is baked with love and warmth, bringing
            sweetness to your day.</p>
          <div class="buttons">
            <a href="order.php" class="button order-now">Order Now</a>
            <a href="contact.php" class="button contact-us">Contact Us</a>

          </div>
        </div>
        <div class="hero-image-wrapper">
          <img src="images/bg5.jpg" alt="Freshly baked artisan cakes" class="hero-image" width="500px" height="400px"
            loading="lazy">
        </div>
      </div>
    </section>

    <!-- WHY CHOOSE US SECTION -->
    <section class="why-section fade-up">
      <h2 class="section-title">⭐ Why Choose Us?</h2>
      <div class="section-content why-content">
        <div class="why-card">
          <img src="images/fresh.jpg" alt="Fresh Ingredients" class="why-icon" loading="lazy">
          <h3>Fresh Ingredients</h3>
          <p>We bake everything fresh daily using premium-quality ingredients.</p>
        </div>
        <div class="why-card">
          <img src="images/chef.jpg" alt="Expert Bakers" class="why-icon" loading="lazy">
          <h3>Expert Bakers</h3>
          <p>Our experienced bakers craft every sweet with love and perfection.</p>
        </div>
        <div class="why-card">
          <img src="images/delivery.jpg" alt="Fast Delivery" class="why-icon" loading="lazy">
          <h3>Fast Delivery</h3>
          <p>Freshly baked goodness delivered right to your doorstep.</p>
        </div>
      </div>
    </section>

    <!-- SIGNATURE ITEMS SECTION -->
    <section class="signature-section slide-up">
      <h2 class="section-title">⭐ Our Signature Items</h2>
      <div class="section-content signature-content">
        <div class="signature-card">
          <img src="images/menu/1764510811_cheesecake.jpg" alt="Chocolate Dream Cake" class="sig-img" loading="lazy">
          <h3>Cheese Cake</h3>
          <p class="price">Rs. 200</p>
          <p>Thickest layer being a blend of soft, fresh & cheesey our all-time bestseller.</p>
        </div>
        <div class="signature-card">
          <img src="images/menu/1765006575_1764573580_coffee-and-bun.jpg" alt="Butter Croissant" class="sig-img"
            loading="lazy">
          <h3>Bun with Cheese</h3>
          <p class="price">Rs. 650</p>
          <p>Soft, fluffy bun filled with rich, melted cheese warm, savory, and perfect for a satisfying snack anytime.
          </p>
        </div>
        <div class="signature-card">
          <img src="images/menu/1765007011_dates cake.jpg" alt="Velvet Cupcakes" class="sig-img" loading="lazy">
          <h3>Dates Cake</h3>
          <p class="price">Rs. 250</p>
          <p>Moist and flavorful dates cake, sweetened naturally with dates, and topped with a soft,
            rich frosting.</p>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <script src="assets/js/home.js"></script>
</body>

</html>