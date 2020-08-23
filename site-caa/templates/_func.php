<?php namespace ProcessWire;

/***************************************************************************************
 * SHARED ARTWORK FUNCTIONS
 *
 * The following functions find and render artworks are are defined here so that
 * they can be used by multiple template files.
 *
 */

/**
 * Returns an array of valid artwork sort properties
 *
 * The keys for the array are normally the field names
 * The values for the array are the printable labels
 *
 * @return array
 *
 */

if (!defined('cnf_CLI')) define('cnf_CLI',false);

/**
 *
 */
function getValidSorts($context='artwork') {

  return [];
  
  foreach (array('brand'          => 'A-Z|Z-A',
		 'year'           => 'Oldest|Newest',
		 'rarity'         => 'A-Z|Z-A',
		 'popularity'     => 'A-Z|Z-A',
		 'person'         => 'A-Z|Z-A',
		 'name'           => 'A-Z|Z-A',
		 'parent.name'    => 'A-Z|Z-A',
		 'images.count'   => 'Least|Most',
		 ) as $item => $sort){
    $label = '';
    if    ($item == 'parent.name')  $label = (empty($context)?"":templates()->get(templates()->get($context)->parentTemplates[0])->getLabel());
    elseif($item == 'name')         $label = (empty($context)?"":templates()->get($context)->getLabel());
    elseif($item == 'images.count') $label = fields()->get('images')->getLabel();
    elseif(!empty($context))  foreach(templates()->get($context)->fields as $f) { if (strpos($f,$item)!==false) $label = fields()->get($f)->getLabel(); }
    if (empty($label)) continue;
    $directions = explode('|',$sort);
    foreach (array('','-') as $k=>$d) $reply [$d.$item] = sprintf("%s (%s)",$label, $directions[$k]);
  }
  return (empty($reply) ? array() : $reply);
}

/*
 * Brackets
 */
function x($tag, $text=''){
  if     ($text === Null) return 'Null';
  elseif (empty($tag))    return $text;
  elseif (cnf_CLI)        return strip_tags($text);

  // 'x' is an "empty" tag
  $tag_clean = preg_replace('/ .*/','',$tag);
  if ($tag_clean === 'x') return $text;

  // Usual brackets
  if(in_array($tag_clean,array('input','img'))) return "<$tag $text />\n";

  // Usual brackets
  switch($tag_clean){
  case '"':  if (!isset($reply))  $reply = '"'.str_replace('"',"'",$text).'"'; return $reply;
  case "'":  if (!isset($reply))  $reply = "'".str_replace("'",'"',$text)."'"; return $reply;
  case "[":  if (!isset($reply))  $reply = '['.$text.']';
  case "(":  if (!isset($reply))  $reply = '('.$text.')';
  case "<":  if (!isset($reply))  $reply = '<'.$text.'>';
    return $reply;
  }

  // HTML tag as a "bracket"
  list($delim,$postfix) = (preg_match('/^(form|select|style)/i',$tag)
			   ? array("\n","")
			   : array("","\n"));
  if (preg_match('/^(div|ul|form|select|style)/i',$tag)) $delim="\n"; else $delim="";
  return join($delim, array("", "<$tag>", $text, "</$tag_clean>")).$postfix;
}

/**
 * Find Object pages using criteria from the given selector string.
 *
 * Serves as a front-end to $pages->find(), filling in some of the redundant
 * functionality used by multiple template files.
 *
 * @param string $selector
 * @return PageArray
 *
 */
function findObjects($selector,$template_name='artwork',$limit=20) {

  $validSorts = getValidSorts($template_name);

  // check if there is a valid 'sort' var in the GET variables
  $sort = sanitizer('name', input()->get('sort'));

  // if no valid sort, then use 'title' as a default
  if(!$sort || !isset($validSorts[$sort])) $sort = 'name';

  // whitelist the sort value so that it is retained in pagination
  if($sort != 'name') input()->whitelist('sort', $sort);

  // expand on the provided selector to limit it to $limit sorted objects
  $selector = (empty($template_name)?"":"template=$template_name, ")."limit=$limit, " . trim($selector, ", ");

  // check if there are any keyword searches in the selector by looking for the presence of ~= operator.
  // if present, then omit the 'sort' param, since ProcessWire sorts by
  // relevance when no sort specified.
  if(strpos($selector, "~=") === false) $selector .= ", sort=$sort";

  // now call upon ProcessWire to find the objects for us
  $objects = pages($selector);

  return $objects;
}

/**
 * Render the <thead> portion of a Object list table
 *
 * @return string
 *
 */
