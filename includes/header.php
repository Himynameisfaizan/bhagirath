<?php
/**
 * Shared frontend header for Bhagirath Enterprises.
 * Resolve all shared asset and navigation URLs from the actual PHP project root,
 * so they continue to work on rewritten URLs such as /category/spices.
 */

// The executing PHP file is the real target after an internal mod_rewrite.
$current_script_file = $_SERVER['SCRIPT_FILENAME'] ?? '';
$current_page = basename($current_script_file ?: ($_SERVER['PHP_SELF'] ?? ''));

// Detect the project URL base (e.g. /projects/bhagirath on localhost, or empty on live root).
$document_root_real = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$script_file_real = realpath($current_script_file);
$project_base_path = '';

if (
    $document_root_real !== false &&
    $script_file_real !== false &&
    strpos($script_file_real, $document_root_real) === 0
) {
    $relative_script_path = str_replace('\\', '/', substr($script_file_real, strlen($document_root_real)));
    $project_base_path = rtrim(str_replace('\\', '/', dirname($relative_script_path)), '/.');
    if ($project_base_path === '/') {
        $project_base_path = '';
    }
} else {
    // Fallback for environments where SCRIPT_FILENAME cannot be resolved.
    $script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $project_base_path = rtrim(str_replace('\\', '/', dirname($script_name)), '/.');
    if ($project_base_path === '/') {
        $project_base_path = '';
    }
}

$base_url = ($project_base_path === '' ? '' : $project_base_path) . '/';
$home_url = $base_url;

// Reusable safe URL helper for paths inside this project.
$project_url = static function (string $path) use ($base_url): string {
    return $base_url . ltrim(str_replace('\\', '/', $path), '/');
};

/**
 * Convert a path stored in the database or written as a relative path into
 * a URL rooted at this PHP project. Use this for product/category/blog images.
 * Full external URLs and data URLs are returned unchanged.
 */
$image_url = static function ($stored_path) use ($project_url, $base_url): string {
    $path = trim((string) $stored_path);

    if ($path === '') {
        return '';
    }

    // Preserve external, protocol-relative and embedded image URLs.
    if (preg_match('~^(https?:)?//|^data:~i', $path)) {
        return $path;
    }

    $path = str_replace('\\', '/', $path);
    $path = preg_replace('~^(?:\./|\.\./)+~', '', $path);
    $path = ltrim($path, '/');

    // A database value may already contain the localhost project prefix.
    $base_without_slashes = trim($base_url, '/');
    if ($base_without_slashes !== '' && strpos($path, $base_without_slashes . '/') === 0) {
        return '/' . $path;
    }

    return $project_url($path);
};

/**
 * Logo records in the logos table usually store only the uploaded filename.
 * Accept either that filename or a path that already starts with admin/uploads/.
 */
$upload_url = static function ($stored_path) use ($image_url): string {
    $path = trim((string) $stored_path);
    if ($path === '') {
        return '';
    }
    if (preg_match('~^(https?:)?//|^data:~i', $path)) {
        return $path;
    }
    $path = ltrim(str_replace('\\', '/', $path), '/');
    if (strpos($path, 'admin/uploads/') !== 0) {
        $path = 'admin/uploads/' . $path;
    }
    return $image_url($path);
};

if (!isset($pageTitle)) {
    $pageTitle = 'Bhagirath Enterprise | Premium Agricultural Exports';
}
if (!isset($meta_description)) {
    $meta_description = 'Bhagirath Enterprise is a trusted global exporter of premium quality dry fruits, whole spices, and authentic Indian agricultural products.';
}
if (!isset($meta_keywords)) {
    $meta_keywords = 'Bhagirath Enterprise, agricultural exports, Indian spices, dry fruits exporter, wholesale spices';
}

$header_logo = $image_url('assets/images/logo/logo.png');
$favicon = $image_url('assets/images/logo/favicon.png');

$t_phone = '+91-8448211202';
$t_email = 'bhagirathenterprise7@gmail.com';
$t_fb = '#';
$t_linkedin = '#';
$t_wp = '#';

$cats_dropdown_query = false;

