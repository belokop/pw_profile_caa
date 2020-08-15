<?php namespace ProcessWire;
$url = $object->url;
$ttl = $object->title;
$caption = $page->label;
echo x("div class='object-list-item' style='margin-bottom:15px'",
       x("div class='uk-grid uk-grid-medium'",
	 x("div class='uk-width-1-1 uk-width-small-1-1' style='margin:auto'",
	   x("a href='$url'",x("img src='$img' alt='$ttl'")).'<br/>'.
	   x("a href='$url'",x("strong",$ttl)))));

	 