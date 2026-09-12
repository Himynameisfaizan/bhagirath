<?php
include('config/connect.php');
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch Specific Product
$productQuery = mysqli_query($conn, "SELECT * FROM products WHERE id = '$product_id' AND status = 1");
$product = mysqli_fetch_assoc($productQuery);

// If product not found, redirect to products page
if (!$product) {
    echo "<script>window.location.href='products.php';</script>";
    exit;
}

// Fetch Global Contact Info for Call Buttons
$contactQuery = mysqli_query($conn, "SELECT phone FROM contacts LIMIT 1");
$contactInfo = mysqli_fetch_assoc($contactQuery);
$sitePhone = !empty($contactInfo['phone']) ? $contactInfo['phone'] : '+919717179432';

// Dynamic Page Title
$pageTitle = $product['pro_name'];

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<section class="pd-section">
    <div class="container">

        <div class="row">
            <!-- Left Column: Image Gallery -->
            <div class="col-lg-5 mb-5 mb-lg-0 reveal py-5">
                <div class="pd-image-gallery">
                    <!-- Dynamic Main Image -->
                    <div class="pd-main-img">
                        <img id="mainImage" src="admin/assets/img/uploads/<?php echo $product['pro_img']; ?>" alt="<?php echo $product['pro_name']; ?>">
                    </div>

                    <!-- Dynamic Thumbnails from product_images table -->
                    <div class="pd-thumbnails">
                        <!-- Main Image as first thumbnail -->
                        <!-- <div class="pd-thumb active" onclick="changeImage(this, 'uploads/<?php echo $product['pro_img']; ?>')">
                            <img src="uploads/<?php echo $product['pro_img']; ?>" alt="Thumb">
                        </div> -->

                        <?php
                        // Fetch additional images if any
                        $galleryQuery = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id = '$product_id'");
                        while ($galleryImg = mysqli_fetch_assoc($galleryQuery)):
                        ?>
                            <div class="pd-thumb" onclick="changeImage(this, 'uploads/<?php echo $galleryImg['image_path']; ?>')">
                                <img src="uploads/<?php echo $galleryImg['image_path']; ?>" alt="Additional Thumb">
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Product Info -->
            <div class="col-lg-7 ps-lg-5 reveal py-5">
                <span class="pd-category"><?php echo $product['brand_name']; ?></span>
                <p style="font-size: 13px; color: #888;">
                    <i class="fa-solid fa-shield-check text-success"></i> 100% Secure & Verified Supplier
                </p>
                <!-- <h2 class="pd-title"><?php echo $product['pro_name']; ?></h2> -->

                <!-- Short Description from DB -->
                <div class="pd-overview">
                    <?php echo $product['short_desc']; ?>
                </div>

                <!-- Action Buttons (Dynamic Contact Link & Phone) -->
                <div class="pd-action-btns">
                    <a href="contact.php?product=<?php echo urlencode($product['pro_name']); ?>" class="btn-lg-quote">
                        Request a Quote <i class="fa-solid fa-file-invoice ms-2"></i>
                    </a>

                    <a href="tel:<?php echo $sitePhone; ?>" class="btn-lg-call">
                        <i class="fa-solid fa-phone me-2"></i> Call for Enquiry
                    </a>
                </div>

            </div>
        </div>

        <!-- Tabs Section for Deep Details -->
        <div class="row pd-tabs-section reveal">
            <div class="col-12">

                <ul class="nav nav-tabs custom-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Full Description</button>
                    </li>
                </ul>

                <div class="tab-content" id="productTabsContent">
                    <!-- Long Description from DB -->
                    <div class="tab-pane fade show active" id="desc" role="tabpanel">
                        <?php echo $product['description']; ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- RELATED PRODUCTS SECTION -->
