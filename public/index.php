<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use \Parsedown as Parsedown;

require __DIR__ . "./../vendor/autoload.php";

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

function getPosts($all = false) { 
    $path = "./../posts";
    $files = array_diff(scandir($path), array(".", ".."));
    $allPosts = [];
    foreach ($files as $file) {
        if (!is_dir("$path/$file")) {
            $post = parseFile($path, $file);
            if (isset($post->draft) && $post->draft == "true") {
                continue;
            } else {
                $allPosts[$post->modified] = $post;
            }
        }
    }
    krsort($allPosts);
    if ($all) {
        return $allPosts;
    }
    return array_slice($allPosts, 0, 5, true);

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
    $page = file_exists("./../pages/$page.md") ? parseFile("./../pages/", $page . ".md") : false;
    return (new PhpRenderer("./../layouts"))->render($response, "layout.php", [
        "content" => $page,
        "type" => "page",
        "menus" => menu(),
        "allPosts" => getPosts(),
    ]);
});

$app->run();
