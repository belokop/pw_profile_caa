<?php namespace ProcessWire;

foreach(['prp_field','prp_year'] as $f){
  if($v=@$_POST[$f])        $_SESSION[$f] = $v;
  if (empty($_SESSION[$f])) $_SESSION[$f] = 'all'; }
if ($_SESSION['prp_field'] == $_SESSION['prp_year']) $_SESSION['prp_year'] = date('Y');

$items = findObjects((($keywords=@$_GET['keywords'])
		      ? "title|prp_authors%=$keywords"
		      : $s=sprintf("prp_field=%s, prp_day0>%s, prp_day0<%s",
				   $_SESSION['prp_field'],
				   ($f=\DateTime::createFromFormat('Y-m-d',$_SESSION['prp_year'].'-01-01')->format('U')),
				   ($t=\DateTime::createFromFormat('Y-m-d',$_SESSION['prp_year'].'-12-31')->format('U')))),
		     'preprint');

region('browserTitle', $page->title);
region('content',
       (empty($keywords) ? files()->render('./includes/prp-list-selector.php') : "").
       x("h5","keywords=$keywords date>".date("Y-m-d",$f)." date<".date('Y-m-d',$t)."  count=".count($items)).
       renderObjectList($items,
			$context="prp",
			$showPagination=false,
			$headline=(empty($keywords)?$page->title:"Search for \"$keywords\"")));
