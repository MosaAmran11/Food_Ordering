<?php

include 'components/connect.php';

session_start(); // بدء جلسة المستخدم

// التحقق مما إذا كان المستخدم مسجلاً للدخول
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // استعادة معرف المستخدم من الجلسة
} else {
    header('location:index.php'); // إعادة التوجيه إذا لم يكن المستخدم مسجلاً للدخول
    exit(); // تأكد من إنهاء السكربت بعد إعادة التوجيه
}

// استعلام لجلب معلومات المستخدم
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

// التحقق من وجود بيانات المستخدم
if (!$fetch_profile) {
    // إذا لم يتم العثور على المستخدم، إعادة التوجيه
    header('location:index.php');
    exit();
}

if (isset($_POST['delete'])) {
    $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $delete_user->execute([$user_id]);
    session_destroy();
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body dir="rtl">

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="user-details">

        <div class="user">
            <img src="images/user-icon.png" alt="">
            <p><i class="fas fa-user"></i><span><span><?= htmlspecialchars($fetch_profile['name'], ENT_QUOTES, 'UTF-8'); ?></span></span></p>
            <p><i class="fas fa-phone"></i><span><?= htmlspecialchars($fetch_profile['number'], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <p><i class="fas fa-envelope"></i><span><?= htmlspecialchars($fetch_profile['email'], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <a href="update_profile.php" class="btn">تحديث المعلومات</a>
            <p class="address"><i class="fas fa-map-marker-alt"></i><span><?php echo htmlspecialchars($fetch_profile['address'] ?: 'يرجى إدخال عنوانك', ENT_QUOTES, 'UTF-8'); ?></span></p>
            <a href="update_address.php" class="btn">تحديث العنوان</a>
            <form action="" method="post">
                <input type="submit" value="حذف الحساب" name="delete" class="btn" onclick="return confirm('هل أنت متأكد من حذف حسابك؟')">
            </form>
        </div>

    </section>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>