if (isset($conn) && $conn instanceof mysqli) {
    $logo_query = mysqli_query(
        $conn,
        "SELECT logo_path FROM logos WHERE location = 'header' AND is_active = 1 ORDER BY id DESC LIMIT 1"
    );
    if ($logo_query && mysqli_num_rows($logo_query) > 0) {
        $logo_data = mysqli_fetch_assoc($logo_query);
        if (!empty($logo_data['logo_path'])) {
            $header_logo = $upload_url($logo_data['logo_path']);
        }
    }

    $fav_query = mysqli_query(
        $conn,
        "SELECT logo_path FROM logos WHERE location = 'favicon' AND is_active = 1 ORDER BY id DESC LIMIT 1"
    );
    if ($fav_query && mysqli_num_rows($fav_query) > 0) {
        $fav_data = mysqli_fetch_assoc($fav_query);
        if (!empty($fav_data['logo_path'])) {
            $favicon = $upload_url($fav_data['logo_path']);
        }
    }

    $contact_query = mysqli_query($conn, 'SELECT * FROM contacts ORDER BY id DESC LIMIT 1');
    if ($contact_query && mysqli_num_rows($contact_query) > 0) {
        $c_info = mysqli_fetch_assoc($contact_query);
        $t_phone = !empty($c_info['phone']) ? $c_info['phone'] : $t_phone;
        $t_email = !empty($c_info['contact_email'])
            ? $c_info['contact_email']
            : (!empty($c_info['email']) ? $c_info['email'] : $t_email);
        $t_fb = !empty($c_info['facebook']) ? $c_info['facebook'] : $t_fb;
        $t_linkedin = !empty($c_info['linkdin']) ? $c_info['linkdin'] : $t_linkedin;
        $t_wp = !empty($c_info['wp_number'])
            ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $c_info['wp_number'])
            : $t_wp;
    }

    $cats_dropdown_query = mysqli_query(
        $conn,
        'SELECT * FROM categories WHERE status = 1 ORDER BY categories ASC'
    );
}

// Identify the current category slug for the active dropdown state.
$current_slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';

// Generate canonical URL from the visible request path.
$is_https = (
    !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off'
) || (
    isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443
);
$protocol = $is_https ? 'https://' : 'http://';
$domain = $_SERVER['HTTP_HOST'] ?? 'bhagirathenterprises.co.in';
$request_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($current_page === 'index.php' || $current_page === '') {
    $request_path = $base_url;
} elseif ($request_path === '/') {
    $request_path = $base_url;
}

$canonical_url = $protocol . $domain . $request_path;
$full_base_url = $protocol . $domain . $base_url;

