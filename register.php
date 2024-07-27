<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $birth_date = $_POST['birth_date'];

    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, email, birth_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $birth_date);
    
    if ($stmt->execute()) {
        header('Location: login.php');
        exit();
    } else {
        $error = "Ошибка при регистрации. Попробуйте снова.";
    }
}

// Получение данных об услугах и акциях
$services = [
    ["image" => "reg.jpg", "title" => "Массаж", "description" => "Расслабляющий и лечебный массаж для снятия стресса и улучшения общего самочувствия."],
    ["image" => "reg1.jpg", "title" => "Маникюр и педикюр", "description" => "Профессиональный уход за ногтями и кожей рук и ног."],
    ["image" => "reg2.jpg", "title" => "Стрижка и укладка", "description" => "Современные стрижки и укладки от наших опытных мастеров."]
];

$promotions = $conn->query("SELECT * FROM promotions WHERE NOW() BETWEEN start_date AND end_date");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
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

    <h1>Регистрация</h1>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <form method="post" action="">
        <label for="username">Имя пользователя</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <label for="birth_date">Дата рождения</label>
        <input type="date" id="birth_date" name="birth_date">
        <button type="submit">
            <p>Зарегистрироваться</p>
            <svg viewBox="0 0 24 24">
                <path d="M12 2L6 9h4v7h4V9h4l-6-7z"/>
            </svg>
        </button>
    </form>

    <h2>Наши услуги</h2>
    <div class="services">
        <?php foreach ($services as $service): ?>
            <div class="service">
                <img src="images/<?php echo $service['image']; ?>" alt="<?php echo $service['title']; ?>">
                <h3><?php echo $service['title']; ?></h3>
                <p><?php echo $service['description']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    
    <h2>Наши акции</h2>
    <div class="promotions">
        <?php while ($promo = $promotions->fetch_assoc()): ?>
            <div class="promotion">
                <h3><?php echo $promo['title']; ?></h3>
                <p><?php echo $promo['description']; ?></p>
                <p>Акция действует до: <?php echo $promo['end_date']; ?></p>
                <div id="timer_<?php echo $promo['id']; ?>" class="timer"></div>
                <script>
                    var endDate = new Date("<?php echo $promo['end_date']; ?>").getTime();
                    var timerId = setInterval(function() {
                        var now = new Date().getTime();
                        var distance = endDate - now;
                        if (distance < 0) {
                            clearInterval(timerId);
                            document.getElementById("timer_<?php echo $promo['id']; ?>").innerHTML = "Акция завершена";
                        } else {
                            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            document.getElementById("timer_<?php echo $promo['id']; ?>").innerHTML = days + "д " + hours + "ч " + minutes + "м " + seconds + "с ";
                        }
                    }, 1000);
                </script>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
