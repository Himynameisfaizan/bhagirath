<?php
include 'config/connect.php';
$pageTitle = "Our Products";

// Fetch Global Contact Info for Call Buttons
$contactQuery = mysqli_query($conn, "SELECT phone FROM contacts LIMIT 1");
$contactInfo = mysqli_fetch_assoc($contactQuery);
$sitePhone = !empty($contactInfo['phone']) ? $contactInfo['phone'] : '+919717179432';

// ==========================================
// 1. FILTER, SEARCH & SORTING LOGIC
// ==========================================
$whereClause = "WHERE status = 1";
$urlParams = []; // Pagination me purane filters yaad rakhne ke liye

// Category Filter
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $cat_id = mysqli_real_escape_string($conn, $_GET['category']);
    $whereClause .= " AND pro_cate = '$cat_id'";
    $urlParams[] = "category=$cat_id";
}

// Search Filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $whereClause .= " AND pro_name LIKE '%$search%'";
    $urlParams[] = "search=" . urlencode($search);
}

// Sorting Logic
$orderBy = "ORDER BY id DESC"; // Default (Latest)
if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
    $urlParams[] = "sort=$sort";
    if ($sort == 'name_asc') {
        $orderBy = "ORDER BY pro_name ASC";
    } elseif ($sort == 'name_desc') {
        $orderBy = "ORDER BY pro_name DESC";
    } elseif ($sort == 'oldest') {
        $orderBy = "ORDER BY id ASC";
    }
}

// ==========================================
// 2. PAGINATION LOGIC
// ==========================================
$limit = 9; // Ek page par 9 products dikhayenge (3x3 grid)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total Products Count
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM products $whereClause");
$totalRow = mysqli_fetch_assoc($totalQuery);
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Build URL string for pagination links
$queryString = !empty($urlParams) ? "&" . implode("&", $urlParams) : "";

