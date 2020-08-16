<?php namespace ProcessWire;

$home = pages('/');
echo
x("div id='offcanvas' class='uk-offcanvas'",
  x("div class='uk-offcanvas-bar'",
    x("ul class='uk-nav uk-nav-offcanvas uk-nav-parent-icon uk-contrast uk-margin-bottom' data-uk-nav",
      $home->and($home->children())->each("<li><a href='{url}'>{title}</a></li>\n")).
    x("p","&nbsp;"))).
  x("a id='offcanvas-toggle' href='#offcanvas' class='uk-visible-small uk-navbar-toggle' data-uk-offcanvas");
