<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . "./../vendor/autoload.php";

$app = AppFactory::create();

require __DIR__ . "./../src/Middleware.php";
require __DIR__ . "./../src/Posts.php";
require __DIR__ . "./../src/Feeds.php";

$app->get("/feed", function (Request $request, Response $response, $args) {
    $response->getBody()->write(rss());
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
