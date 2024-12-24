<?php

/**
 * GentleSource - language.class.inc.php
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */




/**
 * Class name and unique identifier for $GLOBALS array that contains the
 * instance
 */
define('LANGUAGE_CLASS', 'g10e_language');
define('LANGUAGE_INSTANCE', 'g10e_language_instance');


/**
 * Handle file names and pramameters
 *
 * @access public
 */
class g10e_language
{

    /**
     * @var array Query string values
     * @access private
     */
    var $language;

    //--------------------------------------------------------------------------




    /**
     * Constructor
     *
     * @access private
     */
    function language()
    {
    }

    //--------------------------------------------------------------------------




    /**
     * Create single instance
     *
     */
    function &get_instance()
    {
        if (!isset($GLOBALS[LANGUAGE_INSTANCE])) {
            $GLOBALS[LANGUAGE_INSTANCE] = new g10e_language;
        }

        return $GLOBALS[LANGUAGE_INSTANCE];
    }

    //--------------------------------------------------------------------------




    /**
     *
     */
    function get($default)
    {
        global $g10e;
        $ref =& g10e_language::get_instance();
        $list = array();
        $redirect = false;

        // From post
        if (isset($g10e['_post']['g10e_language_selector']) and
            $g10e['_post']['g10e_language_selector'] != '') {
            $list[] = $g10e['_post']['g10e_language_selector'];
            $redirect = true;
        }

        // From get
        if (isset($g10e['_get']['g10e_language_selector']) and
            $g10e['_get']['g10e_language_selector'] != '') {
            $list[] = $g10e['_get']['g10e_language_selector'];
            $redirect = true;
        }

        // From cookie
        if (isset($g10e['_cookie'][$g10e['language_cookie_name']]) and
            $g10e['_cookie'][$g10e['language_cookie_name']] != '') {
            $list[] = $g10e['_cookie'][$g10e['language_cookie_name']];
        }

        // From domain
        $tld = substr($_SERVER['SERVER_NAME'], strrpos($_SERVER['SERVER_NAME'], '.') + 1);
        if (array_key_exists($tld, $g10e['domain_language'])) {
            $list[] = $g10e['domain_language'][$tld];
        }


        // From browser environment
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accept = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($accept AS $key => $val)
            {
                if ($pos = strpos($val, ';') and $pos !== 0) {
                    $val = substr($val, 0, $pos);
                }
                $list[] = trim($val);
            }
        }


        $new_list = array();
        $language_folder = $g10e['language_directory'];

        // Use utf-8 folder if it exists
        if ($g10e['use_utf8'] == 'Y') {
            $language_folder = $g10e['language_directory_utf8'];
        }
        foreach ($list AS $key => $val)
        {
            if (!array_key_exists($val, $g10e['available_languages'])) {
                continue;
            }
            if (!is_file(G10E_ROOT . $language_folder . 'language.' . $val . '.php')) {
                // Go back to default language folder if language does not exists in utf-8 folder
                if (!is_file(G10E_ROOT . $g10e['language_directory'] . 'language.' . $val . '.php')) {
                    continue;
                }
            }
            $new_list[] = $val;
        }
        if (sizeof($new_list) > 0) {
            $new_language = $new_list[0];
        } else {
//            $language_setting = g10e_setting::read('default_language');
            $new_language = $default;
        }

        if (!isset($g10e['_cookie'][$g10e['language_cookie_name']])
                or $g10e['_cookie'][$g10e['language_cookie_name']] != $new_language) {

            $ref->set($new_language);
            $ref->language = $new_language;
        }

        if (true == $redirect) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . urldecode(trim(g10e_gpc_vars('r'))));
        }

        return $new_language;
    }

    //--------------------------------------------------------------------------




    /**
     *
     */
    function set($language)
    {
        global $g10e;
        $ref = g10e_language::get_instance();

        // Write cookie
        setcookie(  $g10e['language_cookie_name'],
                    $language,
                    time()+(3600*24*360*10),
                    $g10e['cookie_path'],
                    $g10e['cookie_domain']);



//        echo   $g10e['language_cookie_name'] . ' ' .
//                    $language . ' ' .
//                    (time()+(3600*24*360*10)) . ' ' .
//                    $g10e['cookie_path'] . ' ' .
//                    $g10e['cookie_domain'];
    }

    //--------------------------------------------------------------------------




    /**
     * Load the content of a specified language file
     *
     * @access public
     * @param string $language
     * @param string $item Part of the language file
     */
    function load($language)
    {
        global $g10e;
        $res = array();

        $language_folder = $g10e['language_directory'];


        // Use utf-8 folder if it exists
        if ($g10e['use_utf8'] == 'Y') {
            $language_folder = $g10e['language_directory_utf8'];
        }

        // Go back to default language folder if language does not exists in utf-8 folder
        if (!is_file(G10E_ROOT . $language_folder . 'language.' . $language . '.php')
                and is_file(G10E_ROOT . $g10e['language_directory'] . 'language.' . $language . '.php')) {
            $language_folder = $g10e['language_directory'];
        }

        $path = G10E_ROOT . $language_folder . 'language.' . $language . '.php';

        include $path;
        if (is_file($path)) {
            include $path;
            $res = $text;
        }

        return $res;
    }

    //--------------------------------------------------------------------------





} // End of class
?>