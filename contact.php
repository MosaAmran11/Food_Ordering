<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// تحقق من وجود CAPTCHA
if (isset($_POST['send'])) {
    // تصفية المدخلات
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);
    $msg = filter_input(INPUT_POST, 'msg', FILTER_SANITIZE_STRING);

    // التحقق من تنسيق البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'البريد الإلكتروني غير صالح!';
    } else {
        // تحقق من CAPTCHA
        if (empty($_POST['captcha']) || $_POST['captcha'] !== $_SESSION['captcha_text']) {
            $message[] = 'التحقق فشل! يرجى إعادة المحاولة.';
        } else {
            // التحقق مما إذا كانت الرسالة قد أرسلت مسبقًا
            $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
            $select_message->execute([$name, $email, $number, $msg]);

            if ($select_message->rowCount() > 0) {
                $message[] = 'تم إرسال الرسالة بالفعل!';
            } else {
                // إدراج الرسالة
                $insert_message = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
                $insert_message->execute([$user_id, htmlspecialchars($name, ENT_QUOTES, 'UTF-8'), htmlspecialchars($email, ENT_QUOTES, 'UTF-8'), htmlspecialchars($number, ENT_QUOTES, 'UTF-8'), htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')]);

                $message[] = 'تم إرسال الرسالة بنجاح!';
            }
        }
    }
}

// توليد نص CAPTCHA
function generateCaptcha() {
    $length = 6;
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captcha = '';
    for ($i = 0; $i < $length; $i++) {
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha;
}

// تخزين نص CAPTCHA في الجلسة
session_start();
$_SESSION['captcha_text'] = generateCaptcha();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>اتصل</title>

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
   <h3>اتصل بنا</h3>
   <p><a href="home.php">الصفحة الرئيسية</a> <span> / اتصل</span></p>
</div>

<!-- contact section starts  -->

<section class="contact">
   <div class="row">
      <div class="image">
         <img src="images/contact-img.svg" alt="">
      </div>

      <form action="" method="post">
         <h3>أخبرنا بشيء!</h3>
         <input type="text" name="name" maxlength="50" class="box" placeholder="ادخل اسمك" required>
         <input type="number" name="number" min="0" max="9999999999" class="box" placeholder="ادخل رقمك" required maxlength="10">
         <input type="email" name="email" maxlength="50" class="box" placeholder="ادخل بريدك الالكتروني" required>
         <textarea name="msg" class="box" required placeholder="ادخل رسالتك الينا" maxlength="500" cols="30" rows="10"></textarea>
         <label for="captcha">أدخل الرمز أدناه:</label>
         <div class="captcha">
            <p><?php echo $_SESSION['captcha_text']; ?></p>
            <input type="text" name="captcha" class="box" placeholder="أدخل الرمز هنا" required>
         </div>
         <input type="submit" value="إرسال الرسالة" name="send" class="btn">
      </form>

   </div>
</section>

<!-- contact section ends -->

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>