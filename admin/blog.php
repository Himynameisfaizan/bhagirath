<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "db-conn.php";

$msg = "";
$msg_class = "";

// --- 1. Slug Clean karne ka Backup Function (Backend Safety) ---
function createSlug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($string)));
    return $slug;
}

// --- 2. Blog Add karne ka Logic ---
if (isset($_POST['add_blog'])) {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $author = mysqli_real_escape_string($conn, trim($_POST['author']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    
    // Agar user ne apna custom slug diya hai toh wo use hoga, nahi toh title se banega
    $user_slug = trim($_POST['slug']);
    $slug = !empty($user_slug) ? createSlug($user_slug) : createSlug($title);

    if (!empty($title) && !empty($description)) {
        $image_name = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_extension, $allowed_extensions)) {
                $image_name = 'blog_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                $upload_path = "assets/img/uploads/blogs/";
                
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_path . $image_name);
            } else {
                $msg = "Invalid image format! Only JPG, JPEG, PNG, and WEBP are allowed.";
                $msg_class = "alert-danger";
            }
        }

        if (empty($msg)) {
            $insert_query = "INSERT INTO `blogs` (`title`, `slug`, `author`, `image`, `description`, `status`) 
                             VALUES ('$title', '$slug', '$author', '$image_name', '$description', '$status')";
            
            if (mysqli_query($conn, $insert_query)) {
                $msg = "Blog post published successfully!";
                $msg_class = "alert-success";
            } else {
                $msg = "Database Error: " . mysqli_error($conn);
                $msg_class = "alert-danger";
            }
        }
    }
}

// --- 3. Blog UPDATE karne ka Logic ---
if (isset($_POST['update_blog'])) {
    $blog_id = mysqli_real_escape_string($conn, $_POST['blog_id']);
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $author = mysqli_real_escape_string($conn, trim($_POST['author']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    
    $user_slug = trim($_POST['slug']);
    $slug = !empty($user_slug) ? createSlug($user_slug) : createSlug($title);

    $img_check = mysqli_query($conn, "SELECT `image` FROM `blogs` WHERE `blog_id` = '$blog_id'");
    $current_row = mysqli_fetch_assoc($img_check);
    $image_name = $current_row['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_image = 'blog_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $upload_path = "assets/img/uploads/blogs/";

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path . $new_image)) {
                if (!empty($image_name) && file_exists($upload_path . $image_name)) {
                    unlink($upload_path . $image_name);
                }
                $image_name = $new_image;
            }
        }
    }

    $update_query = "UPDATE `blogs` SET `title` = '$title', `slug` = '$slug', `author` = '$author', `image` = '$image_name', `description` = '$description', `status` = '$status' WHERE `blog_id` = '$blog_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $msg = "Blog post updated successfully!";
        $msg_class = "alert-success";
    } else {
        $msg = "Update Error: " . mysqli_error($conn);
        $msg_class = "alert-danger";
    }
}