$is_home_page = ($current_page === 'index.php' || $current_page === '');
$is_about_page = ($current_page === 'about.php');
$is_contact_page = ($current_page === 'contact.php');
$is_category_page = ($current_page === 'category.php');
$is_blog_page = in_array($current_page, ['blog.php', 'blog-details.php'], true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Base URL ensures all relative images/links in product, blog, and category pages resolve from root -->
    <base href="<?= htmlspecialchars($full_base_url, ENT_QUOTES, 'UTF-8'); ?>">

    <title><?= htmlspecialchars(!empty($meta_title) ? $meta_title : $pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8'); ?>">

    <meta name="google-site-verification" content="eX_sXjETkL7O-emXnSDL6-LirHz1VsbiMIZMyFqgvIw">

    <link rel="icon" href="<?= htmlspecialchars($favicon, ENT_QUOTES, 'UTF-8'); ?>" type="image/png">
    <link rel="icon" href="<?= htmlspecialchars($project_url('favicon.ico'), ENT_QUOTES, 'UTF-8'); ?>" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?= htmlspecialchars($project_url('favicon-32x32.png'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="icon" type="image/png" sizes="16x16"
        href="<?= htmlspecialchars($project_url('favicon-16x16.png'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="apple-touch-icon"
        href="<?= htmlspecialchars($project_url('apple-touch-icon.png'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="manifest" href="<?= htmlspecialchars($project_url('site.webmanifest'), ENT_QUOTES, 'UTF-8'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet"
        href="<?= htmlspecialchars($project_url('assets/style/include.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">
    <link rel="stylesheet"
        href="<?= htmlspecialchars($project_url('assets/style/style.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">
    <link rel="stylesheet"
        href="<?= htmlspecialchars($project_url('assets/style/about.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">
    <link rel="stylesheet"
        href="<?= htmlspecialchars($project_url('assets/style/blog.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">
    <link rel="stylesheet"
        href="<?= htmlspecialchars($project_url('assets/style/contact.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">
    <link rel="stylesheet"
        href="<?= htmlspecialchars($project_url('assets/style/gallery.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">
    <link rel="stylesheet"
        href="<?= htmlspecialchars($project_url('assets/style/product.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3JGVQX47GN"></script>
    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-PJCRLJB3');
    </script>

    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-3JGVQX47GN');
    </script>

    <?php if ($is_home_page) { ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Bhagirath Enterprise",
        "url": "https://bhagirathenterprises.co.in/",
        "logo": "https://bhagirathenterprises.co.in/assets/images/logo/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+91-8448211202",
            "contactType": "customer service",
            "areaServed": "IN",
            "availableLanguage": ["en", "hi"]
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Office No-102, 1st Floor, Nitika Tower II, Block C-1, Pocket-4, Azadpur",
            "addressLocality": "Delhi",
            "postalCode": "110033",
            "addressCountry": "IN"
        },
        "sameAs": [
            "https://www.facebook.com",
            "https://www.linkedin.com"
        ]
    }
    </script>
    <?php } ?>

    <?php
    if (isset($page_schema) && is_string($page_schema) && trim($page_schema) !== '') {
        echo $page_schema;
    }
    ?>
</head>

<body>
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PJCRLJB3" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
    </noscript>

    <div class="topbar d-none d-lg-block shadow-sm">
        <div class="container">
            <div class="row align-items-center py-2">
                <div class="col-md-7 topbar-left">
                    <a href="mailto:<?= htmlspecialchars($t_email, ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="bi bi-envelope-fill me-1"></i> <?= htmlspecialchars($t_email, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <span class="mx-2 text-white-50">|</span>
                    <a
                        href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $t_phone), ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="bi bi-telephone-fill me-1"></i>
                        <?= htmlspecialchars($t_phone, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>
                <div class="col-md-5 text-end topbar-right">
                    <span class="me-2 text-white-50 small">Follow Us:</span>
                    <?php if ($t_fb !== '#') { ?>
                    <a href="<?= htmlspecialchars($t_fb, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"
                        rel="noopener noreferrer"><i class="bi bi-facebook"></i></a>
                    <?php } ?>
                    <?php if ($t_linkedin !== '#') { ?>
                    <a href="<?= htmlspecialchars($t_linkedin, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"
                        rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
                    <?php } ?>
                    <?php if ($t_wp !== '#') { ?>
                    <a href="<?= htmlspecialchars($t_wp, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"
                        rel="noopener noreferrer"><i class="bi bi-whatsapp"></i></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg custom-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center"
                href="<?= htmlspecialchars($home_url, ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 70vw;">
                <img src="<?= htmlspecialchars($header_logo, ENT_QUOTES, 'UTF-8'); ?>" alt="Bhagirath Enterprise Logo"
                    class="logo-animate img-fluid" style="max-height: 65px; object-fit: contain;"
                    onerror="this.onerror=null;this.src='<?= htmlspecialchars($image_url('assets/images/logo/logo.png'), ENT_QUOTES, 'UTF-8'); ?>'">
            </a>

            <button class="navbar-toggler shadow-none border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="mainNav">
                <ul class="navbar-nav align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link <?= $is_home_page ? 'active' : ''; ?>"
                            href="<?= htmlspecialchars($home_url, ENT_QUOTES, 'UTF-8'); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $is_about_page ? 'active' : ''; ?>"
                            href="<?= htmlspecialchars($project_url('about'), ENT_QUOTES, 'UTF-8'); ?>">About Us</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $is_category_page ? 'active' : ''; ?>"
                            href="javascript:void(0);" id="categoriesDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu border-0 shadow-lg" aria-labelledby="categoriesDropdown">
                            <?php if ($cats_dropdown_query && mysqli_num_rows($cats_dropdown_query) > 0) { ?>
                            <?php while ($cat = mysqli_fetch_assoc($cats_dropdown_query)) {
                                    $category_slug = (string) ($cat['slug_url'] ?? '');
                                    $is_active_category = ($current_slug !== '' && $current_slug === $category_slug);
                                    $category_url = $project_url('category/' . rawurlencode($category_slug));
                                ?>
                            <li>
                                <a class="dropdown-item <?= $is_active_category ? 'active-dropdown-item' : ''; ?>"
                                    href="<?= htmlspecialchars($category_url, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($cat['categories'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <?php } ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $is_blog_page ? 'active' : ''; ?>"
                            href="<?= htmlspecialchars($project_url('blog'), ENT_QUOTES, 'UTF-8'); ?>">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $is_contact_page ? 'active' : ''; ?>"
                            href="<?= htmlspecialchars($project_url('contact'), ENT_QUOTES, 'UTF-8'); ?>">Contact Us</a>
                    </li>
                    <li class="nav-item ms-lg-4 mt-3 mt-lg-0 mb-3 mb-lg-0">
                        <a class="btn btn-quote"
                            href="<?= htmlspecialchars($project_url('contact'), ENT_QUOTES, 'UTF-8'); ?>">Get a
                            Quote</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>