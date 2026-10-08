<?php
include('config/connect.php');

// Check karein ki URL mein slug hai ya id
if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $product_slug = mysqli_real_escape_string($conn, $_GET['slug']);
    $productQuery = mysqli_query($conn, "SELECT * FROM products WHERE (slug_url = '$product_slug' OR id = '$product_slug') AND status = 1");
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $productQuery = mysqli_query($conn, "SELECT * FROM products WHERE id = '$product_id' AND status = 1");
} else {
    $productQuery = false;
}

$product = ($productQuery) ? mysqli_fetch_assoc($productQuery) : null;

// Agar product nahi mila, toh products page par redirect kar do
if (!$product) {
    echo "<script>window.location.href='products.php';</script>";
    exit;
}

// Global variable for product ID
$product_id = $product['id'];

// Fetch Global Contact Info for Call Buttons
$contactQuery = mysqli_query($conn, "SELECT phone FROM contacts LIMIT 1");
$contactInfo = mysqli_fetch_assoc($contactQuery);
$sitePhone = !empty($contactInfo['phone']) ? $contactInfo['phone'] : '+91-8448211202';

// -------------------------------------------------------------
// REVIEW SUBMISSION LOGIC (With PRG Pattern)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $reviewer_name = mysqli_real_escape_string($conn, trim($_POST['reviewer_name']));
    $reviewer_email = mysqli_real_escape_string($conn, trim($_POST['reviewer_email']));
    $rating = intval($_POST['rating']);
    $review_text = mysqli_real_escape_string($conn, trim($_POST['review_text']));

    if (!empty($reviewer_name) && !empty($review_text) && $rating > 0 && $rating <= 5) {
        $insertReview = "INSERT INTO product_reviews (product_id, reviewer_name, reviewer_email, rating, review_text, status, created_at) 
                         VALUES ('$product_id', '$reviewer_name', '$reviewer_email', '$rating', '$review_text', 1, NOW())";
        
        if(mysqli_query($conn, $insertReview)) {
            $redirect_url = isset($_GET['slug']) ? "?slug=" . urlencode($_GET['slug']) : "?id=" . $product_id;
            header("Location: product-details.php" . $redirect_url . "&review=success");
            exit;
        }
    } else {
        $review_msg = "<div class='alert alert-danger mt-3'>Please fill all required fields and select a rating.</div>";
    }
}

// Check for success message from URL
if (isset($_GET['review']) && $_GET['review'] == 'success') {
    $review_msg = "<div class='alert alert-success mt-3'>Thank you! Your review has been added successfully.</div>";
}
// -------------------------------------------------------------

// Fetch Active Reviews for this product
$reviewsQuery = mysqli_query($conn, "SELECT * FROM product_reviews WHERE product_id = '$product_id' AND status = 1 ORDER BY created_at DESC");
$total_reviews = mysqli_num_rows($reviewsQuery);

// Calculate Average Rating
$avgRatingQuery = mysqli_query($conn, "SELECT AVG(rating) as avg_rating FROM product_reviews WHERE product_id = '$product_id' AND status = 1");
$avgRatingRow = mysqli_fetch_assoc($avgRatingQuery);
$avg_rating = $avgRatingRow['avg_rating'] ? number_format($avgRatingRow['avg_rating'], 1) : 0;

// Dynamic Page Title
$pageTitle = !empty($product['meta_title']) ? $product['meta_title'] : $product['pro_name'];
$meta_description = !empty($product['meta_desc']) ? $product['meta_desc'] : strip_tags(substr($product['description'], 0, 160));
$meta_keywords = $product['meta_key'];

// -------------------------------------------------------------
// SMART SCHEMA LOGIC 
// -------------------------------------------------------------
$raw_schema = trim($product['schema_markup'] ?? '');
if (!empty($raw_schema)) {
    if (stripos($raw_schema, '<script') === false) {
        $page_schema = "<script type=\"application/ld+json\">\n" . $raw_schema . "\n</script>";
    } else {
        $page_schema = $raw_schema;
    }
} else {
    $page_schema = "";
}

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<!-- Add Swiper CSS for Smooth Slider -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

