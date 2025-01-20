<?php

include 'components/connect.php';

session_start();

if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
   exit(); // تأكد من إنهاء السكربت بعد إعادة التوجيه
}

// حماية ضد XSS
function sanitize($data)
{
   return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['delete'])) {
   if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
      die("CSRF token validation failed");
   }
   $cart_id = $_POST['cart_id'];
   // تحقق من أن cart_id هو رقم صحيح
   if (filter_var($cart_id, FILTER_VALIDATE_INT)) {
      $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
      $delete_cart_item->execute([$cart_id]);
      $message[] = 'cart item deleted!';
   }
}

if (isset($_POST['delete_all'])) {
   if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
      die("CSRF token validation failed");
   }
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   $message[] = 'deleted all from cart!';
}

if (isset($_POST['update_qty'])) {
   if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
      die("CSRF token validation failed");
   }
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_SPECIAL_CHARS);
   // تحقق من أن qty هو رقم صحيح
   if (filter_var($cart_id, FILTER_VALIDATE_INT) && filter_var($qty, FILTER_VALIDATE_INT)) {
      $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
      $update_qty->execute([$qty, $cart_id]);
      $message[] = 'cart quantity updated';
   }
}

$grand_total = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body dir="rtl">

   <!-- header section starts  -->
   <?php include 'components/user_header.php'; ?>
   <!-- header section ends -->

   <div class="heading">
      <h3>عربة التسوق</h3>
      <p><a href="home.php">الصفحة الرئيسية</a> <span> / عربة التسوق</span></p>
   </div>

   <!-- shopping cart section starts  -->

   <section class="products">

      <h1 class="title">السلة الخاصة بك</h1>

      <div class="box-container">

         <?php
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if ($select_cart->rowCount() > 0) {
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <form action="" method="post" class="box">
                  <input type="hidden" name="cart_id" value="<?= sanitize($fetch_cart['id']); ?>">
                  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                  <a href="quick_view.php?pid=<?= sanitize($fetch_cart['pid']); ?>" class="fas fa-eye"></a>
                  <button type="submit" class="fas fa-times" name="delete" onclick="return confirm('delete this item?');"></button>
                  <img src="uploaded_img/<?= sanitize($fetch_cart['image']); ?>" alt="">
                  <div class="name"><?= sanitize($fetch_cart['name']); ?></div>
                  <div class="flex">
                     <div class="price"><?= sanitize($fetch_cart['price']); ?><span>$دولار</span></div>
                     <input type="number" name="qty" class="qty" min="1" max="99" value="<?= sanitize($fetch_cart['quantity']); ?>" maxlength="2">
                     <button type="submit" class="fas fa-edit" name="update_qty"></button>
                  </div>
                  <div class="sub-total"> السعر الفرعي <span>دولار<?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</span> </div>
               </form>
         <?php
               $grand_total += $sub_total;
            }
         } else {
            echo '<p class="empty">your cart is empty</p>';
         }
         ?>

      </div>

      <div class="cart-total">
         <p>مجموع سعر السلة<span>$<?= sanitize($grand_total); ?></span></p>
         <a href="checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">الانتقال إلى السداد</a>
      </div>

      <div class="more-btn">
         <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <button type="submit" class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>" name="delete_all" onclick="return confirm('delete all from cart?');">حذف الكل من السلة</button>
         </form>
         <a href="menu.php" class="btn">مواصلة التسوق</a>
      </div>

   </section>

   <!-- shopping cart section ends -->

   <!-- footer section starts  -->
   <?php include 'components/footer.php'; ?>
   <!-- footer section ends -->

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>