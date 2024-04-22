<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use \Parsedown as Parsedown;

require __DIR__ . "./../vendor/autoload.php";

$app = AppFactory::create();

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(\Slim\Exception\HttpNotFoundException::class, function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) {
    $response = new \Slim\Psr7\Response();
    return (new PhpRenderer("./../layouts"))->render($response, "layout.php", [
        "content" => null,
        "type" => "page",
        "menus" => menu(),
        "allPosts" => $showAllPosts ? getPosts() : null,
    ]);
});

function getPosts($all = false, $tag = '') {
    $path = "./../posts";
    $files = array_diff(scandir($path), array(".", ".."));
    $allPosts = [];
    $allTags = [];
    foreach ($files as $file) {
        if (!is_dir("$path/$file")) {
            $post = parseFile($path, $file);
            if (isset($post->tags)) {
                $allTags = array_unique(array_merge($post->tags, $allTags), SORT_REGULAR);
            }
            if (isset($post->draft) && $post->draft == "true") {
                continue;
            } elseif ($tag != '' && isset($post->tags) && !in_array($tag, $post->tags)) {
                continue;
            } else {
                $allPosts[$post->modified] = $post;
            }
        }
    }
    krsort($allPosts);
    krsort($allTags);
    if ($all) {
        return (object) [
            "posts" => $allPosts,
            "tags" => $allTags,
        ];
    }
    return (object) [
        "posts" => array_slice($allPosts, 0, 5, true),
        "tags" => $allTags,
    ];
}

function parseFile($path, $file) {
    $post = file_get_contents("$path/$file");
    if (substr($post, 0, 3) == "---") {
        $endOfMeta = strpos($post, "---", 1);
        $meta = substr($post, 4, $endOfMeta - 5);
        $meta = explode("\n", $meta);
        $postMeta = new \stdClass();
        foreach ($meta as $key => $value) {
            preg_match('/^(\w*):\s?(.*)/', $value, $meta_array);
            list($fullString, $metaKey, $metaValue) = $meta_array;
            $postMeta->$metaKey = $metaValue;
            unset($meta[$key]);
        }
        if ($postMeta->tags) {
            $postMeta->tags = array_map('trim', explode(",", $postMeta->tags));
            $postMeta->tagsFormatted = [];
            foreach ($postMeta->tags as $key => $tag) {
                $postMeta->tagsFormatted[] = "<a href=\"/tags/$tag\">$tag</a>";
            }
        }
        $post = substr($post, $endOfMeta + 3);
    }
    $modified = isset($postMeta->date) ? strtotime($postMeta->date) : filemtime("$path/$file");
    $markdown = (new Parsedown)->text($post);
    $markdown = str_replace("href=\"/", "href=\"https://" . $_SERVER["HTTP_HOST"] . "/", $markdown);
    $return = $postMeta ?? new StdClass();
    $return->modified = $modified;
    $return->file = str_replace(".md", "", $file);
    $return->title = $postMeta->title ?? mb_convert_case(str_replace("-", " ", $return->file), MB_CASE_TITLE, "UTF-8");
    $return->date = date("l jS \of F Y", $modified);
    $return->path = "$path/$file";
    $return->markdown = $markdown;
    return $return;
}

function menu() {
    if (!file_exists("./../pages/.menu")) {
        return false;
    }
    $menuFile = explode("\n", file_get_contents("./../pages/.menu"));
    foreach ($menuFile as $item) {
        parseFile("./../pages", $item);
        $menuItems[] = parseFile("./../pages", $item);
    }
    return $menuItems;
}

$app->get("/feed", function (Request $request, Response $response, $args) {
    $rss = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xml:base=\"https://"
            .    $_SERVER["HTTP_HOST"] . "\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
    <channel>
        <title>My Website</title>
        <link>https://" . $_SERVER["HTTP_HOST"] . "</link>
        <atom:link href=\"https://" . $_SERVER["HTTP_HOST"] . "/rss\" rel=\"self\" type=\"application/rss+xml\"/>
        <description>RSS for My Website</description>
        <language>en-gb</language>";
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
        $response->getBody()->write($rss);
        return $response->withHeader("Content-Type", "application/xml");
});

$app->get("/tags[/{tag}]", function (Request $request, Response $response, $args) {
    return (new PhpRenderer("./../layouts"))->render($response, "tags.php", [
        "content" => $post,
        "type" => "post",
        "menus" => menu(),
        "tag" => $args["tag"],
        "allPosts" => getPosts(true, $args["tag"]),
    ]);
});

$app->get("/post/[{post}]", function (Request $request, Response $response, $args) {
    $post = $args["post"] ?? null;
    $post = file_exists("./../posts/$post.md") ? parseFile("./../posts/", $post . ".md") : false;
    return (new PhpRenderer("./../layouts"))->render($response, "layout.php", [
        "content" => $post,
        "type" => "post",
        "menus" => menu(),
        "allPosts" => getPosts(),
    ]);
});

$app->get("/[{page}]", function (Request $request, Response $response, $args) {
    $page = isset($args["page"]) ? strtolower($args["page"])  : "index";
    $showAllPosts = $page == "index" ? true : false;
    $page = file_exists("./../pages/$page.md") ? parseFile("./../pages/", $page . ".md") : false;
    return (new PhpRenderer("./../layouts"))->render($response, "layout.php", [
        "content" => $page,
        "type" => "page",
        "menus" => menu(),
        "allPosts" => $showAllPosts ? getPosts() : null,
    ]);
});

$app->run();
