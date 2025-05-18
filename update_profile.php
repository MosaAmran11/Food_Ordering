<?php

include 'components/connect.php';

session_start();

// إدارة الجلسات بشكل صحيح
if (!isset($_SESSION['user_id'])) {
    header('location:index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
    // تصفية المدخلات
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_SPECIAL_CHARS);

    // التحقق من تنسيق البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'البريد الإلكتروني غير صالح!';
    }

    // تحديث الاسم
    if (!empty($name)) {
        $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
        $update_name->execute([htmlspecialchars($name, ENT_QUOTES, 'UTF-8'), $user_id]);
    }

    // تحديث البريد الإلكتروني
    if (!empty($email)) {
        $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND id != ?");
        $select_email->execute([$email, $user_id]);
        if ($select_email->rowCount() > 0) {
            $message[] = 'البريد الإلكتروني مستخدم بالفعل!';
        } else {
            $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
            $update_email->execute([htmlspecialchars($email, ENT_QUOTES, 'UTF-8'), $user_id]);
        }
    }

    // تحديث الرقم
    if (!empty($number)) {
        $select_number = $conn->prepare("SELECT * FROM `users` WHERE number = ? AND id != ?");
        $select_number->execute([$number, $user_id]);
        if ($select_number->rowCount() > 0) {
            $message[] = 'الرقم مستخدم بالفعل!';
        } else {
            $update_number = $conn->prepare("UPDATE `users` SET number = ? WHERE id = ?");
            $update_number->execute([htmlspecialchars($number, ENT_QUOTES, 'UTF-8'), $user_id]);
        }
    }

    // تحديث كلمة المرور
    $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
    $select_prev_pass->execute([$user_id]);
    $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
    $prev_pass = $fetch_prev_pass['password'];
    $old_pass = isset($_POST['old_pass']) ? sha1($_POST['old_pass']) : '';
    $new_pass = isset($_POST['new_pass']) ? sha1($_POST['new_pass']) : '';
    $confirm_pass = isset($_POST['confirm_pass']) ? sha1($_POST['confirm_pass']) : '';

    if ($old_pass !== '' && $old_pass !== $prev_pass) {
        $message[] = 'كلمة المرور القديمة غير متطابقة!';
    } elseif ($new_pass !== $confirm_pass) {
        $message[] = 'تأكيد كلمة المرور غير متطابق!';
    } elseif ($new_pass !== '') {
        $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
        $update_pass->execute([$confirm_pass, $user_id]);
        $message[] = 'تم تحديث كلمة المرور بنجاح!';
    } elseif ($old_pass === '') {
        $message[] = 'يرجى إدخال كلمة مرور جديدة!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' https://images.example.com; font-src 'self' https://fonts.googleapis.com; script-src 'self' https://trusted-scripts.com;">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحديث الملف الشخصي</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body dir="rtl">

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="form-container update-form">
        <form action="" method="post">
            <h3>تحديث الملف الشخصي</h3>
            <input type="text" name="name" placeholder="<?= htmlspecialchars($fetch_profile['name'], ENT_QUOTES, 'UTF-8'); ?>" class="box" maxlength="50">
            <input type="email" name="email" placeholder="<?= htmlspecialchars($fetch_profile['email'], ENT_QUOTES, 'UTF-8'); ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="number" name="number" placeholder="<?= htmlspecialchars($fetch_profile['number'], ENT_QUOTES, 'UTF-8'); ?>" class="box" min="0" max="9999999999" maxlength="10">
            <input type="password" name="old_pass" placeholder="ادخل كلمة مرورك القديمة" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="new_pass" placeholder="ادخل كلمة مرورك الجديدة" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="confirm_pass" placeholder="تأكيد كلمة مرورك الجديدة" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="تحديث الآن" name="submit" class="btn">
        </form>

        <?php include 'components/footer.php'; ?>

        <!-- custom js file link  -->
        <script src="js/script.js"></script>

</body>

</html>