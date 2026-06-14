<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: portfolio.php');
    exit;
}

// 確認這篇不是幽靈
$stmt = $conn->prepare("SELECT id, title FROM works WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$work) {
    header('Location: portfolio.php');
    exit;
}

// 刪除確認
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $stmt = $conn->prepare("DELETE FROM works WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: portfolio.php');
    exit;
}

include 'header.php';
?>

<div class="page-wrap" style="max-width:480px;">

  <div class="card" style="text-align:center;">
    <div style="font-size:3rem; margin-bottom:1rem;">🗑️</div>
    <h2 style="margin-bottom:.6rem;">確定要抹除這痕印記嗎?</h2>
    <p style="color:#6A7A55; margin-bottom:1.5rem;">
      「<?= htmlspecialchars($work['title']) ?>」<br>
      <span style="font-size:.85rem; color:#B00020;">抹除後無法復原。</span>
    </p>

    <form method="POST" action="work_delete.php?id=<?= $id ?>">
      <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <button type="submit" name="confirm" value="1"
                class="btn btn-primary"
                style="background:#B00020; border-color:#B00020;">
          確定刪除
        </button>
        <a href="work.php?id=<?= $id ?>" class="btn btn-outline">← 取消</a>
      </div>
    </form>
  </div>

</div>

<?php include 'footer.php'; ?>