<?php

require 'config/database.php';
require 'includes/functions.php';

session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (loginUser($pdo, $username, $password)) {
            echo 'Успешный вход.';
            exit();
        } else {
            echo 'Ошибка входа.';
            exit();
        }
    } elseif (isset($_POST['register'])) {
      $username = $_POST['username'];
      $password = $_POST['password'];

      $existingUser = isUserExists($pdo, $username);

      if ($existingUser) {
          echo 'Пользователь с таким именем уже существует.';
          exit();
      }

      if (registerUser($pdo, $username, $password)) {
          echo 'Успешная регистрация.';
          exit();
      } else {
          echo 'Ошибка при регистрации.';
          exit();
      }
  }
}
?>

<?php include 'templates/header.php'; ?>

<div class="container mt-5">
    <h1>Авторизация и Регистрация</h1>

    <div class="row">
        <div class="col-md-6">
            <h2>Вход</h2>
            <form id="login-form" method="POST">
                <div class="form-group">
                    <input type="text" name="username" class="form-control" placeholder="Имя пользователя" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Пароль" required>
                </div>
                <input type="hidden" name="login" value="">
                <button type="submit" name="login" class="btn btn-primary">Войти</button>
            </form>
        </div>

        <div class="col-md-6">
            <h2>Регистрация</h2>
            <form id="register-form" method="POST">
                <div class="form-group">
                    <input type="text" name="username" class="form-control" placeholder="Имя пользователя" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Пароль" required>
                </div>
                <input type="hidden" name="register" value="">
                <button type="submit" name="register" class="btn btn-primary">Зарегистрироваться</button>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $('#login-form, #register-form').submit(function(e) {
      e.preventDefault();
      var form = $(this);
      var url = form.attr('action');
      $.ajax({
          type: 'POST',
          url: url,
          data: form.serialize(),
          success: function(data) {
              Toastify({
                  text: data,
                  duration: 3000,
                  gravity: 'top',
                  position: 'right',
              }).showToast();
              if (data == "Успешный вход.") {
                window.location.replace("/");
                form.trigger('reset');
              } else if (data == "Успешная регистрация.") {
                window.location.replace("/");
                form.trigger('reset');
              }
          }
      });
  });
});

</script>
<?php include 'templates/footer.php'; ?>
