<?php 
// Database connection file include karein
include 'config/connect.php'; 

// 1. Dynamic Banners Query
$banner_res = false;
if (isset($conn)) {
    $banner_res = mysqli_query($conn, "SELECT * FROM banners WHERE status = 0 ORDER BY display_order ASC, id DESC");
}

// 2. Dynamic Services Query (Top 3 on Home)
$services_res = false;
if (isset($conn)) {
    $services_res = mysqli_query($conn, "SELECT * FROM services ORDER BY id DESC LIMIT 3");
}

// 3. Dynamic Products Query (Active 4 Products)
$products_res = false;
if (isset($conn)) {
    $products_res = mysqli_query($conn, "SELECT * FROM products WHERE status = 1 ORDER BY id DESC LIMIT 4");
}

// 5. Dynamic Blogs Query (Latest 3 Active Blogs)
$blogs_res = false;
if (isset($conn)) {
    $blogs_res = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 1 ORDER BY blog_id DESC LIMIT 3");
}

// 4. Dynamic Brands/Clients Query
$brands_res = false;
if (isset($conn)) {
    $brands_res = mysqli_query($conn, "SELECT * FROM brands ORDER BY id DESC");
}

include("includes/header.php"); 
?>

<!-- Hero Slider Section Start -->
<div id="heroCarousel" class="carousel slide carousel-fade hero-slider" data-bs-ride="carousel" data-bs-pause="false">
    
    <!-- Carousel Indicators -->
    <div class="carousel-indicators">
        <?php 
        if ($banner_res && mysqli_num_rows($banner_res) > 0): 
            $i = 0;
            mysqli_data_seek($banner_res, 0);
            while($b_row = mysqli_fetch_assoc($banner_res)):
        ?>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" class="<?= ($i == 0) ? 'active' : '' ?>" aria-current="<?= ($i == 0) ? 'true' : 'false' ?>" aria-label="Slide <?= $i+1 ?>"></button>
        <?php 
            $i++;
            endwhile; 
        else: 
        ?>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <?php endif; ?>
    </div>

    <div class="carousel-inner">
        <?php 
        if ($banner_res && mysqli_num_rows($banner_res) > 0): 
            $j = 0;
            mysqli_data_seek($banner_res, 0);
            while($banner = mysqli_fetch_assoc($banner_res)):
                // Database me banner_path 'uploads/banners/...' format me save hai
                $bannerImg = !empty($banner['banner_path']) ? $banner['banner_path'] : 'assets/images/black.png';
        ?>
            <!-- Dynamic Slide -->
            <div class="carousel-item <?= ($j == 0) ? 'active' : '' ?>" data-bs-interval="5000">
                <div class="slide-bg" style="background-image: url('admin/<?= htmlspecialchars($bannerImg) ?>');"></div>
                <div class="carousel-caption">
                    <div class="container">
                        <h1><?= htmlspecialchars($banner['title']) ?></h1>
                        <p><?= htmlspecialchars($banner['description']) ?></p>
                        <div>
                            <a href="<?= !empty($banner['link_url']) ? htmlspecialchars($banner['link_url']) : 'products.php' ?>" class="btn-primary-custom">Explore Products</a>
                            <a href="contact.php" class="btn-outline-custom">Contact an Expert</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            $j++;
            endwhile; 
        else: 
        ?>
            <!-- Fallback Static Slide (Agar DB me banner na mile) -->
            <div class="carousel-item active" data-bs-interval="5000">
                <div class="slide-bg" style="background-image: url('assets/images/banner1.jpg');"></div>
                <div class="carousel-caption">
                    <div class="container">
                        <h1>Premium Indian Spices <br><span style="color: #E3000F;">& Food Products</span></h1>
                        <p>Exporting the finest quality rice, chilli, turmeric, and authentic spices worldwide with unmatched purity.</p>
                        <div>
                            <a href="products.php" class="btn-primary-custom">Explore Products</a>
                            <a href="contact.php" class="btn-outline-custom">Contact an Expert</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<!-- Hero Slider Section End -->

