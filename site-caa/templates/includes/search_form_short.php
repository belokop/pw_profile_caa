<?php namespace ProcessWire;

/*
from site-language

        <form class='search' action='<?php echo $pages->get('template=search')->url; ?>' method='get'>
                <label for='search' class='visually-hidden'><?php echo _x('Search:', 'label'); ?></label>
                <input type='text' name='q' id='search' placeholder='<?php echo _x('Search', 'placeholder'); ?>' />
                <button type='submit' name='submit' class='visually-hidden'><?php echo _x('Search', 'button'); ?></button>
        </form>
*/

/*
from pw site, admin search

<form class='pw-search-form' data-action='/sh/processwire/page/search/live/' action='/sh/processwire/page/search/live/' method='get'>
  <div class='uk-inline'>
    <span class='uk-form-icon'>
      <span class='pw-search-icon'><i class='fa fa-search'></i></span>
      <span class='pw-spinner-icon uk-hidden'><i class='fa fa-spinner fa-spin'></i></span>	
    </span>
    <input type='text' class='pw-search-input uk-input uk-form-width-medium' name='q'>
  </div>
  <input class='uk-hidden' type='submit' name='search' value='Search' />
  <input type='hidden' name='show_options' value='1' />
</form>
*/

$action = $pages->get('template=h_search')->url;
echo (false
      ? x("form class='pw-search-form' data-action='$action' action='$action' method='get'",
	  x("div class='uk-inline'",
	    x("span class='uk-form-icon'",
	      x("span class='pw-search-icon'",           x("i class='fa fa-search'")).
	      x("span class='pw-spinner-icon uk-hidden'",x("i class='fa fa-spinner fa-spin'")))).
	  x("input type='text' class='pw-search-input uk-input uk-form-width-medium' name='keywords'").
	  x("input class='uk-hidden' type='submit' name='search' value='Search'"))
      
      : x("form class='pw-search-form' data-action='$action' action='$action' method='get'",
	  x("label for='search' class='visually-hidden'",_x('Search:', 'label')).
	  x("input type='text' name='keywords' id='search' placeholder='"._x('Search', 'placeholder')."'").
	  x("button type='submit' name='submit' class='visually-hidden'", _x('Search', 'button'))));
