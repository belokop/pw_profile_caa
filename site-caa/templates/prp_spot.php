<?php namespace ProcessWire;

$prp_groups = $GLOBALS['prp_groups'] = ['all'=> 'All preprints',
					'AP' => 'Astrophysics',
					'CM' => 'Condensed Matter',
					'HE' => 'High Energy Physics'];

foreach(['prp_field','prp_year'] as $f){
  if($v=@$_POST[$f])        $_SESSION[$f] = $v;
  if (empty($_SESSION[$f])) $_SESSION[$f] = 'all'; }
if ($_SESSION['prp_field'] == $_SESSION['prp_year']) $_SESSION['prp_year'] = date('Y');

$selector = "";
if ($keywords=@$_GET['keywords']){
  $selector .= "title|prp_authors%=$keywords";
  $headline = "Search for \"$keywords\"";
}else{
  if(($f=$_SESSION['prp_field'])!== 'all') $selector .= "prp_field=$f, "; 
  if(($y=$_SESSION['prp_year']) !== 'all') $selector .= sprintf("prp_day0>%s, prp_day0<%s",
								\DateTime::createFromFormat('Y-m-d',"$y-01-01")->format('U'),
								\DateTime::createFromFormat('Y-m-d',"$y-12-31")->format('U'));
  $headline = $prp_groups[$f].' '.($y=='all'? 'all years' : $y);
}

region('browserTitle', $page->title);
region('content',
       (empty($keywords) ? files()->render('./includes/prp-list-selector.php') : "").
       renderObjectList(findObjects($selector,'prp_preprint',$limit=9999),
			$context='tr',
			$showPagination=true,
			$headline=$headline));
