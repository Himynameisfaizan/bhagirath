<?php
// Initialize default variables
$footer_logo = "assets/images/logo/logo.png";
$c_address = "3 RD FLOOR, FLAT NO.303, FORTUNE ICONIA, MAIN ROAD, PALAKALURU ROAD, BEHIND GUNTUR CLUB, GUNTUR, Andhra Pradesh, 522006";
$c_phone = "99123 00247";
$c_email = "eurasiastoneindia@gmail.com";
$c_fb = "#";
$c_linkedin = "#";
$c_wp = "#";

if (isset($conn)) {
    // 1. Fetch Footer Logo
    $f_logo_query = mysqli_query($conn, "SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    if ($f_logo_query && mysqli_num_rows($f_logo_query) > 0) {
        $f_logo_data = mysqli_fetch_assoc($f_logo_query);
        $footer_logo = 'admin/uploads/' . $f_logo_data['logo_path'];
    }

    // 2. Fetch Contact Details & Social Links
    $contact_query = mysqli_query($conn, "SELECT * FROM contacts ORDER BY id DESC LIMIT 1");
    if ($contact_query && mysqli_num_rows($contact_query) > 0) {
        $contact_info = mysqli_fetch_assoc($contact_query);
        
        $c_address = !empty($contact_info['address']) ? $contact_info['address'] : $c_address;
        $c_phone = !empty($contact_info['phone']) ? $contact_info['phone'] : $c_phone;
        // Check contact_email first, then fallback to email
        $c_email = !empty($contact_info['contact_email']) ? $contact_info['contact_email'] : (!empty($contact_info['email']) ? $contact_info['email'] : $c_email);
        
        $c_fb = !empty($contact_info['facebook']) ? $contact_info['facebook'] : $c_fb;
        $c_linkedin = !empty($contact_info['linkdin']) ? $contact_info['linkdin'] : $c_linkedin;
        // Format WhatsApp number for API link
        $c_wp = !empty($contact_info['wp_number']) ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $contact_info['wp_number']) : $c_wp;
    }

    // 3. Fetch Dynamic Products for Footer Links (Top 5)
    $footer_products = mysqli_query($conn, "SELECT id, pro_name, slug_url FROM products WHERE status = 1 ORDER BY id DESC LIMIT 5");
}
?>

