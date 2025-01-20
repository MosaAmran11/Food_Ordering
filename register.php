<?php

include 'components/connect.php';

session_start(); // بدء جلسة المستخدم

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // استعادة معرف المستخدم من الجلسة
} else {
    $user_id = '';
}

// التحقق مما إذا كان النموذج قد تم تقديمه
if (isset($_POST['submit'])) {
    // تصفية المدخلات مع التحقق من صحة البيانات
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    // التحقق من صحة المدخلات
    if (empty($name) || empty($email) || empty($number) || empty($pass) || empty($cpass)) {
        $message[] = 'يرجى ملء جميع الحقول!';
        return; // إنهاء العملية إذا كانت المدخلات غير صالحة
    }

    // التحقق من تنسيق البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'البريد الإلكتروني غير صالح!';
        return; // إنهاء العملية إذا كان البريد الإلكتروني غير صالح
    }

    // التحقق من وجود المستخدم
    try {
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
        $select_user->execute([$email, $number]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);

        if ($select_user->rowCount() > 0) {
            $message[] = 'البريد الإلكتروني أو الرقم موجود بالفعل!';
        } else {
            if ($pass !== $cpass) {
                $message[] = 'تأكيد كلمة المرور غير متطابقة!';
            } else {
                // تجزئة كلمة المرور
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

                // إدراج المستخدم
                $insert_user = $conn->prepare("INSERT INTO `users`(name, email, number, password, is_verified) VALUES(?,?,?,?,0)");
                
                // بداية كود معالجة الأخطاء
                if ($insert_user->execute([$name, $email, $number, $hashed_pass])) {
                    // إرسال بريد تأكيد
                    $user_id = $conn->lastInsertId(); // الحصول على ID المستخدم الجديد
                    $verification_code = bin2hex(random_bytes(16)); // توليد كود تأكيد فريد

                    // إدراج كود التحقق في قاعدة البيانات
                    $insert_code = $conn->prepare("INSERT INTO `email_verifications` (user_id, code) VALUES (?, ?)");
                    $insert_code->execute([$user_id, $verification_code]);

                    // إعداد رابط التحقق
                    $verification_link = "http://yourdomain.com/verify_email.php?code=" . $verification_code;

                    // إرسال البريد الإلكتروني (يمكنك استخدام PHPMailer أو mail())
                    $to = $email;
                    $subject = "تأكيد بريدك الإلكتروني";
                    $message = "يرجى تأكيد بريدك الإلكتروني بالنقر على الرابط التالي: $verification_link";
                    mail($to, $subject, $message); // إرسال البريد الإلكتروني

                    // إعادة توجيه المستخدم
                    $_SESSION['user_id'] = $user_id; // تعيين الجلسة
                    header('location:home.php');
                    exit(); // تأكد من إنهاء السكربت بعد إعادة التوجيه
                } else {
                    $message[] = 'حدث خطأ أثناء إنشاء الحساب!';
                }
                // نهاية كود معالجة الأخطاء
            }
        }
    } catch (PDOException $e) {
        // معالجة الاستثناءات في حالة حدوث خطأ في قاعدة البيانات
        $message[] = 'خطأ في قاعدة البيانات: ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>انشاء حساب</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

   <form action="" method="post">
      <h3>انشاء حساب</h3>
      <input type="text" name="name" required placeholder="ادخل اسمك" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="ادخل بريدك الالكتروني" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" required placeholder="ادخل رقم هاتفك" class="box" min="0" max="9999999999" maxlength="10">
      <input type="password" name="pass" required placeholder="ادخل كلمة سر" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="أكد رقمك السري" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="انشاء حساب" name="submit" class="btn">
      <p>هل لديك حساب من قبل ؟ <a href="login.php">تسجيل الدخول الآن</a></p>
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>