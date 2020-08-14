<?php namespace ProcessWire;

//region('headline',     __('Hermès and other'));
//region('browserTitle', __('Hermès and other'));
region('headline',     $page->template->getLabel());
region('browserTitle', $page->template->getLabel());

// display a random photo from this page to display at the top
if ($page->images && ($photo = $page->images->getRandom())){
  region('mainHeader',
	 files()->render('./includes/banner-photo.php',
			 array('photo'   => $photo->maxWidth(1600),
			       'caption' => sanitizer()->entitiesMarkdown($photo->description))));
}
  
if (empty($page->artworks_featured) && ($f=fields()->get('artworks_featured'))){
  $page->template->fieldgroup->add($f);
  $page->template->fieldgroup->save();
}
$items_featured = (empty($p=$page->artworks_featured)
		   ? (empty($f=$page->featured_artworks) ? [] : $f)
		   : $p->find("limit=4, sort=random"));

$items_spots = PageArray();
foreach(templates() as $tp) if(preg_match("/spot$/",$tp->name)) if ($p=pages()->findOne("template=$tp")) $items_spots->add($p);

region('content+',
       (count($items_featured)?renderObjectList($items_featured, 2, false, __('Featured Artworks')):"") .
       (count($items_spots)   ?renderObjectList($items_spots, 2, false, __('More Collections')):""));
