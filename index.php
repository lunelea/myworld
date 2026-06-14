<?php
session_start();
include 'header.php';
?><?php
session_start();
include 'db.php';

// 如果有登入，從資料庫撈新資料
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username, email, bio, hometown, grade FROM users WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div class="page-wrap">

  <!-- Hero -->
  <div style="text-align:center; padding: 2.5rem 0 2rem;">
    <div style="
      width: 110px; height: 110px; border-radius: 50%;
      background: linear-gradient(135deg, #F5E642, #C6DE41);
      margin: 0 auto 1.2rem;
      display: flex; align-items: center; justify-content: center;
      font-size: 3rem; box-shadow: 0 6px 24px rgba(180,200,60,.3);
    "><img src="images\IMG_0265.PNG" alt="頭像" class="profile-img"></div>
    <h1 style="font-size: 2.2rem; margin-bottom: .3rem;">About me</h1>
    <p style="color: #6A7A55; font-size: 1.05rem;">─── ✧ 願屬於你的盛夏永不結束 ✧ ───</p>
  </div>

  <div class="card" style="margin-bottom: 1.5rem;">
  <h2>🌿 關於我</h2>
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">

    <div>
      <p style="font-size:.8rem;color:#8A9A6A;font-weight:700;margin-bottom:.3rem;">📌 姓名</p>
      <p style="font-size:1.05rem;">✧邱子瑄✧</p>
    </div>

    <div>
      <p style="font-size:.8rem;color:#8A9A6A;font-weight:700;margin-bottom:.3rem;">🏫 學校 / 科系</p>
      <p style="font-size:1.05rem;">✧ 勤益科技大學 資訊工程系 ✧</p>
    </div>

    <div>
      <p style="font-size:.8rem;color:#8A9A6A;font-weight:700;margin-bottom:.3rem;">🎂 年級</p>
      <p style="font-size:1.05rem;">
        <?= ($user && $user['grade']) ? htmlspecialchars($user['grade']) : '大二' ?>
      </p>
    </div>

    <div>
      <p style="font-size:.8rem;color:#8A9A6A;font-weight:700;margin-bottom:.3rem;">📍 來自</p>
      <p style="font-size:1.05rem;">
        <?= ($user && $user['hometown']) ? htmlspecialchars($user['hometown']) : '台中' ?>
      </p>
    </div>
  </div>
  </div>

  <!-- 興趣愛好 -->
   <div class="card" style="margin-bottom: 1.5rem;">
     <h2>✨ 興趣 &amp; 愛好</h2>
     <div style="display:flex; flex-wrap:wrap; gap:.6rem;">
       <?php
        $interests_str = ($user && $user['interests']) ? $user['interests'] : 'VOCALOID,《特殊傳說》,Minecraft,蛋糕、布丁';
        $interest_list = array_map('trim', explode(',', $interests_str));
      foreach ($interest_list as $item):
        if ($item === '') continue;
       ?>
        <span class="lemon-accent" style="font-size:.95rem; padding:.3rem .9rem;">
           <?= htmlspecialchars($item) ?>
         </span>
        <?php endforeach; ?>
      </div>
    </div>

  <div class="card" style="margin-bottom: 1.5rem;">
  <h2>💬 自我介紹</h2>
  <p style="line-height:1.9; color:#4A5A35;">
    <?php if ($user && $user['bio']): ?>
      <?= nl2br(htmlspecialchars($user['bio'])) ?>
    <?php else: ?>
      一個厭惡數學的資工生 <br>－－請多指教
    <?php endif; ?>
  </p>
  </div>

  <div class="card" style="margin-bottom: 1.5rem;">
   <h2>🍋 技能</h2>
    <div style="display:flex; flex-direction:column; gap:1rem;">
      <?php
     $skills_str = ($user && $user['skills']) ? $user['skills'] : '寫作:60,繪畫:50,程式設計:40,數學:30';
     $skill_list = array_map('trim', explode(',', $skills_str));
     foreach ($skill_list as $item):
       if ($item === '') continue;
        $parts = explode(':', $item);
        $name  = trim($parts[0]);
        $pct   = intval(trim($parts[1] ?? 50));
       $pct   = max(0, min(100, $pct));
      ?>
        <div>
         <div style="display:flex; justify-content:space-between; font-size:.9rem; color:#6A7A55; margin-bottom:.4rem;">
           <span><?= htmlspecialchars($name) ?></span>
           <span><?= $pct ?>%</span>
          </div>
          <div style="background:#E8EDD8; border-radius:99px; height:8px;">
           <div style="width:<?= $pct ?>%; background:linear-gradient(90deg,#F5E642,#C6DE41); border-radius:99px; height:8px;"></div>
         </div>
       </div>
      <?php endforeach; ?>
   </div>
  </div>

  <!-- 如果已登入，顯示連結 -->
  <?php if(isset($_SESSION['user_id'])): ?>
  <div style="text-align:center; margin-top: 1.5rem;">
    <a href="profile_edit.php" class="btn btn-primary">✏️ 編輯我的資料</a>
  </div>
  <?php else: ?>
  <div style="text-align:center; margin-top: 1.5rem;">
    <a href="login.php" class="btn btn-outline">🔑 登入帳號</a>
  </div>
  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