function renderObjectListSort($template_name='artwork') {

	// query string that will be used to retain other GET variables in searches
	input()->whitelist->remove('sort');
	$queryString = input()->whitelist->queryString();
	if($queryString) $queryString = sanitizer('entities', "&$queryString");

	// get the 'sort' property, if it's present
	$sort = input()->get('sort');
	$validSorts = getValidSorts($template_name);

	// validate the 'sort' pulled from input
	if(!$sort || !isset($validSorts[$sort])) $sort = 'name';

	$options = array();
	$selectedLabel = '';

	// generate options
	foreach($validSorts as $key => $label) {
		if($key === $sort) $selectedLabel = $label;
		$options["./?sort=$key$queryString"] = $label;
	}

	// render output
	$out = files()->render('./includes/object-list-sort.php',
			       array('options' => $options,
				     'selectedLabel' => $selectedLabel
				     ));
	
	return $out;
}

/**
 * Render a list of pages
 *
 * @param PageArray $pages Objects to render
 * @param string $cols Number of columns OR (if not numeric) context
 * @param bool $showPagination Whether pagination links should be shown
 * @param string $headline
 * @return string The rendered markup
 *
 */
function renderObjectList(PageArray $pages, $cols=1, $showPagination=true, $headline='') {

        if (!count($pages)) return;
	$pagination = '';
	$sortSelect = '';
	$items = array();

	// includes name
	if (!is_numeric($cols)){
	  $context = $cols;
	  $showPagination = false;
	  $cols = 1;
	}else{
	  $context = 'ul';
	}

	if($showPagination && $pages->count()) {
	  $headline = $pages->getPaginationString('Objects'); // i.e. Objects 1-10 of 500
	  $pagination = renderPagination($pages); // pagination links
	  $sortSelect = renderObjectListSort($pages->first->template->name);
	}

	foreach($pages as $object) {
	  $items[] = renderObjectListItem($object, $context);
	}

	$selector = (string) $pages->getSelectors();
	//if($selector) $selector = makePrettySelector($selector);
	
	$out = files()->render("./includes/${context}-list.php",
			       array('context'=> $context,
				     'cols'   => $cols,
				     'pages'  => $pages,
				     'headline'=> $headline,
				     'items' => $items,
				     'pagination' => $pagination,
				     'sortSelect' => $sortSelect,
				     'selector' => $selector,
				     ));
		
	return $out;
}

/**
 * Render a single object for presentation in a object list
 *
 * @param Page $object The Object to render
 * @return string
 *
 */
function renderObjectListItem(Page $page, $context='ul'){
  
  /** @var Pageimages $images */
  $images = $page->get('images');
  
  // make a thumbnail if the first object image
  if(!empty($images) && ($image = $images->first())) {
    // our thumbnail is 200px wide with proportional height
    $thumb = $image->width(200);
    $img   = $thumb->url;
  } else {
    $img = config()->urls->templates . "styles/images/photo_placeholder.png";
  }
  
  // here's a fun trick, set what gets displayed when value isn't available.
  // the property "unknown" is just something we made up and are setting to the page.
  $page->set('unknown', '??');
  
  // Object caption
  foreach (getTaggedFields($page,'caption') as $f){ // av_duty|br_duty|aw_brand etc
    $v = $page->get($f);
    if ($v instanceof PageArray){
      $caption = $v->each("{title}<br>");
    }elseif ($v->type instanceof FieldtypeOptions){
      if (!count($page->$f_name)) continue;
      $value = $page->$f_name->last->getTitle();
    }else{
      if (is_object($v) && empty($value = $v->title)) $value = $v;
      if (!empty($value)) $caption = $value;
    }
  }
  if (empty($caption) && !empty($page->parent)) $caption = $page->parent->get("title");
  
  $out = files()->render("./includes/${context}-list-item.php", // say, object-list-item.php
			 array('page' => $page,
			       'img'  => $img,
			       'caption' => @$caption,
			       'summary' => summarizeText(strip_tags($page->get('body')), 100)
			       ));
  
  return $out;
}

/**
 * ProcessWire pagination nav for UIkit
 *
 * @param PageArray $items
 * @return string
 *
 */
function renderPagination(PageArray $items) {

        if(!$items->getLimit() || $items->getTotal() <= $items->getLimit()) return '';
        $page = page();
        if(!$page->template->allowPageNum) {
                return "Pagination is not enabled for this template";
        }

        // customize the MarkupPagerNav to output in Foundation-style pagination links
        $options = array(
                'numPageLinks' => 5,
                'nextItemLabel' => '<i class="uk-icon-angle-double-right"></i>',
                'nextItemClass' => '',
                'previousItemLabel' => '<span><i class="uk-icon-angle-double-left"></i></span>',
                'previousItemClass' => '',
                'lastItemClass' => '',
                'currentItemClass' => 'uk-active',
                'separatorItemLabel' => '<span>&hellip;</span>',
                'separatorItemClass' => 'uk-disabled',
                'listMarkup' => "<ul class='uk-pagination uk-text-left'>{out}</ul>",
                'itemMarkup' => "<li class='{class}'>{out}</li>",
                'linkMarkup' => "<a href='{url}'>{out}</a>",
                'currentLinkMarkup' => "<span>{out}</span>"
        );

        $pager = modules('MarkupPagerNav');
        $pager->setBaseUrl($page->url);

        return $pager->render($items, $options);
}

