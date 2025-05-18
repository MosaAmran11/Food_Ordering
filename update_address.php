<?php

include 'components/connect.php';

if (!isset($_SESSION)) {
   session_start();
}

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:index.php');
};

if (isset($_POST['submit'])) {

   $address = $_POST['area'] . ', ' . $_POST['town'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_SPECIAL_CHARS);

   $update_address = $conn->prepare("UPDATE `users` set address = ? WHERE id = ?");
   $update_address->execute([$address, $user_id]);

   $message[] = 'address saved!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' https://images.example.com; font-src 'self' https://fonts.googleapis.com; script-src 'self' https://trusted-scripts.com;">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update address</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body dir="rtl">

   <?php include 'components/user_header.php' ?>

   <section class="form-container">

      <form action="" method="post">
         <h3>your address</h3>
         <input type="text" class="box" placeholder="محافظة" required maxlength="50" name="area">
         <input type="text" class="box" placeholder="اسم المديرية" required maxlength="50" name="town">
         <input type="text" class="box" placeholder="اسم المنطقه" required maxlength="50" name="city">
         <input type="text" class="box" placeholder="جوار" required maxlength="50" name="state">
         <input type="number" class="box" placeholder="ادخل رمز سري خاص بك" required max="999999" min="0" maxlength="6" name="pin_code">
         <input type="submit" value="save address" name="submit" class="btn">
      </form>

   </section>










   <?php include 'components/footer.php' ?>







   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>