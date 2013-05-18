<?php

/**
 * Output data as JSON format
 *
 * @access	public
 * @param	array  data to output
 * @return	true
 */
if ( ! function_exists('output_json'))
{
	function output_json($data)
	{

		$CI =& get_instance();
		//return $CI->security->xss_clean($str, $is_image);
		$CI->output->set_content_type('application/json');
		$CI->output->set_header('Cache-Control: no-cache, must-revalidate');
		$CI->output->enable_profiler(FALSE);
		$CI->output->set_output(json_encode($data));
	}
}
