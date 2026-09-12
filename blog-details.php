<?php
// Database connection include karein
include('config/connect.php');

// URL se slug fetch karein
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';

// Database se specific blog fetch karein
$blogQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE slug = '$slug' AND status = 1");
$blog = mysqli_fetch_assoc($blogQuery);

// Agar blog nahi milta (invalid slug) toh wapas blog page par bhej dein
if (!$blog) {
    echo "<script>window.location.href='blog.php';</script>";
    exit;
}

// Dynamic Variables Setup
$pageTitle = $blog['title'];
$publishDate = date('F d, Y', strtotime($blog['created_at']));
$authorName = !empty($blog['author']) ? $blog['author'] : 'Admin Team';
$mainImage = !empty($blog['image']) ? 'admin/assets/img/uploads/blogs/' . $blog['image'] : 'https://images.unsplash.com/photo-1606914501449-5a96b6ce24ca?q=80&w=1200';

// Current Page URL for Social Sharing
$currentURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<section class="single-blog-section">
    <div class="container">
        <div class="row">

            <!-- Main Content Area -->
            <div class="col-lg-8 pe-lg-5">
                <div class="blog-details-content">

                    <img src="<?php echo $mainImage; ?>" alt="<?php echo $blog['title']; ?>">

                    <div class="blog-meta-top">
                        <span><i class="fa-regular fa-calendar-days"></i> <?php echo $publishDate; ?></span>
                        <span><i class="fa-regular fa-user"></i> By <?php echo $authorName; ?></span>
                        <span><i class="fa-regular fa-folder-open"></i> News & Insights</span>
                    </div>

                    <h1><?php echo $blog['title']; ?></h1>

                    <div class="blog-description py-4">
                        <?php echo $blog['description']; ?>
                    </div>

                    <!-- Share Options -->
                    <div class="share-box">
                        <span>Share this article:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentURL); ?>" target="_blank" class="share-btn bg-fb"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($currentURL); ?>&text=<?php echo urlencode($blog['title']); ?>" target="_blank" class="share-btn bg-tw"><i class="fa-brands fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($currentURL); ?>" target="_blank" class="share-btn bg-in"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($blog['title'] . " " . $currentURL); ?>" target="_blank" class="share-btn bg-wa"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>

                </div>
            </div>

            <!-- Sidebar Area -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="blog-sidebar">

                    <!-- Search Widget -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Search</h4>
                        <form class="sidebar-search" action="blog.php" method="GET">
                            <input type="text" name="search" placeholder="Search insights...">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <!-- Categories Widget -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Categories</h4>
                        <ul class="sidebar-cats">
                            <li><a href="blog.php">Export Trends <span>(12)</span></a></li>
                            <li><a href="blog.php">Farming Practices <span>(08)</span></a></li>
                            <li><a href="blog.php">Health Benefits <span>(15)</span></a></li>
                            <li><a href="blog.php">Quality & Testing <span>(05)</span></a></li>
                            <li><a href="blog.php">Company News <span>(03)</span></a></li>
                        </ul>
                    </div>

                    <!-- Dynamic Recent Posts Widget -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Recent Posts</h4>

                        <?php
                        // Fetch 3 Recent Blogs excluding the current one
                        $recentQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 1 AND blog_id != '{$blog['blog_id']}' ORDER BY created_at DESC LIMIT 3");

                        if (mysqli_num_rows($recentQuery) > 0) {
                            while ($recentBlog = mysqli_fetch_assoc($recentQuery)):
                                $r_date = date('M d, Y', strtotime($recentBlog['created_at']));
                                $r_img = !empty($recentBlog['image']) ? 'admin/assets/img/uploads/blogs/' . $recentBlog['image'] : 'https://images.unsplash.com/photo-1615486171448-4228965f7c32?q=80&w=200';
                        ?>
                                <div class="recent-post-item">
                                    <img src="<?php echo $r_img; ?>" alt="<?php echo $recentBlog['title']; ?>">
                                    <div class="recent-post-info">
                                        <h4><a href="blog-details.php?slug=<?php echo $recentBlog['slug']; ?>"><?php echo $recentBlog['title']; ?></a></h4>
                                        <span><?php echo $r_date; ?></span>
                                    </div>
                                </div>
                        <?php
                            endwhile;
                        } else {
                            echo "<p style='color: #666; font-size: 13px;'>No recent posts available.</p>";
                        }
                        ?>
                    </div>

                    <!-- CTA Widget -->
                    <div class="sidebar-widget text-center" style="background: var(--primary-green); color: white;">
                        <i class="fa-solid fa-box-open" style="font-size: 40px; color: var(--accent-orange); margin-bottom: 15px;"></i>
                        <h4 style="font-weight: 800; margin-bottom: 15px;">Looking for Bulk Spices?</h4>
                        <p style="font-size: 0.95rem; opacity: 0.9; margin-bottom: 20px;">Get a free quotation for your international export requirements today.</p>
                        <a href="contact.php" class="btn-theme" style="background: var(--accent-orange); color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none; font-weight: 700; display: inline-block;">Request Quote</a>
                    </div>

                </div>
            </div>

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

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>