<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';
$success = '';
$error = '';

//新增文章
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']    ?? '');
    $category = $_POST['category']      ?? '';
    $excerpt  = trim($_POST['excerpt']  ?? '');
    $content  = trim($_POST['content']  ?? '');

    if ($title === '' || $content === '' || !in_array($category, ['article','poem'])) {
        $error = '標題、分類、內文都是必填的！';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO works (title, category, excerpt, content) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('ssss', $title, $category, $excerpt, $content);
        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            header("Location: work.php?id=$new_id");
            exit;
        } else {
            $error = '新增失敗，請再試一次。';
        }
        $stmt->close();
    }
}

include 'header.php';
?>

<div class="page-wrap" style="max-width:680px;">

  <div style="margin-bottom:1.5rem;">
    <h1>✏️ 在這裡留下一痕印記</h1>
  </div>

  <div class="card">
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="work_add.php">

      <div class="form-group">
        <label for="title">📌 標題</label>
        <input type="text" id="title" name="title"
               placeholder="作品標題"
               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>📂 分類</label>
        <div style="display:flex; gap:1rem; margin-top:.4rem;">
          <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
            <input type="radio" name="category" value="article"
                   <?= ($_POST['category'] ?? '')==='article' ? 'checked' : '' ?>>
            📄 文章
          </label>
          <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
            <input type="radio" name="category" value="poem"
                   <?= ($_POST['category'] ?? '')==='poem' ? 'checked' : '' ?>>
            🌸 散文
          </label>
        </div>
      </div>

      <div class="form-group">
        <label for="excerpt">💬 摘要（選填）</label>
        <textarea id="excerpt" name="excerpt"
                  placeholder="一兩句話描述這篇作品的感覺…"
                  style="height:80px;"><?= htmlspecialchars($_POST['excerpt'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="content">📝 內文</label>
        <textarea id="content" name="content"
                  placeholder="在這個小世界留下痕跡"
                  style="height:320px;"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
      </div>    

      <div style="display:flex; gap:1rem; margin-top:.5rem; flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary">💾 發布</button>
        <a href="portfolio.php" class="btn btn-outline">← 取消</a>
      </div>

    </form>
  </div>
</div>

<?php include 'footer.php'; ?>