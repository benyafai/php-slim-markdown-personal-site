# Personal Site based on Markdown posts.

The site is driven by PHP using the [Slim Framework](https://www.slimframework.com/docs/v4/)

You can enter your static pages into the /pages folder and they'll be available at yoursite.com/pagename

There is a file named `.menu` - any entries in here will be displayed inthe Nav bar at the top of each page in the order they appear in the file.

___
Blog posts go into the /posts folder and will be available at yoursite.com/post/postname

These blog posts will also show under 'Recent Posts'

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