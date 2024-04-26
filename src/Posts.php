<?php

function getPosts($all = false, $tag = ''): object
{
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
            } elseif ($post->modified > time()) {
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

function parseFile($path, $file): ?object
{
    $post = file_get_contents("$path/$file");
    if ($post === false) {
        return null;
    }
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
    $markdown = (new \ParsedownExtra)->text($post);
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

function menu(): array
{
    if (!file_exists("./../pages/.menu")) {
        return false;
    }
    $menuFile = explode("\n", file_get_contents("./../pages/.menu"));
    foreach ($menuFile as $item) {
        if (file_exists("./../pages/" . $item)) {
            $menuItems[] = parseFile("./../pages", $item);
        } elseif (strpos($item, 'http') !== false) {
            $link = new \StdClass();
            $link->file = $item;
            $link->title = preg_replace('/https{0,1}:\/\/(.*)/', "$1", $item);
            $menuItems[] = $link;
        } else {
            $link = new \StdClass();
            $link->file = $item;
            $link->title = ucwords($item);
            $menuItems[] = $link;
        }
    }
    return $menuItems;
}
