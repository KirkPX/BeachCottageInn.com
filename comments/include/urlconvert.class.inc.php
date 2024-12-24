<?php

/**
 * GentleSource Guestbook Script - urlconvert.class.inc.php
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */




/**
 * Convert URL parameters to and fro
 */
class g10e_url_convert
{




    /**
     * Get query
     *
     * @access public
     */
    function get_query()
    {
        global $g10e;

        switch ($g10e['url_handling']) {
            case 'querystring':
                $query = getenv('QUERY_STRING');
                g10e_clean_input($query);
                return $query;
                break;
            case 'pathinfo':
                $query = str_replace('/', '', getenv('PATH_INFO'));
                g10e_clean_input($query);
                return $query;
                break;
            case 'parameter':
            case 'modrewrite':
            default:
                $query = g10e_gpc_vars('url');
                return $query;
                break;
        }

    }

// -----------------------------------------------------------------------------




    /**
     * put query
     *
     * @access public
     */
    function put_query($item)
    {
        global $g10e;

        switch ($g10e['url_handling']) {
            case 'querystring':
                $query = '?' . $item;
                return $query;
                break;
            case 'pathinfo':
                $query = '/' . $item;
                return $query;
                break;
            case 'modrewrite':
                return $item;
                break;
            case 'parameter':
            default:
                $query = '?url=' . $item;
                return $query;
                break;
        }

    }

// -----------------------------------------------------------------------------




    /**
     * Convert page
     *
     * @access public
     */
    function page_output($item)
    {
        $page = 'page' . $item . '.html';
        $page = g10e_url_convert::put_query($page);
        return $page;
    }

// -----------------------------------------------------------------------------




    /**
     * Convert page
     *
     * @access public
     */
    function page_input()
    {
        $item = g10e_url_convert::get_query();
        if (trim($item) == '') {
            return 1;
        }
        if (strpos($item, 'page') === false) {
            return 1;
        }
        $id = trim(substr($item, strpos($item, 'page') + 4, -5));
        if (is_numeric($id)) {
            return $id;
        }
    }

// -----------------------------------------------------------------------------




    /**
     * Convert id
     *
     * @access public
     */
    function id_output()
    {
        global $g10e;
        $arg_list = func_get_args();
        $url = join('-', $arg_list) . '.html';
        $url = g10e_url_convert::put_query($url);
        return $url;
    }

// -----------------------------------------------------------------------------




    /**
     * Convert id
     *
     * @access public
     */
    function id_input()
    {
        $item = g10e_url_convert::get_query();
        if (strpos($item, 'page') !== false) {
            return false;
        }
        $id = trim(substr($item, strrpos($item, '-') + 1, -5));
        if (is_numeric($id)) {
            return $id;
        }
    }

// -----------------------------------------------------------------------------





} // End of class








?>
