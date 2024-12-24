<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


//require_once 'database.class.inc.php';




/**
 *
 */
class g10e_installation
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
    function g10e_installation()
    {
    }

// -----------------------------------------------------------------------------




    /**
     * Check if dbconfig.php exists
     *
     */
    function status()
    {
        if (is_file(G10E_ROOT . 'dbconfig.php')) {
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

        include G10E_ROOT . 'language/language.' . $g10e['default_language'] . '.php';
        $g10e['text'] = $text;

        // Configuration
        $detail_template                = 'installation.tpl.html';
        $message                        = array();
        $g10e['output']                  = array();
        $g10e['multi_domain'] = false;
        $g10e['domain_id'] = 0;

        // Includes
        require_once 'HTML/QuickForm.php';

        // Start output handling
        $out = new g10e_output($detail_template);

        // Start form field handling
        $form = new HTML_QuickForm('install', 'POST');
        require_once 'installation_form.inc.php';


        // Validate form
        $show_form = true;
        $db_error  = false;
        if ($form->validate()) {
            define('G10E_COMMENT_TABLE',     strtolower($g10e['_post']['prefix']) . 'entry');
            define('G10E_SETTING_TABLE',     strtolower($g10e['_post']['prefix']) . 'setting');

            $g10e['tables']['comment']       = G10E_COMMENT_TABLE;
            $g10e['tables']['setting']       = G10E_SETTING_TABLE;
            $dsn = array(   'phptype'   => 'mysql',
                            'hostspec'  => $g10e['_post']['hostname'],
                            'database'  => $g10e['_post']['database'],
                            'username'  => $g10e['_post']['username'],
                            'password'  => $g10e['_post']['dbpassword']
                            );
            if (!$db = $this->connect($dsn)) {
                $g10e['message'][] = $g10e['text']['txt_enter_correct_database_data'];
            } else {
                if (!$this->process($dsn)) {
                    $g10e['message'][]  = $g10e['text']['txt_installation_failed'];
                } else {

                    // Set admin account
                    if ($g10e['alternative_password'] == true) {
                        $password = md5(strtolower($g10e['_post']['login_name']) . $g10e['_post']['password']);
                    } else {
                        $password = md5($g10e['_post']['password']);
                    }
                    $arr = array(   'login'     => $g10e['_post']['login_name'],
                                    'email'     => $g10e['_post']['email'],
                                    'password'  => $password
                                    );
                    $ser = serialize($arr);
                    g10e_setting::write('administration_login', $ser);

                    // Set script URL
                    // $script_url = str_replace('admin', '', str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])));
                    $script_url = str_replace('//', '/', str_replace('admin', '', str_replace('\\', '/', dirname($_SERVER['PHP_SELF']) . '/')));
                    g10e_setting::write('script_url', $script_url);
                    g10e_setting::write('database_version', $g10e['version']);

                    // Write dbconfig.php file
                    $write_file = true;
                    $dsn['prefix'] = strtolower($g10e['_post']['prefix']);
                    if (!$this->install_file(
                                    G10E_ROOT . 'include/dbconfig.php.tpl',
                                    $dsn,
                                    G10E_ROOT . 'dbconfig.php')) {
                        $write_file = false;
                        $g10e['message'][]  = $g10e['text']['txt_write_dbconfig_failed'];
                    }

                    // Write settings
                    if ($write_file == true) {

                        $g10e['message'][]  = $g10e['text']['txt_installation_successful'];
                        $show_form = false;
                    }
                }
            }

        } else {
            if (sizeof($g10e['_post']) > 0) {
                $g10e['message'][] = $g10e['text']['txt_fill_out_required'];
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
            $db =& MDB2::connect($dsn);
            if (PEAR::isError($db)) {
                system_debug::add_message($db->getMessage(), $db->getDebugInfo(), 'system');
            } else {
                $db->setFetchMode(MDB2_FETCHMODE_ASSOC);
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
            $res =& $db->query($sql);
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
        $file = G10E_ROOT . 'include/sql/install.sql';
        $error = false;
        if (is_file($file)) {
            $sql = $this->parse_sql(file($file));
            reset($sql);
            foreach ($sql AS $statement)
            {
                // Replace prefix
                $statement = str_replace('{prefix}', strtolower($g10e['_post']['prefix']), $statement);
                if (!$this->query($dsn, $statement)) {
                    $error = true;
                }
            }
        } else {
            system_debug::add_message('Install File Not Found', $file, 'system');
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
        $num        = count($statement);
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
     * Write files
     *
     * @param Array $data Data to be written into file
     * @param String $path Path to the place where the file is to be written
     * @param String $template Path to the template to be used
     * @access private
     */
    function install_file($source, $data, $target)
    {
        $content = join('', file($source));

        reset($data);
        foreach ($data AS $marker => $value)
        {
            $content = str_replace('{$' . $marker . '}', $value, $content);
        }

        if (file_put_contents($target, $content)) {
            return true;
        }
    }


//------------------------------------------------------------------------------





} // End of class








?>
