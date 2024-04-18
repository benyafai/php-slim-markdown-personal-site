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

        <!-- Main Content -->
        <?php if ($content): ?>
            <?php if (strtolower($content->title) != 'index'): ?>
                <h1><?=$content->title?></h1>
            <?php endif; ?>
            <?php if ($type == 'post'): ?>
                <p><?=$content->date?><p>
                <?php if ($content->tags): ?>
                    <p><small>Tags: <?=implode(', ', $content->tagsFormatted)?></small>
                <?php endif; // $content->tags ?>
                <hr />
            <?php endif; // $type == 'post' ?>
            <?=$content->markdown?>
        <?php else: // $content ?>
            <h1>404</h1>
            <p>This page could not be found!</p>
        <?php endif; // $content ?>

        <!-- Recent Blog Posts -->
        <?php if ($allPosts->posts): ?>
            <hr />
            <div class="recent">
                <h2>Recent Posts</h2>
                <?php foreach ($allPosts->posts as $recent): ?>
                    <p><?=date("Y-m-d", $recent->modified)?> <a href="/post/<?=$recent->file?>"><?=$recent->title?></a></p>
                <?php endforeach; // $allPosts ?>
            </div>
        <?php endif; // $allPosts ?>
    </div>
</body>
</html>