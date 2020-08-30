<?php namespace ProcessWire;
if (!defined('myOrg_name'))  define('myOrg_name','Nordita');
if (!defined('PRP_OK'))      define('PRP_OK'          , 'ok');
if (!defined('PRP_CANCELED'))define('PRP_CANCELED'    , 'canceled');
if (!defined('PRP_RESERVED'))define('PRP_RESERVED'    , 'reserved');
if (!defined('PRP_TEXT_reserve'))define('PRP_TEXT_reserve', 'reserve new preprint number');
$t_classes = ['prp_serial' => ['prp_serial']];

if (function_exists("date_default_timezone_set") and
    function_exists("date_default_timezone_get"))  @date_default_timezone_set(@date_default_timezone_get());

$GLOBALS['page'] = $page;
$GLOBALS['doing'] = $doing = 'list'; // managing
$GLOBALS['t']['class'] = [];

if (!function_exists('ProcessWire\prp_header')){
  function prp_header(){
    $header = array('prp_serial' => ' ');
    if ($GLOBALS["doing"] == 'managing'){
      if (PRP_censor_here)    $header['prp_avid']  = 'Entered by';
      $header['prp_field']  = 'Field';
    }
    $header['_preprint']   = 'Title and author(s)';
    $header['prp_publisher']  = 'Published in';
    if (@$_POST['prp_field'] != 'all') unset($header['prp_field']);
    $GLOBALS['t']['th_attr']['prp_publisher'] = ['style="width:25%;"'];
    return $header;
  }
}

if (!function_exists('ProcessWire\prp_serial')){
  function prp_serial(Page $page,$prefix=True){
    $serial = trim((int)$page->prp_id
		   ? sprintf("%s %s-%03d%s",
			     ($prefix ? mb_strToUpper(myOrg_name) : ''),
			     date('Y',(int)$page->prp_day0),
			     (int)$page->prp_report,
			     ((date('Y',(int)$page->prp_day0) <= 2003) ? $page->prp_field : ""))
		   : 'to be allocated');
    return trim($serial);
  }
}


if (!function_exists('ProcessWire\prp_href')){
  function prp_href($title){
    global $rec;
    $valid_extension = function($l) {return file_exists($l);};
    if ($valid_extension($l=trim($rec['prp_archive']))){
      $reply = (file_exists($l)
		? x("a href='$l'",$title)
		: $title);
    }elseif ($url=$rec['prp_archive']){
      $reply = ($rec["prp_status"] == PRP_CANCELED
		? $title
		: x("a href='$l'",$title));
    }elseif($f=$rec['prp_local']){
      $l = trim(`mdfind -name $f | head -1`);
      $reply = (file_exists($l)
		? x("a href='file://$l'",$title)
		: $title);
    }else{
      $reply = $title;
    }
    return $reply;
  }
}

foreach($page->fields as $f) $rec[$f->name] = $GLOBALS['rec'][$f->name] = $page->$f;
$t_classes = ['prp_serial' => ['prp_serial']];

print x('tr',x('td')); // spacer... better to be done by CSS
foreach($t_classes as $k=>$v) $t['class'][$k][] = $v;

//$rec['prp_avid'] = bForm_Avatar::_fmtName('fl',$rec);

// Title & authors
$_preprint[] = prp_href(x('span class="prp_title"',str_replace('<','%3E',$rec['prp_title'])));
if (($a = str_replace('<','%3E',$rec['prp_authors']))) $_preprint[] = x('span class="prp_authors"',$a);
$rec['_preprint'] = join('<br>',$_preprint);

// Publisher
if (($rec["prp_status"] !== PRP_CANCELED) && ($doi=@$rec['prp_doi'])){
  $rec['prp_publisher'] = x("a href='http://dx.doi.org/doi:$doi'",$rec['prp_publisher']);
}

//
// if ($rec["prp_status"] == PRP_CANCELED) foreach(array('_preprint','prp_publisher') as $f) $rec[$f] = strip_tags($rec[$f]);

