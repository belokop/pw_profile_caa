<?php namespace ProcessWire;

//foreach(pages()->find("children.count>0") as $p) if(!in_array($p->name,array('home','artworks'))) $ssearchable[] = $p->template->name;
$searchable = array('collection','seller','brand','person','possession');

//list($spot_url,$spot_prfx) = getSpotURLs($page);
$GLOBALS['spot_url'] = $spot_url;

function collect($name){
  global $input, $spot_url;
  $reply = array();
  foreach(pages()->get($n="/${spot_url}${name}s/")->children() as $item) {
    $selected = ($item->name == @$input->whitelist->$name ? " selected=' selected' " : '');
    $reply[] = x("option$selected value='".$item->name."'",$item->title);
  }
  print "$name $n<br>";
  return $reply;
}

function show_collect($name,$select){
  return empty($select)
    ? ""
    : x("div class='uk-width-1-2'",
	x("div class='uk-form-row'",
	  x("label class='uk-form-label' for='search_$name'",templates()->get($name)->getLabel()).
	  x("div class='uk-form-controls'",
	    x("input type='hidden' name='template' value='$name'"). 
	    x("select id='search_$name' name='$name' class='uk-form-width-large'",
	      x("option value=''",__('Any').
		join("\n",$select))))));
}

$to_show=array(); foreach($searchable as $item)  $to_show[] = show_collect($spot_prfx.$item,collect($spot_prfx.$item));

$cs = (empty($input)
       ? ""
       : (($i=$input->whitelist('keywords')) ? $sanitizer->entities($i) : ""));

echo x("div id='artwork-search' class='uk-panel uk-panel-box xuk-panel-box-primary uk-margin-bottom'",
       x("div id='artwork-search' class='uk-panel uk-panel-box xuk-panel-box-primary uk-margin-bottom'",
	 x("h3 class='uk-panel-title'", x("i class='uk-icon-paint-brush'")."Artwork Search").
	 x(sprintf("form class='uk-form uk-form-stacked' method='get' action='%s${spot_url}${spot_prfx}search/'",$config->urls->root),
	   x("div class='uk-form-row'",
	     x("label class='uk-form-label' for='search_keywords'","Keywords").
	     x("div class='uk-form-controls'",
	       x("input type='text' class='uk-form-width-large' name='keywords' id='search_keywords' value='$cs'"))).
	   x("div class='uk-grid uk-grid-small uk-margin-top'",join("\n",$to_show)).
	   x("div class='uk-margin-top'",
	     x("button type='submit' id='search_submit' class='uk-button uk-button-primary' name='submit' value='1'",
	       x("i class='uk-icon-search'")."Search")))));
