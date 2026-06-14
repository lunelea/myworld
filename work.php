<?php
session_start();
include 'db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: portfolio.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM works WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$work) {
    header('Location: portfolio.php');
    exit;
}

include 'header.php';
?>

<div class="page-wrap" style="max-width:680px;">

  <div style="margin-bottom:.8rem;">
    <a href="portfolio.php" style="color:#8A9A6A; font-size:.9rem;">← 回作品集</a>
  </div>

  <div class="card">
    <div style="display:flex; align-items:center; gap:.6rem; margin-bottom:.8rem;">
      <span class="lemon-accent" style="font-size:.8rem; padding:.2rem .7rem;">
        <?= $work['category']==='article' ? '📄 文章' : '🌸 散文' ?>
      </span>
      <span style="font-size:.8rem; color:#8A9A6A;">
        <?= date('Y / m / d', strtotime($work['created_at'])) ?>
      </span>
    </div>

    <h1 style="font-size:1.6rem; margin-bottom:1.2rem; color:#3A4A25;">
      <?= htmlspecialchars($work['title']) ?>
    </h1>

    <div style="line-height:2; color:#4A5A35; white-space:pre-wrap; font-size:1rem;"><?= htmlspecialchars($work['content']) ?></div>

    <?php if (isset($_SESSION['user_id'])): ?>
      <hr style="border:none; border-top:1.5px dashed #E0DCC0; margin:1.5rem 0;">
      <div style="display:flex; gap:.8rem; flex-wrap:wrap;">
        <a href="work_edit.php?id=<?= $work['id'] ?>" class="btn btn-outline"
            style="font-size:.85rem;">✏️ 編輯這篇</a>
        <a href="work_delete.php?id=<?= $work['id'] ?>" class="btn btn-outline"
            style="font-size:.85rem; color:#B00020; border-color:#B00020;">🗑️ 刪除</a>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php include 'footer.php'; ?>