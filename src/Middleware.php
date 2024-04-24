<?php

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