$rec['prp_serial'] = prp_serial($page,False);

$u = explode('/',$page->url); array_pop($u); array_pop($u);
$same_url = join('/',$u).'/';

if ($GLOBALS["doing"] == 'managing'){
  $p = new bForm_prp($rec['prp_id']);
  if ($p->isWritable() && $p->getValue('prp_status') != PRP_CANCELED){
    //$t->extraTD[]=b_btn::submit_icon('ICON_edit','modify',"$same_url?form=bForm_prp&id=$p->ID&mode=RO",False);
  }
}

switch ($rec["prp_status"]) {
case PRP_RESERVED:
  foreach(prp_header() as $k=>$v) $t['class'][$k][] = 'prp_reserved';
  $rec['prp_avid'] .= '<br/>'.date(' Y-m-d',(empty($rec['prp_tm'])
					     ? $rec['prp_day0']
					     : $rec['prp_tm']));
  if (is_object($p) && $p->isWritable()){
    if (PRP_censor_here) $t->extraTD[]=x("a href='$same_url?prp_accept_once=$p->ID'".
					 " onClick='return confirm(\"Really accept?\");'",
					 "<img src='./images/i-ok.png' alt='accept' />");
    
    $t->extraTD[]=x("a href='$same_url?prp_delete_once=$p->ID' onClick='return confirm(\"Really delete?\");'",
		    "<img src='./images/i-drop.png' alt='drop' />");
  }
  break;
  
case PRP_CANCELED:
  foreach(prp_header() as $k=>$v) $t['class'][$k][] = 'prp_canceled';
  $t->extraTD[] = "<img src='./images/i-drop2.png' alt='canceled' />";
  break;
  
case PRP_OK:
  $t['class'] = $t_classes;
}

//
// What is done only once - page header
//
if (!@$GLOBALS[__FILE__]++){

  $rec['url'] = $page->url;
  foreach($page->fields as $f) $rec[$f->name] = $page->$f;
  //echo"<pre>rec\n";print_r($rec);echo"</pre>";
  echo"<pre>header\n";print_r(prp_header());echo"</pre>";
  echo"<pre>rec\n";print_r($rec);echo"</pre>";
  //echo"<pre>";print_r($input);echo"</pre>";

  $action = '/caa/prp_spot/';

  foreach(['all'=> 'All preprints',
	   'AP' => 'Astrophysics',
	   'CM' => 'Condensed Matter',
	   'HE' => 'High Energy Physics'] as $k=>$v) $options_g[] = x("option value='$k'".(@$_POST['prp_field']==$k?" selected='selected'":""),$v);
  for($y=date('Y'); $y>1994; $y--) $options_y[] = x("option value='$y'".(@$_POST['prp_year']==$y?" selected='selected'":""),$y);

  echo(x("div id='prp_selector'",
	 x("ul",
	   x("li",
	     x("form action='$action' method='POST' enctype='application/x-www-form-urlencoded' class='only_online' name='selector1' accept-charset='UTF-8'",
	       x("select name='prp_field' onchange='submit()' class='shadow-box'",join("",$options_g)))).
	   x("li",
	     x("form action='$action' method='POST' enctype='application/x-www-form-urlencoded' class='only_online' name='selector2' accept-charset='UTF-8'",
	       x("select name='prp_year'  onchange='submit()' class='shadow-box'",join("",$options_y)))))).
       x("div style='clear:both'"));

  // Print table header
  echo "<table>\n";
  $th = ''; foreach(prp_header() as $k=>$v) $th .= x("th class='".(empty($c=@$t['th_attr'][$k])?"":join(' ',$c))."'",$v);
  echo x("tr",$th);
  /*
  echo "<table>\n".x("tr",
		     x("th").x("th").
		     x("th",'Title and author(s)').
		     x("th",'Published in'));
  */
}

$td = ''; foreach(prp_header() as $k=>$v) $td .= x("td class='$k'",$rec[$k]);
echo x("tr",$td);
