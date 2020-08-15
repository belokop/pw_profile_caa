<?php namespace ProcessWire;
/**
 * List of preprints with managing options
 */

region('browserTitle', $page->title);
region('content',
       renderObjectList(findObjects("prp_field=AP",'preprint'),
			$cols = 1,
			$showPagination=false,
			$headline=$page->title));
$context="prp"))-list-item.php",

