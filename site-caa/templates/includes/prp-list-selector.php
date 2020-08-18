<?php namespace ProcessWire;

$GLOBALS["doing"] = 'list';

if (!function_exists('ProcessWire\prp_header')){
  function prp_header(){
    $header = array('prp_serial' => ' ');
    if ($GLOBALS["doing"] == 'managing'){
      if (PRP_censor_here)    $header['prp_avid']  = 'Entered by';
      $header['prp_field']  = 'Field';
    }
    $header['_preprint']   = 'Title and author(s)';
    $header['prp_publisher']  = 'Published in';
    if (@$_SESSION['prp_field'] != 'all') unset($header['prp_field']);
    $GLOBALS['t']['th_attr']['prp_publisher'] = ['style="width:25%;"'];
    return $header;
  }
}

$action = '/caa/prp_spot/';

$options_y = [x("option value='all'".(@$_SESSION['prp_year']=='all'?" selected='selected'":""),'All years')];
for($y=date('Y'); $y>1994; $y--) $options_y[] = x("option value='$y'".(@$_SESSION['prp_year']==$y?" selected='selected'":""),$y);

if (0) if (empty($prp_groups)) $prp_groups = ['all'=> 'All preprints',
					      'AP' => 'Astrophysics',
					      'CM' => 'Condensed Matter',
					      'HE' => 'High Energy Physics'];
foreach($GLOBALS['prp_groups'] as $k=>$v) $options_g[] = x("option value='$k'".(@$_SESSION['prp_field']==$k?" selected='selected'":""),$v);

echo(x("div id='prp_selector'",
       x("ul",
	 x("li",
	   x("form action='$action' method='POST' enctype='application/x-www-form-urlencoded' class='only_online' name='selector1' accept-charset='UTF-8'",
	     x("select name='prp_field' class=prp_field' onchange='submit()' class='shadow-box'",join("",$options_g)))).
	 x("li",
	   x("form action='$action' method='POST' enctype='application/x-www-form-urlencoded' class='only_online' name='selector2' accept-charset='UTF-8'",
	     x("select name='prp_year'  class='prp_year' onchange='submit()' class='shadow-box'",join("",$options_y)))))).
     x("div style='clear:both'"));
