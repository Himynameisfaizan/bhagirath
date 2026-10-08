<?php
include('config/connect.php');

// URL se category slug fetch karein
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';

// Category fetch karein
$catQuery = mysqli_query($conn, "SELECT * FROM categories WHERE (slug_url = '$slug' OR cate_id = '$slug' OR id = '$slug') AND (status = 1 OR status = 'Active')");
$category = mysqli_fetch_assoc($catQuery);

// Agar category nahi milti toh products page par redirect kar dein
if (!$category) {
    echo "<script>window.location.href='products.php';</script>";
    exit;
}

$cate_id = $category['cate_id'] ?? '';
$cat_primary_id = $category['id'] ?? ''; 
$categoryName = $category['categories'] ?? '';

// Fetch Site Phone for Call Button
$contactQuery = mysqli_query($conn, "SELECT phone FROM contacts LIMIT 1");
$contactInfo = mysqli_fetch_assoc($contactQuery);
$sitePhone = !empty($contactInfo['phone']) ? $contactInfo['phone'] : '+91-8448211202';

// 1. SEO Meta Variables
$pageTitle = !empty($category['meta_title']) ? $category['meta_title'] : $categoryName . " | Bhagirath Enterprise";
$meta_description = !empty($category['meta_desc']) ? $category['meta_desc'] : "Explore high quality " . $categoryName . " exported directly from India.";
$meta_keywords = $category['meta_key'] ?? '';

