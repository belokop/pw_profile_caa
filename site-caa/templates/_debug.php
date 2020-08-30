<?php namespace ProcessWire;

if (!function_exists('ProcessWire\tidy_dump') && !function_exists('tidy_dump')){
  function tidy_dump($object,$title="tidy_dump",$options=array()){
    $substr    = (is_numeric($options) ? $options : 0);
    $max_level = (empty($options['l']) ? 0 :  $options['l']);
    $print     = true;
    ob_start(); var_dump($object); $dump=ob_get_contents(); ob_end_clean();
    if ($title === 'get_object_name'){
      $t = array('/object.ProcessWire.([A-Za-z]*)..(\d*).*/'=> '$1',
		 '/string\([0-9]*\) /'                      => '',
		 '/\n.*/'                                   => '');
    }else{
      $t = array('/\}/'                  => ')',
		 '/\{/'                  => '(',
		 '/\"/'                  => '',
		 '/\n *array\(\d*\) */'  => "\n",
		 '/\(\n *\)/'            => '()',
		 '/=>\n */'              => '=>',
		 '/=>string\([0-9]*\) /' => '=>',
		 '/=>int.(\d*). ?/'      => '=>$1',
		 '/=>array\([0-9]*\) /'=>'=>array',
		 //'/\[\d*\]=>/'           =>'',       // array indexes
		 '/\n[^=]*=.NULL/'       =>'',
		 '/object.ProcessWire.([A-Za-z]*)..(\d*)[^\(]*\([^\(]*/'=> '$1.$2',
		 '/[\[\]]/'              => '',
		 //'/\n *([^\n]*)\n */'    => ',$1,',
		 //'/\(,([^\n]*)\n *\)/'   => '($1)',
		 //'/\(,([^,]*),\)/'       => '($1)',
		 '/^array\(\d*\) *?/'    => '',
		 '/ \(\n *([^\)]*)\)/'   => ' '.'[$1]',
		 '/\n *\)/'              =>     ']',
		 '/\(/'                  => '[',
		 '/\[\n *([^\]]*)\]/'    => '[$1]',
		 '/( *)(\[[^\[]*\[)([^\n]*\[)/'    => '$1$2'."\n    ".'$3', // [class=>[prp_serial=>[
		 '/( *)(\[[^\[]*\[)([^\n^\[]*\n)/' => '$1$2'."\n      ".'$3', // [._av_duty=>[l=>Person duty
		 '/\[\n *([^\]]*)\]/'    => '[$1]',
		 '/\n *\]\n/'            => ']'."\n",
		 '/\n *\n/' => "\n",
		 );
    }
    $reply = preg_replace(array_keys($t),array_values($t),$dump);
    if ($title === 'get_object_name') return (empty($substr) ? $reply : substr($reply,0,$substr));
    if ($title === 'get_object_name') return "Object $reply";
    if ($title === '')                return $reply;
    if ($max_level > 0){
      $t = array('/\n'.str_repeat('  ',$max_level+1).'[^\n]*/' => '',
		 '/\(\n'.str_repeat('  ',$max_level).'\)/'     => '( ... )',
		 );
      $reply = preg_replace(array_keys($t),array_values($t),$reply);
    }

    list($o,$c) = (!empty($_SERVER['TERM']) ? ["",""] : ["<pre>","</pre>"]);
    if ($print) printf("$o\n%s%s$c\n",
		       preg_replace("/ProcessWire./","",$title),
		       str_replace("\n","\n          ","\n".$reply));
    return $reply;
  }
}

if (!function_exists('ProcessWire\_formatData')){
  function _formatData($data,$maxLength=66){
    
    static $tp = array("/\n/"          => '',
		       "/ => /"        => '=>',
		       "/ *\( */"      => '(',
		       "/ *\) */"      => ')',
		       "/[\s]+/"       => ' ',
		       "/ *\[\d*\]=>/" => ',',
		       "/ *\[0\]=>/"   => '',
		       "/\(,/"         => '(',
		       "/\)\[/"        => '),[',
					      "/ProcessWire./"=> '');
    static $ts = array("\n"            => ' ',
		       " => "          => '=>');
    
    list($o,$c) = (!empty($_SERVER['TERM']) ? ["",""] : ["<pre>","</pre>"]);

    if (is_array($data)){
      ob_start();    print_r($data);    $output = ob_get_contents();    ob_end_clean();
      return $o.shortText(str_replace(array_keys($ts),array_values($ts),
				      preg_replace(array_keys($tp),array_values($tp),$output),$maxLength)).$c;
    }elseif($data instanceof NullPage){
      return "NullPage";
      return "Object NullPage";
    }elseif(is_object($data)){
      if (!empty($data->name)) return $data->name;
      return tidy_dump($data,'get_object_name');
    }elseif(is_bool($data)){
      return var_export($data,True);
    }elseif(is_string($data)){
      return $o.shortText($data,$maxLength).$c;
      return '(string) '.shortText($data,$maxLength);
    }elseif(is_numeric($data)){
      return $data;
      return "(numeric) $data";
    }elseif (is_null($data)){
      return 'NULL';
    }else{
      return 'UNKNOWN TYPE '. var_export($data,True);
    }
  }
}

if (!function_exists('ProcessWire\shortText')){
  function shortText($text, $maxLength = 500) {
    if(!strlen($text)) return '';
    $summary = trim(strip_tags($text));
    if(strlen($summary) <= $maxLength) return $summary;
    $summary = substr($summary, 0, $maxLength);
    $lastPos = 0;
    foreach(array('. ', '!', '?') as $punct) {
      $pos = strrpos($summary, $punct);
      if($pos > $lastPos) $lastPos = $pos;
    }
    if(!$lastPos)     $lastPos = strrpos($summary, ' ');
    if ($lastPos)     $summary = substr($summary, 0, $lastPos + 1); // and truncate to last sentence
    return trim($summary).'...';
  }
}
