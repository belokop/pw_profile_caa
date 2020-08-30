<?php namespace ProcessWire;

if (empty($cols)) $cols = 3;
region('content',
       "<ul class='uk-grid uk-grid-width-medium-1-$cols'>".
       page()->children->each("<li><a href='{url}'>{title}</a></li>").
       "</ul>");
