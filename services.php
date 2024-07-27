<?php
require 'auth.php';
require 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Услуги</title>
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

    <h1>Наши услуги</h1>
    <div class="services">
        <div class="service">
            <img src="images/pn.jpg" alt="Услуга 1">
            <h3>Массаж</h3>
            <p>Расслабляющий и лечебный массаж для снятия стресса и улучшения общего самочувствия.</p>
        </div>
        <div class="service">
            <img src="images/pn1.jpg" alt="Услуга 2">
            <h3>Маникюр и педикюр</h3>
            <p>Профессиональный уход за ногтями и кожей рук и ног.</p>
        </div>
        <div class="service">
            <img src="images/pn2.jpg" alt="Услуга 3">
            <h3>Стрижка и укладка</h3>
            <p>Современные стрижки и укладки от наших опытных мастеров.</p>
        </div>
    </div>
</body>
</html>
