<?php namespace ProcessWire;

region('browserTitle', $page->title);
region('content',
       renderObjectList(findObjects("prp_field=AP",'preprint'),
			"prp-list-item.php",
			$showPagination=false,
			$headline=$page->title));
