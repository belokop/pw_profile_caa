<?php namespace ProcessWire;
/**
 * Variables:
 *   $page
 *   $pages     Related pages, the first image of those is drawn as page images (with links to the original)
 *   $pages_heading  Some text 
 *   $related     Artworks that mention the same title in their body
 *   $width       Image(s) width
 *   $o           images on the right (default left)
 */

$f_author = null; foreach ($page->fields as $f) if(strpos($f->name,'aw_person')!==false) $f_author=$f->name;
$authors = $page->get($f_author);
if(empty($authors)) $authors = array();
if(empty($related) || empty($related->id)) $related = array();
if (empty($o)) $o = 'L'; // images on the left (if not on the rigth) hand site

foreach(array('page','authors','pages','pages_heading','related','width','f_author') as $g){if(!isset($$g)) $$g=''; $GLOBALS[$g] = $$g;}

function o_p_images($c='1-3'){
  global $page, $pages, $width;

  echo "<div class='object-images uk-width-medium-$c uk-text-center'>\n";
  foreach((empty($pages) ? [$page] : $pages) as $object){
    if(!empty($images=$object->get('images'))){
      foreach($images as $image){
	$thumb = $image->width($width);
	echo x("div class='object-image uk-margin-small'",
	       x("a href='$image->url' data-uk-lightbox=\"{group:'photos'}\"",
		 x("img src='$thumb->url' alt='$image->description'")).
	       ($image->description ? x("div class='caption uk-text-small uk-text-muted'",
					x("span",$image->description)) : ""));
	if (!empty($pages)) break;
      }
    }else{
      echo x("div class='object-image uk-margin-small'",
	     x("img src='".urls()->templates."styles/images/photo_placeholder.png' alt=''").
	     x("div class='caption uk-text-small uk-text-muted'",x("span","Photo not available")));
    }
  }
  echo"  </div>\n";
}

function o_p_tr_line($label,$items){
  if (empty($items)) return;
  if ($items instanceof PageArray && count($items))
    echo x("tr",x("th",$label).x("td",x("ul class='uk-list uk-margin-remove'",$items->each("<li><a href='{url}'>{title}</a></li>"))));
  else foreach($items as $i)
	 echo $i['comment'].x("tr",x("th",$i['label']).x("td",x("a href='$i[url]'",$i['value'])));
}

function o_p_main($c='2-3'){
  global $page, $authors, $object, $pages_heading, $related,$f_author;
  
  $search_url = getSearchURL($page);
  echo"  <div class='uk-width-medium-$c'>\n";
  
  if (!empty($taggedFields=getTaggedFields($page,'page')) || !empty($authors)){
    echo "<table class='uk-table object-info'> <tbody>\n";

    // Author 
    o_p_tr_line(($t=templates()->get($f_author)) ? $t->label : __('Author'), $authors);

    // Author related pages
    foreach ($page->fields as $f){
      if(strpos($f->name,'aw_person')!==false || $f->type != 'FieldtypePage') continue;
      o_p_tr_line($f->getLabel(),$page->$f);
    }

    // Table of tagged fields
    o_p_tr_line('',$taggedFields);
    echo "</tbody></table>\n";
  }

  //
  // body
  //
  if (!empty($page->body)) echo x("h2",__("About")." ".$page->title).$page->body;

  /*
  // (Extra) object
  if (!empty($object)){
    echo x("h2",$pages_heading);
    foreach($object as $item){
      echo x("li",x("a href='$item->url'",$item->title.','.$item->parent->title));
    }
  }
  */
  
  if(!empty($related) || !empty($authors)){
    echo x("h2",__("See Also"));
    
    echo "<ul class='uk-list uk-list-line uk-margin-bottom'>";
    foreach($related as $item){
      echo x("li",x("a href='$item->url'",$item->title.','.$item->parent->title));
    }
    foreach($authors as $item){
      echo "<li><a href='$item->url'>".__("Artworks by")." $item->title</a></li>";
    }
  }else{
    echo "<ul class='uk-list uk-list-line uk-margin-bottom'>";
  }
  //echo x("li",x("a href='../'",$page->parent->title));
  echo "</ul>\n </div>\n";
}


echo "<div class='uk-grid uk-grid-medium'>\n";
if ($o == 'L'){ o_p_images();  o_p_main();  }
else          { o_p_main();    o_p_images();}
echo "</div>\n";
