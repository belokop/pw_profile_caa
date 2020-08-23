<?php namespace ProcessWire;
if(!isset($cols)) $cols = 2;
/** @var PageArray $items */
?>
<ul class='uk-grid uk-grid-width-medium-1-<?=$cols?>'>
<?php
  // echo $items->each("<li><a href='{url}'>{title}</a></li>");
  foreach($items as $i) echo x("li",$i);
?>
</ul>
