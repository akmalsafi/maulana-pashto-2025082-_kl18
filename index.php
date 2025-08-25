<?php

$db = new SQLite3(__DIR__ . '/data.db');

// NY: rendera artikelkort per kategori, sorterat på senaste insättning
function renderArticles($db, $category, $limit = 3) {
    $limit = (int)$limit;
    $sql = "
        SELECT id, title, excerpt, image, date
        FROM articles
        WHERE TRIM(category) = :category
        ORDER BY date DESC
        LIMIT $limit
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':category', $category, SQLITE3_TEXT);
    $res = $stmt->execute();

    echo '<div class="grid">';
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $img = trim($row['image'] ?? '') !== '' ? $row['image'] : 'assets/fallback.jpg';
        echo '<div class="card">';
        echo '<img src="'.htmlspecialchars($img).'" alt="" class="article-cover" loading="lazy">';
        echo '<h3 style="margin-top:.25rem"><a href="article.php?id='.$row['id'].'" style="text-decoration:none;color:inherit">'.htmlspecialchars($row['title']).'</a></h3>';
        echo '<p class="badge">'.htmlspecialchars($row['date']).'</p>';
        echo '<p>'.htmlspecialchars($row['excerpt']).'</p>';
        echo '<a class="btn" href="article.php?id='.$row['id'].'">لوستل</a>';
        echo '</div>';
    }
    echo '</div>';
}

?>


<!doctype html>
<html lang="ps" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>د سولې او روحانیت کتابتون – د مولانا وحیدالدین خان لیکنې</title>
<link rel="stylesheet" href="styles.css">

 <style type="text/css"> 
  .padded img { 
    padding-left: 20em; 
    padding-right: 20em; 
  } 
  </style>
</head>
<body>


 <?php include 'nav.php'; ?>





<main class="container">
 <section>
  <h2 style="margin:0 0 .2rem">📖 تازه مطالب</h2>
  <?php renderArticles($db, 'Dagens läsning', 3); ?>
</section>

<section>
  <h2 style="margin:0 0.5rem">📚 ځانګړي مطالب</h2>
  <?php renderArticles($db, 'Veckans läsning', 3); ?>
</section>

<section>
  <h2 style="margin:0 0 .5rem">الهامي ویناوې</h2>
  <?php renderArticles($db, 'Dagens inspiration', 3); ?>
</section>

</main>

<p>

<center>
  <img class="padded" src="assets/logo.svg" width="36" height="36" style="border-radius:10px"> 
 <img class="padded" src="assets/logo.svg" width="36" height="36" style="border-radius:10px"> 
 <img class="padded" src="assets/logo.svg" width="36" height="36" style="border-radius:10px"> 

</center>


<?php include 'footer.php'; renderFooter(); ?>

</body>
</html>
