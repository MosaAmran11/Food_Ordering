<?php
if (isset($message)) {
   if (is_array($message)) {
      foreach ($message as $msg) {
         echo '
         <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   } else {
      echo '
      <div class="message">
         <span>' . htmlspecialchars($msg) . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">Food Order 😋</a>

      <nav class="navbar">
         <a href="home.php">الصفحةالرئيسية</a>
         <a href="about.php">عنا</a>
         <a href="menu.php">قائمة الطعام</a>
         <a href="orders.php">الطلبات</a>
         <a href="contact.php">اتصل بنا</a>
      </nav>

      <div class="icons">
         <?php
         $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $count_cart_items->execute([$user_id]);
         $total_cart_items = $count_cart_items->rowCount();
         ?>
         <a href="search.php"><i class="fas fa-search"></i></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_items; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="menu-btn" class="fas fa-bars"></div>
      </div>

      <div class="profile">
         <?php
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_profile->execute([$user_id]);
         if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
            <p class="name"><?= $fetch_profile['name']; ?></p>
            <div class="flex">
               <a href="profile.php" class="btn">الملف الشخصي</a>
               <a href="components/user_logout.php" onclick="return confirm('هل أنت متأكد من تسجيل الخروج؟');" class="delete-btn">تسجيل الخروج</a>
            </div>
            <p class="account">
               <a href="login.php">تسجيل الدخول</a> او
               <a href="register.php">انشاء حساب</a>
            </p>
         <?php
         } else {
         ?>
            <!-- <p class="name">الرجاء تسجيل الدخول أولا!</p> -->
            <a href="register.php" class="btn">انشاء حساب</a>
            <a href="login.php" class="btn">تسجيل الدخول</a>
         <?php
         }
         ?>
      </div>

   </section>

</header>