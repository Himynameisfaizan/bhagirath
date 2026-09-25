<?php
include ('config/connect.php'); 

$pageTitle = "Blogs"; 
include 'includes/header.php';
include 'includes/breadcrumb.php';

$limit = 6; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM blogs WHERE status = 1");
$totalRow = mysqli_fetch_assoc($totalQuery);
$total_blogs = $totalRow['total'];

$grid_total_records = max(0, $total_blogs - 1);
$total_pages = ceil($grid_total_records / $limit);

$featuredQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 1 ORDER BY created_at DESC LIMIT 1");
$featuredBlog = mysqli_fetch_assoc($featuredQuery);

$gridQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 1 ORDER BY created_at DESC");

$currentPage = basename($_SERVER['PHP_SELF']);
$seo_query = mysqli_query($conn, "SELECT meta_title, meta_key, meta_desc FROM meta WHERE page_url = '$currentPage'");

if ($seo_query && mysqli_num_rows($seo_query) > 0) {
    $seo_data = mysqli_fetch_assoc($seo_query);
    // Ye variables header.php catch kar lega
    $pageTitle = $seo_data['meta_title'];
    $meta_keywords = $seo_data['meta_key'];
    $meta_description = $seo_data['meta_desc'];
}
?>

    
<section class="blog-page-section">
    <div class="container">

        <!-- BLOG GRID -->
        <div class="row g-4 mt-2">
            <?php 
            if(mysqli_num_rows($gridQuery) > 0):
                while($blog = mysqli_fetch_assoc($gridQuery)): 
                    $date = date('M d, Y', strtotime($blog['created_at']));
                    $excerpt = mb_substr(strip_tags($blog['description']), 0, 100) . '...';
                    $img = !empty($blog['image']) ? 'admin/assets/img/uploads/blogs/' . $blog['image'] : 'https://images.unsplash.com/photo-1615486171448-4228965f7c32?q=80&w=800';
            ?>
            <div class="col-lg-4 col-md-6 reveal">
                <div class="blog-card">
                    <div class="blog-img-wrapper">
                        <span class="featured-category" style="top: 15px; left: 15px; font-size: 10px; padding: 4px 12px;">News</span>
                        <a href="blog-details.php?slug=<?php echo $blog['slug']; ?>">
                            <img src="<?php echo $img; ?>" alt="<?php echo $blog['title']; ?>">
                        </a>
                    </div>
                    <div class="blog-content">
                        <div class="featured-meta" style="font-size: 13px;">
                            <i class="fa-regular fa-calendar-days"></i> <?php echo $date; ?>
                            <i class="fa-regular fa-user"></i> <?php echo $blog['author']; ?>
                        </div>
                        <a href="blog-details.php?slug=<?php echo $blog['slug']; ?>" class="blog-title"><?php echo $blog['title']; ?></a>
                        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;"><?php echo $excerpt; ?></p>
                        <a href="blog-details.php?slug=<?php echo $blog['slug']; ?>" class="read-more-btn">Read More <i class="fa-solid fa-arrow-right-long"></i></a>
                    </div>
                </div>
            </div>
            <?php 
                endwhile; 
            else:
                // Show this if no other blogs exist
                if(!$featuredBlog) {
                    echo "<div class='col-12 text-center py-5'><h3 style='color: var(--text-muted);'>No Articles Found</h3></div>";
                }
            endif;
            ?>
        </div>

        <!-- DYNAMIC PAGINATION -->
        <?php if($total_pages > 1): ?>
        <div class="row reveal mt-5">
            <div class="col-12">
                <ul class="k2k-pagination">
                    <!-- Prev Button -->
                    <?php if($page > 1): ?>
                        <li class="prev"><a href="?page=<?php echo ($page-1); ?>"><i class="fa-solid fa-arrow-left me-2"></i> Prev</a></li>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <?php if($page < $total_pages): ?>
                        <li class="next"><a href="?page=<?php echo ($page+1); ?>">Next <i class="fa-solid fa-arrow-right ms-2"></i></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Scroll Animation Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const reveals = document.querySelectorAll(".reveal");
        const revealOnScroll = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        reveals.forEach(reveal => revealOnScroll.observe(reveal));
    });
</script>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>