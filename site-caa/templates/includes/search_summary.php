<?php namespace ProcessWire;
if(count($items)){
  echo "<div class='uk-alert'>\n" . __('Search Summary'); 
  foreach($items as $key => $value){
    if ($value) echo x("i class='uk-icon-arrow-right'").x("strong class='uk-text-capitalize'",$key)."$value&nbsp;";
  }
  echo "</div>";
}