// --- 4. Blog Delete karne ka Logic ---
if (isset($_GET['delete_id'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    $img_res = mysqli_query($conn, "SELECT `image` FROM `blogs` WHERE `blog_id` = '$del_id'");
    if (mysqli_num_rows($img_res) > 0) {
        $img_row = mysqli_fetch_assoc($img_res);
        if (!empty($img_row['image']) && file_exists("assets/img/uploads/blogs/" . $img_row['image'])) {
            unlink("assets/img/uploads/blogs/" . $img_row['image']);
        }
    }

    $delete_query = "DELETE FROM `blogs` WHERE `blog_id` = '$del_id'";
    if (mysqli_query($conn, $delete_query)) {
        $msg = "Blog post deleted successfully!";
        $msg_class = "alert-success";
    }
}

// --- 5. Blogs Fetch karna ---
$all_blogs = mysqli_query($conn, "SELECT * FROM `blogs` ORDER BY `blog_id` DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Blogs | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .custom-card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        .blog-thumb { width: 55px; height: 55px; object-fit: cover; border-radius: 5px; }
        .preview-img { max-width: 130px; max-height: 90px; display: none; margin-top: 10px; border-radius: 5px; }
        .modal-preview-img { max-width: 100px; max-height: 70px; border-radius: 5px; margin-top: 5px; }
        .slug-input-prefix { background-color: #f1f3f5; color: #6c757d; font-size: 0.85rem; display: d-flex; align-items: center; padding: 0 10px; border: 1px solid #ced4da; border-right: 0; border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;}
    </style>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0"><div class="row"><div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div></div></div>

        <div class="main_content_iner">
            <div class="container-fluid p-3">
                
                <?php if (!empty($msg)): ?>
                    <div class="alert <?= $msg_class ?> alert-dismissible fade show" role="alert">
                        <?= $msg ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Create Blog Form -->
                    <div class="col-xl-5 col-lg-12 mb-4">
                        <div class="white_card custom-card">
                            <div class="card-header bg-white border-0 pt-3">
                                <h3 class="mb-0 fw-bold">Write New Blog Post</h3>
                            </div>
                            <div class="white_card_body py-3">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Blog Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" id="blog_title_add" placeholder="Enter blog title" required>
                                    </div>

                                    <!-- Custom Slug Input -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Custom Slug (SEO URL) <span class="text-danger">*</span></label>
                                        <div class="d-flex">
                                            <span class="slug-input-prefix">site.com/blog/</span>
                                            <input type="text" class="form-control" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" name="slug" id="blog_slug_add" placeholder="e.g. customized-url-structure" required>
                                        </div>
                                        <small class="text-muted text-small">Sirf letters, numbers aur hyphens (-) allowed hain.</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Author Name</label>
                                            <input type="text" class="form-control" name="author" value="Admin">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="1">Publish (Active)</option>
                                                <option value="0">Draft (Hidden)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Featured Image</label>
                                        <input type="file" class="form-control" name="image" id="imageInput" accept="image/*">
                                        <img id="imagePreview" class="preview-img" src="#" alt="Preview">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Blog Content <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" rows="7" placeholder="Type blog description here..." required></textarea>
                                    </div>
                                    <button type="submit" name="add_blog" class="btn btn-primary w-100"><i class="fas fa-paper-plane me-2"></i>Publish Blog Post</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Blog List View -->
                    <div class="col-xl-7 col-lg-12">
                        <div class="white_card custom-card">
                            <div class="card-header bg-white border-0 pt-3"><h3 class="mb-0 fw-bold">Published Blogs</h3></div>
                            <div class="white_card_body py-3">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Title & URL</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (mysqli_num_rows($all_blogs) > 0): ?>
                                                <?php while($blog = mysqli_fetch_assoc($all_blogs)): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if (!empty($blog['image']) && file_exists("assets/img/uploads/blogs/" . $blog['image'])): ?>
                                                                <img class="blog-thumb" src="assets/img/uploads/blogs/<?= $blog['image']; ?>" alt="blog">
                                                            <?php else: ?>
                                                                <img class="blog-thumb" src="assets/img/uploads/blogs/default-blog.png" alt="default">
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($blog['title']); ?>">
                                                                <?= htmlspecialchars($blog['title']); ?>
                                                            </h6>
                                                            <small class="text-primary d-block text-truncate" style="max-width: 220px;">
                                                                <i class="fas fa-link me-1" style="font-size:0.75rem;"></i><?= htmlspecialchars($blog['slug']); ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?= $blog['status'] == 1 ? 'success' : 'warning'; ?>"><?= $blog['status'] == 1 ? 'Live' : 'Draft'; ?></span>
                                                        </td>
                                                        <td class="text-end">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-primary edit-btn"
                                                                    data-id="<?= $blog['blog_id']; ?>"
                                                                    data-title="<?= htmlspecialchars($blog['title']); ?>"
                                                                    data-slug="<?= htmlspecialchars($blog['slug']); ?>"
                                                                    data-author="<?= htmlspecialchars($blog['author']); ?>"
                                                                    data-status="<?= $blog['status']; ?>"
                                                                    data-desc="<?= htmlspecialchars($blog['description']); ?>"
                                                                    data-img="assets/img/uploads/blogs/<?= $blog['image']; ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="blog.php?delete_id=<?= $blog['blog_id']; ?>" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center py-4 text-muted">No blog posts found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- --- EDIT BLOG MODAL --- -->
    <div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editBlogModalLabel">Update Blog Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="blog_id" id="edit_blog_id">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Blog Title</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>

                        <!-- Edit Slug Control -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Edit Slug (SEO URL)</label>
                            <div class="d-flex">
                                <span class="slug-input-prefix">site.com/blog/</span>
                                <input type="text" class="form-control" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" name="slug" id="edit_slug" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Author Name</label>
                                <input type="text" class="form-control" name="author" id="edit_author">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" name="status" id="edit_status">
                                    <option value="1">Publish (Active)</option>
                                    <option value="0">Draft (Hidden)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Change Image <small class="text-muted">(Leave blank to keep old image)</small></label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <div class="mt-2">
                                <span class="small text-muted d-block">Current Image:</span>
                                <img id="edit_current_img" class="modal-preview-img" src="" alt="current image">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Blog Content</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="6" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_blog" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <script>
        // JS Function string ko clean slug format me convert karne ke liye
        function convertToSlug(text) {
            return text.toLowerCase()
                       .replace(/[^a-z0-9 -]/g, '') // removes invalid chars
                       .replace(/\s+/g, '-')        // replaces spaces with -
                       .replace(/-+/g, '-');        // removes duplicate dashes
        }

        // Add form ke liye automated slug generation trick
        document.getElementById('blog_title_add').addEventListener('input', function() {
            document.getElementById('blog_slug_add').value = convertToSlug(this.value);
        });
        
        // Manual overwrite safety (agar user khud spaces likhe toh real-time dash bane)
        document.getElementById('blog_slug_add').addEventListener('blur', function() {
            this.value = convertToSlug(this.value);
        });
        document.getElementById('edit_slug').addEventListener('blur', function() {
            this.value = convertToSlug(this.value);
        });

        // Form Image Live Preview
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) { preview.src = e.target.result; preview.style.display = 'block'; }
                reader.readAsDataURL(file);
            }
        });

        // Bootstrap Modal Data Populate Script
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const slug = this.getAttribute('data-slug');
                const author = this.getAttribute('data-author');
                const status = this.getAttribute('data-status');
                const desc = this.getAttribute('data-desc');
                const img_src = this.getAttribute('data-img');

                document.getElementById('edit_blog_id').value = id;
                document.getElementById('edit_title').value = title;
                document.getElementById('edit_slug').value = slug;
                document.getElementById('edit_author').value = author;
                document.getElementById('edit_status').value = status;
                document.getElementById('edit_description').value = desc;
                document.getElementById('edit_current_img').src = img_src;

                const editModal = new bootstrap.Modal(document.getElementById('editBlogModal'));
                editModal.show();
            });
        });
    </script>
</body>
</html>