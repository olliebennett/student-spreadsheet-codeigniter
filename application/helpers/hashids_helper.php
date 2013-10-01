<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/third_party/hashids/Hashids.php';

/**
 * Create the hashid object using config settings unless override values are passed thru.
 *
 * @access  public
 * @return  object
 */
if( ! function_exists('hashids_createobject'))
{
    function hashids_createobject($salt_ov=NULL, $min_hash_length_ov=NULL, $alphabet_ov=NULL)
    {
        $CI =& get_instance();

        $salt               = (!$salt_ov) ? $CI->config->item('hashids_salt') : $salt_ov;
        $min_hash_length    = (!$min_hash_length_ov) ? $CI->config->item('hashids_min_hash_length') : $min_hash_length_ov;
        $alphabet           = (!$alphabet_ov) ? $CI->config->item('hashids_alphabet') : $alphabet_ov;

        return new Hashids\Hashids($salt, $min_hash_length, $alphabet);
    }
}

/**
 * Encrypt an ID to a hashid.
 *
 * @access  public
 * @param   integer
 * @return  string  hashid
 */
if( ! function_exists('hashids_encrypt'))
{
    function hashids_encrypt($input, $salt=NULL, $min_hash_length=NULL, $alphabet=NULL)
    {
        $CI =& get_instance();

        if( !is_array($input) ) $input = array( intval($input) );

        $hashids = hashids_createobject($salt, $min_hash_length, $alphabet);

        $return = call_user_func_array( array($hashids, "encrypt"), $input );

        log_message('info', "hashids_encrypt(): Encrypted ID '" . $input[0] . "' to '$return'.");

        return $return;
    }
}

/**
 * Decrypt a hashid to an integer or array of integers.
 *
 * @access  public
 * @param   string  hashid
 * @return  array or integer - array returned if more than one value exists, else integer - NULL if not decryptable.
 */
if( ! function_exists('hashids_decrypt'))
{
    function hashids_decrypt($hash, $salt='', $min_hash_length=0, $alphabet='')
    {

        $hashids    = hashids_createobject($salt, $min_hash_length, $alphabet);
        $output     = $hashids->decrypt($hash);
        if (count($output) < 1) {
            log_message('warning', "hashids_decrypt(): Failed to decrypt hash '$hash'.");
            return NULL;
        }
        $return = (count($output) == 1) ? reset($output) : $output;
        log_message('info', "hashids_decrypt(): Decrypted HASH '$hash' to '$return'.");
        return $return;
    }
}

/* End of file hashids_helper.php */