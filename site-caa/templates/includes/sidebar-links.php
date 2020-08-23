<?php namespace ProcessWire;
//function __($a) { return $a; }
?>
<div class='uk-panel uk-panel-box uk-panel-box-secondary uk-margin-top uk-margin-bottom'>
  <h3 class='uk-panel-title'><i class='uk-icon-link'></i>Quick Links</h3>
  <ul class='uk-nav uk-nav-side'>
    <li><a href='<?=$config->urls->root?>search/?awx_rarity=R,1,2&amp;sort=-awx_rarity'><?=__('Most rare scarves')?></a></li>
    <li><a href='<?=$config->urls->root?>search/?awx_popularity=A,B&amp;sort=-awx_popularity'><?=__('Most popular scarves')?></a></li>
    <li><a href='<?=$config->urls->root?>search/?awx_year=<?php printf("%s-%s",date("Y")-10,date("Y")); ?>'><?=__('Artworks from the last decade')?></a></li>
    <li><a href='<?=$config->urls->root?>search/?awx_year=1900-1950&amp;sort=awx_year'><?=__('Early artworks')?></a></li>
    <li><a href='<?=$config->urls->root?>all/'><?=__('All artworks')?> (A-Z)</a></li>
  </ul>
</div>
	
