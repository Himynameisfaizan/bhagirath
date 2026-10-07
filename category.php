<?php
include('config/connect.php');

// URL se category slug fetch karein
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';

// Category fetch karein (slug, cate_id, ya id se)
$catQuery = mysqli_query($conn, "SELECT * FROM categories WHERE (slug_url = '$slug' OR cate_id = '$slug' OR id = '$slug') AND (status = 1 OR status = 'Active')");
$category = mysqli_fetch_assoc($catQuery);

// Agar category nahi milti toh products page par redirect kar dein
if (!$category) {
    echo "<script>window.location.href='products.php';</script>";
    exit;
}

$cate_id = $category['cate_id'];
$cat_primary_id = $category['id']; 
$categoryName = $category['categories'];

// 1. SEO Meta Variables Setup
$pageTitle = !empty($category['meta_title']) ? $category['meta_title'] : $categoryName . " | Bhagirath Enterprise";
$meta_description = !empty($category['meta_desc']) ? $category['meta_desc'] : "Explore high quality " . $categoryName . " exported directly from India by Bhagirath Enterprise.";
$meta_keywords = $category['meta_key'] ?? '';

// 2. Smart Schema Markup Logic for Category
$raw_schema = trim($category['schema_markup'] ?? '');
if (!empty($raw_schema)) {
    if (stripos($raw_schema, '<script') === false) {
        $page_schema = "<script type=\"application/ld+json\">\n" . $raw_schema . "\n</script>";
    } else {
        $page_schema = $raw_schema;
    }
} else {
    // Default CollectionPage Schema
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

<!-- CATEGORY PRODUCTS SECTION -->
<section class="category-products-section" style="padding: 50px 0 80px 0; background-color: #fdfdfd;">
    <div class="container">
        <!-- Category Title Header -->
        <div class="text-center mb-5 reveal">
            <h1 style="font-size: 2.2rem; font-weight: 800; color: #222222; text-transform: capitalize;"><?= htmlspecialchars($categoryName); ?></h1>
            <div style="width: 60px; height: 3px; background: var(--primary-green); margin: 15px auto;"></div>
        </div>

        <div class="row g-4 reveal">
            <?php
            // ADVANCED QUERY: Checks cate_id, primary id, and category name just in case
            $sql = "SELECT * FROM products WHERE (pro_cate = '$cate_id' OR pro_cate = '$cat_primary_id' OR pro_cate = '" . mysqli_real_escape_string($conn, $categoryName) . "') AND (status = 1 OR status = '1' OR status = 'Active') ORDER BY id DESC";
            $prodQuery = mysqli_query($conn, $sql);
            
            if ($prodQuery && mysqli_num_rows($prodQuery) > 0) {
                while ($prod = mysqli_fetch_assoc($prodQuery)):
                    $shortDesc = !empty($prod['short_desc']) ? $prod['short_desc'] : 'Premium quality product sourced directly from Indian farms.';
            ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card h-100 d-flex flex-column" style="border: 1px solid #f0f0f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.03); background: #ffffff;">

                        <!-- Product Image -->
                        <a href="product-details.php?slug=<?php echo $prod['slug_url']; ?>" style="text-decoration:none;">
                            <div style="height: 180px; overflow: hidden; background: #fff; padding: 15px; text-align: center;">
                                <img src="admin/assets/img/uploads/<?php echo $prod['pro_img']; ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="<?php echo htmlspecialchars($prod['pro_name']); ?>" onerror="this.src='assets/images/black.png'">
                            </div>
                        </a>

                        <!-- Product Content -->
                        <div style="padding: 15px; display: flex; flex-direction: column; flex-grow: 1; border-top: 1px solid #f9f9f9;">
                            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 8px;">
                                <a href="product-details.php?slug=<?php echo $prod['slug_url']; ?>" style="color: #222; text-decoration: none;">
                                    <?php echo htmlspecialchars($prod['pro_name']); ?>
                                </a>
                            </h3>

                            <p class="text-muted mb-3" style="font-size: 0.85rem; line-height: 1.4; display: -webkit-box; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8em;">
                                <?php echo htmlspecialchars(strip_tags($shortDesc)); ?>
                            </p>

                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f0; padding-top: 12px; margin-top: auto;">
                                <a href="product-details.php?slug=<?php echo $prod['slug_url']; ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600; font-size: 13px;">
                                    View Details <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <a href="contact.php?product=<?php echo urlencode($prod['pro_name']); ?>" style="background-color: var(--accent-orange); color: white; padding: 6px 12px; border-radius: 4px; font-weight: 600; font-size: 12px; text-decoration: none;">
                                    Request Quote
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php 
                endwhile;
            } else {
                // PREMIUM EMPTY STATE UI (Agar us category me koi product nahi hua toh ye dikhega)
                echo '
                <div class="col-12 text-center py-5 my-4">
                    <div style="width: 100px; height: 100px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-box-open" style="font-size: 2.5rem; color: #ccc;"></i>
                    </div>
                    <h3 class="fw-bold" style="color: #333;">No Products Found</h3>
                    <p class="text-muted fs-6 mb-4">We are currently updating our catalog for <strong>' . htmlspecialchars($categoryName) . '</strong>.<br>Please check back later or explore other products.</p>
                    <a href="products.php" class="btn text-white px-4 py-2 rounded-pill fw-bold shadow-sm" style="background: var(--primary-green);"><i class="fa-solid fa-arrow-left me-2"></i> View All Products</a>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Inquiry Form & Footer -->
<?php include ('includes/inquiry-form.php'); ?>
<?php include 'includes/footer.php'; ?>