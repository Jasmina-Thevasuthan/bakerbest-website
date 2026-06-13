<?php
session_start();
require_once "admin/db_connection.php";

$msg = "";

if (isset($_GET['success'])) {
    $msg = "Thank you for contacting us! Your message has been sent to the admin. The admin will reply soon. Keep in touch with BakerBest.";
}

//  form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message_content = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message_content)) {
        $check = $conn->prepare("SELECT id FROM contact_messages WHERE email=? AND subject=? AND message=? LIMIT 1");
        $check->bind_param("sss", $email, $subject, $message_content);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $msg = "You have already sent this message.";
        } else {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $subject, $message_content);

            if ($stmt->execute()) {
                header("Location: contact.php?success=1");
                exit();
            } else {
                $msg = "Sorry! There was an error sending your message. Please try again.";
            }

            $stmt->close();
        }
        $check->close();
    } else {
        $msg = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Baker Best</title>

    <link rel="stylesheet" href="assets/css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="images/logo.png" type="image/png">
</head>

<body>

    <?php include 'header.php'; ?>

    <?php if ($msg): ?>
        <div id="popupModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Message Status</h2>
                <p><?= htmlspecialchars($msg) ?></p>
                <button id="okBtn">OK</button>
            </div>
        </div>
    <?php endif; ?>

    <section class="contact-banner">
        <h1>Contact Us</h1>
        <p>We're here to serve you fresh & delicious bakery items.</p>
    </section>

    <section class="contact-section">
        <div class="contact-wrapper">

            <div class="contact-form">
                <h2>Send Us a Message</h2>

                <form action="" method="POST" id="contactForm">
                    <div class="input-row">
                        <input type="text" name="name" placeholder="Your Name" required>
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>

                    <input type="text" name="subject" placeholder="Subject" required>
                    <textarea name="message" placeholder="Your Message" required></textarea>

                    <button type="submit" class="btn-submit">
                        Send Message <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>

            <div class="contact-info">
                <h2>Get In Touch</h2>

                <div class="info-box">
                    <i class="fa-solid fa-location-dot"></i>
                    <p>No.12, Main Street, Colombo, Sri Lanka</p>
                </div>

                <div class="info-box">
                    <i class="fa-solid fa-phone"></i>
                    <p>+94 77 123 4567</p>
                </div>

                <div class="info-box">
                    <i class="fa-solid fa-envelope"></i>
                    <p>bakerbest@gmail.com</p>
                </div>

                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="https://www.facebook.com/"><i class="fa-brands fa-facebook"></i></a>
                    <a href="https://www.instagram.com/"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://x.com/"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>

        </div>
    </section>

    <section class="map-section">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63356.86086483642!2d79.815005!3d6.927078!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2591a52b72f0f%3A0x65d874d2c0445c30!2sColombo!5e0!3m2!1sen!2slk!4v1700000000000"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>

    <?php include 'footer.php'; ?>

    <script>
        // Modal popup
        const modal = document.getElementById("popupModal");
        const closeBtn = document.querySelector(".close-btn");
        const okBtn = document.getElementById("okBtn");

        if (modal) {
            closeBtn.onclick = () => modal.style.display = "none";
            okBtn.onclick = () => modal.style.display = "none";

            window.onclick = (e) => {
                if (e.target === modal) modal.style.display = "none";
            };
        }

        document.getElementById("contactForm").addEventListener("submit", function () {
            document.querySelector(".btn-submit").disabled = true;
        });
    </script>

</body>

</html>