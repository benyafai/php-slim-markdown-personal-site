<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=isset($content->title) && strtolower($content->title) != 'index' ? "$content->title | " : null?>My Website</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="alternate" type="application/rss+xml" title="RSS Feed" href= "https://<?=$_SERVER["HTTP_HOST"]?>/feed">
    <link rel="alternate" type="application/json" title="JSON Feed" href="https://<?=$_SERVER["HTTP_HOST"]?>/json">
</head>