<!-- About Us Section -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-img-wrapper">
                    <img src="assets/images/about.jpg" alt="EURASIASTONEINDIA Team" onerror="this.src='https://images.unsplash.com/photo-1596040033229-a9821ebd058d?q=80&w=800&auto=format&fit=crop'">
                    <div class="about-experience">
                        <h3 class="mb-0">100%</h3>
                        <p class="mb-0 small">Authentic Quality</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <h4 class="text-uppercase" style="color: #E3000F; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Who We Are</h4>
                <h2 class="section-title mb-4">Exporting the Finest Flavors & Agricultural Wealth of India</h2>
                <p class="text-muted-custom mb-4">At <strong>EURASIASTONEINDIA</strong>, we specialize in processing and exporting premium quality rice, chilli, turmeric, and authentic Indian spices. Our commitment is to deliver farm-fresh, unadulterated, and richly flavored food products to international markets while maintaining the highest levels of purity.</p>
                <ul class="list-unstyled mb-4 text-muted-custom">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Ethically sourced directly from the finest Indian farms.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Strict compliance with global food safety & hygiene standards.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Uncompromised purity, natural aroma, and rich taste.</li>
                </ul>
                <a href="about.php" class="btn btn-quote" style="background-color:#17385A; border-color:#17385A;">Read More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Services Section -->
<section class="section-padding bg-light-grey">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-uppercase" style="color: #E3000F; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Our Offerings</h4>
            <h2 class="section-title mx-auto">Premium Agricultural Exports</h2>
            <p class="text-muted-custom mt-3 max-w-700 mx-auto" style="max-width: 600px;">We supply a diverse range of high-quality, farm-fresh agricultural products, carefully sourced and processed to meet global food standards.</p>
        </div>

        <div class="row g-4">
            <?php 
            if ($services_res && mysqli_num_rows($services_res) > 0): 
                while($srv = mysqli_fetch_assoc($services_res)):
                    $srvImg = !empty($srv['img_path']) ? 'admin/assets/img/uploads/' . $srv['img_path'] : 'assets/images/black.png';
            ?>
                <!-- Dynamic Service Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card h-100 bg-white shadow-sm rounded overflow-hidden">
                        <div class="service-img-container" style="height: 200px; overflow: hidden;">
                            <img src="<?= htmlspecialchars($srvImg) ?>" alt="<?= htmlspecialchars($srv['service_name']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='assets/images/black.png'">
                        </div>
                        <div class="card-body p-4">
                            <h4 class="service-title" style="color: #17385A; font-weight: 700;"><?= htmlspecialchars($srv['service_name']) ?></h4>
                            <p class="text-muted-custom small mb-4">
                                <?= htmlspecialchars(substr($srv['short_desc'], 0, 110)) ?>...
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="service-details.php?id=<?= $srv['id'] ?>" class="service-link small fw-bold" style="color: #17385A;">View Details <i class="bi bi-arrow-right"></i></a>
                                <a href="contact.php?service=<?= urlencode($srv['service_name']) ?>" class="btn-quote-outline">Inquire Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else: 
            ?>
                <div class="col-12 text-center text-muted">No services available right now.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-uppercase" style="color: #E3000F; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Why EURASIASTONEINDIA</h4>
            <h2 class="section-title mx-auto">The Trusted Choice for Global Exports</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                    <h5 class="feature-title">Certified Quality</h5>
                    <p class="text-muted-custom small mb-0">Our products meet rigorous global food safety standards ensuring 100% purity and authenticity.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-globe"></i></div>
                    <h5 class="feature-title">Global Export</h5>
                    <p class="text-muted-custom small mb-0">Seamless international logistics and timely delivery to our clients across the globe.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-basket"></i></div>
                    <h5 class="feature-title">Farm Fresh Sourcing</h5>
                    <p class="text-muted-custom small mb-0">Ethically sourced directly from the finest Indian farms to preserve natural aroma and taste.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <h5 class="feature-title">Competitive Pricing</h5>
                    <p class="text-muted-custom small mb-0">Premium quality agricultural and food exports offered at the best international market rates.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Products Section -->
