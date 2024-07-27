<?php
require 'auth.php';
require 'db.php';

$promotions = $conn->query("SELECT * FROM promotions WHERE NOW() BETWEEN start_date AND end_date");
$personal_promotions = [];
$birthday_promotion = null;

if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    $personal_promotions = $conn->query("SELECT pp.*, p.title, p.description FROM personal_promotions pp JOIN promotions p ON pp.promotion_id = p.id WHERE pp.user_id = $user_id AND NOW() BETWEEN pp.start_date AND pp.end_date");

    // Проверка на день рождения
    $stmt = $conn->prepare("SELECT birth_date FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($birth_date);
    $stmt->fetch();
    $stmt->close();

    if ($birth_date) {
        $today = date('m-d');
        $birth_date_formatted = date('m-d', strtotime($birth_date));

        if ($today == $birth_date_formatted) {
            // Создание акции на день рождения
            $birthday_promotion = [
                'title' => 'С Днем Рождения!',
                'description' => 'Получите специальный подарок и скидку 20% на все услуги в честь вашего дня рождения!',
                'end_date' => date('Y-m-d H:i:s', strtotime('+1 day'))
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Акции</title>
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

    <h1>Наши акции</h1>
    <div class="promotions">
        <?php while ($promo = $promotions->fetch_assoc()): ?>
            <div class="promotion">
                <h3><?php echo $promo['title']; ?></h3>
                <p><?php echo $promo['description']; ?></p>
                <p>Акция действует до: <?php echo $promo['end_date']; ?></p>
                <div id="timer_<?php echo $promo['id']; ?>"></div>
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
    
    <?php if (is_logged_in() && $personal_promotions->num_rows > 0): ?>
        <h2>Ваши индивидуальные акции</h2>
        <div class="promotions">
            <?php while ($promo = $personal_promotions->fetch_assoc()): ?>
                <div class="promotion">
                    <h3><?php echo $promo['title']; ?></h3>
                    <p><?php echo $promo['description']; ?></p>
                    <p>Акция действует до: <?php echo $promo['end_date']; ?></p>
                    <div id="personal_timer_<?php echo $promo['id']; ?>"></div>
                    <script>
                        var personalEndDate = new Date("<?php echo $promo['end_date']; ?>").getTime();
                        var personalTimerId = setInterval(function() {
                            var now = new Date().getTime();
                            var distance = personalEndDate - now;
                            if (distance < 0) {
                                clearInterval(personalTimerId);
                                document.getElementById("personal_timer_<?php echo $promo['id']; ?>").innerHTML = "Акция завершена";
                            } else {
                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                document.getElementById("personal_timer_<?php echo $promo['id']; ?>").innerHTML = days + "д " + hours + "ч " + minutes + "м " + seconds + "с ";
                            }
                        }, 1000);
                    </script>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <?php if ($birthday_promotion): ?>
        <h2>Специальная акция на день рождения</h2>
        <div class="promotion">
            <h3><?php echo $birthday_promotion['title']; ?></h3>
            <p><?php echo $birthday_promotion['description']; ?></p>
            <p>Акция действует до: <?php echo $birthday_promotion['end_date']; ?></p>
            <div id="birthday_timer"></div>
            <script>
                var birthdayEndDate = new Date("<?php echo $birthday_promotion['end_date']; ?>").getTime();
                var birthdayTimerId = setInterval(function() {
                    var now = new Date().getTime();
                    var distance = birthdayEndDate - now;
                    if (distance < 0) {
                        clearInterval(birthdayTimerId);
                        document.getElementById("birthday_timer").innerHTML = "Акция завершена";
                    } else {
                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        document.getElementById("birthday_timer").innerHTML = days + "д " + hours + "ч " + minutes + "м " + seconds + "с ";
                    }
                }, 1000);
            </script>
        </div>
    <?php endif; ?>
</body>
</html>