<section class="pd-section" style="padding: 40px 0; background: #fdfdfd;">
    <div class="container-ng">

        <div class="row bg-white p-3 p-md-4 rounded shadow-sm border border-light">
            
            <div class="col-lg-5 mb-5 mb-lg-0 reveal" style="height: auto !important;">
                <div class="pd-image-gallery position-relative" style="height: auto !important;">
                    
                    <!-- Top Left Badges -->
                    <div class="position-absolute top-0 start-0 p-2" style="z-index: 10;">
                        <span class="badge bg-success mb-2 d-block shadow-sm" style="font-size: 13px;"><i class="fa-solid fa-leaf me-1"></i> 100% Natural</span>
                        <?php if($product['trending'] == 1): ?>
                            <span class="badge bg-danger shadow-sm" style="font-size: 13px;"><i class="fa-solid fa-fire me-1"></i> Hot Selling</span>
                        <?php endif; ?>
                    </div>

                    <div class="pd-main-img border rounded bg-white d-flex align-items-center justify-content-center p-3 mb-3" style="height: auto !important; min-height: 250px; overflow: hidden; width: 100%;">
                        <img id="mainImage" src="admin/assets/img/uploads/<?php echo $product['pro_img']; ?>" alt="<?php echo htmlspecialchars($product['pro_name']); ?>" class="img-fluid" style="max-height: 400px; width: 100%; height: auto; object-fit: contain; display: block;">
                    </div>

                    <!-- Gallery Thumbnails -->
                    <?php
                    $galleryQuery = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id = '$product_id'");
                    if ($galleryQuery && mysqli_num_rows($galleryQuery) > 0):
                    ?>
                    <div class="pd-thumbnails mt-3 d-flex gap-2 overflow-auto pb-2 justify-content-center">
                        <div class="pd-thumb active border rounded overflow-hidden p-1" style="width: 70px; height: 70px; cursor: pointer; flex-shrink: 0;" onclick="changeImage(this, 'admin/assets/img/uploads/<?php echo $product['pro_img']; ?>')">
                            <img src="admin/assets/img/uploads/<?php echo $product['pro_img']; ?>" alt="Thumb" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <?php while ($galleryImg = mysqli_fetch_assoc($galleryQuery)): ?>
                            <div class="pd-thumb border rounded overflow-hidden p-1" style="width: 70px; height: 70px; cursor: pointer; flex-shrink: 0;" onclick="changeImage(this, 'admin/uploads/<?php echo $galleryImg['image_path']; ?>')">
                                <img src="admin/uploads/<?php echo $galleryImg['image_path']; ?>" alt="Gallery Thumb" style="width: 100%; height: 100%; object-fit: contain;">
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Product Overview -->
            <div class="col-lg-7 ps-lg-4 mt-4 mt-lg-0 reveal" style="clear: both; position: relative; z-index: 2;">
                <span class="pd-category text-muted fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 13px; display: inline-block; padding-top: 10px;"><?php echo htmlspecialchars($product['brand_name']); ?></span>
                
                <div class="pd-overview mb-4" style="color: #444; line-height: 1.7; font-size: 1.05rem;">
                    <?php echo $product['short_desc']; ?>
                </div>

                 <!-- Rating Stars Summary -->
                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning me-2" style="font-size: 1.1rem;">
                        <?php 
                        for($i=1; $i<=5; $i++) {
                            if($i <= round($avg_rating)) echo '<i class="fa-solid fa-star"></i>';
                            else echo '<i class="fa-regular fa-star"></i>';
                        }
                        ?>
                    </div>
                    <span class="text-muted fw-bold">(<?php echo $avg_rating; ?>/5) based on <?php echo $total_reviews; ?> Reviews</span>
                </div>
                
                <!-- Trust Badges Line -->
                <div class="d-flex flex-wrap gap-4 mb-4 py-3 border-top border-bottom">
                    <span class="d-flex align-items-center fw-bold" style="color: #2b5e2c; font-size: 14px;">
                        <i class="fa-solid fa-circle-check fs-5 me-2"></i> Quality Tested
                    </span>
                    <span class="d-flex align-items-center fw-bold" style="color: #2b5e2c; font-size: 14px;">
                        <i class="fa-solid fa-truck-fast fs-5 me-2"></i> Global Shipping
                    </span>
                    <span class="d-flex align-items-center fw-bold" style="color: #2b5e2c; font-size: 14px;">
                        <i class="fa-solid fa-handshake-angle fs-5 me-2"></i> Verified Supplier
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="pd-action-btns d-flex flex-wrap gap-3">
                    <a href="contact.php?product=<?php echo urlencode($product['pro_name']); ?>" class="btn px-4 py-2 text-white fw-bold shadow-sm" style="background: var(--accent-orange); border-radius: 6px; font-size: 1.1rem;">
                        Request a Quote <i class="fa-solid fa-file-invoice ms-2"></i>
                    </a>
                    <a href="tel:<?php echo $sitePhone; ?>" class="btn px-4 py-2 border fw-bold shadow-sm" style="color: var(--primary-green); border-color: var(--primary-green) !important; border-radius: 6px; font-size: 1.1rem; background: #fff;">
                        <i class="fa-solid fa-phone me-2"></i> Call Enquiry
                    </a>
                </div>
            </div>
        </div>

        <!-- BOTTOM SECTION: Description (Left) & Reviews (Right) -->
        <div class="row mt-4">
            <!-- Left Side: Full Description -->
            <div class="col-lg-7 mb-4 mb-lg-0 reveal">
                <div class="bg-white p-3 p-md-4 rounded shadow-sm border border-light h-100">
                    <h3 class="border-bottom pb-3 mb-4 fw-bold" style="color: #222;">Product Details</h3>
                    <div class="full-description-content" style="color: #444; line-height: 1.8;">
                        <?php echo $product['description']; ?>
                    </div>
                </div>
            </div>

            <!-- Right Side: Reviews & Ratings -->
            <div class="col-lg-5 reveal">
                <div class="bg-white p-3 p-md-4 rounded shadow-sm border border-light h-100">
                    <h3 class="border-bottom pb-3 mb-4 fw-bold" style="color: #222;">Customer Reviews</h3>
                    
                    <!-- Add Review Form -->
                    <div class="review-form-box p-3 rounded mb-4" style="background: #f9f9f9; border: 1px solid #eaeaea;">
                        <h6 class="fw-bold mb-3 text-dark">Write a Review</h6>
                        <?php if(isset($review_msg)) echo $review_msg; ?>
                        
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Your Rating</label>
                                <select name="rating" class="form-select form-select-sm" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (5/5) - Excellent</option>
                                    <option value="4">⭐⭐⭐⭐ (4/5) - Very Good</option>
                                    <option value="3">⭐⭐⭐ (3/5) - Good</option>
                                    <option value="2">⭐⭐ (2/5) - Fair</option>
                                    <option value="1">⭐ (1/5) - Poor</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" name="reviewer_name" class="form-control form-control-sm" placeholder="Your Name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" name="reviewer_email" class="form-control form-control-sm" placeholder="Email (Optional)">
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea name="review_text" rows="2" class="form-control form-control-sm" placeholder="Write your experience with this product..." required></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn w-100 fw-bold text-white shadow-sm btn-sm py-2" style="background: var(--primary-green);">Submit Review</button>
                        </form>
                    </div>

                    <!-- Reviews List -->
                    <div class="reviews-list" style="max-height: 450px; overflow-y: auto; padding-right: 10px;">
                        <?php if ($total_reviews > 0): ?>
                            <?php while($rev = mysqli_fetch_assoc($reviewsQuery)): ?>
                                <div class="review-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0" style="color: #333;"><?php echo htmlspecialchars($rev['reviewer_name']); ?></h6>
                                        <div class="text-warning" style="font-size: 0.85rem;">
                                            <?php 
                                            for($i=1; $i<=5; $i++) {
                                                if($i <= $rev['rating']) echo '<i class="fa-solid fa-star"></i>';
                                                else echo '<i class="fa-regular fa-star"></i>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <span class="text-muted d-block mb-2" style="font-size: 0.75rem;"><i class="fa-regular fa-calendar me-1"></i> <?php echo date('M d, Y', strtotime($rev['created_at'])); ?></span>
                                    <p class="mb-0 text-muted" style="font-size: 0.9rem; font-style: italic;">"<?php echo htmlspecialchars($rev['review_text']); ?>"</p>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fa-regular fa-comments fs-2 mb-2 text-light"></i>
                                <p class="small">No reviews yet. Be the first to review!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- RELATED PRODUCTS SECTION WITH SWIPER SLIDER -->
<section class="related-products" style="padding: 50px 0 80px 0; background-color: #ffffff;">
    <div class="container">
        <!-- Section Title -->
        <div class="text-center mb-5 reveal">
            <h2 style="font-size: 2rem; font-weight: 800; color: #222222;">Explore Related Products</h2>
            <div style="width: 60px; height: 3px; background: var(--primary-green); margin: 15px auto;"></div>
        </div>

        <!-- Swiper Container -->
        <div class="swiper relatedProductsSwiper reveal px-3 py-2">
            <div class="swiper-wrapper">
                <?php
                // Fetch up to 10 random related products to populate the slider
                $relatedQuery = mysqli_query($conn, "SELECT * FROM products WHERE status = 1 AND id != '$product_id' ORDER BY RAND() LIMIT 10");
                while ($related = mysqli_fetch_assoc($relatedQuery)):
                    $shortDesc = !empty($related['short_desc']) ? $related['short_desc'] : (!empty($related['meta_desc']) && $related['meta_desc'] != $related['pro_name'] ? $related['meta_desc'] : 'Premium quality agricultural export product sourced directly from Indian farms.');
                ?>
                    <div class="swiper-slide">
                        <div class="product-card h-100 d-flex flex-column" style="border: 1px solid #f0f0f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.03); background: #ffffff;">

                            <!-- Product Image -->
                            <a href="product-details.php?slug=<?php echo $related['slug_url']; ?>" style="text-decoration:none;">
                                <div style="height: 180px; overflow: hidden; background: #fff; padding: 15px; text-align: center;">
                                    <img src="admin/assets/img/uploads/<?php echo $related['pro_img']; ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="<?php echo htmlspecialchars($related['pro_name']); ?>" onerror="this.src='assets/images/black.png'">
                                </div>
                            </a>

                            <!-- Product Content -->
                            <div style="padding: 15px; display: flex; flex-direction: column; flex-grow: 1; border-top: 1px solid #f9f9f9;">
                                <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 8px;">
                                    <a href="product-details.php?slug=<?php echo $related['slug_url']; ?>" style="color: #222; text-decoration: none;">
                                        <?php echo htmlspecialchars($related['pro_name']); ?>
                                    </a>
                                </h3>

                                <p class="text-muted mb-3" style="font-size: 0.85rem; line-height: 1.4; display: -webkit-box; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8em;">
                                    <?php echo htmlspecialchars(strip_tags($shortDesc)); ?>
                                </p>

                                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f0; padding-top: 12px; margin-top: auto;">
                                    <a href="product-details.php?slug=<?php echo $related['slug_url']; ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600; font-size: 13px;">
                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                    <a href="contact.php?product=<?php echo urlencode($related['pro_name']); ?>" style="background-color: var(--accent-orange); color: white; padding: 6px 12px; border-radius: 4px; font-weight: 600; font-size: 12px; text-decoration: none;">
                                        Request Quote
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Swiper Navigation Buttons -->
            <div class="swiper-button-next" style="color: var(--primary-green);"></div>
            <div class="swiper-button-prev" style="color: var(--primary-green);"></div>
            <!-- Swiper Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<?php include ('includes/inquiry-form.php'); ?>

<!-- Swiper JS Script -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
    // Initialize Swiper for Related Products
    var swiper = new Swiper(".relatedProductsSwiper", {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 25,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 30,
            },
        },
    });

    // Image Change Function
    function changeImage(element, imageSrc) {
        document.getElementById('mainImage').src = imageSrc;

        let thumbs = document.querySelectorAll('.pd-thumb');
        thumbs.forEach(thumb => {
            thumb.classList.remove('active');
            thumb.style.borderColor = 'transparent';
        });

        element.classList.add('active');
        element.style.borderColor = 'var(--primary-green)';
    }

    // Scroll Reveal Animation
    document.addEventListener("DOMContentLoaded", function() {
        const reveals = document.querySelectorAll(".reveal");
        const revealOnScroll = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        reveals.forEach(reveal => revealOnScroll.observe(reveal));
    });
</script>

<style>
    .pd-section .container-ng{ padding: 0 20px;}

    .reviews-list::-webkit-scrollbar { width: 5px; }
    .reviews-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .reviews-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    .reviews-list::-webkit-scrollbar-thumb:hover { background: #aaa; }
    
    .pd-thumb.active { border: 2px solid var(--primary-green) !important; opacity: 1 !important; }
    .pd-thumb { border: 2px solid transparent; opacity: 0.6; transition: all 0.3s; }
    .pd-thumb:hover { opacity: 1; }
    
    .full-description-content table { width: 100% !important; max-width: 100%; margin-bottom: 1rem; }
    .full-description-content img { max-width: 100%; height: auto; }
    
    /* Swiper custom styles */
    .swiper-button-next:after, .swiper-button-prev:after { font-size: 20px !important; font-weight: bold; }
    .swiper-button-next, .swiper-button-prev { background: white; width: 40px; height: 40px; border-radius: 50%; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .swiper-pagination-bullet-active { background: var(--primary-green) !important; }
    .relatedProductsSwiper { padding-bottom: 50px !important; }

    @media (max-width: 768px) {
        .pd-section .container-ng{ padding: 0 5px;}
    }
</style>

<?php include 'includes/footer.php'; ?>