<section class="section-padding" style="background-color: #ffffff;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h4 class="text-uppercase" style="color: #E3000F; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Our Produce</h4>
                <h2 class="section-title mb-0">Premium Export Products</h2>
            </div>
            <div class="d-none d-md-block">
                <a href="products.php" class="btn btn-outline-dark" style="border-radius: 20px; font-weight: 600;">View All Products</a>
            </div>
        </div>

        <div class="row g-4">
            <?php 
            if ($products_res && mysqli_num_rows($products_res) > 0): 
                while($prod = mysqli_fetch_assoc($products_res)):
                    $proImg = !empty($prod['pro_img']) ? 'admin/assets/img/uploads/' . $prod['pro_img'] : 'assets/images/black.png';
                    $slug = !empty($prod['slug_url']) ? $prod['slug_url'] : $prod['id'];
            ?>
                <!-- Dynamic Product Card -->
                <div class="col-lg-3 col-md-6">
                    <div class="product-card h-100 shadow-sm border rounded overflow-hidden">
                        <span class="product-badge">Export Grade</span>
                        <div class="product-img-wrapper" style="height: 200px; overflow: hidden;">
                            <img src="<?= htmlspecialchars($proImg) ?>" alt="<?= htmlspecialchars($prod['pro_name']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='assets/images/black.png'">
                        </div>
                        <div class="p-4">
                            <h4 class="product-title" style="font-size: 1.05rem; font-weight: 700; height: 48px; overflow: hidden;">
                                <?= htmlspecialchars($prod['pro_name']) ?>
                            </h4>
                            <div class="mb-3">
                                <a href="product-details.php?slug=<?= $slug ?>" class="view-details-link">View Details <i class="bi bi-chevron-right" style="font-size: 0.8rem;"></i></a>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="tel:+919912300247" class="btn-call" title="Call for inquiry">
                                    <i class="bi bi-telephone-fill"></i>
                                </a>
                                <a href="contact.php?product=<?= urlencode($prod['pro_name']) ?>" class="btn btn-quote-full flex-grow-1">Inquire Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else: 
            ?>
                <div class="col-12 text-center text-muted">No products found.</div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4 d-block d-md-none">
            <a href="products.php" class="btn btn-outline-dark" style="border-radius: 20px; font-weight: 600;">View All Products</a>
        </div>
    </div>
</section>

<!-- Dynamic Blog / News Section -->
<!-- <section class="section-padding bg-light" style="border-top: 1px solid #eaeaea;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h4 class="text-uppercase" style="color: #E3000F; font-size: 14px; font-weight: 600; letter-spacing: 1px;">Our Latest Insights</h4>
                <h2 class="section-title mb-0">Agro-Export News & Updates</h2>
            </div>
            <div class="d-none d-md-block">
                <a href="blog.php" class="btn btn-outline-dark" style="border-radius: 20px; font-weight: 600;">View All Blogs</a>
            </div>
        </div>

        <div class="row g-4">
            <#?php 
            if ($blogs_res && mysqli_num_rows($blogs_res) > 0): 
                while($blog = mysqli_fetch_assoc($blogs_res)):
                    // Image fetch handling with fallback
                    $blogImg = !empty($blog['image']) ? 'admin/assets/img/uploads/blogs/' . $blog['image'] : 'assets/images/default-blog.jpg';
                    // Date formatting (e.g., "Sep 09, 2026")
                    $blogDate = date('M d, Y', strtotime($blog['created_at']));
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="blog-card bg-white shadow-sm rounded-4 overflow-hidden h-100 position-relative" style="transition: all 0.3s ease; border: 1px solid #f0f0f0;">
                        
                        <div class="blog-img-wrapper position-relative" style="height: 240px; overflow: hidden;">
                            <img src="<?= htmlspecialchars($blogImg) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;" onerror="this.src='assets/images/default-blog.jpg'">
                            
                            <div class="date-badge position-absolute shadow-sm" style="top: 15px; left: 15px; background-color: #E3000F; color: white; padding: 6px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; z-index: 2;">
                                <i class="bi bi-calendar3 me-1"></i> <?= $blogDate ?>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="author-info mb-2 small text-muted">
                                <i class="bi bi-person-circle me-1" style="color: #17385A;"></i> By <span class="fw-semibold"><?= htmlspecialchars($blog['author']) ?></span>
                            </div>
                            
                            <h4 class="blog-title mb-3" style="font-size: 1.15rem; font-weight: 700; line-height: 1.4; height: 50px; overflow: hidden;">
                                <a href="blog-details.php?slug=<?= htmlspecialchars($blog['slug']) ?>" class="text-decoration-none" style="color: #17385A; transition: 0.3s;">
                                    <?= htmlspecialchars($blog['title']) ?>
                                </a>
                            </h4>
                            
                            <p class="text-muted-custom small mb-4" style="height: 65px; overflow: hidden;">
                                <?= htmlspecialchars(substr(strip_tags($blog['description']), 0, 110)) ?>...
                            </p>
                            
                            <div class="border-top pt-3 mt-auto">
                                <a href="blog-details.php?slug=<?= htmlspecialchars($blog['slug']) ?>" class="btn-read-more text-uppercase d-inline-flex align-items-center" style="font-size: 0.85rem; font-weight: 700; color: #E3000F; text-decoration: none;">
                                    Read Article <i class="bi bi-arrow-right ms-2" style="transition: transform 0.3s;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
             <#?php  
                // endwhile;
            // else: 
            // ?>
                <div class="col-12 text-center text-muted py-5 border rounded bg-white">
                    <h5>No recent insights published yet.</h5>
                    <p class="small">Check back later for market updates and agricultural news.</p>
                </div>
            <?#php endif; ?>
        </div>
        
        <div class="text-center mt-4 d-block d-md-none">
            <a href="blog.php" class="btn btn-outline-dark" style="border-radius: 20px; font-weight: 600;">View All Blogs</a>
        </div>
    </div>
