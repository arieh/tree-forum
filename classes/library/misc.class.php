<?php
class misc {

   static function html2rgb($color){
	    if ($color[0] == '#')
	        $color = substr($color, 1);
	
	    if (strlen($color) == 6)
	        list($r, $g, $b) = array($color[0].$color[1],
	                                 $color[2].$color[3],
	                                 $color[4].$color[5]);
	    elseif (strlen($color) == 3)
	        list($r, $g, $b) = array($color[0], $color[1], $color[2]);
	    else
	        return false;
	
	    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
	
	    return array($r, $g, $b);
	}
	
	static function rgb2html($r, $g=-1, $b=-1){
	    if (is_array($r) && sizeof($r) == 3)
	        list($r, $g, $b) = $r;
	
	    $r = intval($r); $g = intval($g);
	    $b = intval($b);
	
	    $r = dechex($r<0?0:($r>255?255:$r));
	    $g = dechex($g<0?0:($g>255?255:$g));
	    $b = dechex($b<0?0:($b>255?255:$b));
	
	    $color = (strlen($r) < 2?'0':'').$r;
	    $color .= (strlen($g) < 2?'0':'').$g;
	    $color .= (strlen($b) < 2?'0':'').$b;
	    return '#'.$color;
	}
	
	static function isPoly($int){
		$int = "$int";
		$j = strlen($int)-1;
		$i = 0;
		
		while ($i<$j){
			$a = substr($int,$i,1);
			$b = substr($int,$j,1);
			//echo "<hr>a:$a b:$b <hr>";
			$i++;
			$j--;
			if ($a!=$b) return false;
		}
		
		return ($a==$b);
	}
	
	static function getDocFromHTML($source){
		$doc = new HTML_TO_DOC();
		return $doc->getDocData($source);
	}
	
	static function arr_shift(&$arr){
		$arr = array_reverse($arr);
		$ret = array_pop($arr);
		$arr = array_reverse($arr);
		return $ret; 
	}
	
	static function str_upWords($str){
		$str[0] = strtoupper($str[0]);
	/*	while ($pos = strpos($str,' ')){
			$str[$pos+1] = strtoupper($str[$pos+1]);
		}*/
		return $str;
	}
	
	static function camelCase($str){
		if (strlen($str)>0){
			$str = ucwords($str);
			$str[0] = strtolower($str[0]);
			return str_replace(" ","",$str);
		}
		return '';
	}
	
	static function implodeCamelCase($arr){
		$str = implode(" ",$arr);
		return self::camelCase($str);
	}
	
	/**
	 * Splits up a string into an array similar to the explode() function but according to CamelCase.
	 * Uppercase characters are treated as the separator but returned as part of the respective array elements.
	 * @author Charl van Niekerk <charlvn@charlvn.za.net>
	 * @param string $string The original string
	 * @param bool $lower Should the uppercase characters be converted to lowercase in the resulting array?
	 * @return array The given string split up into an array according to the case of the individual characters.
	 */
	static function explodeCase($string, $lower = true)
	{
	  // Split up the string into an array according to the uppercase characters
	  $array = preg_split('/([A-Z][^A-Z]*)/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	  
	  // Convert all the array elements to lowercase if desired
	  if ($lower) {
	    $array = array_map('strtolower', $array);
	  }
	  
	  // Return the resulting array
	  return $array;
	}
		
}