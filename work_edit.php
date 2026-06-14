<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';
$success = '';
$error = '';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: portfolio.php');
    exit;
}

//獲取原本的文章
$stmt = $conn->prepare("SELECT * FROM works WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$work) {
    header('Location: portfolio.php');
    exit;
}

// 送出資料
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']    ?? '');
    $category = $_POST['category']      ?? '';
    $excerpt  = trim($_POST['excerpt']  ?? '');
    $content  = trim($_POST['content']  ?? '');

    if ($title === '' || $content === '' || !in_array($category, ['article','poem'])) {
        $error = '標題、分類、內文都是必填的！';
    } else {
        $stmt = $conn->prepare(
            "UPDATE works SET title=?, category=?, excerpt=?, content=? WHERE id=?"
        );
        $stmt->bind_param('ssssi', $title, $category, $excerpt, $content, $id);
        if ($stmt->execute()) {
            header("Location: work.php?id=$id");
            exit;
        } else {
            $error = '更新失敗，請再試一次。';
        }
        $stmt->close();
    }
}

include 'header.php';
?>

<div class="page-wrap" style="max-width:680px;">

  <div style="margin-bottom:1.5rem;">
    <h1>✏️ 修改印痕</h1>
  </div>

  <div class="card">
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="work_edit.php?id=<?= $id ?>">

      <div class="form-group">
        <label for="title">📌 標題</label>
        <input type="text" id="title" name="title"
               value="<?= htmlspecialchars($_POST['title'] ?? $work['title']) ?>">
      </div>

      <div class="form-group">
        <label>📂 分類</label>
        <div style="display:flex; gap:1rem; margin-top:.4rem;">
          <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
            <input type="radio" name="category" value="article"
                   <?= ($_POST['category'] ?? $work['category'])==='article' ? 'checked' : '' ?>>
            📄 文章
          </label>
          <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
            <input type="radio" name="category" value="poem"
                   <?= ($_POST['category'] ?? $work['category'])==='poem' ? 'checked' : '' ?>>
            🌸 散文
          </label>
        </div>
      </div>

      <div class="form-group">
        <label for="excerpt">💬 摘要（選填）</label>
        <textarea id="excerpt" name="excerpt"
                  style="height:80px;"><?= htmlspecialchars($_POST['excerpt'] ?? $work['excerpt']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="content">📝 內文</label>
        <textarea id="content" name="content"
                  style="height:320px;"><?= htmlspecialchars($_POST['content'] ?? $work['content']) ?></textarea>
      </div>

      <div style="display:flex; gap:1rem; margin-top:.5rem; flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary">💾 儲存變更</button>
        <a href="work.php?id=<?= $id ?>" class="btn btn-outline">← 取消</a>
      </div>

    </form>
  </div>
</div>

<?php include 'footer.php'; ?>