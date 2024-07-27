<?php
require 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (authenticate($username, $password)) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Неправильное имя пользователя или пароль.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Главная</a>
        <a href="services.php">Услуги</a>
        <a href="promotions.php">Акции</a>
        <?php if (is_logged_in()): ?>
            <a href="logout.php">Выйти (<?php echo $_SESSION['username']; ?>)</a>
        <?php else: ?>
            <a href="login.php">Войти</a>
            <a href="register.php">Зарегистрироваться</a>
        <?php endif; ?>
    </div>

    <h1>Вход</h1>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <form method="post" action="">
        <label for="username">Имя пользователя</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Войти</button>
    </form>
</body>
</html>
