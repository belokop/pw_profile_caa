<?php namespace ProcessWire;
define('myOrg_name','Nordita');
define('PRP_OK'          , 'ok');
define('PRP_CANCELED'    , 'canceled');
define('PRP_RESERVED'    , 'reserved');
define('PRP_TEXT_reserve', 'reserve new preprint number');
$t_classes = ['_serial' => ['prp_serial']];

$GLOBALS['field_selected'] = 'all';
$GLOBALS['doing'] = $doing = 'list'; // managing 
$prp_publisher = $page->prp_publisher;

/*
<tr class='hightlighMarked' origcl='b_table-zebra'>
  <td class='align_right b_wrap'>&nbsp;</td>
  <td class='b_table-zebra2 align_right prp_serial b_wrap'>2020-068</td>
  <td class='b_table-zebra2 align_left b_wrap'><a href='https://arxiv.org/abs/2006.14756'><span class='prp_title'>Implications of the search for optical counterparts during the second part of the Advanced LIGO's and Advanced Virgo's third observing run: lessons learned for future follow-up observations</span></a><br><span class='prp_authors'>Michael W. Coughlin, Tim Dietrich, Sarah Antier, Mouza Almualla, Shreya Anand, Mattia Bulla et al.</span></td>
  <td class='b_table-zebra2 align_left b_wrap'><a class='fancybox.iframe iframe' title='Monthly Notices of the Royal Astronomical Society, Volume 497, Issue 1, pp.1181-1196' href='https://dx.doi.org/doi:10.1093/mnras/staa1925#content'>Monthly Notices of the Royal Astronomical Society, Volume 497, Issue 1, pp.1181-1196</a></td>
 </tr>
  */ 

print x('tr',x('td')); // spacer... better to be done by CSS
foreach($t_classes as $k=>$v) $GLOBALS['prp_class'][$k] = $v;

$page->prp_number = _serial($page,False);
//$page->prp_avid = bForm_Avatar::_fmtName('fl',$this->rec);
// Title & authors
$preprint[] = href(x('span class="prp_title"',str_replace('<','%3E',$page->prp_title)));
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

switch ($page->prp_status) {
case PRP_RESERVED:
  foreach(header() as $k=>$v) $GLOBALS['prp_class'][$k][] = 'prp_reserved';
  $page->prp_avid .= '<br/>'.date(' Y-m-d',(empty($page->prp_tm)
					    ? $page->prp_day0
					    : $page->prp_tm));
  //          $img = "<img src='$f' alt='".b_fmt::escape($description)."' $c />";
  if (is_object($p) && $p->isWritable()){
    if (PRP_censor_here) $GLOBALS['extraTD'][]=x("a href='".b_url::same("?prp_accept_once=$p->ID").
					       "' onClick='return confirm(\"Really accept?\");'",
					       bIcons()->get(array('d'=>'accept',
								   'i'=>'ok')));
    $GLOBALS['extraTD'][]=x("a href='".b_url::same("?prp_delete_once=$p->ID")."' onClick='return confirm(\"Really delete?\");'",
			    bIcons()->get(array('d'=>'delete',
						'i'=>'drop')));
  }
  break;
  
case PRP_CANCELED:
  foreach(header() as $k=>$v) $GLOBALS['prp_class'][$k][] = 'prp_canceled';
  $GLOBALS['extraTD'][] = bIcons()->get(array('d'=>'canceled','i'=>'i-drop2'));
  break;
  
case PRP_OK:
  $GLOBALS['prp_class'] = $t_classes;
}

function header(){
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

function href($title){
  if (true){ // PRP::valid_extension($l=trim($page->prp_archive))){
    $reply = (file_exists($l) ? x("a href='$l'",$title) : $title);
  }elseif ($url=$page->prp_archive){
    $reply = ($page->prp_status == PRP_CANCELED ? $title : x("a href='$l'",$title));
  }elseif($page->prp_local){
    $p = new bForm_prp($page->prp_id);
    $l = $p->url_local();
    $reply = (file_exists($l) ? x("a href='$l'",$title) : $title);
  }else{
    $reply = $title;
  }
  return $reply;
}

function _serial(Page $page,$prefix=True){
  $serial = trim((int)$page->prp_id
	     ? sprintf("%s %s-%03d%s",
		       ($prefix ? mb_strToUpper(myOrg_name) : ''),
		       date('Y',(int)$page->prp_day0),
		       (int)$page->prp_report,
		       ((date('Y',(int)$page->prp_day0) <= 2003) ? $page->prp_field : ""))
		 : 'to be allocated'); 
  return trim($serial);
}
