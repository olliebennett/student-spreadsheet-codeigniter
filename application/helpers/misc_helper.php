<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* Display variable */
function d($d,$name = null){
	echo '<pre>';
	if ($name != null) echo $name.": ";
	else echo "Unknown Variable: ";
	var_dump($d);
	echo '</pre>';
}

// not used after all that!
function d_profiler($d,$name = 'Unknown Variable') {
	
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

/* Currency Code to ASCII */
/* Converts currency codes (GBP) to ASCII (&#163;). */
function cur_code_to_ascii($currency_code)
{
	if ($currency_code == 'GBP') return '&#163;';
	elseif ($currency_code == 'EUR') return '&#128;';
	elseif ($currency_code == 'USD') return '&#36;';
	elseif ($currency_code == 'JPY') return '&#165;';
	else return '[ERROR]';
}

/* Display Time Ago */
// $time = time in the past that we're comparing with.
// $gran = Granularity - detail to which time_ago is given.
function time_ago($time,$gran=2) {
	
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
	
/* Validate (UK?) Mobile Phone Number */
// Returns number as 12 digits, in "447#########" format, or "0" if invalid number.
function validate_mobile($num)
{
	// Strip Whitespace
	$num = str_replace(' ','',$num);
	// Check validity
	if (!ctype_digit(substr($num,-1,9))) // last 9 are not digits
	{
		$num = '0';
	}
	elseif ((substr($num,0,4) == '+447') && (strlen($num) == 13)) // +447#########
	{
		// cut off initial "+"
		$num = substr($num,1,13);
	}
	elseif ((substr($num,0,3) == '447') && (strlen($num) == 12)) // 447#########
	{
		$num = $num; // return number as-is
	}
	elseif ((substr($num,0,2) == '07') && (strlen($num) == 11)) // 07#########
	{
		// replace "07" with "447".
		$num = '447'.substr($num,2,11);
	}
	else
	{
		$num = '0';
	}
	return $num;
}


// EOF