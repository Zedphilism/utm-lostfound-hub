<?php // includes/nav.php ?>
<header class="relative h-24 md:h-32 lg:h-40">
  <!-- Background image -->
  <div
    class="absolute inset-0 bg-cover bg-center"
    style="background-image: url('/assets/images/banner.webp');"
  ></div>
  <!-- Blue tint overlay -->
  <div class="absolute inset-0 bg-blue-800 opacity-50"></div>

  <!-- Logo, title, and nav -->
  <nav class="relative z-10">
    <div class="container mx-auto px-4 py-3 flex items-center">
      <!-- Logo (clickable to homepage) -->
      <a href="/index.php" class="flex-none">
        <img
          src="/assets/images/utm-logo.png"
          alt="UTM Logo"
          class="h-16 md:h-20 lg:h-24 w-auto"
        >
      </a>

      <!-- Title -->
      <span
        data-aos="fade-down"
        class="ml-4 text-white text-2xl md:text-3xl lg:text-4xl font-bold"
      >
        UTM SPACE KL — Lost & Found Hub
      </span>

      <!-- Spacer -->
      <div class="flex-grow"></div>

      <!-- Action buttons and links -->
      <div class="flex items-center space-x-4">
        <?php
          // Show “Report an Item” only on public pages
          if (strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false): 
        ?>
          <a
            href="/add.php"
            class="inline-flex items-center whitespace-nowrap bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm md:text-base"
          >
            + Report an Item
          </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
          <a
            href="/admin/index.php"
            class="text-white hover:text-gray-200 text-sm md:text-base"
          >
            Dashboard
          </a>
          <a
            href="/admin/logout.php"
            class="text-white hover:text-gray-200 text-sm md:text-base"
          >
            Logout
          </a>
        <?php else: ?>
          <a
            href="/admin/login.php"
            class="text-white hover:text-gray-200 text-sm md:text-base"
          >
            Admin Login
          </a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</header>

<!-- SVG Wave Divider -->
<div class="relative -mt-1">
  <svg viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg" class="block w-full">
    <path fill="#f3f4f6" d="M0,64L48,74.7C96,85,192,107,288,112C384,117,480,107,576,106.7C672,107,768,117,864,122.7C960,128,1056,128,1152,106.7C1248,85,1344,43,1392,21.3L1440,0L1440,100L1392,100C1344,100,1248,100,1152,100C1056,100,960,100,864,100C768,100,672,100,576,100C480,100,384,100,288,100C192,100,96,100,48,100L0,100Z"/>
  </svg>
</div>
