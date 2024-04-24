<?php include __DIR__ . "/header.php" ?>
<body>
    <div class="container">

        <!-- Navigation -->
        <nav>
            <a href="/">Home</a>
            <?php foreach ($menus as $menu): ?>
                <a href="/<?=$menu->file?>"><?=$menu->title?></a>
            <?php endforeach; // $menus ?>
        </nav>

        <?php if ($tag && $allPosts->posts): ?>
            <div class="recent">
                <h1>Tag: <?=$tag?></h1>
                <?php foreach ($allPosts->posts as $recent): ?>
                    <p><?=date("Y-m-d", $recent->modified)?> <a href="/post/<?=$recent->file?>"><?=$recent->title?></a></p>
                <?php endforeach; // $allPosts ?>
            </div>
        <?php else: // $tag ?>
            <div>
                <h1>Tags</h1>
                <?php foreach ($allPosts->tags as $thisTag): ?>
                    <h3><a href="/tags/<?=$thisTag?>"><?=$thisTag?></a></h3>
                    <?php foreach ($allPosts->posts as $recent): ?>
                        <?php if (isset($recent->tags) && in_array($thisTag, $recent->tags)): ?>
                            <p><?=date("Y-m-d", $recent->modified)?> <a href="/post/<?=$recent->file?>"><?=$recent->title?></a></p>
                        <?php endif; // this tag ?>
                    <?php endforeach; // $allPosts ?>
                <?php endforeach; // $allPosts ?>
            </div>
        <?php endif; // $tag ?>

        <!-- Recent Blog Posts for this Tag -->
        <?php if ($allPosts): ?>

        <?php endif; // $allPosts ?>
    </div>
</body>
</html>