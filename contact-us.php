<?php
// contact-us.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('mailer.php');

$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($name && $email && $subject && $message) {
        $body = "<h3>New Contact Inquiry</h3>
                 <p><strong>Name:</strong> $name</p>
                 <p><strong>Email:</strong> $email</p>
                 <p><strong>Subject:</strong> $subject</p>
                 <p><strong>Message:</strong><br>$message</p>";
        
        // Send email to admin (using the sender email for now as per mailer config)
        $result = sendEmail('zalanirbhay21@gmail.com', "Contact Us: $subject", $body);
        
        if ($result === true) {
            $message_sent = true;
        } else {
            $error_message = "Failed to send message. Please try again later.";
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}

ob_start();
?>

<style>
    .contact-hero {
        background-color: #f9fafb;
        padding: 4rem 0;
        text-align: center;
    }

    .contact-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .contact-hero p {
        font-size: 1.1rem;
        color: #6b7280;
        max-width: 600px;
        margin: 0 auto;
    }

    .contact-section {
        padding: 4rem 0;
    }

    .contact-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .contact-wrapper {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 3rem;
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .contact-info {
        background: #b8735c;
        padding: 3rem;
        color: #ffffff;
    }

    .contact-info h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item i {
        font-size: 1.25rem;
        margin-top: 0.2rem;
    }

    .info-item p {
        font-size: 0.95rem;
        line-height: 1.6;
        opacity: 0.9;
    }

    .contact-form-wrapper {
        padding: 3rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-input, .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        transition: border-color 0.3s;
        outline: none;
    }

    .form-input:focus, .form-textarea:focus {
        border-color: #b8735c;
        box-shadow: 0 0 0 3px rgba(184, 115, 92, 0.1);
    }

    .btn-submit {
        background: #b8735c;
        color: #ffffff;
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        width: 100%;
    }

    .btn-submit:hover {
        background: #9a5b45;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-error {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    @media (max-width: 768px) {
        .contact-wrapper {
            grid-template-columns: 1fr;
        }
        .contact-info {
            padding: 2rem;
        }
        .contact-form-wrapper {
            padding: 2rem;
        }
    }
</style>

<div class="contact-hero">
    <div class="contact-container">
        <h1>Get in Touch</h1>
        <p>Have questions or feedback? We'd love to hear from you. Fill out the form below and we'll get back to you as soon as possible.</p>
    </div>
</div>

<section class="contact-section">
    <div class="contact-container">
        <div class="contact-wrapper">
            <div class="contact-info">
                <h3>Contact Information</h3>
                <div class="info-item">
                    <i class="ri-map-pin-line"></i>
                    <p>123 KidsKorner Lane,<br>Happy Valley, CA 90210</p>
                </div>
                <div class="info-item">
                    <i class="ri-mail-line"></i>
                    <p>support@kidskorner.com</p>
                </div>
                <div class="info-item">
                    <i class="ri-phone-line"></i>
                    <p>+1 (555) 123-4567</p>
                </div>
                <div class="info-item">
                    <i class="ri-time-line"></i>
                    <p>Mon - Fri: 9:00 AM - 6:00 PM</p>
                </div>
            </div>

            <div class="contact-form-wrapper">
                <?php if ($message_sent): ?>
                    <div class="alert alert-success">
                        <i class="ri-checkbox-circle-line"></i> Thank you! Your message has been sent successfully.
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="ri-error-warning-line"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" id="name" name="name" class="form-input" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" required placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-input" required placeholder="Inquiry about...">
                    </div>
                    <div class="form-group">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" name="message" class="form-textarea" rows="5" required placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include 'layout.php';
?>