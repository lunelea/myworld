<?php
session_start();

// 如果有登入就跳回首頁
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

include 'db.php';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if($username === '' || $password === '') {
        $error = '帳號和密碼都要填';
    } else {
        // 查詢使用者
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = '帳號或密碼錯誤，請再試一次！';
        }
        $stmt->close();
    }
}

include 'header.php';
?>

<div class="page-wrap" style="max-width:480px;">

  <div style="text-align:center; margin-bottom: 2rem;">
    <div style="font-size:3rem; margin-bottom:.5rem;">🔑</div>
    <h1>登入</h1>
    <p style="color:#8A9A6A; margin-top:.4rem;">歡迎回來！</p>
  </div>

  <div class="card">
    <?php if($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">

      <div class="form-group">
        <label for="username">帳號</label>
        <input
          type="text" id="username" name="username"
          placeholder="輸入你的帳號"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          autocomplete="username">
      </div>

      <div class="form-group">
        <label for="password">密碼</label>
        <input
          type="password" id="password" name="password"
          placeholder="輸入你的密碼"
          autocomplete="current-password">
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; margin-top:.5rem;">
        登入 →
      </button>

    </form>
  </div>

</div>

<?php include 'footer.php'; ?>
