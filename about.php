<?php include 'includes/header.php'; ?>
<div class="page-content">
    <!-- About Section -->
    <div class="container mx-auto px-5 lg:px-20 py-20">
        <h1 class="text-4xl font-bold mb-10">About <?php echo $settings['agency_name'] ?? 'Luxit Global Escapes'; ?></h1>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20">
            <div>
                <p class="text-lg text-slate-600 mb-6">
                    We are a premier travel agency dedicated to providing unforgettable experiences.
                </p>
                <!-- Add more about content here -->
            </div>
            <div>
                <img src="assets/images/about/pic1.jpg" alt="About Us" class="rounded-3xl shadow-xl">
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
