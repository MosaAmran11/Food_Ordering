<?php

include 'components/connect.php';

session_start();

// التحقق من استخدام HTTPS
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header('location:home.php');
    exit();
}

if (isset($_POST['submit'])) {
    // تصفية المدخلات
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $method = filter_input(INPUT_POST, 'method', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $total_products = filter_input(INPUT_POST, 'total_products', FILTER_SANITIZE_STRING);
    $total_price = filter_input(INPUT_POST, 'total_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // التحقق من العنوان
    if (empty($address)) {
        $message[] = 'يرجى إضافة عنوانك!';
    } else {
        // التحقق من وجود عناصر في السلة
        $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
        $check_cart->execute([$user_id]);

        if ($check_cart->rowCount() > 0) {
            // إدخال الطلب
            $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
            $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

            // حذف العناصر من السلة
            $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $delete_cart->execute([$user_id]);

            $message[] = 'تم تقديم الطلب بنجاح!';
        } else {
            $message[] = 'سلة التسوق فارغة';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>الدفع</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>الدفع</h3>
   <p><a href="home.php">الصفحة الرئيسية</a> <span> / الدفع</span></p>
</div>

<section class="checkout">

   <h1 class="title">ملخص الطلب</h1>

   <form action="" method="post">

      <div class="cart-items">
         <h3>عناصر السلة</h3>
         <?php
            $grand_total = 0;
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                    $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
                    echo '<p><span class="name">' . htmlspecialchars($fetch_cart['name'], ENT_QUOTES, 'UTF-8') . '</span><span class="price">$' . htmlspecialchars($fetch_cart['price'], ENT_QUOTES, 'UTF-8') . ' x ' . htmlspecialchars($fetch_cart['quantity'], ENT_QUOTES, 'UTF-8') . '</span></p>';
                }
            } else {
                echo '<p class="empty">سلة التسوق فارغة</p>';
            }
         ?>
         <p class="grand-total"><span class="name">المجموع الإجمالي:</span><span class="price">$<?= number_format($grand_total, 2); ?></span></p>
         <a href="cart.php" class="btn">عرض السلة</a>
      </div>

      <input type="hidden" name="total_products" value="<?= htmlspecialchars($total_products, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="total_price" value="<?= htmlspecialchars($grand_total, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_profile['name'], ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="number" value="<?= htmlspecialchars($fetch_profile['number'], ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($fetch_profile['email'], ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="address" value="<?= htmlspecialchars($fetch_profile['address'], ENT_QUOTES, 'UTF-8'); ?>">

      <div class="user-info">
         <h3>المعلومات الخاصة بك</h3>
         <p><i class="fas fa-user"></i><span><?= htmlspecialchars($fetch_profile['name'], ENT_QUOTES, 'UTF-8'); ?></span></p>
         <p><i class="fas fa-phone"></i><span><?= htmlspecialchars($fetch_profile['number'], ENT_QUOTES, 'UTF-8'); ?></span></p>
         <p><i class="fas fa-envelope"></i><span><?= htmlspecialchars($fetch_profile['email'], ENT_QUOTES, 'UTF-8'); ?></span></p>
         <a href="update_profile.php" class="btn">تعديل المعلومات</a>
         <h3>عنوان التسليم الطلب</h3>
         <p><i class="fas fa-map-marker-alt"></i><span><?php echo htmlspecialchars($fetch_profile['address'] ?: 'يرجى إدخال عنوانك', ENT_QUOTES, 'UTF-8'); ?></span></p>
         <a href="update_address.php" class="btn">تعديل العنوان</a>
         <select name="method" class="box" required>
            <option value="" disabled selected>اختار طريقة الدفع</option>
            <option value="cash on delivery">الدفع عند الاستلام</option>
            <option value="credit card">بطاقة إئتمان</option>
            <option value="paytm">وان كاش</option>
            <option value="paypal">باي بال</option>
         </select>
         <input type="submit" value="تقديم الطلب" class="btn <?php if (empty($fetch_profile['address'])) { echo 'disabled'; } ?>" style="width:100%; background:var(--red); color:var(--white);" name="submit">
      </div>

   </form>
   
</section>

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>