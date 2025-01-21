<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:index.php');
   exit(); // تأكد من إنهاء السكربت بعد إعادة التوجيه
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>الطلبات</title>

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
      <h3>الطلبات</h3>
      <p><a href="html.php">الرئيسية</a> <span> / الطلبات</span></p>
   </div>

   <section class="orders">

      <h1 class="title">طلباتك</h1>

      <div class="box-container">

         <?php
         if ($user_id == '') {
            echo '<p class="empty">يرجى تسجيل الدخول لرؤية طلباتك</p>';
         } else {
            // استخدام prepared statements لتجنب SQL Injection
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
            $select_orders->execute([$user_id]);

            if ($select_orders->rowCount() > 0) {
               while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
         ?>
                  <div class="box">
                     <p>تم الطلب في : <span><?= htmlspecialchars($fetch_orders['placed_on'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                     <p>الاسم : <span><?= htmlspecialchars($fetch_orders['name'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                     <p>البريد الإلكتروني : <span><?= htmlspecialchars($fetch_orders['email'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                     <p>رقم الهاتف : <span><?= htmlspecialchars($fetch_orders['number'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                     <p>العنوان : <span><?= htmlspecialchars($fetch_orders['address'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                     <p>طريقة الدفع : <span><?= htmlspecialchars($fetch_orders['method'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                     <p>طلباتك : <span><?= htmlspecialchars($fetch_orders['total_products'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                     <p>إجمالي السعر : <span>$<?= htmlspecialchars($fetch_orders['total_price'], ENT_QUOTES, 'UTF-8'); ?>/-</span></p>
                     <p>حالة الدفع : <span style="color:<?php echo ($fetch_orders['payment_status'] == 'pending') ? 'red' : 'green'; ?>"><?= htmlspecialchars($fetch_orders['payment_status'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                  </div>
         <?php
               }
            } else {
               echo '<p class="empty">لم يتم تقديم أي طلبات بعد!</p>';
            }
         }
         ?>
      </div>
   </section>

   <!-- footer section starts  -->
   <?php include 'components/footer.php'; ?>
   <!-- footer section ends -->
   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>