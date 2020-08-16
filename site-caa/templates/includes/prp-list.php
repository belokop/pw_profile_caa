
<?php if($headline): 
        if (empty($cols)) $cols = 1; ?>
	<h2><?=$headline?></h2>
<?php endif; ?>

<?php if(FALSE && $pagination): ?>
	<div class='uk-grid uk-margin-bottom pagination'>
		<div class='uk-width-medium-2-3'><?=$pagination?></div>	
		<div class='uk-width-medium-1-3'><?=$sortSelect?></div>
	</div>	
<?php endif; ?>

<?php if(!empty($items)): ?>

<!--	<div class='object-list'>   -->
        <table>
		<?php foreach($items as $item): ?>
			<li><?=$item?></li>
		<?php endforeach; ?>
        </table>
<!--	</div>  -->
	<?php if(FALSE && $selector): ?>
		<p class='uk-alert uk-margin-bottom'>
		The selector used to find the pages shown above is:<br />
		<span class='pw-selector'><?=$selector?></span>
	</p>
	<?php endif; ?>
<?php else: ?>
	<div class='uk-alert uk-alert-danger'>
		<i class='uk-icon-warning'></i> No objects found
	</div>
<?php endif; ?>

	
