<?php
$db = new SQLite3(__DIR__ . '/data.db');
$id = $_GET['id'] ?? 0;
$stmt = $db->prepare("SELECT * FROM articles WHERE id=:id");
$stmt->bindValue(':id',$id,SQLITE3_INTEGER);
$res = $stmt->execute();
$article = $res->fetchArray(SQLITE3_ASSOC);

if (!$article) {
    echo "❌ Artikel hittades inte.";
    exit;
}

// Hämta tre slumpade artiklar från Dagens läsning
$relatedDaily = $db->query("SELECT id,title,excerpt,image FROM articles WHERE category='Dagens läsning' AND id != $id ORDER BY RANDOM() LIMIT 3");
// Hämta tre slumpade artiklar från Veckans läsning
$relatedWeekly = $db->query("SELECT id,title,excerpt,image FROM articles WHERE category='Veckans läsning' AND id != $id ORDER BY RANDOM() LIMIT 3");
?>
<!doctype html>
<html lang="ps" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($article['title']); ?></title>
<link rel="stylesheet" href="styles.css">
<style>
  /* Bakgrundsmönster + halvtransparent container */
  body {
    background: url('assets/pattern-light.png') repeat;
  }
  .article-container {
    background: rgba(255,255,255,0.2); /* halvtransparent vit så mönstret syns */
    border-radius: 12px;
    padding: 2rem;
    margin-top: 0.01rem;
  }
  .article-layout {
    display: flex;
    flex-direction: row;
    gap: 2rem;
    align-items: flex-start;
  }
  .article-text { flex: 2; }
  .article-image { flex: 1; }
  .article-image img {
    max-width: 100%;
    border-radius: 10px;
  }
  @media (max-width: 768px) {
    .article-layout { flex-direction: column; }
    .article-image { margin-top: 1rem; }
  }

  .related-section { margin-top: 3rem; text-align:center; }
  .related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(220px,1fr));
    gap: 1.5rem;
    justify-items: center;
  }
  .related-card {
    background: rgba(255,255,255,1); /* samma halvtransparent för korten */
    padding: .75rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform .2s;
    width: 100%;
    max-width: 250px;
    text-align: center;
  }
  .related-card:hover {
    transform: translateY(-3px);
  }
  .related-card img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 6px;
  }
  .related-card h4 {
    margin: .5rem 0 .25rem;
    font-size: 1.1rem;
  }
  .related-card p {
    font-size: .85rem;
    color: #333;
  }
</style>
</head>
<body>
<?php include 'nav.php'; ?>

<main class="container">
  <div class="article-container">
    <h1><?php echo htmlspecialchars($article['title']); ?></h1>
    <p class="badge"><?php echo htmlspecialchars($article['date']); ?> · <?php echo htmlspecialchars($article['category']); ?></p>
    <div class="article-layout">
      <div class="article-text">
        <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
      </div>
      <?php if ($article['image']): ?>
      <div class="article-image">
        <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="">
      </div>
      <?php endif; ?>
    </div>
  </div>

  <p style="text-align:center;margin-top:1rem">
    <a class="btn" href="index.php">⬅️ بېرته کور ته</a>
  </p>

  <div class="related-section">
    <h2>📖 ورته مطالب</h2>
    <div class="related-grid">
      <?php while ($row = $relatedDaily->fetchArray(SQLITE3_ASSOC)): ?>
        <div class="related-card">
          <img src="<?php echo htmlspecialchars($row['image'] ?: 'assets/fallback.jpg'); ?>" alt="">
          <h4><a href="article.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h4>
          <p><?php echo htmlspecialchars($row['excerpt']); ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <div class="related-section">
    <h2>📚 ځانګړي مطالب</h2>
    <div class="related-grid">
      <?php while ($row = $relatedWeekly->fetchArray(SQLITE3_ASSOC)): ?>
        <div class="related-card">
          <img src="<?php echo htmlspecialchars($row['image'] ?: 'assets/fallback.jpg'); ?>" alt="">
          <h4><a href="article.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h4>
          <p><?php echo htmlspecialchars($row['excerpt']); ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</main>

<?php include 'footer.php'; renderFooter(); ?>

</body>
</html>