</section> -->

<!-- Dynamic Brands / Clients Section -->
<section class="brands-section py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <h5 class="text-center mb-4" style="color: #17385A; font-weight: 700; font-size: 1.1rem; letter-spacing: 1px;">OUR TRUSTED CLIENTS & PARTNERS</h5>
        <div class="row align-items-center justify-content-center g-4 text-center">
            <?php 
            if ($brands_res && mysqli_num_rows($brands_res) > 0): 
                while($brand = mysqli_fetch_assoc($brands_res)):
                    $brandLogo = !empty($brand['logo_path']) ? $brand['logo_path'] : '';
            ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="brand-box p-2 bg-white rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 90px;">
                        <?php if(!empty($brandLogo)): ?>
                            <img src="admin/<?= htmlspecialchars($brandLogo) ?>" alt="<?= htmlspecialchars($brand['brand_name']) ?>" title="<?= htmlspecialchars($brand['brand_name']) ?>" style="max-height: 60px; max-width: 100%; object-fit: contain;">
                        <?php else: ?>
                            <span class="fw-bold small text-dark"><?= htmlspecialchars($brand['brand_name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
                endwhile;
            else: 
            ?>
                <!-- Fallback Certifications if brands empty -->
                <div class="col-6 col-md-4 col-lg-2"><h4 class="brand-logo" style="color: #333; font-weight: 800;">FSSAI</h4></div>
                <div class="col-6 col-md-4 col-lg-2"><h4 class="brand-logo" style="color: #333; font-weight: 800;">APEDA</h4></div>
                <div class="col-6 col-md-4 col-lg-2"><h4 class="brand-logo" style="color: #333; font-weight: 800;">SPICES BOARD</h4></div>
                <div class="col-6 col-md-4 col-lg-2"><h4 class="brand-logo" style="color: #333; font-weight: 800;">ISO 22000</h4></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <i class="bi bi-globe-central-south-asia" style="font-size: 3rem; color: #E3000F; margin-bottom: 20px; display: inline-block;"></i>
                <h2 class="mb-3" style="font-weight: 700;">Stay Updated on Global Agro Trends</h2>
                <p class="mb-4" style="color: #c9d6e4;">Subscribe to our newsletter to receive the latest market updates, harvest trends, and exclusive offers on our premium agricultural exports.</p>
                
                <form action="newsletter-process.php" method="POST" class="d-flex justify-content-center">
                    <div class="input-group" style="max-width: 500px;">
                        <input type="email" name="email" class="form-control newsletter-input" placeholder="Enter your email address" required>
                        <button class="btn newsletter-btn" type="submit">Subscribe</button>
                    </div>
                </form>
                <p class="mt-3 small" style="color: #8cabc7;">We respect your privacy. No spam, ever.</p>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>