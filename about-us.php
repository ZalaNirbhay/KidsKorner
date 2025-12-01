<?php
// about-us.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
?>

<style>
    .about-hero {
        background-color: #f9fafb;
        padding: 4rem 0;
        text-align: center;
    }

    .about-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .about-hero p {
        font-size: 1.1rem;
        color: #6b7280;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .about-section {
        padding: 4rem 0;
    }

    .about-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .about-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .about-content h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }

    .about-content p {
        font-size: 1rem;
        color: #4b5563;
        margin-bottom: 1.5rem;
        line-height: 1.7;
    }

    .about-image {
        width: 100%;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 3rem;
    }

    .value-card {
        background: #ffffff;
        padding: 2rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        text-align: center;
        transition: transform 0.3s;
    }

    .value-card:hover {
        transform: translateY(-5px);
    }

    .value-icon {
        font-size: 2.5rem;
        color: #b8735c;
        margin-bottom: 1rem;
    }

    .value-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .value-text {
        color: #6b7280;
        font-size: 0.95rem;
    }

    @media (max-width: 768px) {
        .about-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        .values-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="about-hero">
    <div class="about-container">
        <h1>Our Story</h1>
        <p>Welcome to KidsKorner, where we believe in bringing joy, comfort, and style to every little one's life. Our journey began with a simple mission: to provide high-quality, adorable essentials for babies and toddlers.</p>
    </div>
</div>

<section class="about-section">
    <div class="about-container">
        <div class="about-grid">
            <div class="about-image-wrapper">
                <img src="asetes/images/baby-2.png" alt="Happy Baby" class="about-image">
            </div>
            <div class="about-content">
                <h2>Crafted with Love</h2>
                <p>At KidsKorner, we understand that your child deserves the best. That's why we meticulously select fabrics that are soft on delicate skin and durable enough for everyday adventures. From cozy onesies to stylish outfits, every piece is chosen with care.</p>
                <p>We are more than just a store; we are a community of parents who want the best for their children. We are committed to sustainability and ethical practices, ensuring that our products are safe for your little ones and the planet.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section" style="background-color: #f9fafb;">
    <div class="about-container">
        <div style="text-align: center; max-width: 700px; margin: 0 auto;">
            <h2>Why Choose Us?</h2>
            <p style="color: #6b7280;">We strive to offer an exceptional shopping experience with products you can trust.</p>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon"><i class="ri-heart-line"></i></div>
                <div class="value-title">Quality First</div>
                <div class="value-text">Premium materials that ensure comfort and durability for your child.</div>
            </div>
            <div class="value-card">
                <div class="value-icon"><i class="ri-shield-star-line"></i></div>
                <div class="value-title">Safe & Secure</div>
                <div class="value-text">Products tested for safety, giving you peace of mind with every purchase.</div>
            </div>
            <div class="value-card">
                <div class="value-icon"><i class="ri-customer-service-2-line"></i></div>
                <div class="value-title">Customer Love</div>
                <div class="value-text">Dedicated support team ready to help you with any questions or concerns.</div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