// Fetch Final Data
$productsQuery = mysqli_query($conn, "SELECT * FROM products $whereClause $orderBy LIMIT $limit OFFSET $offset");

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<section class="products-page-section">
    <div class="products-page-container">
        <div class="row">

            <!-- ==============================
                 LEFT SIDEBAR (Filters & Categories)
                 ============================== -->
            <div class="col-lg-3 mb-5 mb-lg-0 reveal">
                <div class="catalog-sidebar">
                    
                    <!-- Search Widget -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Search Products</h4>
                        <form class="sidebar-search" action="products.php" method="GET">
                            <!-- Preserve category if searching within category -->
                            <?php if(isset($_GET['category'])): ?>
                                <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">
                            <?php endif; ?>
                            <input type="text" name="search" placeholder="Type product name..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <!-- Categories Widget -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">All Categories</h4>
                        <ul class="cat-list">
                          
                            <?php 
                            // Fetch active categories from database
                            $catQuery = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1");
                            while($cat = mysqli_fetch_assoc($catQuery)): 
                                // Count products in this category
                                $countQ = mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE pro_cate='".$cat['cate_id']."' AND status=1");
                                $pCount = mysqli_fetch_assoc($countQ)['c'];
                                
                                $isActive = (isset($_GET['category']) && $_GET['category'] == $cat['cate_id']) ? 'active' : '';
                            ?>
                            <li>
                                <a href="products.php?category=<?php echo $cat['cate_id']; ?>" class="<?php echo $isActive; ?>">
                                    <?php echo $cat['categories']; ?> <span><?php echo $pCount; ?></span>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- Quick Support Banner -->
                    <div class="sidebar-widget text-center" style="background: var(--bg-light); padding: 20px; border-radius: 12px; border: 1px dashed var(--accent-orange);">
                        <i class="fa-solid fa-headset" style="font-size: 30px; color: var(--primary-green); margin-bottom: 10px;"></i>
                        <h5 style="font-weight: 700; margin-bottom: 10px;">Need Help?</h5>
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">Contact our export team for bulk orders.</p>
                        <a href="tel:<?php echo $sitePhone; ?>" style="color: var(--accent-orange); font-weight: 800; text-decoration: none; font-size: 1.1rem;"><?php echo $sitePhone; ?></a>
                    </div>

                </div>
            </div>

            <!-- ==============================
                 RIGHT MAIN CONTENT (Products Grid)
                 ============================== -->
            <div class="col-lg-9">
                
                <!-- Filter & Sorting Bar -->
                <div class="filter-bar reveal">
                    <div class="filter-result-count">
                        <?php 
                        $startItem = ($total_records > 0) ? $offset + 1 : 0;
                        $endItem = min($offset + $limit, $total_records);
                        ?>
                        Showing <span><?php echo $startItem; ?>–<?php echo $endItem; ?></span> of <?php echo $total_records; ?> results
                    </div>
                    
                    <div class="sort-box">
                        <label for="sortBy">Sort by:</label>
                        <select id="sortBy" onchange="window.location.href=this.value;">
                            <?php 
                            $sortBaseUrl = "products.php?";
                            $sortParams = [];
                            if(isset($_GET['category'])) $sortParams[] = "category=".$_GET['category'];
                            if(isset($_GET['search'])) $sortParams[] = "search=".$_GET['search'];
                            if(!empty($sortParams)) $sortBaseUrl .= implode("&", $sortParams) . "&";
                            ?>
                            <option value="<?php echo $sortBaseUrl; ?>sort=latest" <?php echo (!isset($_GET['sort']) || $_GET['sort']=='latest') ? 'selected' : ''; ?>>Latest Products</option>
                            <option value="<?php echo $sortBaseUrl; ?>sort=oldest" <?php echo (isset($_GET['sort']) && $_GET['sort']=='oldest') ? 'selected' : ''; ?>>Oldest Products</option>
                            <option value="<?php echo $sortBaseUrl; ?>sort=name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort']=='name_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
                            <option value="<?php echo $sortBaseUrl; ?>sort=name_desc" <?php echo (isset($_GET['sort']) && $_GET['sort']=='name_desc') ? 'selected' : ''; ?>>Name: Z to A</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row g-4">
                    <?php
                    if($total_records > 0) {
                        while ($product = mysqli_fetch_assoc($productsQuery)):
                    ?>
                        <!-- Product Item (Now col-lg-4 inside the 9-col wrapper) -->
                        <div class="col-lg-4 col-md-6 col-12 reveal">
                            <div class="product-card">
                                <a href="product-details.php?id=<?php echo $product['id']; ?>" class="product-img-link">
                                    <div class="product-img-wrapper">
                                        <img src="admin/assets/img/uploads/<?php echo $product['pro_img']; ?>" alt="<?php echo $product['pro_name']; ?>">
                                        <div class="view-details-overlay">
                                            <span>View Details</span>
                                        </div>
                                    </div>
                                </a>

                                <div class="product-content">
                                    <a href="product-details.php?id=<?php echo $product['id']; ?>" style="text-decoration: none;">
                                        <h3 class="product-title"><?php echo $product['pro_name']; ?></h3>
                                    </a>

                                    <div class="product-actions">
                                        <a href="contact.php?product=<?php echo urlencode($product['pro_name']); ?>" class="btn-quote-sm">
                                            Request to Quote
                                        </a>
                                        <a href="tel:<?php echo $sitePhone; ?>" class="btn-call-sm" title="Call for Enquiry">
                                            <i class="fa-solid fa-phone"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    } else {
                        // Empty State if no products match the filter
                        echo "
                        <div class='col-12 text-center py-5'>
                            <i class='fa-solid fa-box-open' style='font-size: 50px; color: #ccc; margin-bottom: 15px;'></i>
                            <h3 style='color: var(--text-dark);'>No Products Found</h3>
                            <p style='color: var(--text-muted);'>Try selecting a different category or search term.</p>
                            <a href='products.php' class='btn-quote-sm mt-3' style='display:inline-block; padding: 10px 20px;'>Clear All Filters</a>
                        </div>";
                    }
                    ?>
                </div>

                <!-- Dynamic Pagination -->
                <?php if($total_pages > 1): ?>
                <div class="row reveal mt-5">
                    <div class="col-12">
                        <ul class="k2k-pagination">
                            <!-- Prev Button -->
                            <?php if($page > 1): ?>
                                <li class="prev"><a href="?page=<?php echo ($page-1).$queryString; ?>"><i class="fa-solid fa-arrow-left me-2"></i> Prev</a></li>
                            <?php endif; ?>

                            <!-- Page Numbers -->
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a href="?page=<?php echo $i.$queryString; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next Button -->
                            <?php if($page < $total_pages): ?>
                                <li class="next"><a href="?page=<?php echo ($page+1).$queryString; ?>">Next <i class="fa-solid fa-arrow-right ms-2"></i></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

            </div> <!-- End Main Content Col-9 -->
        </div>
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

<?php include 'includes/footer.php'; ?>