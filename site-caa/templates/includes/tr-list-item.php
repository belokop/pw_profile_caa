<?php namespace ProcessWire;

if (!defined('record_is_writable')) define('record_is_writable',$user->hasRole('preprints_censor')); // "true" if owner or censor

if (!defined('myOrg_name'))  define('myOrg_name','Nordita');
if (!defined('PRP_OK'))      define('PRP_OK'          , 'ok');
if (!defined('PRP_CANCELED'))define('PRP_CANCELED'    , 'canceled');
if (!defined('PRP_RESERVED'))define('PRP_RESERVED'    , 'reserved');
if (!defined('PRP_TEXT_reserve'))define('PRP_TEXT_reserve', 'reserve new preprint number');

if (function_exists("date_default_timezone_set") and
    function_exists("date_default_timezone_get"))  @date_default_timezone_set(@date_default_timezone_get());

$GLOBALS['doing'] =  'list'; // managing
$GLOBALS['page'] = $page;
global $page, $doing;

if (!function_exists('ProcessWire\prp_header')){
  global $user;
  function prp_header(){
    $header = ['prp_serial' => ' '];
    if ($GLOBALS['doing'] == 'managing'){
      if ($user->hasRole('preprints_censor'))    $header['prp_avid']  = 'Entered by';
      $header['prp_field']  = 'Field';
    }
    $header['_preprint']   = 'Title and author(s)';
    $header['prp_publisher']  = 'Published in';
    if (@$_POST['prp_field'] != 'all') unset($header['prp_field']);
    return $header;
  }
}

if (!function_exists('ProcessWire\prp_serial')){
  function prp_serial(Page $page,$prefix=True){
    global $rec;
    $serial = trim((int)$rec['prp_id']
		   ? sprintf("%s %s-%03d%s",
			     ($prefix ? mb_strToUpper(myOrg_name) : ''),
			     date('Y',(int)$rec['prp_day0']),
			     (int)$rec['prp_report'],
			     ((date('Y',(int)$rec['prp_day0']) <= 2003) ? $rec['prp_field'] : ""))
		   : 'to be allocated');
    return trim($serial);
  }
}


if (!function_exists('ProcessWire\prp_href')){
  global $rec;
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

$t = [];
foreach(prp_header() as $k=>$v) $t['class'][$k][] = $k;

$u = explode('/',$page->url); array_pop($u); array_pop($u);
$same_url = join('/',$u)."/?$_SERVER[QUERY_STRING]";
$icons_url= '/assets/files/';

foreach($page->fields as $f) $rec[$f->name] = $GLOBALS['rec'][$f->name] = $page->$f;

if (!empty($rec['prp_person'])) $rec['prp_avid'] = $page->prp_person->title;

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

if ($GLOBALS["doing"] == 'managing'){
  $p = new bForm_prp($rec['prp_id']);
  if ($p->isWritable() && $p->getValue('prp_status') != PRP_CANCELED){
    $t['extraTD'][]=b_btn::submit_icon('ICON_edit','modify',"$same_url?form=bForm_prp&id=$p->ID&mode=RO",False);
  }
}

switch ($rec["prp_status"]) {
case PRP_RESERVED:
  foreach(prp_header() as $k=>$v) $t['class'][$k][] = 'prp_reserved';
  $rec['prp_avid'] .= '<br/>'.date(' Y-m-d',(empty($rec['prp_tm'])
					     ? $rec['prp_day0']
					     : $rec['prp_tm']));
  if (record_is_writable){
    $id = (empty($page->prp_id) ? $page->id : $page->prp_id);
    if ($user->hasRole('preprints_censor')) $t['extraTD'][]=x("a href='$same_url?prp_accept_once=$id' ".
							      " 'onClick='return confirm(\"Really accept?\");'",
							      "<img src='$icons_url/i-ok.png' alt='accept' />");
    
    $t['extraTD'][]=x("a href='$same_url?prp_delete_once=$id' onClick='return confirm(\"Really delete?\");'",
		    "<img src='$icons_url/i-drop.png' alt='drop' />");
  }
  break;
case PRP_CANCELED:
  foreach(prp_header() as $k=>$v) $t['class'][$k][] = 'prp_canceled';
  $t['extraTD'][] = "<img src='$icons_url/i-drop2.png' alt='canceled' />";
  break;
case PRP_OK:
}

// Print table header
if (!@$GLOBALS[__FILE__]++){
  $th = ''; foreach(prp_header() as $k=>$v) $th .= x("th",$v);
  echo x("tr",$th);
}

$c = @$GLOBALS[basename(__FILE__)]++;

$td = '';
foreach(prp_header() as $k=>$v){
  if (in_array($k,['prp_publisher','_preprint','prp_field','prp_avid'])) $t['class'][$k][] = (empty($c%2)?'tr_odd':'tr_even');
  $td .= x("td class=".x("'",join(' ',$t['class'][$k])),$rec[$k]);
}
if(!empty($t['extraTD'])) foreach($t['extraTD'] as $i) $td .= x("td style=''",$i);
echo x("tr",$td);
