<?php
// Current page ka naam nikalne ke liye taaki active link highlight ho sake
$current_page = basename($_SERVER['PHP_SELF']);

// --- DYNAMIC SEO HANDLING ---
// Agar kisi page par variables set nahi hain, toh ye default values show hongi
if (!isset($pageTitle)) { 
    $pageTitle = "EURASIASTONEINDIA | Premium Agricultural Exports"; 
}
if (!isset($meta_description)) { 
    $meta_description = "EURASIASTONEINDIA is a trusted global exporter of premium quality rice, chilli, turmeric, and authentic Indian spices."; 
}
if (!isset($meta_keywords)) { 
    $meta_keywords = "EURASIASTONEINDIA, agricultural exports, Indian spices, rice exporter, turmeric"; 
}

$header_logo = "assets/images/logo/logo.png"; 
$favicon = "assets/images/logo/favicon.png"; 

if (isset($conn)) {
    // 1. Fetch Header Logo (Jiska location 'header' aur status active ho)
    $logo_query = mysqli_query($conn, "SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    if ($logo_query && mysqli_num_rows($logo_query) > 0) {
        $logo_data = mysqli_fetch_assoc($logo_query);
        $header_logo = 'admin/uploads/' . $logo_data['logo_path'];
    }

    // 2. Fetch Favicon (Jiska location 'favicon' aur status active ho)
    $fav_query = mysqli_query($conn, "SELECT logo_path FROM logos WHERE location = 'favicon' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    if ($fav_query && mysqli_num_rows($fav_query) > 0) {
        $fav_data = mysqli_fetch_assoc($fav_query);
        $favicon = 'uploads/' . $fav_data['logo_path'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords); ?>">
    
    <link rel="icon" href="<?= htmlspecialchars($favicon); ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/style/include.css">
    <link rel="stylesheet" href="assets/style/style.css">
    <link rel="stylesheet" href="assets/style/about.css">
    <link rel="stylesheet" href="assets/style/blog.css">
    <link rel="stylesheet" href="assets/style/contact.css">
    <link rel="stylesheet" href="assets/style/gallery.css">
    <link rel="stylesheet" href="assets/style/product.css">
    
</head>
<body>

<!-- Header Section -->
<nav class="navbar navbar-expand-lg fixed-top custom-navbar">
    <div class="container">
        
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="<?= htmlspecialchars($header_logo); ?>" alt="EURASIASTONEINDIA Logo" class="logo-animate" onerror="this.src='assets/images/logo/logo.png'">
            <span class="company-name ms-2">EURASIASTONEINDIA</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Nav Links -->
        <div class="collapse navbar-collapse justify-content-end" id="mainNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'products.php' || $current_page == 'product-details.php') ? 'active' : ''; ?>" href="products.php">Products</a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'services.php' || $current_page == 'service-details.php') ? 'active' : ''; ?>" href="services.php">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'blog.php' || $current_page == 'blog-details.php') ? 'active' : ''; ?>" href="blog.php">Blog</a>
                </li> -->
                <!-- <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'gallery.php') ? 'active' : ''; ?>" href="gallery.php">Gallery</a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
                </li>
                <li class="nav-item ms-lg-4 mt-3 mt-lg-0">
                    <a class="btn btn-quote" href="contact.php">Get a Quote</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div style="margin-top: 85px;"></div>