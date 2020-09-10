<?php namespace ProcessWire;
$cols=3;
region('content',
       files()->render('./includes/object-page.php',
		       array('page'   => $page,
			     'related'=> pages('limit=20, id!=$page->id, body*='.sanitizer()->selectorValue($page->title)),
			     'width'  => 300)).
       x("ul class='uk-grid uk-grid-width-medium-1-$cols'",
	 page()->children->each("<li><a href='{url}'>{title}</a></li>")));
