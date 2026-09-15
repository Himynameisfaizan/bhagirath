<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "db-conn.php";

$msg = "";
$msg_class = "";

function createSlug($string) {
    return preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($string)));
}

// --- Blog UPDATE karne ka Logic ---
if (isset($_POST['update_blog'])) {
    $blog_id = mysqli_real_escape_string($conn, $_POST['blog_id']);
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $author = mysqli_real_escape_string($conn, trim($_POST['author']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    
    // SEO Fields
    $meta_title = mysqli_real_escape_string($conn, trim($_POST['meta_title']));
    $meta_key = mysqli_real_escape_string($conn, trim($_POST['meta_key']));
    $meta_desc = mysqli_real_escape_string($conn, trim($_POST['meta_desc']));
    
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

    $update_query = "UPDATE `blogs` SET `title` = '$title', `slug` = '$slug', `author` = '$author', `image` = '$image_name', `description` = '$description', `status` = '$status', `meta_title` = '$meta_title', `meta_key` = '$meta_key', `meta_desc` = '$meta_desc' WHERE `blog_id` = '$blog_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $msg = "Blog post updated successfully!";
        $msg_class = "alert-success";
    } else {
        $msg = "Update Error: " . mysqli_error($conn);
        $msg_class = "alert-danger";
    }
}

// --- Blog Delete karne ka Logic ---
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

if (isset($_GET['status']) && $_GET['status'] == 'added') {
    $msg = "New blog post published successfully!";
    $msg_class = "alert-success";
}

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
        .modal-preview-img { max-width: 100px; max-height: 70px; border-radius: 5px; margin-top: 5px; }
        .slug-input-prefix { background-color: #f1f3f5; color: #6c757d; font-size: 0.85rem; display: flex; align-items: center; padding: 0 10px; border: 1px solid #ced4da; border-right: 0; border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;}
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
                    <div class="col-xl-12 col-lg-12">
                        <div class="white_card custom-card">
                            <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0 fw-bold">Published Blogs</h3>
                                <a href="add-blog.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add New Blog</a>
                            </div>
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
                                                            <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($blog['title']); ?>">
                                                                <?= htmlspecialchars($blog['title']); ?>
                                                            </h6>
                                                            <small class="text-primary d-block text-truncate" style="max-width: 300px;">
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
                                                                    data-metatitle="<?= htmlspecialchars($blog['meta_title']); ?>"
                                                                    data-metakey="<?= htmlspecialchars($blog['meta_key']); ?>"
                                                                    data-metadesc="<?= htmlspecialchars($blog['meta_desc']); ?>"
                                                                    data-img="assets/img/uploads/blogs/<?= $blog['image']; ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="blog.php?delete_id=<?= $blog['blog_id']; ?>" onclick="return confirm('Are you sure you want to delete this blog?');" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
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
    <div class="modal fade" id="editBlogModal" aria-labelledby="editBlogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
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

                        <!-- SEO Fields Edit Section -->
                        <div class="col-md-12"><hr class="my-3"><h5 class="fw-bold text-primary mb-3">SEO Meta Configuration</h5></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" id="edit_meta_title">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_key" id="edit_meta_key">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Meta Description</label>
                                <textarea class="form-control" name="meta_desc" id="edit_meta_desc" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12"><hr class="my-3"></div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Change Image <small class="text-muted">(image pixel should be 1536X864)</small></label>
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
    
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
    // --- BOOTSTRAP 4 CKEDITOR FOCUS FIX (Notice the underscore in _enforceFocus) ---
    if (typeof $.fn.modal !== 'undefined') {
        $.fn.modal.Constructor.prototype._enforceFocus = function() {
            // Isko khali chhodne se Bootstrap modal CKEditor ke input ko block nahi karega
        };
    }

    // --- CKEditor Initialization ---
    CKEDITOR.replace('edit_description');

    function convertToSlug(text) {
        return text.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');        
    }

    document.getElementById('edit_slug').addEventListener('blur', function() {
        this.value = convertToSlug(this.value);
    });

    // --- Modal Data Populate Script ---
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            const slug = this.getAttribute('data-slug');
            const author = this.getAttribute('data-author');
            const status = this.getAttribute('data-status');
            const desc = this.getAttribute('data-desc');
            const metaTitle = this.getAttribute('data-metatitle');
            const metaKey = this.getAttribute('data-metakey');
            const metaDesc = this.getAttribute('data-metadesc');
            const img_src = this.getAttribute('data-img');

            document.getElementById('edit_blog_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_slug').value = slug;
            document.getElementById('edit_author').value = author;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_meta_title').value = metaTitle;
            document.getElementById('edit_meta_key').value = metaKey;
            document.getElementById('edit_meta_desc').value = metaDesc;
            document.getElementById('edit_current_img').src = img_src;

            // Update CKEditor Data
            CKEDITOR.instances['edit_description'].setData(desc);

            const editModal = new bootstrap.Modal(document.getElementById('editBlogModal'));
            editModal.show();
        });
    });
</script>
</body>
</html>