<?php

function rss() {
$rss = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xml:base=\"https://"
        . $_SERVER["HTTP_HOST"] . "\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
<channel>
    <title>My Website</title>
    <link>https://" . $_SERVER["HTTP_HOST"] . "</link>
    <atom:link href=\"https://" . $_SERVER["HTTP_HOST"] . "/rss\" rel=\"self\" type=\"application/rss+xml\"/>
    <description>RSS for My Website</description>
    <language>en-GB</language>";
    $posts = getPosts(true);
    foreach ($posts->posts as $post) {
        $rss .= "
    <item>
        <title>" . $post->title . "</title>
        <pubDate>" . date("r", $post->modified) . "</pubDate>
        <link>https://" . $_SERVER["HTTP_HOST"] . "/" . $post->file . "</link>
        <description>" . str_replace("<", "&lt;", str_replace(">", "&gt;", $post->markdown)) . "</description>
        <dc:creator>Me!</dc:creator>
        <guid>https://" . $_SERVER["HTTP_HOST"] . "/" . $post->file . "</guid>
    </item>";
    }
    $rss .= "
</channel>
</rss>";
    return $rss;
}

function json() {
    $json = [
        "version" => "https://jsonfeed.org/version/1.1",
        "user_comment" => "This is a JSON feed. Subscribe by copying the URL from the address bar into your newsreader.",
        "title" => "My Website",
        "description" => "RSS for My Website",
        "home_page_url" => "https://" . $_SERVER["HTTP_HOST"],
        "feed_url" => "https://" . $_SERVER["HTTP_HOST"] . "/json",
        "authors" => [
            [
                "name" => "Me!",
                "url" => "https://" . $_SERVER["HTTP_HOST"],
                // "avatar" => "https://" . $_SERVER["HTTP_HOST"] . "uploads/avatar.jpg"
            ]
        ],
        "language" => "en-GB",
        "items" => [],
    ];
    $posts = getPosts(true);
    foreach ($posts->posts as $post) {
        $json["items"][] = [
            "id" => $post->file,
            "title" => $post->title,
            "url" => "https://" . $_SERVER["HTTP_HOST"] . "/" . $post->file,
            "date_published" => date("c", $post->modified),
            "tags" => $post->tags,
            "content_html" => $post->markdown,
        ];
    }
    return json_encode($json);
}
