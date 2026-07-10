<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "DB file not found: $dbPath\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert users
    $now = (new DateTime())->format('Y-m-d H:i:s');
    $stmt = $pdo->prepare('INSERT INTO users (name,email,password,created_at,updated_at) VALUES (?,?,?,?,?)');
    $stmt->execute(['Mina','mina@example.com','$2y$10$placeholder', $now, $now]);
    $minaId = $pdo->lastInsertId();
    $stmt->execute(['Theo','theo@example.com','$2y$10$placeholder', $now, $now]);
    $theoId = $pdo->lastInsertId();

    // Insert posts for each user
    $insertPost = $pdo->prepare('INSERT INTO posts (user_id,text,image_url,embedding,authenticity_score,created_at,updated_at) VALUES (?,?,?,?,?,?,?)');
    $samples = [
        'Coffee mornings and quiet journaling set the tone for a grounded day.',
        'A new mural downtown feels like a burst of optimism and color.',
        'Weekend plans are simple: walk the river, try a new brunch spot, and talk for hours.',
    ];
    for ($i=0;$i<6;$i++){
        $userId = $i%2===0 ? $minaId : $theoId;
        $text = $samples[$i%count($samples)] . ' #' . $i;
        $image = $i%3===0 ? 'https://picsum.photos/seed/'.$i.'/800/600' : null;
        $embedding = json_encode(array_fill(0,384,0.0));
        $score = 0.5 + ($i%5)*0.1;
        $created = (new DateTime())->modify('-'.($i+1).' days')->format('Y-m-d H:i:s');
        $insertPost->execute([$userId,$text,$image,$embedding,$score,$created,$created]);
    }

    // Follows
    $pdo->exec("INSERT INTO follows (follower_id,followed_id,created_at,updated_at) VALUES ($minaId,$theoId,'$now','$now')");
    $pdo->exec("INSERT INTO follows (follower_id,followed_id,created_at,updated_at) VALUES ($theoId,$minaId,'$now','$now')");

    // Interactions: mark every other post as viewed by Mina
    $posts = $pdo->query('SELECT id FROM posts')->fetchAll(PDO::FETCH_COLUMN);
    $insertInt = $pdo->prepare('INSERT INTO interactions (user_id,post_id,type,created_at,updated_at) VALUES (?,?,?,?,?)');
    foreach ($posts as $postId) {
        if ($postId % 2 === 0) {
            $insertInt->execute([$minaId,$postId,'view',$now,$now]);
        }
    }

    // Print counts
    $counts = [];
    foreach (['users','posts','interactions','follows'] as $t) {
        $counts[$t] = $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
    }
    echo "Seeded OK\n";
    echo "Users: {$counts['users']}\n";
    echo "Posts: {$counts['posts']}\n";
    echo "Interactions: {$counts['interactions']}\n";
    echo "Follows: {$counts['follows']}\n";

} catch (Exception $e) {
    echo 'ERR: '. $e->getMessage() . "\n";
    exit(1);
}
