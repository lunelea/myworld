<?php
session_start();
include 'db.php';

$error = '';

// 新增漂流瓶
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nickname = trim($_POST['nickname'] ?? '');
    $content  = trim($_POST['content']  ?? '');
    $pw       = trim($_POST['edit_password'] ?? '');

    if ($nickname === '' || $content === '') {
        $error = '暱稱和留言都要填！';
    } elseif ($pw === '') {
        $error = '請設定編輯密碼！';
    } else {
        $hashed_pw = password_hash($pw, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO comments (nickname, content, edit_password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $nickname, $content, $hashed_pw);
        $stmt->execute();
        $stmt->close();
        header('Location: guestbook.php');
        exit;
    }
}

// 編輯瓶中信
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_submit'])) {
    $edit_id      = intval($_POST['edit_id']);
    $edit_content = trim($_POST['edit_content'] ?? '');
    $edit_pw      = trim($_POST['edit_password'] ?? '');

    if ($edit_id > 0 && $edit_content !== '') {
        $chk = $conn->prepare("SELECT edit_password FROM comments WHERE id=?");
        $chk->bind_param('i', $edit_id);
        $chk->execute();
        $row = $chk->get_result()->fetch_assoc();
        $chk->close();

        if ($row && password_verify($edit_pw, $row['edit_password'])) {
            $stmt = $conn->prepare("UPDATE comments SET content=? WHERE id=?");
            $stmt->bind_param('si', $edit_content, $edit_id);
            $stmt->execute();
            $stmt->close();
            header('Location: guestbook.php');
            exit;
        } else {
            $error = '暗號錯誤，無法編輯！';
        }
    }
}

// 刪除漂流瓶
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_submit'])) {
    $del_id = intval($_POST['delete_id']);
    $del_pw = trim($_POST['del_password'] ?? '');

    $chk = $conn->prepare("SELECT edit_password FROM comments WHERE id=?");
    $chk->bind_param('i', $del_id);
    $chk->execute();
    $row = $chk->get_result()->fetch_assoc();
    $chk->close();

    if ($row && password_verify($del_pw, $row['edit_password'])) {
        $stmt = $conn->prepare("DELETE FROM comments WHERE id=?");
        $stmt->bind_param('i', $del_id);
        $stmt->execute();
        $stmt->close();
        header('Location: guestbook.php');
        exit;
    } else {
        $error = '暗號錯誤，無法刪除！';
    }
}

// 撈瓶子
$result   = $conn->query("SELECT * FROM comments ORDER BY created_at DESC");
$comments = $result->fetch_all(MYSQLI_ASSOC);

$editing_id = intval($_GET['edit'] ?? 0);

include 'header.php';
?>

<div class="ocean-page">
<div class="page-wrap" style="max-width:680px;">

  <div style="margin-bottom:1.8rem;">
    <h1>🌊 大海</h1>
    <p style="color:#8A9A6A; margin-top:.3rem;">在這片海洋留下漂流瓶</p>
  </div>

  <!-- 表單 -->
<div class="card" style="margin-bottom:1.5rem;">
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="guestbook.php">
      <div class="form-group">
        <label for="nickname">暱稱</label>
        <input type="text" id="nickname" name="nickname"
               placeholder="你的名字或暱稱"
               value="<?= htmlspecialchars($_POST['nickname'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="content">漂流瓶內容</label>
        <textarea id="content" name="content"
                  placeholder="寫下你想說的..."
                  style="height:100px;"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label for="edit_password">🔑 暗號</label>
        <p style="font-size:.8rem;margin-bottom:.4rem;">設一個暗號，才能連結你的漂流瓶以修改或刪除它</p>
        <input type="password" id="edit_password" name="edit_password" placeholder="自訂暗號">
      </div>
      <button type="submit" name="submit" class="btn btn-primary">把漂流瓶丟入大海 →</button>
    </form>
</div>

</div>
  <!-- 留言列表 -->
  <?php if (empty($comments)): ?>
    <div class="card" style="text-align:center; max-width:630px; margin:0 auto;">這片海域目前是空的</div>
  <?php else: ?>
    <?php foreach ($comments as $c): ?>
      <div class="bottle-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem;">
          <span style="font-weight:700; color:#4A5A35;">
            🍋 <?= htmlspecialchars($c['nickname']) ?>
          </span>
          <span style="font-size:.8rem; color:#8A9A6A;">
            <?= date('Y/m/d H:i', strtotime($c['created_at'])) ?>
          </span>
        </div>

        <?php if ($editing_id === (int)$c['id']): ?>
          <!-- 編輯模式 -->
          <form method="POST" action="guestbook.php">
            <input type="hidden" name="edit_id" value="<?= $c['id'] ?>">
            <div class="form-group">
              <textarea name="edit_content"
                        style="height:80px;"><?= htmlspecialchars($c['content']) ?></textarea>
            </div>
            <div class="form-group">
              <input type="password" name="edit_password" placeholder="輸入當初設定的暗號">
            </div>
            <div style="display:flex; gap:.6rem;">
              <button type="submit" name="edit_submit" class="btn btn-primary"
                      style="font-size:.85rem; padding:.3rem .9rem;">💾 儲存</button>
              <a href="guestbook.php" class="btn btn-outline"
                 style="font-size:.85rem; padding:.3rem .9rem;">取消</a>
            </div>
          </form>

        <?php else: ?>
          <p style="line-height:1.8; color:#4A5A35; white-space:pre-wrap;">
            <?= htmlspecialchars($c['content']) ?>
          </p>

          <!-- 編輯＋刪除按鈕 -->
          <div style="display:flex; gap:.8rem; margin-top:.8rem; flex-wrap:wrap; align-items:center;">
            <a href="guestbook.php?edit=<?= $c['id'] ?>" class="btn btn-outline"
               style="font-size:.8rem; padding:.2rem .7rem;">✏️ 編輯</a>

            <form method="POST" action="guestbook.php" style="display:flex; gap:.4rem; align-items:center;">
              <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
              <input type="password" name="del_password" placeholder="暗號"
                     style="width:90px; padding:.25rem .5rem; font-size:.8rem;
                            border:1px solid #C8D4A0; border-radius:6px; background:#fff;">
              <button type="submit" name="delete_submit" class="btn btn-outline"
                      style="font-size:.8rem; padding:.2rem .7rem; color:#B00020; border-color:#B00020;"
                      onclick="return confirm('確定要讓這個瓶子沉入海底？')">🗑️ 刪除</button>
            </form>
          </div>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>
</div> 
<style>
  body:has(.ocean-page) footer {
    background: #023e8a;
    color: #caf0f8;
  }
  body:has(.ocean-page) footer * {
    color: #fbfeff !important;
  }
  .ocean-page * {
    color: #03045e;
  }
  .ocean-page .btn-primary,
  .ocean-page .btn-primary * {
    color: #ffffff !important;
  }
</style>
<?php include 'footer.php'; ?>