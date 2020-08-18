<?php namespace ProcessWire;
$url = $object->url;
$ttl = $object->title;
$taggedFields = '';
foreach(getTaggedFields($object,'list') as $f=>$i) $taggedFields = $taggedFields . x("div class='uk-width-1-3 uk-width-small-1-5'",$i['value']."<br/>" .
										     x("small class='uk-text-muted'",$i['label']));
echo x("div class='object-list-item' style='margin-bottom:15px'",
       x("div class='uk-grid uk-grid-medium'",
	 x("div class='uk-width-1-3 uk-width-small-1-5'",
	   x("a href='$url'",x("img src='$img' alt='$ttl'"))).
	 x("div class='uk-width-2-3 uk-width-small-4-5'",
	   x("div class='uk-grid uk-grid-small uk-margin-small-bottom'",
	     x("div class='uk-width-1-1 uk-width-small-2-5 uk-margin-small-bottom'",
	       x("a href='$url' class='uk-text-bold'",$ttl).
	       x("div class='object-city uk-text-muted'",$caption)).
	     $taggedFields).
	   x("p",$summary))));
/*
<div class='object-list-item' style="margin-bottom:15px">
	<div class='uk-grid uk-grid-medium'>
		<div class='uk-width-1-3 uk-width-small-1-5'>
			<a href='<?=$object->url?>'><img src='<?=$img?>' alt='<?=$object->title?>' /></a>
		</div>
		<div class='uk-width-2-3 uk-width-small-4-5'>
			<div class='uk-grid uk-grid-small uk-margin-small-bottom'>
				<div class='uk-width-1-1 uk-width-small-2-5 uk-margin-small-bottom'>
					<a href='<?=$object->url?>' class='uk-text-bold'>
						<?=$object->title?>
					</a>
					<div class='object-city uk-text-muted'>
						<?=$caption?>
					</div>
				</div>
                                <?php
				  foreach(getTaggedFields($object,'list') as $f=>$i) print x("div class='uk-width-1-3 uk-width-small-1-5'",$i['value']."<br/>" .
								                             x("small class='uk-text-muted'",$i['label']));?>
			</div>
			<p><?=$summary?></p>
		</div>
	</div>	
</div>
*/
