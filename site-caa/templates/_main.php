<?php namespace ProcessWire;
/**
 * Main output template for site using Uikit
 *
 * Copyright 2016 by Ryan Cramer
 *
 * The follow phpdoc doc declarations are just to keep IDE inspections happy,
 * and they are not necessary to keep here.
 */
/** @var Config $config */
/** @var Page $page */
?><!DOCTYPE html>
<html lang="<?=(empty($languages)?'en':_x('en', 'HTML language code'))?>">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <?=x('title',region('browserTitle').(input()->pageNum() > 1 ? " (Page ".input()->pageNum().")":""))."\n"?>
    
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'></script>
    
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Lato:400,400i,700' />
    <link rel='stylesheet' type='text/css' href='<?=$config->urls->root?>site/uikit/css/uikit.gradient.min.css' />
    <link rel='stylesheet' type='text/css' href='<?=$config->urls->root?>site/uikit/css/components/slidenav.gradient.min.css' />
<!--    <link rel='stylesheet' type='text/css' href='<?=urls('templates')?>styles/main.css' /> -->
    <link rel='stylesheet' type='text/css' href='<?=urls('templates')?>styles/objects.css' />
<?php
          // Are we in the spot?
          getSpotURLs();
          $home = pages("/");
          $spot_home = pages("/$GLOBALS[SPOT_url]");

          // Are we in the search page?
          $in_search = (strpos($page->url,'search/') !== false);

          if (!empty($languages)){
          // handle output of 'hreflang' link tags for multi-language (needed for SEO)
          foreach($languages as $language) {
	    if(!$page->viewable($language)) continue;
	    // hreflang code for language uses language name from homepage
	    $hreflang = $home->getLanguageValue($language, 'name');
	    // output the <link> tag: note that this assumes your language names are the same as required by hreflang.
	    $url = $page->localHttpUrl($language); // get the http URL for this page in the given language
	    echo "\t<link rel='alternate' hreflang='$hreflang' href='$url' />\n";
          }
	  }
          include("./includes/google-analytics.php");
?>
  </head>
  
  <body>
    <div id='masthead' class='uk-margin-large-top uk-margin-bottom'>
      <div id='primary-headline' class='uk-container uk-container-center uk-margin-bottom'>
	<h2 style='float:left;'>
<?php
	  $home->set('headline', 'Home');
	  echo $page->parents->each("\t<a href='{url}'>{headline|title}</a> <i class='uk-icon-angle-right'></i>\n ");
	  echo region('headline');
?>
	</h2>

        <ul class='uk-navbar-nav' style='float:right;'>
	  <?php	   // Search and login
	    include './includes/search_form_short.php';
            echo (user()->isGuest() 
		  ? x("li",x("a href='{$config->urls->admin}login/'",x("i class='uk-icon-user'")        .' '.__('Login')))
		  : (page()->editable() ? x("li",x("a href='$page->editUrl'",x("i class='uk-icon-edit'").' '.__('Edit'))) : "").
		  x("li",x("a href='{$config->urls->admin}login/logout/'"),  x("i class='uk-icon-user'").' '.__('Logout')));
?>
        </ul>
      </div>

      <nav id='topnav' class='uk-navbar uk-navbar-attached uk-hidden-small'>
	<div class='uk-container uk-container-center'>
	  <ul class='uk-navbar-nav float_left'>
<?php
	    foreach(($GLOBALS['SPOT_url'] ? $spot_home->and($spot_home->children) : $home->and($home->children)) as $item) {
	       if (preg_match(";(countries|search)/;",$item->url) || empty($item->numChildren)) continue;
	       if (preg_match(";(spot)/;",$item->url) && !$GLOBALS['SPOT_url'])  continue;
	       $class = ($item->id==$page->rootParent->id || $item->id==$page->parent->id ? 'uk-active' : '');
	       echo "\t\t<li class='$class'><a href='$item->url'>$item->title</a></li>\n";
	    }
?>
	  </ul>
<?php if (!empty($languages)){?>
          <!-- language switcher  -->
          <!-- <div class='uk-navbar-flip' style='float:right;'> -->
            <ul class='languages uk-navbar-nav' role='navigation' style='float:right;'>
<?php
              foreach($languages as $language) {
		 if(!$page->viewable($language)) continue;
                 $hreflang = $home->getLanguageValue($language, 'name');
                 printf("\t\t\t<li%s><a hreflang='$hreflang' href='%s'>%s</a></li>\n",
			($language->id==$user->language->id ? " class='uk-active'" : ""),
			$page->localUrl($language),
			$language->title);
		 //echo "\t<li><a hreflang='$hreflang' href='$url'>".$language->title."</a></li>\n";
	  }
?>
            </ul>
          <!-- </div> -->
          <!-- language switcher / navigation end -->
<?php } ?>
	</div>	
      </nav>
    </div><!--/masthead-->
    
    <div id='main'>
      <div class='uk-container uk-container-center'>
	<?=region('mainHeader')?>
	<div class='uk-grid uk-grid-medium'>
<!--	  <div id='content' class='uk-width-large-2-3 uk-margin-bottom'> -->
	  <div id='content' class="uk-width-large-<?=($in_search?'3-4':'1-1')?> uk-margin-bottom">
	    <?=region('content')?>
	  </div>
	    <?php
	    if ($in_search) {
	      echo "<div id='sidebar' class='uk-width-large-1-4'>\n";
	      echo region('sidebarHeader');
	      // include("./includes/search_form.php");
	      echo region('sidebar');
	      include("./includes/sidebar-links.php");
	      echo "</div>\n";
            }
	    ?>
	</div>
      </div>
    </div> <!--/main-->
    
    <footer id='foot' class='uk-margin-large-top'>
      <div class='uk-container uk-container-center uk-margin-bottom'>
	<div class='uk-text-muted uk-text-center'>
<!--
	  <span class='foot-text'>Powered by <a href='https://processwire.com'>ProcessWire Open Source CMS</a></span>
-->
	</div>
      </div>
    </footer>
    
    <?php include("./includes/offcanvas.php"); ?>
    
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'></script>
    <script src='<?=$config->urls->root?>site/uikit/js/uikit.min.js'></script>
    <script src='<?=$config->urls->root?>site/uikit/js/components/lightbox.min.js'></script>
    <script src='<?=urls('templates')?>scripts/objects.js'></script>
    
  </body>
</html>
