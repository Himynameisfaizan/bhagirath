<?php
// 1. ERROR FIX: Variable ko default path ke sath initialize karna zaroori hai
$footer_logo = "assets/images/logo/logo.png";

$c_address = "Office No-102, 1st Floor, Nitika Tower II, Block C-1, Pocket-4, Azadpur, Delhi - 110033, India";
$c_phone = "+91-8448211202";
$c_email = "bhagirathenterprise7@gmail.com";
$c_fb = "#";
$c_linkedin = "#";
$c_wp = "#";
$footer_about_text = "Bhagirath Enterprise is a leading exporter of premium agricultural products, specializing in farm-fresh spices and dry fruits."; // Fallback text

if (isset($conn)) {
    // 2. ERROR FIX: Pehli query mein 'footer' location search karni hai
    $f_logo_query = mysqli_query($conn, "SELECT logo_path FROM logos WHERE location = 'footer' AND is_active = 1 ORDER BY id DESC LIMIT 1");

    // Fallback: Agar footer logo nahi mila, tab 'header' logo check karega
    if (!$f_logo_query || mysqli_num_rows($f_logo_query) == 0) {
        $f_logo_query = mysqli_query($conn, "SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    }

    if ($f_logo_query && mysqli_num_rows($f_logo_query) > 0) {
        $f_logo_data = mysqli_fetch_assoc($f_logo_query);
        $footer_logo = 'admin/uploads/' . $f_logo_data['logo_path'];
    }

    $contact_query = mysqli_query($conn, "SELECT * FROM contacts ORDER BY id DESC LIMIT 1");
    if ($contact_query && mysqli_num_rows($contact_query) > 0) {
        $contact_info = mysqli_fetch_assoc($contact_query);

        $c_address = !empty($contact_info['address']) ? $contact_info['address'] : $c_address;
        $c_phone = !empty($contact_info['phone']) ? $contact_info['phone'] : $c_phone;
        $c_email = !empty($contact_info['contact_email']) ? $contact_info['contact_email'] : (!empty($contact_info['email']) ? $contact_info['email'] : $c_email);

        $c_fb = !empty($contact_info['facebook']) ? $contact_info['facebook'] : $c_fb;
        $c_linkedin = !empty($contact_info['linkdin']) ? $contact_info['linkdin'] : $c_linkedin;
        $c_instagram = !empty($contact_info['instagram']) ? $contact_info['instagram'] : '$c_instagram';
        $c_wp = !empty($contact_info['wp_number']) ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $contact_info['wp_number']) : $c_wp;
    }

    $about_query = mysqli_query($conn, "SELECT content FROM about_us ORDER BY id ASC LIMIT 1");
    if ($about_query && mysqli_num_rows($about_query) > 0) {
        $about_data = mysqli_fetch_assoc($about_query);
        if (!empty($about_data['content'])) {
            $clean_text = strip_tags($about_data['content']);
            $footer_about_text = strlen($clean_text) > 120 ? substr($clean_text, 0, 120) . '...' : $clean_text;
        }
    }

    $footer_products = mysqli_query($conn, "SELECT id, pro_name, slug_url FROM products WHERE status = 1 ORDER BY id DESC LIMIT 5");
}
?>

<footer class="custom-footer pt-5">
    <div class="container pb-5">
        <div class="row g-4">

            <!-- Column 1: About & Logo -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="footer-brand bg-white d-inline-block p-2 mb-4 shadow-sm" style="border-radius: 8px;">
                    <img src="<?= htmlspecialchars($footer_logo); ?>" alt="Bhagirath Enterprise" style="width: 130px; height: auto; object-fit: contain;" onerror="this.src='assets/images/logo/logo.png'">
                </div>
                <h4 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing: 0.5px; font-size: 1.1rem;">BHAGIRATH ENTERPRISE</h4>
                <!-- Dynamic Company Content -->
                <p class="footer-text mb-4">
                    <?= htmlspecialchars($footer_about_text); ?>
                </p>

                <!-- Verified Supplier Badge -->
                <span class="badge" style="background-color: #711b3c; font-weight: 500; font-size: 13px; padding: 8px 15px; letter-spacing: 0.5px;">
                    <i class="bi bi-shield-check me-2"></i> Verified Exporter
                </span>
            </div>

            <!-- Column 2: Information Links -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h4 class="footer-heading">Information</h4>
                <ul class="footer-links list-unstyled">
                    <li><a href="index.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Home</a></li>
                    <li><a href="about.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Company Profile</a></li>
                    <li><a href="contact.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Contact Us</a></li>
                    <li><a href="terms-condition.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Terms & Conditions</a></li>
                    <li><a href="privacy-policy.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Privacy Policy</a></li>
                    <li><a href="shipping-return.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Shipping & Returns</a></li>
                    <li><a href="refund-policy.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Refund & Cancellation</a></li>
                </ul>
            </div>

            <!-- Column 3: Dynamic Products -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h4 class="footer-heading">Our Products</h4>
                <ul class="footer-links list-unstyled">
                    <?php
                    if (isset($footer_products) && mysqli_num_rows($footer_products) > 0) {
                        while ($f_prod = mysqli_fetch_assoc($footer_products)) {
                            $prod_slug = !empty($f_prod['id']) ? $f_prod['id'] : $f_prod['id'];
                    ?>
                            <li>
                                <a href="product-details.php?id=<?= htmlspecialchars($prod_slug); ?>">
                                    <i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> <?= htmlspecialchars($f_prod['pro_name']); ?>
                                </a>
                            </li>
                        <?php
                        }
                    } else {
                        ?>
                        <li><a href="products.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Whole Spices</a></li>
                        <li><a href="products.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Dry Fruits</a></li>
                        <li><a href="products.php"><i class="bi bi-chevron-right small me-2" style="color: #711b3c;"></i> Premium Nuts</a></li>
                    <?php } ?>
                </ul>
            </div>

            <!-- Column 4: Contact Details -->
            <div class="col-lg-3 col-md-6">
                <h4 class="footer-heading">Contact Details</h4>
                <ul class="list-unstyled">
                    <li class="d-flex align-items-start mb-3">
                        <div class="footer-contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <span class="footer-text mt-1">
                            <?= htmlspecialchars($c_address); ?>
                        </span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <div class="footer-contact-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <span>
                            <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $c_phone)); ?>" class="footer-text text-decoration-none">
                                <?= htmlspecialchars($c_phone); ?>
                            </a>
                        </span>
                    </li>
                    <li class="d-flex align-items-center mb-4">
                        <div class="footer-contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <span>
                            <a href="mailto:<?= htmlspecialchars($c_email); ?>" class="footer-text text-decoration-none" style="word-break: break-all;">
                                <?= htmlspecialchars($c_email); ?>
                            </a>
                        </span>
                    </li>
                </ul>

                <!-- Social Media Icons -->
                <div class="footer-social d-flex gap-3">
                    <?php if ($c_fb != '#'): ?>
                        <a href="<?= htmlspecialchars($c_fb); ?>" target="_blank" style="background-color: #3b5998;">
                            <i class="bi bi-facebook"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($c_instagram != '#'): ?>
                        <a href="<?= htmlspecialchars($c_instagram); ?>" target="_blank" style="background-color: #e1306c;">
                            <i class="bi bi-instagram"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($c_linkedin != '#'): ?>
                        <a href="<?= htmlspecialchars($c_linkedin); ?>" target="_blank" style="background-color: #007bb5;">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($c_wp != '#'): ?>
                        <a href="<?= htmlspecialchars($c_wp); ?>" target="_blank" style="background-color: #25D366;">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Copyright & Developer Info -->
    <div class="py-4 mt-2" style="background-color: #0a0a0a; border-top: 1px solid #222;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0" style="color: #888; font-size: 0.9rem;">
                    &copy; <?= date('Y'); ?> <strong class="text-white">Bhagirath Enterprise</strong>. All rights reserved.
                </div>
                <div class="col-md-6 text-center text-md-end" style="color: #888; font-size: 0.9rem;">
                    Powered by <a href="https://digitalwebtrackers.com" target="_blank" class="text-decoration-none" style="color: #df6e97; font-weight: 600; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#df6e97'">digitalwebtrackers.com</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Floating Contact Buttons -->
<div class="floating-contact">

    <!-- Phone Call Floating Button -->
    <?php if (!empty($c_phone) && $c_phone != '#'): ?>
        <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $c_phone)); ?>" class="float-btn float-phone shadow-lg" title="Call Us">
            <i class="bi bi-telephone-fill"></i>
        </a>
    <?php endif; ?>
    <!-- WhatsApp Floating Button -->

    <?php if (!empty($c_wp) && $c_wp != '#'): ?>
        <a href="<?= htmlspecialchars($c_wp); ?>" target="_blank" class="float-btn float-whatsapp shadow-lg" title="Chat on WhatsApp">
            <i class="bi bi-whatsapp"></i>
        </a>
    <?php endif; ?>

</div>