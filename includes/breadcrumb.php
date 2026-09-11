<?php
$title = isset($pageTitle) ? $pageTitle : 'Kisan To Kitchen';
?>

<section class="breadcrumb-wrapper">
    <div class="container">
        <!-- Dynamic Title yahan print hoga -->
        <h2 class="breadcrumb-title"><?php echo $title; ?></h2>
        
        <ul class="custom-breadcrumb">
            <li><a href="index.php"><i class="fa-solid fa-house"></i> Home</a></li>
            <!-- Dynamic Title current page ke liye -->
            <li class="active"><?php echo $title; ?></li>
        </ul>
    </div>
</section>