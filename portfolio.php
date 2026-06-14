<?php
session_start();
include 'db.php';
include 'header.php';

$category = $_GET['category'] ?? 'all';

if ($category === 'article') {
    $stmt = $conn->prepare("SELECT id, title, category, excerpt, created_at FROM works WHERE category='article' ORDER BY created_at DESC");
} elseif ($category === 'poem') {
    $stmt = $conn->prepare("SELECT id, title, category, excerpt, created_at FROM works WHERE category='poem' ORDER BY created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT id, title, category, excerpt, created_at FROM works ORDER BY created_at DESC");
}

$stmt->execute();
$works = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="page-wrap">

  <div style="margin-bottom: 1.8rem;">
    <h1>📚 作品集</h1>
    <p style="color:#8A9A6A; margin-top:.3rem;">我的小小世界</p>
  </div>

  <!-- 篩選 -->
  <div style="display:flex; gap:.6rem; margin-bottom:1.5rem; flex-wrap:wrap;">
    <a href="portfolio.php"
       class="btn <?= $category==='all' ? 'btn-primary' : 'btn-outline' ?>">全部</a>
    <a href="portfolio.php?category=article"
       class="btn <?= $category==='article' ? 'btn-primary' : 'btn-outline' ?>">📄 文章</a>
    <a href="portfolio.php?category=poem"
       class="btn <?= $category==='poem' ? 'btn-primary' : 'btn-outline' ?>">🌸 散文</a>
  </div>

  <?php if (isset($_SESSION['user_id'])): ?>
    <div style="margin-bottom:1.2rem;">
      <a href="work_add.php" class="btn btn-primary">✏️ 新增作品</a>
    </div>
  <?php endif; ?>

  <!-- 列表 -->
  <?php if (empty($works)): ?>
    <div class="card" style="text-align:center; color:#8A9A6A;">
      還沒有作品，<a href="work_add.php">一起創作</a>吧！
    </div>
  <?php else: ?>
    <?php foreach ($works as $w): ?>
      <div class="card" style="margin-bottom:1.2rem;">
        <div style="display:flex; align-items:center; gap:.6rem; margin-bottom:.4rem;">
          <span class="lemon-accent" style="font-size:.8rem; padding:.2rem .7rem;">
            <?= $w['category']==='article' ? '📄 文章' : '🌸 散文' ?>
          </span>
          <span style="font-size:.8rem; color:#8A9A6A;">
            <?= date('Y / m / d', strtotime($w['created_at'])) ?>
          </span>
        </div>
        <h2 style="margin:.4rem 0 .6rem; font-size:1.2rem;">
          <a href="work.php?id=<?= $w['id'] ?>"
             style="color:#4A5A35; text-decoration:none;">
            <?= htmlspecialchars($w['title']) ?>
          </a>
        </h2>
        <?php if ($w['excerpt']): ?>
          <p style="color:#6A7A55; line-height:1.8; font-size:.95rem;">
            <?= htmlspecialchars($w['excerpt']) ?>
          </p>
        <?php endif; ?>
        <div style="margin-top:.8rem;">
          <a href="work.php?id=<?= $w['id'] ?>" class="btn btn-outline"
             style="font-size:.85rem; padding:.3rem .9rem;">閱讀全文 →</a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>