Infrared
========

Infrared registers clicks for pages. For each click, three values are registered:

- the x coordinate for the click
- the y coordinate for the click
- the elapsed time since page load

This data allows the creation of a replay of all clicks for a page, all starting at the time the page was loaded. The intensity of the display of the clicks is calculated based on the total number of clicks for that page.

###installation

*The viewer / server*

1. Install the project like you would normally do with any PHP project and point Apache to `/public/`.
2. Run `composer install`

*The js library*

Include the js file from `/public/plugin` in your project, and setup like this:

```js
$().infrared('init', {
    server_endpoint:'http://server.infrared.com/clicks',
});
```

where `http://server.infrared.com` is the url of the backend 