<footer class="custom-footer" style="background-color: #1a1a1a; color: #d1d1d1; padding-top: 60px;">
    <div class="container">
        <div class="row g-4">
            
            <!-- Column 1: About & Logo -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="footer-brand bg-white d-inline-block p-1 mb-3" style="border-radius: 5px;">
                    <img src="<?= htmlspecialchars($footer_logo); ?>" alt="EURASIASTONEINDIA" style="width: 90px; height: auto;" onerror="this.src='assets/images/logo/logo.png'">
                </div>
                <h5 class="text-white fw-bold mb-2 text-uppercase">EURASIASTONEINDIA</h5>
                <p class="small text-muted mb-2">EURASIASTONEINDIA...</p>
                
                <!-- Verified Supplier Badge -->
                <span class="badge" style="background-color: #198754; font-weight: normal; font-size: 13px;">
                    <i class="bi bi-check-circle-fill me-1"></i> Verified Supplier
                </span>
            </div>

            <!-- Column 2: Information Links -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-white mb-4" style="border-bottom: 2px solid #007bff; display: inline-block; padding-bottom: 5px;">Information</h5>
                <ul class="footer-links list-unstyled" style="line-height: 2.2;">
                    <li><a href="index.php" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">Home</a></li>
                    <li><a href="about.php" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">Company Profile</a></li>
                    <!-- <li><a href="gallery.php" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">Our Gallery</a></li> -->
                    <li><a href="contact.php" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">Contact Us</a></li>
                    <li><a href="terms-condition.php" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">Terms & Conditions</a></li>
                    <li><a href="privacy-policy.php" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">Privacy Policy</a></li>
                    <li><a href="shipping-return.php" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">Shipping & Returns</a></li>
                    <li><a href="refund-policy.php" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">Refund & Cancellation</a></li>
                </ul>
            </div>

            <!-- Column 3: Dynamic Products -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-white mb-4" style="border-bottom: 2px solid #007bff; display: inline-block; padding-bottom: 5px;">Our Products</h5>
                <ul class="footer-links list-unstyled" style="line-height: 2.2;">
                    <?php 
                    if (isset($footer_products) && mysqli_num_rows($footer_products) > 0) {
                        while($f_prod = mysqli_fetch_assoc($footer_products)) {
                            $prod_slug = !empty($f_prod['slug_url']) ? $f_prod['slug_url'] : $f_prod['id'];
                    ?>
                        <li>
                            <a href="product-details.php?slug=<?= htmlspecialchars($prod_slug); ?>" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;">
                                <i class="bi bi-chevron-right small me-2"></i> <?= htmlspecialchars($f_prod['pro_name']); ?>
                            </a>
                        </li>
                    <?php 
                        }
                    } else {
                        // Fallback static links if no products are found in DB
                    ?>
                        <li><a href="products.php?category=rice" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;"><i class="bi bi-chevron-right small me-2"></i> Rice</a></li>
                        <li><a href="products.php?category=cumin-seeds" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;"><i class="bi bi-chevron-right small me-2"></i> Cumin Seeds</a></li>
                        <li><a href="products.php?category=turmeric" class="text-decoration-none" style="color: #d1d1d1; transition: 0.3s;"><i class="bi bi-chevron-right small me-2"></i> Turmeric</a></li>
                    <?php } ?>
                </ul>
            </div>

            <!-- Column 4: Contact Details -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-4" style="border-bottom: 2px solid #007bff; display: inline-block; padding-bottom: 5px;">Contact Details</h5>
                <ul class="footer-links footer-contact list-unstyled" style="line-height: 1.8;">
                    <li class="d-flex mb-3">
                        <i class="bi bi-geo-alt-fill me-3 mt-1" style="color: #007bff;"></i>
                        <span class="small">
                            <?= htmlspecialchars($c_address); ?>
                        </span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="bi bi-telephone-fill me-3 mt-1" style="color: #007bff;"></i>
                        <span>
                            <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $c_phone)); ?>" class="text-decoration-none" style="color: #d1d1d1;">
                                <?= htmlspecialchars($c_phone); ?>
                            </a>
                        </span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="bi bi-envelope-fill me-3 mt-1" style="color: #007bff;"></i>
                        <span>
                            <a href="mailto:<?= htmlspecialchars($c_email); ?>" class="text-decoration-none" style="color: #d1d1d1; word-break: break-all;">
                                <?= htmlspecialchars($c_email); ?>
                            </a>
                        </span>
                    </li>
                </ul>
                
                <!-- Social Media Icons (Dynamically linked) -->
                <div class="social-icons mt-3 d-flex gap-2">
                    <?php if($c_fb != '#'): ?>
                    <a href="<?= htmlspecialchars($c_fb); ?>" target="_blank" class="rounded-circle d-flex align-items-center justify-content-center text-white text-decoration-none" style="width: 35px; height: 35px; background-color: #3b5998;">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if($c_linkedin != '#'): ?>
                    <a href="<?= htmlspecialchars($c_linkedin); ?>" target="_blank" class="rounded-circle d-flex align-items-center justify-content-center text-white text-decoration-none" style="width: 35px; height: 35px; background-color: #007bb5;">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if($c_wp != '#'): ?>
                    <a href="<?= htmlspecialchars($c_wp); ?>" target="_blank" class="rounded-circle d-flex align-items-center justify-content-center text-white text-decoration-none" style="width: 35px; height: 35px; background-color: #25D366;">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Copyright & Developer Info -->
    <div class="footer-bottom mt-5" style="background-color: #111; padding: 20px 0; border-top: 1px solid #333;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0 small" style="color: #888;">
                    &copy; <?= date('Y'); ?> <strong>EURASIASTONEINDIA</strong>. All rights reserved.
                </div>
                <div class="col-md-6 text-center text-md-end small" style="color: #888;">
                    Powered by <a href="https://go2exportmart.com" target="_blank" class="text-decoration-none" style="color: #007bff;">go2exportmart.com</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Additional Javascript logic for active states and animations -->
</body>
</html>