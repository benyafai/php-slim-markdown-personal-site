<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=isset($content->title) && strtolower($content->title) != 'index' ? "$content->title | " : null?>My Website</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="container">

        <!-- Navigation -->
        <nav>
            <a href="/">Home</a>
            <?php foreach ($menus as $menu): ?>
                <a href="/<?=$menu->file?>"><?=$menu->title?></a>
            <?php endforeach; // $menus ?>
        </nav>

        <!-- Recent Blog Posts for this Tag -->
        <?php if ($allPosts): ?>
            <hr />
            <div class="recent">
                <h2>Recent Posts for tag: "<?=$tag?>"</h2>
                <?php foreach ($allPosts as $recent): ?>
                    <p><?=date("Y-m-d", $recent->modified)?> <a href="/post/<?=$recent->file?>"><?=$recent->title?></a></p>
                <?php endforeach; // $allPosts ?>
            </div>
        <?php endif; // $allPosts ?>
    </div>
</body>
</html>