/**
 * Make the selector better for display readability
 *
 * Since we're displaying the selector to screen for demonstration purposes, this method optimizes the
 * selector is the most readable fashion and removes any parts that aren't necessary
 *
 * This is not something you would bother with on a site that wasn't demonstrating a CMS. :)
 *
 * @param string $selector
 * @return string
 *
 */
function makePrettySelector($selector) {
  if(preg_match('/(person|parent)=(\d+)/', $selector, $matches)) {
    if($page = pages()->get($matches[2]))
      $selector = str_replace($matches[0], "$matches[1]={$page->path}", $selector);
    if($matches[1] == 'parent') $selector = str_replace("template=artwork, ", "", $selector); // template not necessary here
  }
  $selector = sanitizer('entities', $selector);
  $span = "<span class='uk-text-nowrap'>";
  $selector = $span . str_replace(", ", ",</span> $span ", $selector) . "</span>";
  return $selector;
}

/**
 * Generate a summary from the given block of text or HTML and truncate to last sentence
 *
 * @param string $text
 * @param int $maxLength
 * @return string
 *
 */
function summarizeText($text, $maxLength = 500) {

        if(!strlen($text)) return '';
        $summary = trim($text);

        // Get the first phrase
        if (stripos($summary,"<p>") !== false && stripos($summary,"</p>") !== false){
          $t = explode("</p>",str_replace("</P>","</p>",$summary));
          return trim(str_ireplace("<p>","",$t[0]))."&nbsp;...";
        }

        if(strlen($summary) <= $maxLength) return $summary;
        $summary = strip_tags($summary);
        $summary = substr($summary, 0, $maxLength);
        $lastPos = 0;

        foreach(array('. ', '!', '?') as $punct) {
                // truncate to last sentence
                $pos = strrpos($summary, $punct);
                if($pos > $lastPos) $lastPos = $pos;
        }

	// if no last sentence was found, truncate to last space
	if(!$lastPos) $lastPos = strrpos($summary, ' ');
        if ($lastPos) $summary = substr($summary, 0, $lastPos + 1); // and truncate to last sentence

        return trim($summary)."&nbsp;...";
}

/**
 *
 */
function getSpotURLs(){
  global $SPOT_id, $SPOT_url, $SPOT_search;
  // Are we in the sub-site?
  $uri = $_SERVER['REQUEST_URI'];
  preg_match(";(/[^/]*home)?(/([a-z]*)_?spot/);",$uri,$url_match);
  list($SPOT_url,$SPOT_id) = (empty($url_match[0])
                              ? (strpos($uri,'/h_')
                                 ? array('','h')
                                 : array('',''))
                              : array(substr($url_match[0],1), $url_match[3]));
  $SPOT_search = config('urls')->root . $SPOT_url . $SPOT_id . "_search/";
  if (!@$GLOBALS[__function__]++) echo str_replace('</pre><pre>', '; ', _formatData($uri)._formatData($url_match)._formatData("$SPOT_id, $SPOT_url, $SPOT_search"));
}

/**
 *
 */
function getTaggedFields($page,$context='page'){
  global $SPOT_search;
  $reply = WireArray();
  if (!empty($page) && !empty($page->fields)){
    foreach ($page->fields as $f) {
      $f_name = $f->name;
      if(!$f->hasTag($context)) continue;
      if ($f->type instanceof FieldtypeOptions){
       	if (!count($page->$f_name)) continue;
	$value = $page->$f_name->last->getTitle(); 
      }elseif ($f->type instanceof FieldtypeDatetime){
	$value = date("Y-m-d",$page->get($f_name));
      }elseif (strpos($f->name,'price')){
	$value = number_format($page->get($f_name),0,","," ").' SEK';
      }else{
	$value = $page->get($f_name);
      }
      if (empty($value)) continue;
      // printf("%s=%s %s <br>\n",$f_name,$value,t_dump($page->$f_name,'get_object_name'));
      $reply->add(array('field' => $f->name,
			'label' => $page->getField($f->name)->getLabel(),
			'value' => (is_numeric($value)?$value:$value),
			'url'   => sprintf("%s?%s=%s",$SPOT_search,$f->name,$value),
			'comment'=> "<!-- $f_name  ------------------------------------>\n"));
    }
  }
  return $reply;
}
