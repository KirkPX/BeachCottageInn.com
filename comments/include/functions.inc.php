<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */




/**
 * Check and get GPC vars
 */
function g10e_gpc_vars($variable, $default = '')
{
    global $g10e;

    if (isset($g10e['_get'][$variable])) {
        return $g10e['_get'][$variable];
    }
    if (isset($g10e['_post'][$variable])) {
        return $g10e['_post'][$variable];
    }
    if (isset($g10e['_cookie'][$variable])) {
        return $g10e['_cookie'][$variable];
    }
    if ($default != '') {
        return $default;
    }
}

// -----------------------------------------------------------------------------




/**
 * Format numbers to given format
 *
 * @param float $number
 * @return string
 */
function g10e_format_number($number)
{
    global $conf;

    $number = number_format($number, $conf['decimal_places'], $conf['decimals_delimiter'], $conf['thousands_delimiter']);
    return $number;
}

// -----------------------------------------------------------------------------




/**
 * Convert given number into float
 *
 * @param string $number
 * @return float
 */
function g10e_clean_number($number)
{
    global $conf;
    $pieces    = explode($conf['decimals_delimiter'], $number);
    $pieces[0] = preg_replace('#[^0-9]#', '', $pieces[0]);
    return (float) join('.', $pieces);
}

// -----------------------------------------------------------------------------




/**
 * Provide the content of a specified language file
 *
 */
function g10e_load_language($language)
{
    global $conf;
    $res = array();
    $path = $conf['language_directory'] . 'language.' . $language . 'inc.php';
    if (is_file($path)) {
        include $path;
        $res = $txt;
    }
    return $res;
}

//------------------------------------------------------------------------------




/**
 *
 */
function g10e_print_a($ar, $htmlize = 0)
{
    if ($htmlize == 1) {
        if (is_array($ar)) {
            array_walk($ar, create_function('&$ar', 'if (is_string($ar)) {$ar = htmlspecialchars($ar);}'));
        } else {
            $ar = htmlspecialchars($ar);
        }
    }

    echo '<pre>';
    print_r($ar);
    echo '</pre>';
}

//------------------------------------------------------------------------------




/**
 *
 */
function g10e_array_append()
{
    $args = func_get_args();
    $arr  = array();

    for ($i = 0; $i < count($args); $i++)
    {
        if (empty($args[$i])) {
            continue;
        }

        if (!is_array($args[$i])) {
            trigger_error('Supplied argument is not an array', E_USER_NOTICE);
        }

        while (list($key, $val) = each($args[$i]))
        {
            $arr[$key] = $val;
        }
    }
    return $arr;
}

//------------------------------------------------------------------------------






// HTML entities for input
function g10e_entity_input(&$value)
{
    if (is_array($value)) {
        array_walk($value, 'g10e_entity_input');
        return;
    }
//    $value = htmlentities($value);
    $value = strip_tags($value);
}




// Clean input
function g10e_clean_input(&$value)
{
    if (is_array($value)) {
        array_walk($value, 'g10e_clean_input');
        return;
    }

    if (ini_get('magic_quotes_gpc')) {
        $value = stripslashes($value);
    }
    $value = addslashes($value);
}

// Clean input
function g10e_clean_output(&$value)
{
    if (is_array($value)) {
        array_walk($value, 'g10e_clean_output');
        return;
    }
    $value = stripslashes($value);
}

// Escape output
function g10e_escape_output(&$value, $character_set = null)
{
    global $g10e;

    if (is_array($value)) {
        array_walk($value, 'g10e_escape_output');
        return;
    }
    $value = strip_tags($value, $g10e['output_ignore_tags']);
    if ($g10e['output_htmlentities'] == true) {
        $value = htmlentities($value, ENT_QUOTES, $g10e['text']['txt_charset']);
    }
}




// Unset all global variables
function g10e_unset_globals()
{
    if (ini_get('register_globals')) {
        foreach ($_REQUEST as $k => $v) {
            unset($GLOBALS[$k]);
        }
    }
}




/**
 * Create random string
 *
 */
function g10e_create_random($length, $pool = '')
{
    $random = '';

    if (empty($pool)) {
        $pool    = 'abcdefghkmnpqrstuvwxyz';
        $pool   .= '23456789';
    }

    srand ((double)microtime()*1000000);

    for($i = 0; $i < $length; $i++)
    {
        $random .= substr($pool,(rand()%(strlen ($pool))), 1);
    }

    return $random;
}



if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $content, $flags = 0) {
        if (!($file = fopen($filename, ($flags & 1) ? 'a' : 'w'))) {
            return false;
        }
        $n = fwrite($file, $content);
        fclose($file);
        return $n ? $n : false;
    }
}






// UTF-8 encode
function g10e_utf8_encode($value, $charset = null)
{
    global $g10e;

    if ($charset == null) {
        $charset = $g10e['text']['txt_charset'];
    }
    $encoded = false;
    if (function_exists('mb_convert_encoding')) {
        $encoded = mb_convert_encoding($value, 'UTF-8', $charset);
    }

    if (function_exists('iconv')) {
        $encoded = iconv($charset, 'UTF-8', $value);
    }

    if ($encoded == false) {
        $encoded = utf8_encode($value);
    }
    return $encoded;
}

function g10e_get_language_file_charset($file)
{
    include $file;
    return $text['txt_charset'];
}