<section class="related-products" style="padding: 0 0 100px 0; background-color: #ffffff;">
    <div class="container">
        <!-- Section Title with Updated Brand Colors -->
        <div class="text-center mb-5 reveal">
            <h2 style="font-size: 2rem; font-weight: 800; color: #222222;">Explore Related Products</h2>
            <div style="width: 60px; height: 3px; background: #711b3c; margin: 15px auto;"></div>
        </div>

        <div class="row g-4 reveal">
            <?php
            $relatedQuery = mysqli_query($conn, "SELECT * FROM products WHERE status = 1 AND id != '$product_id' ORDER BY RAND() LIMIT 4");
            while ($related = mysqli_fetch_assoc($relatedQuery)):
                $shortDesc = !empty($related['short_desc']) ? $related['short_desc'] : (!empty($related['meta_desc']) && $related['meta_desc'] != $related['pro_name'] ? $related['meta_desc'] : 'Premium quality agricultural export product sourced directly from Indian farms.');
            ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card h-100 d-flex flex-column" style="border: 1px solid #f0f0f0; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.03); background: #ffffff;">

                        <!-- Product Image -->
                        <a href="product-details.php?id=<?php echo $related['id']; ?>" style="text-decoration:none;">
                            <div style="height: 200px; overflow: hidden; background: #f8f9fa; padding: 10px;">
                                <img src="admin/assets/img/uploads/<?php echo $related['pro_img']; ?>" style="width: 100%; height: 100%; object-fit: contain;" alt="<?php echo htmlspecialchars($related['pro_name']); ?>" onerror="this.src='assets/images/black.png'">
                            </div>
                        </a>

                        <!-- Product Content -->
                        <div style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1;">
                            <!-- Product Title -->
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 8px;">
                                <a href="product-details.php?id=<?php echo $related['id']; ?>" style="color: #222222; text-decoration: none;">
                                    <?php echo htmlspecialchars($related['pro_name']); ?>
                                </a>
                            </h3>

                            <!-- Product Short Description (Limited to exactly 2 lines) -->
                            <p class="text-muted mb-4" style="font-size: 0.85rem; line-height: 1.5; display: -webkit-box; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3em;">
                                <?php echo htmlspecialchars(strip_tags($shortDesc)); ?>
                            </p>

                            <!-- Action Buttons (Left: View Details, Right: Request Quote) -->
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f0; padding-top: 15px; margin-top: auto;">
                                <a href="product-details.php?id=<?php echo $related['id']; ?>" style="color: #711b3c; text-decoration: none; font-weight: 600; font-size: 13px;">
                                    View Details <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <a href="contact.php?product=<?php echo urlencode($related['pro_name']); ?>" style="background-color: #222222; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; text-decoration: none;">
                                    Request Quote
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Premium Inquiry Section -->
<section class="inquiry-section section-padding position-relative" style="background-color: #711b3c; background-image: linear-gradient(135deg, rgba(113, 27, 60, 0.95) 0%, rgba(40, 10, 20, 0.98) 100%), url('assets/images/contact-bg.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
    
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            
            <!-- Left Side Content -->
            <div class="col-lg-5 text-white mb-5 mb-lg-0 pe-lg-4">
                <span class="badge mb-3 px-3 py-2" style="background: rgba(255,255,255,0.15); color: #fff; font-weight: 600; letter-spacing: 1px; border-radius: 30px;">GET IN TOUCH</span>
               <ul class="list-unstyled mb-0 contact-info-list">
                    <!-- Head Office -->
                    <li class="mb-4 d-flex align-items-center p-3 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                        <div class="icon-box me-4 text-center d-flex align-items-center justify-content-center shadow-sm" style="background: #ffffff; width: 55px; height: 55px; border-radius: 50%; flex-shrink: 0;">
                            <i class="bi bi-geo-alt-fill" style="font-size: 1.5rem; color: #711b3c;"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1" style="color: #fff; font-size: 1.1rem; letter-spacing: 0.5px;">Head Office</strong>
                            <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem; line-height: 1.4; display: block;">Office No-102, 1st Floor, Nitika Tower II, Block C-1, Pocket-4, Azadpur, Delhi - 110033</span>
                        </div>
                    </li>
                    <!-- Phone -->
                    <li class="mb-4 d-flex align-items-center p-3 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                        <div class="icon-box me-4 text-center d-flex align-items-center justify-content-center shadow-sm" style="background: #ffffff; width: 55px; height: 55px; border-radius: 50%; flex-shrink: 0;">
                            <i class="bi bi-telephone-fill" style="font-size: 1.5rem; color: #711b3c;"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1" style="color: #fff; font-size: 1.1rem; letter-spacing: 0.5px;">Call Us</strong>
                            <span style="color: rgba(255,255,255,0.7); font-size: 0.95rem; display: block;">+91-8448211202 (Mr. Anuj)</span>
                            <span style="color: rgba(255,255,255,0.7); font-size: 0.95rem; display: block;">+91-9870491393 (Vicky)</span>
                        </div>
                    </li>
                    <!-- Email -->
                    <li class="d-flex align-items-center p-3 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                        <div class="icon-box me-4 text-center d-flex align-items-center justify-content-center shadow-sm" style="background: #ffffff; width: 55px; height: 55px; border-radius: 50%; flex-shrink: 0;">
                            <i class="bi bi-envelope-fill" style="font-size: 1.5rem; color: #711b3c;"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1" style="color: #fff; font-size: 1.1rem; letter-spacing: 0.5px;">Email Us</strong>
                            <span style="color: rgba(255,255,255,0.7); font-size: 0.95rem; display: block;">bhagirathenterprise7@gmail.com</span>
                        </div>
                    </li>
                </ul>
            </div>
            
            <!-- Right Side Inquiry Form (Floating Labels Design) -->
            <div class="col-lg-7">
                <div class="inquiry-form-wrapper bg-white p-4 p-md-5 rounded-4 shadow-lg position-relative" style="border: 1px solid rgba(0,0,0,0.05);">
                    <div class="text-center mb-4">
                        <h3 class="text-dark mb-2" style="font-weight: 800; font-size: 2rem;">Request a Free Quote</h3>
                        <p class="text-muted small">Fill out the form below and our team will get back to you within 24 hours.</p>
                    </div>
                    
                    <form action="inquiry-process.php" method="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="name" class="form-control premium-input" id="nameInput" placeholder="Full Name" required>
                                    <label for="nameInput">Full Name *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="phone" class="form-control premium-input" id="phoneInput" placeholder="Phone Number" required>
                                    <label for="phoneInput">Phone Number *</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="email" name="email" class="form-control premium-input" id="emailInput" placeholder="Email Address" required>
                                    <label for="emailInput">Email Address *</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="text" name="subject" class="form-control premium-input" id="subjectInput" placeholder="Product of Interest" required>
                                    <label for="subjectInput">Product of Interest *</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea name="message" class="form-control premium-input" id="messageInput" placeholder="Your Message" style="height: 120px" required></textarea>
                                    <label for="messageInput">Your Message / Requirements *</label>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-maroon-glow w-100 py-3 text-uppercase fw-bold tracking-wide">
                                    Send Inquiry Now <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</section>

<script>
    // JS for changing main image when thumbnail is clicked
    function changeImage(element, imageSrc) {
        document.getElementById('mainImage').src = imageSrc;

        let thumbs = document.querySelectorAll('.pd-thumb');
        thumbs.forEach(thumb => thumb.classList.remove('active'));

        element.classList.add('active');
    }

    // Scroll Animation
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

<?php include 'includes/footer.php'; ?>