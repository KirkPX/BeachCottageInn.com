<?php

/**
 * GentleSource Guestbook Script - setting.class.inc.php
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


//require_once 'database.class.inc.php';




/**
 * Handle comments
 */
class g10e_setting
{









    /**
     * Write setting to database
     *
     * @access public
     */
    function write($name, $value)
    {
        global $g10e;

        $setting_domain_id = null;
        $domain_id_statement = ' = 0';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $setting_domain_id  = $domain_id;
            $domain_id_statement = ' = ' . (int)$domain_id;
        }

        if (g10e_setting::read($name)) {
            $data = array('setting_value' => $value, 'setting_domain_id' => $setting_domain_id);
            $where = "setting_name = ? AND setting_domain_id " . $domain_id_statement;
            $where_data = array($name);
            g10e_database::update('setting', $data, $where, $where_data);
        } else {
            $data = array('setting_name' => $name, 'setting_value' => $value, 'setting_domain_id' => $setting_domain_id);
            g10e_database::insert('setting', $data);
        }
        $g10e[$name] = $value;
    }






    /**
     * Get setting from database
     *
     * @access public
     */
    function read($name)
    {
        global $g10e;

        $sql = "SELECT setting_name, setting_value
                FROM " . G10E_SETTING_TABLE . "
                WHERE setting_name = ?
                AND   setting_domain_id = " . (int) $g10e['domain_id'] . "
                ";
        if ($db = g10e_database::query($sql, array($name))) {
            $res = $db->fetchRow();
            if (sizeof($res) > 0) {
                return $res;
            }
        }
    }






    /**
     * Get settings
     */
    function read_all($domain_id = null)
    {
        global $g10e;

        if ($domain_id === null) {
            $domain_id = $g10e['domain_id'];
        }

        $list = array();
        $sql = "SELECT      setting_name, setting_value
                FROM        " .  G10E_SETTING_TABLE . "
                WHERE       setting_domain_id = " . (int) $domain_id;

        if ($db = g10e_database::connection()) {
            if ($res =& $db->query($sql)) {
                if (PEAR::isError($res)) {
                    system_debug::add_message($res->getMessage(), $res->getDebugInfo(), 'error', $res->getBacktrace());
                    system_debug::add_message('SQL Statement', $sql, 'error');
                    return false;
                }
                while ($row = $res->fetchRow())
                {
                    g10e_clean_output($row);
                    $list[$row['setting_name']] = $row['setting_value'];
                }
            }
        }
        return $list;
    }





} // End of class








?>
