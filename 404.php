<?php
include 'config/connect.php';
$pageTitle = "404 - Page Not Found | Bhagirath Enterprise";
// Apna header path yahan set karein
include 'includes/header.php'; 
?>

<style>
/* ==========================================
   BHAGIRATH ENTERPRISE PREMIUM 404 DESIGN
   ========================================== */
:root {
    --theme-maroon: #851a2a;
    /* Bhagirath main brand color */
    --theme-yellow: #f2ba1d;
    /* Submit inquiry button color */
    --theme-dark: #1a1a1a;
}

.bhagirath-error-wrap {
    background-color: var(--theme-dark);
    /* Yahan aap spices ya farm ka dark overlay image laga sakte hain jaise hero section me hai */
    background-image: linear-gradient(rgba(26, 26, 26, 0.85), rgba(26, 26, 26, 0.95)), url('https://via.placeholder.com/1920x1080/2a1619/111111?text=Spices+Background');
    background-size: cover;
    background-position: center;
    color: #ffffff;
    padding: 120px 20px;
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    position: relative;
}

.bhagirath-error-content {
    position: relative;
    z-index: 2;
    max-width: 650px;
    margin: 0 auto;
}

.bhagirath-404-text {
    font-size: clamp(6rem, 15vw, 11rem);
    font-weight: 900;
    line-height: 1;
    margin-bottom: 0;
    color: transparent;
    font-family: 'Poppins', sans-serif;
    /* Maroon outline matching the theme */
    -webkit-text-stroke: 3px var(--theme-maroon);
    text-shadow: 0 15px 35px rgba(133, 26, 42, 0.25);
    letter-spacing: 5px;
}

.bhagirath-error-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-top: -15px;
    margin-bottom: 15px;
    color: #ffffff;
    font-family: 'Poppins', sans-serif;
}

/* Maroon underline matching "Our Premium Categories" section */
.bhagirath-error-title::after {
    content: "";
    display: block;
    width: 60px;
    height: 3px;
    background-color: var(--theme-maroon);
    margin: 15px auto 0;
}

.bhagirath-error-desc {
    color: #cccccc;
    font-size: 1.1rem;
    margin-top: 25px;
    margin-bottom: 40px;
    line-height: 1.7;
}

/* Button style matching "Explore Products" & "Get a Quote" */
.btn-bhagirath-maroon {
    background-color: var(--theme-maroon);
    color: #ffffff !important;
    padding: 14px 38px;
    border-radius: 50px;
    /* Pill shape from the design */
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: 2px solid var(--theme-maroon);
}

.btn-bhagirath-maroon:hover {
    background-color: transparent;
    color: var(--theme-maroon) !important;
    box-shadow: 0 5px 20px rgba(133, 26, 42, 0.3);
}

/* Smooth Fade Up Animation */
@keyframes fadeInUpBhagirath {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-up {
    animation: fadeInUpBhagirath 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>

<section class="bhagirath-error-wrap">
    <div class="bhagirath-error-content animate-fade-up">
        <h1 class="bhagirath-404-text">404</h1>
        <h2 class="bhagirath-error-title">Page Not Found</h2>
        <p class="bhagirath-error-desc">We couldn't find the page you're looking for. The link might be broken, or the
            page has been moved. Explore our premium export quality products from the homepage.</p>

        <a href="index.php" class="btn-bhagirath-maroon">
            <i class="fa-solid fa-house"></i> Back to Homepage
        </a>
    </div>
</section>

<?php 
// Apna footer path yahan set karein
include 'includes/footer.php'; 
?>