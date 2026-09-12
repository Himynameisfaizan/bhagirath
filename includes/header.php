<?php
// Current page ka naam nikalne ke liye taaki active link highlight ho sake
$current_page = basename($_SERVER['PHP_SELF']);

// --- DYNAMIC SEO HANDLING ---
if (!isset($pageTitle)) { 
    $pageTitle = "Bhagirath Enterprise | Premium Agricultural Exports"; 
}
if (!isset($meta_description)) { 
    $meta_description = "Bhagirath Enterprise is a trusted global exporter of premium quality dry fruits, whole spices, and authentic Indian agricultural products."; 
}
if (!isset($meta_keywords)) { 
    $meta_keywords = "Bhagirath Enterprise, agricultural exports, Indian spices, dry fruits exporter, wholesale spices"; 
}

$header_logo = "assets/images/logo/logo.png"; 
$favicon = "assets/images/logo/favicon.png"; 

// Topbar default variables
$t_phone = "+91-8448211202";
$t_email = "bhagirathenterprise7@gmail.com";
$t_fb = "#"; $t_linkedin = "#"; $t_wp = "#";

if (isset($conn)) {
    // 1. Fetch Header Logo
    $logo_query = mysqli_query($conn, "SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    if ($logo_query && mysqli_num_rows($logo_query) > 0) {
        $logo_data = mysqli_fetch_assoc($logo_query);
        $header_logo = 'admin/uploads/' . $logo_data['logo_path'];
    }

    // 2. Fetch Favicon
    $fav_query = mysqli_query($conn, "SELECT logo_path FROM logos WHERE location = 'favicon' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    if ($fav_query && mysqli_num_rows($fav_query) > 0) {
        $fav_data = mysqli_fetch_assoc($fav_query);
        $favicon = 'admin/uploads/' . $fav_data['logo_path'];
    }

    // 3. Fetch Contacts for Topbar
    $contact_query = mysqli_query($conn, "SELECT * FROM contacts ORDER BY id DESC LIMIT 1");
    if ($contact_query && mysqli_num_rows($contact_query) > 0) {
        $c_info = mysqli_fetch_assoc($contact_query);
        $t_phone = !empty($c_info['phone']) ? $c_info['phone'] : $t_phone;
        $t_email = !empty($c_info['contact_email']) ? $c_info['contact_email'] : (!empty($c_info['email']) ? $c_info['email'] : $t_email);
        $t_fb = !empty($c_info['facebook']) ? $c_info['facebook'] : $t_fb;
        $t_linkedin = !empty($c_info['linkdin']) ? $c_info['linkdin'] : $t_linkedin;
        $t_wp = !empty($c_info['wp_number']) ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $c_info['wp_number']) : $t_wp;
    }

    // 4. Fetch Categories for Dropdown
    $cats_dropdown_query = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1 ORDER BY categories ASC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="eX_sXjETkL7O-emXnSDL6-LirHz1VsbiMIZMyFqgvIw" />
    <title><?= htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords); ?>">
    
    <link rel="icon" href="<?= htmlspecialchars($favicon); ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
     <link rel="stylesheet" href="assets/style/include.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="assets/style/about.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="assets/style/blog.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="assets/style/contact.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="assets/style/gallery.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="assets/style/product.css?v=<?php echo time() ?>">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3JGVQX47GN"></script>
 <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PJCRLJB3');
    
    </script>
  
  
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-3JGVQX47GN"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-3JGVQX47GN');
    </script>
    
</head>
<body>



<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PJCRLJB3"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>


<!-- Topbar Section (Desktop Only) -->
<div class="topbar d-none d-lg-block shadow-sm">
    <div class="container">
        <div class="row align-items-center py-2">
            <div class="col-md-7 topbar-left">
                <a href="mailto:<?= htmlspecialchars($t_email); ?>"><i class="bi bi-envelope-fill me-1"></i> <?= htmlspecialchars($t_email); ?></a>
                <span class="mx-2 text-white-50">|</span>
                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $t_phone); ?>"><i class="bi bi-telephone-fill me-1"></i> <?= htmlspecialchars($t_phone); ?></a>
            </div>
            <div class="col-md-5 text-end topbar-right">
                <span class="me-2 text-white-50 small">Follow Us:</span>
                <?php if($t_fb != '#') echo "<a href='$t_fb' target='_blank'><i class='bi bi-facebook'></i></a>"; ?>
                <?php if($t_linkedin != '#') echo "<a href='$t_linkedin' target='_blank'><i class='bi bi-linkedin'></i></a>"; ?>
                <?php if($t_wp != '#') echo "<a href='$t_wp' target='_blank'><i class='bi bi-whatsapp'></i></a>"; ?>
            </div>
        </div>
    </div>
</div>

<!-- Header / Navbar Section -->
<nav class="navbar navbar-expand-lg custom-navbar sticky-top">
    <div class="container">
        
        <!-- Navbar Brand with Mobile Fix (max-width prevents pushing button to next line) -->
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="max-width: 70vw;">
            <img src="<?= htmlspecialchars($header_logo); ?>" alt="Bhagirath Enterprise Logo" class="logo-animate img-fluid" style="max-height: 65px; object-fit: contain;" onerror="this.src='assets/images/logo/logo.png'">
        </a>
        
        <button class="navbar-toggler shadow-none border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Nav Links -->
        <div class="collapse navbar-collapse justify-content-end" id="mainNav">
            <ul class="navbar-nav align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a>
                </li>
                
                <!-- Dynamic Products Dropdown (Hover Enabled in CSS) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= ($current_page == 'products.php' || $current_page == 'product-details.php') ? 'active' : ''; ?>" href="products.php" id="productsDropdown" data-bs-toggle="dropdown" aria-expanded="false" onclick="window.location.href='products.php';">
                        Products
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg" aria-labelledby="productsDropdown">
                        <?php 
                        if (isset($cats_dropdown_query) && mysqli_num_rows($cats_dropdown_query) > 0) {
                            while($cat = mysqli_fetch_assoc($cats_dropdown_query)) {
                                $isActiveCat = (isset($_GET['category']) && $_GET['category'] == $cat['slug_url']) ? 'active-dropdown-item' : '';
                        ?>
                            <li><a class="dropdown-item <?= $isActiveCat; ?>" href="products.php?category=<?= $cat['slug_url']; ?>"><?= htmlspecialchars($cat['categories']); ?></a></li>
                        <?php 
                            }
                        } 
                        ?>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'blog.php' || $current_page == 'blog-details.php') ? 'active' : ''; ?>" href="blog.php">Blog</a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
                </li>
                <li class="nav-item ms-lg-4 mt-3 mt-lg-0 mb-3 mb-lg-0">
                    <a class="btn btn-quote" href="contact.php">Get a Quote</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>