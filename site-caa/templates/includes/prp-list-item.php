<?php namespace ProcessWire;
if (!defined('myOrg_name'))  define('myOrg_name','Nordita');
if (!defined('PRP_OK'))      define('PRP_OK'          , 'ok');
if (!defined('PRP_CANCELED'))define('PRP_CANCELED'    , 'canceled');
if (!defined('PRP_RESERVED'))define('PRP_RESERVED'    , 'reserved');
if (!defined('PRP_TEXT_reserve'))define('PRP_TEXT_reserve', 'reserve new preprint number');
$t_classes = ['_serial' => ['prp_serial']];

if (function_exists("date_default_timezone_set") and 
    function_exists("date_default_timezone_get"))  @date_default_timezone_set(@date_default_timezone_get());

$GLOBALS['page'] = $page;
$GLOBALS['field_selected'] = 'all';
$GLOBALS['doing'] = $doing = 'list'; // managing
$prp_publisher = $page->prp_publisher;

if (!@$GLOBALS[__FILE__]++){

  $rec['url'] = $page->url;
  foreach($page->fields as $f) $rec[$f->name] = $page->$f;

  echo"<pre>";print_r($rec);echo"</pre>";
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
  echo "<table>\n".x("tr",
		     x("th").x("th").
		     x("th",'Title and author(s)').
		     x("th",'Published in'));
  
}

if (!function_exists('ProcessWire\prp_header')){
  function prp_header(){
    $header = array('_serial' => ' ');
    if ($GLOBALS['doing']  == 'managing'){
      if (PRP_censor_here)      $header['prp_avid']  = 'Entered by';
      $header['prp_field'] = 'Field';
    }
    $header['_preprint']     = 'Title and author(s)';
    $header['prp_publisher']= 'Published in';
    if ($GLOBALS['field_selected'] != 'all') unset($header['prp_field']);
    $GLOBALS['th_attr']['prp_publisher'] = 'style="width:25%;"';
    return $header;
  }
}
if (!function_exists('ProcessWire\prp_href')){
  function prp_href($title){
    $page = $GLOBALS['page'];
    $valid_extension=function($l){ return file_exists($l); };
    if ($valid_extension($l=trim($page->prp_archive))){
      $reply = (file_exists($l) ? x("a href='$l'",$title) : $title);
    }elseif ($url=$page->prp_archive){
      $reply = ($page->prp_status == PRP_CANCELED ? $title : x("a href='$l'",$title));
    }elseif($l=$page->prp_local){
      //$p = new bForm_prp($page->prp_id);
      //$l = $p->url_local();
      $reply = (file_exists($l) ? x("a href='$l'",$title) : $title);
    }else{
      $reply = $title;
    }
    return $reply;
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

/*
<tr class='hightlighMarked' origcl='b_table-zebra'>
  <td class='align_right b_wrap'>&nbsp;</td>
  <td class='b_table-zebra2 align_right prp_serial b_wrap'>2020-068</td>
  <td class='b_table-zebra2 align_left b_wrap'><a href='https://arxiv.org/abs/2006.14756'><span class='prp_title'>Implications of the search for optical counterparts during the second part of the Advanced LIGO's and Advanced Virgo's third observing run: lessons learned for future follow-up observations</span></a><br><span class='prp_authors'>Michael W. Coughlin, Tim Dietrich, Sarah Antier, Mouza Almualla, Shreya Anand, Mattia Bulla et al.</span></td>
  <td class='b_table-zebra2 align_left b_wrap'><a class='fancybox.iframe iframe' title='Monthly Notices of the Royal Astronomical Society, Volume 497, Issue 1, pp.1181-1196' href='https://dx.doi.org/doi:10.1093/mnras/staa1925#content'>Monthly Notices of the Royal Astronomical Society, Volume 497, Issue 1, pp.1181-1196</a></td>
 </tr>
  */

foreach($t_classes as $k=>$v) $GLOBALS['prp_class'][$k] = $v;

$page->prp_number = prp_serial($page,False);
//$page->prp_avid = bForm_Avatar::_fmtName('fl',$this->rec);
// Title & authors
$preprint[] = prp_href(x('span class="prp_title"',str_replace('<','%3E',$page->prp_title)));
if (($a = str_replace('<','%3E',$page->prp_authors))) $preprint[] = x('span class="prp_authors"',$a);
$page->_preprint = join('<br>',$preprint);
// Publisher
if (($page->prp_status !== PRP_CANCELED) && ($doi=$page->prp_doi)){
  $page->prp_publisher = x("a href='http://dx.doi.org/doi:$doi'",$page->prp_publisher);
}

if ($GLOBALS['doing']  == 'managing'){
  $p = new bForm_prp($page->prp_id);
  if ($p->isWritable() && $p->getValue('prp_status') != PRP_CANCELED){
    $GLOBALS['extraTD'][]=b_btn::submit_icon('ICON_edit','modify',b_url::same("?form=bForm_prp&id=$p->ID&mode=RO"),False);
  }
}

$same_url = $page->url; // "/prp_spot/";
switch ($page->prp_status) {
case PRP_RESERVED:
  foreach(prp_header() as $k=>$v) $GLOBALS['prp_class'][$k][] = 'prp_reserved';
  $page->prp_avid .= '<br/>'.date(' Y-m-d',(empty($page->prp_tm)
					    ? $page->prp_day0
					    : $page->prp_tm));
  //          $img = "<img src='$f' alt='".b_fmt::escape($description)."' $c />";
  if (is_object($p) && $p->isWritable()){
    if (PRP_censor_here) $GLOBALS['extraTD'][]=x("a href='$same_url?prp_accept_once=$p->id' onClick='return confirm(\"Really accept?\");'",
						 "<img src='./images/i-ok.png' alt='accept' />");
    $GLOBALS['extraTD'][]=x("a href='$same_url?prp_delete_once=$p->id' onClick='return confirm(\"Really delete?\");'",
			    "<img src='./images/i-drop.png' alt='drop' />");
  }
  break;

case PRP_CANCELED:
  foreach(prp_header() as $k=>$v) $GLOBALS['prp_class'][$k][] = 'prp_canceled';
  $GLOBALS['extraTD'][] = "<img src='./images/i-drop2.png' alt='canceled' />";
  break;

case PRP_OK:
  $GLOBALS['prp_class'] = $t_classes;
}

echo(x("tr",x("td")). // spacer... better to be done by CSS
     x("tr class='hightlighMarked' origcl='b_table-zebra'",
       x("td class='align_right b_wrap'","&nbsp;").
       x("td class='b_table-zebra2 align_right prp_serial b_wrap'",$page->prp_number).
       x("td class='b_table-zebra2 align_left b_wrap'",
	 x("a href='https://arxiv.org/abs/2006.14756'",
	   x("span class='prp_title'",$page->prp_title)).'<br>'.
	 x("span class='prp_authors'",$page->prp_authors))));

