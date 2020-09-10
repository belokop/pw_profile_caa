<?php namespace ProcessWire;

region('content',files()->render('./includes/object-page.php',
				 array('page'   => $page,
				       'related'=> pages('limit=20, id!=$page->id, body*='.sanitizer()->selectorValue($page->title)),
				       'width'  => 300)));

