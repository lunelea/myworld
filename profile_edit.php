<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';
$success = '';
$error   = '';

// 獲得資料
$stmt = $conn->prepare("SELECT username, email, bio, hometown, grade, skills, interests FROM users WHERE id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 送出表單
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $bio      = trim($_POST['bio']      ?? '');
    $hometown = trim($_POST['hometown'] ?? '');
    $grade    = trim($_POST['grade']    ?? '');
    $new_pw   = $_POST['new_password']  ?? '';
    $skills    = trim($_POST['skills']    ?? '');
    $interests = trim($_POST['interests'] ?? '');

    //要不要改密碼
    if($new_pw !== '') {
        $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "UPDATE users SET email=?, bio=?, hometown=?, grade=?, password=?, skills=?, interests=? WHERE id=?"
        );
        $stmt->bind_param('ssssssi', $email, $bio, $hometown, $grade, $hashed, $skills, $interests, $_SESSION['user_id']);
    } else {
        $stmt = $conn->prepare(
            "UPDATE users SET email=?, bio=?, hometown=?, grade=?, skills=?, interests=? WHERE id=?"
        );
        $stmt->bind_param('ssssi', $email, $bio, $hometown, $grade, $skills, $interests, $_SESSION['user_id']);
    }

    if($stmt->execute()) {
        $success = '✅ 資料更新成功！';
        $user['email']    = $email;
        $user['bio']      = $bio;
        $user['hometown'] = $hometown;
        $user['grade']    = $grade;
    } else {
        $error = '更新失敗，請再試一次。';
    }
    $stmt->close();
}

include 'header.php';
?>

<div class="page-wrap" style="max-width:600px;">

  <div style="margin-bottom: 1.8rem;">
    <h1>✏️ 編輯個人資料</h1>
  </div>

  <div class="card">
    <?php if($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="profile_edit.php">

      <div class="form-group">
        <label>帳號（無法修改）</label>
        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled
               style="background:#F5F5F0; color:#999;">
      </div>

      <div class="form-group">
        <label for="email">📧 Email</label>
        <input type="email" id="email" name="email"
               placeholder="your@email.com"
               value="<?= htmlspecialchars($user['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="hometown">📍 來自哪裡</label>
        <input type="text" id="hometown" name="hometown"
               placeholder="例：台中市"
               value="<?= htmlspecialchars($user['hometown'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="grade">🎓 年級</label>
        <input type="text" id="grade" name="grade"
               placeholder="例：四年級"
               value="<?= htmlspecialchars($user['grade'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="bio">💬 自我介紹</label>
        <textarea id="bio" name="bio"
                  placeholder="寫句話介紹自己吧！"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="interests">✨ 興趣愛好</label>
        <input type="text" id="interests" name="interests"
            placeholder="用逗號分隔，例：VOCALOID, 寫作, Minecraft"
            value="<?= htmlspecialchars($user['interests'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="skills">🍋 技能</label>
        <p style="font-size:.8rem;color:#8A9A6A;margin-bottom:.5rem;">
        </p>
        <input type="text" id="skills" name="skills"
          placeholder="HTML/CSS:80, PHP:60, JavaScript:40"
          value="<?= htmlspecialchars($user['skills'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="new_password">新密碼</label>
        <input type="password" id="new_password" name="new_password"
               placeholder="留空表示不更改密碼"
               autocomplete="new-password">
      </div>

      <hr style="border:none; border-top:1.5px dashed #E0DCC0; margin:1.5rem 0;">
      <p style="font-size:.85rem; color:#8A9A6A; margin-bottom:1rem;">🔒 更改密碼（不想改就留空）</p>
      <div style="display:flex; gap:1rem; margin-top:.5rem; flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary">💾 儲存變更</button>
        <a href="index.php" class="btn btn-outline">← 回首頁</a>
      </div>

    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
