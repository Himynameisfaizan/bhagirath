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
            <div class="col-lg-5 mb-5 mb-lg-0 reveal">
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
            <div class="col-lg-7 ps-lg-5 reveal">
                <span class="pd-category"><?php echo $product['brand_name']; ?></span>
                <p style="font-size: 13px; color: #888;">
                    <i class="fa-solid fa-shield-check text-success"></i> 100% Secure & Verified Supplier
                </p>
                <h2 class="pd-title"><?php echo $product['pro_name']; ?></h2>

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
        <div class="text-center mb-5 reveal">
            <h2 style="font-size: 2rem; font-weight: 800; color: var(--primary-green);">Explore Related Products</h2>
            <div style="width: 60px; height: 3px; background: var(--accent-orange); margin: 15px auto;"></div>
        </div>

        <div class="row g-4 reveal">
            <?php
            // Fetch 4 random related products excluding the current one
            $relatedQuery = mysqli_query($conn, "SELECT * FROM products WHERE status = 1 AND id != '$product_id' ORDER BY RAND() LIMIT 4");
            while ($related = mysqli_fetch_assoc($relatedQuery)):
            ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card" style="border: 1px solid #f0f0f0; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                        <a href="product-details.php?id=<?php echo $related['id']; ?>">
                            <div style="aspect-ratio: 4/3; overflow: hidden; background: #f9f9f9;">
                                <img src="admin/assets/img/uploads/<?php echo $related['pro_img']; ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo $related['pro_name']; ?>">
                            </div>
                        </a>
                        <div style="padding: 20px;">
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 15px;">
                                <a href="product-details.php?id=<?php echo $related['id']; ?>" style="color: #1A1A1A; text-decoration: none;">
                                    <?php echo $related['pro_name']; ?>
                                </a>
                            </h3>
                            <div style="display: flex; gap: 10px; border-top: 1px solid #f0f0f0; padding-top: 15px;">
                                <a href="contact.php?product=<?php echo urlencode($related['pro_name']); ?>" style="flex-grow: 1; text-align: center; border: 1.5px solid var(--primary-green); color: var(--primary-green); padding: 8px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none;">Request Quote</a>
                                <a href="tel:<?php echo $sitePhone; ?>" style="background: rgba(230,126,34,0.1); color: var(--accent-orange); padding: 8px 12px; border-radius: 8px;"><i class="fa-solid fa-phone"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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