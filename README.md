# Personal Site based on Markdown posts.

![ScreenShot](https://raw.githubusercontent.com/benyafai/php-slim-markdown-personal-site/master/screenshot.png)

The site is driven by PHP using the [Slim Framework](https://www.slimframework.com/docs/v4/)

You can enter your static pages into the /pages folder and they'll be available at yoursite.com/pagename

There is a file named `.menu` - any entries in here will be displayed inthe Nav bar at the top of each page in the order they appear in the file.

Blog posts go into the /posts folder and will be available at yoursite.com/post/postname

These blog posts will also show under 'Recent Posts' header on every page.

___

There is some basic meta data in every page or post: 
- the title that will show on the page or as links text
  - If ommitted, we'll take the filename
- the date of the post (used to order posts)
  - If ommitted, we'll take the file's last modified date 
- draft status - whether to show in 5 most 'Recent Posts'
  - If omitted, the page/post is visible

```
---
title: The Title Of My Page
date: 2024-01-26
draft: true
---
```

---

there are two main .php files:

`/public/index.php` which is the brains of the operation. 

It routes our URLs, parses metadata, lists recent posts, and converts markdown to HTML markup.

`/layouts/layout.php`

This is the only page that actually gets rendered and styled for users to see. 
If you want to change the design (layout) of the site then you only need to change this file and `/public/style.css`

___

To get started, you'll need to navigate to the root of this project in terminal and run `composer install`

