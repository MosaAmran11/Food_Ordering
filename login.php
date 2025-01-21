<?php
// بداية الكود الخاص بتسجيل الدخول

include 'components/connect.php';
session_start();

// تعيين الحد الأقصى لمحاولات تسجيل الدخول
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300); // 5 دقائق

// التحقق من وجود جلسة المستخدم
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// التحقق من تقديم النموذج
if (isset($_POST['submit'])) {
    // تصفية المدخلات
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $pass = $_POST['pass']; // لا تقم بتصفية كلمة المرور هنا

    // التحقق من عدد محاولات تسجيل الدخول
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        if (time() < $_SESSION['lockout_time']) {
            $message[] = 'تم قفل حسابك مؤقتًا. حاول مرة أخرى بعد 5 دقائق.';
        } else {
            // إعادة تعيين عدد المحاولات بعد انتهاء فترة القفل
            unset($_SESSION['login_attempts']);
            unset($_SESSION['lockout_time']);
        }
    }

    // استعلام قاعدة البيانات
    if (!isset($_SESSION['login_attempts']) || $_SESSION['login_attempts'] < MAX_LOGIN_ATTEMPTS) {
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_user->execute([$email]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);

        // التحقق من وجود المستخدم وكلمة المرور
        if ($row && password_verify($pass, $row['password'])) {
            // التحقق من CSRF
            if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $_SESSION['user_id'] = $row['id'];
                // إعادة تعيين محاولات تسجيل الدخول
                unset($_SESSION['login_attempts']);
                header('location:home.php');
                exit(); // تأكد من إنهاء السكربت بعد إعادة التوجيه
            } else {
                $message[] = 'خطأ في التحقق من الأمان!';
            }
        } else {
            // زيادة عدد محاولات تسجيل الدخول
            $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;

            if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
                $_SESSION['lockout_time'] = time() + LOCKOUT_TIME; // تعيين وقت القفل
            }
            $message[] = 'البريد الإلكتروني أو كلمة المرور غير صحيحة!';
        }
    }
}

// توليد رمز CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body dir="rtl">

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="form-container">
        <form action="" method="post">
            <h3>تسجيل الدخول الآن</h3>
            <input type="email" name="email" required placeholder="ادخل بريدك الالكتروني" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" required placeholder="ادخل كلمة المرور" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <!-- xss -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"> <!-- ترميز قيمة CSRF لتجنب XSS -->
            <input type="submit" value="تسجيل الدخول" name="submit" class="btn">
            <p>ليس لديك حساب؟<a href="register.php">انشاء حساب الان</a></p>
        </form>
    </section>

    <?php include 'components/footer.php'; ?>

    <script src="js/script.js"></script>
</body>

</html>