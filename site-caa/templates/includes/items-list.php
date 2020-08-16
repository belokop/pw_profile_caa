<ul class='uk-grid uk-grid-width-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-4 uk-margin-bottom'>
   <?php foreach($items as $item): ?>
   <li>
   <a href='<?=$item->url?>'><?=substr($item->title,0,25)?></a> 
   <?php if($item->numChildren)echo"<small class='uk-text-muted'><?=$item->numChildren?></small>\n"?>
   </li>
   <?php endforeach; ?>
</ul>
