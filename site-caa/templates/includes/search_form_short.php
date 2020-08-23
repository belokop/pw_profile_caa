<?php namespace ProcessWire;
if (empty($GLOBALS['SPOT_id'])) $GLOBALS['SPOT_id'] = 'h';
$action = $pages->get("template=".$GLOBALS['SPOT_id']."_search")->url;
echo (false
      // Version 1
      ? x("form class='pw-search-form' data-action='$action' action='$action' method='get'",
	  x("div class='uk-inline'",
	    x("span class='uk-form-icon'",
	      x("span class='pw-search-icon'",           x("i class='fa fa-search'")).
	      x("span class='pw-spinner-icon uk-hidden'",x("i class='fa fa-spinner fa-spin'")))).
	  x("input type='text' class='pw-search-input uk-input uk-form-width-medium' name='keywords'").
	  x("input class='uk-hidden' type='submit' name='search' value='Search'"))

      // Version 2
      : x("form class='pw-search-form' data-action='$action' action='$action' method='get'",
	  x("label for='search' class='visually-hidden'",_x('Search:', 'label')).
	  x("input type='text' name='keywords' id='search' placeholder='"._x('Search', 'placeholder')."'").
	  x("input type='hidden' name='tags' value='$GLOBALS[SPOT_id]'").
	  x("button type='submit' name='submit' class='visually-hidden'", _x('Search', 'button'))));
