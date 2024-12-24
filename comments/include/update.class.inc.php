<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */




/**
 *
 */
class g10e_update
{

    /**
     * @var string
     * @access private
     */
//    var $default;

// -----------------------------------------------------------------------------




    /**
     * Manage installation
     *
     */
    function g10e_update()
    {
    }

// -----------------------------------------------------------------------------




    /**
     * Check if script and database table structure version match
     *
     */
    function status()
    {
        global $g10e;

        $current_version = null;
        $sql = "SELECT setting_name, setting_value
                FROM " . G10E_SETTING_TABLE . "
                WHERE setting_name = 'database_version'";
        if ($db = g10e_database::query($sql)) {
            $res = $db->fetchRow();
            if (sizeof($res) > 0) {
                $current_version = $res['setting_value'];
            }
        }

        if (is_null($current_version)) {
            $database_version = '1.0.0';
        } else {
            $database_version = $current_version;
        }
        $script_version = $g10e['version'];
        if ($g10e['version'] == '1.0') {
            $script_version = '1.0.0';
        }

        if (version_compare($script_version, $database_version) <= 0) {
            return true;
        }
    }

// -----------------------------------------------------------------------------




    /**
     * Start installation process
     *
     */
    function start()
    {
        global $g10e;

        // Configuration
        $detail_template                = 'update.tpl.html';
        $message                        = array();

        // Includes
        require_once 'HTML/QuickForm.php';

        // Start output handling
        $out = new g10e_output($detail_template);

        // Start form field handling
        $form = new HTML_QuickForm('install', 'POST');
        require_once 'update_form.inc.php';


        // Validate form
        $show_form = true;
        $db_error  = false;
        if ($form->validate()) {

            $dsn = array(   'phptype'   => 'mysql',
                            'hostspec'  => $g10e['_post']['hostname'],
                            'database'  => $g10e['_post']['database'],
                            'username'  => $g10e['_post']['username'],
                            'password'  => $g10e['_post']['dbpassword']
                            );


            // Check if dsn data from the form match the dsn data from dbconfig.php
            $database_data = true;
            foreach ($dsn AS $key => $value)
            {
                if (!isset($g10e['dsn'][$key]) or $g10e['dsn'][$key] != $value) {
                    $database_data = false;
                }
            }

            if ($database_data != true) {
                $g10e['message'][] = $g10e['text']['txt_enter_correct_database_data'];
            }

            // Check if admin data from the form match the admin data from the settings
            $admin_data = false;
            if ($ser = g10e_setting::read('administration_login')) {
                $login_data = unserialize($ser['setting_value']);
                if ($g10e['_post']['login_name'] == $login_data['login']
                        and md5($g10e['_post']['password']) == $login_data['password']) {
                    $admin_data = true;
                }
            }

            if ($admin_data != true) {
                $g10e['message'][] = $g10e['text']['txt_enter_correct_admin_data'];
            }

            // Process update if everything is okay
            if ($database_data == true and $admin_data == true){


                if (!$this->process($dsn)) {
                    $g10e['message'][]  = $g10e['text']['txt_update_failed'];
                } else {

                    $data = array('setting_value' => $g10e['version']);
                    $where = "setting_name = ?";
                    $where_data = array('database_version');
                    g10e_database::update('setting', $data, $where, $where_data);

                    $g10e['message'][] = $g10e['text']['txt_update_successful'];
                    $show_form = false;
                }
            }

        } else {
            if (sizeof($g10e['_post']) > 0) {
                $g10e['message'][]  = $g10e['text']['txt_fill_out_required'];
            }
        }


        require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($out->get_object, true);

        $form->accept($renderer);


        // Assign array with form data
        $out->assign('form', $renderer->toArray());


        // Output
        $out->assign(array('show_form' => $show_form));
        $out->finish();
        exit;
    }

// -----------------------------------------------------------------------------




    /**
     * Connect to database
     *
     * @access private
     */
    function connect($dsn)
    {
        global $g10e;
        if (!isset($GLOBALS['database_connection'])) {
            $db = DB::connect($dsn);
            if (PEAR::isError($db)) {
                system_debug::add_message($db->getMessage(), $db->getDebugInfo(), 'system');
            } else {
                $db->setFetchMode(DB_FETCHMODE_ASSOC);
                $GLOBALS['database_connection'] = $db;
            }
        }
        if (isset($GLOBALS['database_connection'])) {
            return $GLOBALS['database_connection'];
        }
    }

//------------------------------------------------------------------------------




    /**
     * Database query
     *
     * @access public
     * @param string $sql SQL statement
     *
     * @return mixed  a new DB_result object for successful SELECT queries
     *                 or DB_OK for successul data manipulation queries.
     *                 A DB_Error object on failure.
     */
    function query($dsn, $sql)
    {
        if ($db = $this->connect($dsn)) {
            $res = $db->query($sql);
            if (PEAR::isError($res)) {
                system_debug::add_message($res->getMessage(), $res->getDebugInfo(), 'error', $res->getBacktrace());
                system_debug::add_message('SQL Statement', $sql, 'error');
                return false;
            } else {
                return $res;
            }
        }
    }

//------------------------------------------------------------------------------




    /**
     * Process SQL statements
     *
     * @access private
     */
    function process($dsn)
    {
        global $g10e;

        $error = false;
        $sql = $this->parse_sql($this->select_update_files());
        reset($sql);
        foreach ($sql AS $statement)
        {
            // Replace prefix
            $statement = str_replace('{prefix}', strtolower($g10e['database_table_prefix']), $statement);
            if (!$this->query($dsn, $statement)) {
                $error = true;
            }
        }
        if ($error == false) {
            return true;
        }
    }

//------------------------------------------------------------------------------




    /**
     * Parse SQL file
     *
     * @access private
     */
    function parse_sql($sql)
    {
        if (!is_array($sql)) {
            $statement  = explode("\n", $sql);
        } else {
            $statement = $sql;
        }
        $num        = sizeof($statement);
        $previous   = '';
        $result     = array();
        for ($i = 0; $i < $num; $i++) {
            $line = trim($statement[$i]);
            // Check for line breaks within lines
            if (substr($line, -1) != ';') {
                $previous .= $line;
                continue;
            }

            if ($previous != '') {
                $line = $previous . $line;
            }
            $previous = '';

            $result[] = $line;
        }

        if (isset($result)) {
            return $result;
        }
    }


//------------------------------------------------------------------------------




    /**
     *
     */
    function select_update_files()
    {
        global $g10e;
        $list = array();
        require_once 'Find.php';
        if ($items = &File_Find::glob( '#update_(.*?)\.sql#', G10E_ROOT . 'include/sql/', "perl" )) {
//            asort($items);

            if (isset($g10e['database_version'])) {
                $current_version = $g10e['database_version'];
                if ($current_version == '1.0') {
                    $current_version = '1.0.0';
                }
            } else {
                $current_version = '1.0.0';
            }

            // $ver contains the older version
            // $sion contains the newer version
            while (list($key, $val) = each($items))
            {
                $new_version = substr($val, strlen('update_'));
                $new_version = substr($new_version, 0, strrpos($new_version, '-'));

                if (version_compare($new_version, $current_version) >= 0) {
                    $list[$new_version] = $val;
                }

            }
        }
        ksort($list);

        $sql = array();
        foreach ($list AS $file)
        {
            $sql[] = file_get_contents(G10E_ROOT . 'include/sql/' . $file);
        }
        return join("\n", $sql);
    }


//------------------------------------------------------------------------------





} // End of class








?>
