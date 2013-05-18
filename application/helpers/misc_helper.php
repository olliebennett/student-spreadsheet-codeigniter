<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* Display variable */
function d($d,$name = 'Unspecified Variable'){
	echo "<code>$name :</code><br /><br />";
	var_dump($d);
}

function filterCsv($str) {
	return str_replace(',', '_', $str);
}

function filterXml($str) {
	return str_replace('<', '&lt;', str_replace('>', '$gt;', $str));
}












// not used after all that!
function UNUSEDd_profiler($d, $name = 'Unspecified Variable') {
	
	$slug = str_replace(' ','_',trim(str_replace(array('$',':','-'),' ',strtolower($name))));
	
echo '
<fieldset id="ci_profiler_'.$slug.'" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">
';
echo '<legend style="color:#000;">&nbsp;&nbsp;Variable: '.$name.'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_'.$slug.'_table\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\'Show\'?\'Hide\':\'Show\';">Show</span>)</legend>';
echo '<table style=\'width:100%;display:none\' id=\'ci_profiler_'.$slug.'_table\'>';

foreach ($d as $k => $v) {
echo '
<tr>
<td style=\'padding:5px; vertical-align: top;color:#900;background-color:#ddd;\'>'.$k.'&nbsp;&nbsp;</td>
<td style=\'padding:5px; color:#000;background-color:#ddd;\'>'.$v.'</td>
</tr>
';
}

echo '
</table>
</fieldset>';
	


}

/**
 * Render Price
 * - Ignore any small variations from 0.
 * - Display "-" sign if negative (but don't bother with "+" sign)
 * - Wrap units and decimals in ".pounds" and ".price" span tags respectively, for styling. * 
 */
function render_price($val, $cur = '&#163;')
{
	
  if (!is_numeric($val)) {
    return 'NaN';
  }

  $pounds = intval(abs($val));
  $pence = substr(round(abs($val)*100%100) . '00', 0, 2);

  return ((abs($val) < 0.005) ? '' : ($val < 0 ? '-' : '<!--+-->')) . '&nbsp;' . $cur . '&nbsp;' . '<span class="price_pounds">' . $pounds . '</span>.<span class="price_pence">' . $pence . '</span>';

}

/**
 * Currency Code to ASCII
 * - Converts currency codes (GBP) to ASCII (&#163;).
 */
function cur_code_to_ascii($currency_code)
{
	if ($currency_code == 'GBP') return '&#163;';
	elseif ($currency_code == 'EUR') return '&#128;';
	elseif ($currency_code == 'USD') return '&#36;';
	elseif ($currency_code == 'JPY') return '&#165;';
	else return '[ERROR]';
}

/**
 * Render Help Tip
 * - build html for pop-over help tips.
 */
function helptip($str, $href='#') {
	return '<a href="' . $href . '" class="helptip" data-toggle="tooltip" title="' . $str . '"><i class="icon-question-sign"></i></a>';
}

/**
 * Validate (UK) Mobile Phone Number
 * - Returns number as 12 digits, in "447#########" format, or "0" if invalid number.
 */
function validate_mobile($num)
{
	
    $num = preg_replace("/[^0-9]/", "", $num); // Strip non-numbers

	if ((substr($num, 0, 3) == '447') && (strlen($num) == 12)) { // 447#########
		// return number as-is
		return $num; 
	}
	elseif ((substr($num,0,2) == '07') && (strlen($num) == 11)) { // 07#########
		// replace "07" with "447".
		return '447'.substr($num,2,11);
	} else {
		return FALSE;
	}
}




/* Display Time Ago */
// $time = time in the past that we're comparing with.
// $gran = Granularity - detail to which time_ago is given.
function UNUSEDtime_ago($time,$gran=2) {
	
		// Convert to unix time if needed (ie if YYYY-MM-DD HH-SS format is provided instead)
		if (!is_int($time)) $time = strtotime($time);
		
		$diff = time() - $time; // Time difference
		
		if ($diff < 0) return 'in the future';
		elseif ($diff<5) return 'a few moments ago';

		$periods = array('year' => 31536000,
		'month' => 2628000,
		'week' => 604800, 
		'day' => 86400,
		'hour' => 3600,
		'minute' => 60,
		'second' => 1);
		
		$str = '';
		
		foreach ($periods as $key => $value) {
			if ($diff >= $value) {
				$numtime = floor($diff/$value);
				$diff %= $value;
				$str .= ($str ? ' ' : '').$numtime.' ';
				$str .= (($numtime > 1) ? $key.'s' : $key);
				$gran--;
			}
			if ($gran == '0') { break; }
		}
		return $str.' ago';      
}

// EOF
