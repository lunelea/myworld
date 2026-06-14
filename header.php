<?php

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>我的小小世界</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍋</text></svg>">
</head>
<body>

<nav>
  <div class="nav-inner">
    <a class="nav-logo" href="index.php">🍋 <span>我的世界</span></a>
    <div class="nav-links">
      <a href="index.php"   <?= $current_page=='index.php'   ? 'class="active"' : '' ?>>首頁</a>
      <a href="portfolio.php" <?= $current_page=='portfolio.php' ? 'class="active"' : '' ?>>作品集</a>
      <a href="guestbook.php" <?= $current_page=='guestbook.php' ? 'class="active"' : '' ?>>留下漂流瓶</a>
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="profile_edit.php" <?= $current_page=='profile_edit.php' ? 'class="active"' : '' ?>>編輯資料</a>
        <a href="logout.php">登出</a>
      <?php else: ?>
        <a href="login.php" <?= $current_page=='login.php' ? 'class="active"' : '' ?>>登入</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