// 2. Schema Markup
$raw_schema = trim($category['schema_markup'] ?? '');
if (!empty($raw_schema)) {
    if (stripos($raw_schema, '<script') === false) {
        $page_schema = "<script type=\"application/ld+json\">\n" . $raw_schema . "\n</script>";
    } else {
        $page_schema = $raw_schema;
    }
} else {
    $page_schema = '
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "' . htmlspecialchars($categoryName, ENT_QUOTES) . '",
        "description": "' . htmlspecialchars(strip_tags($meta_description), ENT_QUOTES) . '"
    }
    </script>';
}

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<!-- CATEGORY SHOP SECTION -->
<section class="category-shop-section" style="padding: 60px 0; background-color: #fdfdfd;">
    <div class="container">
        
        <!-- Category Title Header -->
        <div class="text-center mb-5">
            <h1 style="font-size: 2.5rem; font-weight: 800; color: #222222; text-transform: capitalize;"><?= htmlspecialchars($categoryName); ?></h1>
            <div style="width: 60px; height: 3px; background: var(--primary-green, #2b5e2c); margin: 15px auto;"></div>
        </div>

        <div class="row g-4">
            
            <!-- LEFT COLUMN: PRODUCTS GRID -->
            <div class="col-lg-9">
                <div class="row g-4">
                    <?php
                    $sql = "SELECT * FROM products WHERE (pro_cate = '$cate_id' OR pro_cate = '$cat_primary_id' OR pro_cate = '" . mysqli_real_escape_string($conn, $categoryName) . "') AND (status = 1 OR status = '1' OR status = 'Active') ORDER BY id DESC";
                    $prodQuery = mysqli_query($conn, $sql);
                    
                    if ($prodQuery && mysqli_num_rows($prodQuery) > 0) {
                        while ($prod = mysqli_fetch_assoc($prodQuery)):
                            $shortDesc = !empty($prod['short_desc']) ? $prod['short_desc'] : 'Premium quality product sourced directly from Indian farms.';
                            $imgSrc = !empty($prod['pro_img']) ? 'admin/assets/img/uploads/' . $prod['pro_img'] : 'assets/images/placeholder.png';
                            $prodSlug = !empty($prod['slug_url']) ? $prod['slug_url'] : $prod['id'];
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="product-card h-100 d-flex flex-column" style="border: 1px solid #eaeaea; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03); background: #ffffff; transition: transform 0.3s ease, box-shadow 0.3s ease;">

                                <!-- Product Image -->
                                <a href="product-details.php?slug=<?php echo htmlspecialchars($prodSlug); ?>" style="text-decoration:none;">
                                    <div style="height: 220px; overflow: hidden; background: #f8f9fa; padding: 20px; text-align: center; position: relative;">
                                        <?php if(isset($prod['trending']) && $prod['trending'] == 1): ?>
                                            <span class="badge bg-danger position-absolute top-0 start-0 m-2 shadow-sm"><i class="fa-solid fa-fire me-1"></i> Hot</span>
                                        <?php endif; ?>
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="<?php echo htmlspecialchars($prod['pro_name']); ?>" onerror="this.src='assets/images/black.png'">
                                    </div>
                                </a>

                                <!-- Product Content -->
                                <div style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1;">
                                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 8px;">
                                        <a href="product-details.php?slug=<?php echo htmlspecialchars($prodSlug); ?>" style="color: #222; text-decoration: none;">
                                            <?php echo htmlspecialchars($prod['pro_name']); ?>
                                        </a>
                                    </h3>

                                    <p class="text-muted mb-4" style="font-size: 0.88rem; line-height: 1.5; display: -webkit-box; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3em;">
                                        <?php echo htmlspecialchars(strip_tags($shortDesc)); ?>
                                    </p>

                                    <!-- Action Buttons -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f0; padding-top: 15px; margin-top: auto;">
                                        <a href="product-details.php?slug=<?php echo htmlspecialchars($prodSlug); ?>" style="color: var(--primary-green, #2b5e2c); text-decoration: none; font-weight: 700; font-size: 13px;">
                                            View Details <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                        <a href="contact.php?product=<?php echo urlencode($prod['pro_name']); ?>" style="background-color: #222; color: white; padding: 8px 15px; border-radius: 6px; font-weight: 600; font-size: 12px; text-decoration: none;">
                                            Quote
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    } else {
                        // PREMIUM EMPTY STATE UI
                        echo '
                        <div class="col-12 text-center py-5 my-4 bg-white border rounded shadow-sm">
                            <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fa-solid fa-box-open" style="font-size: 2rem; color: #ccc;"></i>
                            </div>
                            <h4 class="fw-bold" style="color: #333;">No Products Found</h4>
                            <p class="text-muted fs-6 mb-4">We are currently updating our catalog for <strong>' . htmlspecialchars($categoryName) . '</strong>.</p>
                            <a href="products.php" class="btn text-white px-4 py-2 rounded-pill fw-bold shadow-sm" style="background: var(--primary-green, #2b5e2c);"><i class="fa-solid fa-arrow-left me-2"></i> View All Products</a>
                        </div>';
                    }
                    ?>
                </div>
            </div>

            <!-- RIGHT COLUMN: SIDEBAR -->
            <div class="col-lg-3">
                <div class="sidebar-widgets position-sticky" style="top: 20px;">
                    
                    <!-- 1. Search Widget -->
                    <div class="widget-box bg-white p-4 rounded shadow-sm border mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 1.1rem; color: #222;">Search</h5>
                        <form action="products.php" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search product..." required style="font-size: 0.9rem; border-color: #ddd;">
                            <button type="submit" class="btn text-white px-3" style="background: var(--primary-green, #2b5e2c);"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <!-- 2. Categories Widget -->
                    <div class="widget-box bg-white p-4 rounded shadow-sm border mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 1.1rem; color: #222;">Categories</h5>
                        <ul class="list-unstyled mb-0 category-list">
                            <?php
                            $sideCatQuery = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1 ORDER BY id DESC");
                            while($sideCat = mysqli_fetch_assoc($sideCatQuery)):
                                $isActive = ($sideCat['cate_id'] == $cate_id || $sideCat['slug_url'] == $slug) ? 'fw-bold active-cat' : 'text-muted';
                            ?>
                            <li class="mb-2 pb-2 border-bottom" style="border-color: #f8f9fa !important;">
                                <a href="category.php?slug=<?= htmlspecialchars($sideCat['slug_url']) ?>" class="text-decoration-none d-flex justify-content-between align-items-center category-link <?= $isActive ?>" style="font-size: 0.95rem;">
                                    <span><i class="fa-solid fa-angle-right me-2" style="font-size: 0.75rem;"></i> <?= htmlspecialchars($sideCat['categories']) ?></span>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- 3. Request Quote / Need Help Banner -->
                    <div class="widget-box rounded shadow-sm text-center p-4 text-white" style="background: linear-gradient(135deg, var(--primary-green, #2b5e2c) 0%, #1a3f1b 100%);">
                        <div class="icon-wrap mb-3">
                            <i class="fa-solid fa-headset fs-1 text-white opacity-75"></i>
                        </div>
                        <h4 class="fw-bold mb-2 text-white">Bulk Order?</h4>
                        <p class="small mb-4 opacity-75" style="line-height: 1.5;">Contact us today to get the best wholesale pricing for your business.</p>
                        <a href="contact.php" class="btn bg-white w-100 fw-bold shadow-sm mb-3" style="color: var(--primary-green, #2b5e2c); border-radius: 6px;">Request Quote</a>
                        <div>
                            <a href="tel:<?= htmlspecialchars($sitePhone) ?>" class="text-white text-decoration-none small fw-bold"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($sitePhone) ?></a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Custom CSS for Card and Sidebar Hover Effects -->
<style>
    .product-card:hover { transform: translateY(-5px) !important; box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important; border-color: #ddd !important; }
    .category-link { color: #555; transition: all 0.2s; }
    .category-link:hover { color: var(--primary-green, #2b5e2c) !important; padding-left: 5px; }
    .active-cat { color: var(--primary-green, #2b5e2c) !important; padding-left: 5px; }
    .category-list li:last-child { border-bottom: none !important; padding-bottom: 0 !important; margin-bottom: 0 !important; }
</style>

<!-- Inquiry Form & Footer -->
<?php include ('includes/inquiry-form.php'); ?>
<?php include 'includes/footer.php'; ?>