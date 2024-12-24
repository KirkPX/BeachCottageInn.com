<?php

/**
 * GentleSource Guestbook Script -  database.class.inc.php
 *
 * @copyright   (C) Ralf Stadtaus , {@link http://www.gentlesource.com/}
 *
 */

require_once 'MDB2.php';




/**
 * Handler/Wrapper for database
 */
class g10e_database
{




    /**
     * Connect to database
     *
     * @access private
     */
    function connect($force_new_connection = false)
    {
        global $g10e;

        if (!isset($GLOBALS['database_connection'])
                or $force_new_connection == true) {
            $db = MDB2::connect($g10e['dsn']);
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






    /**
     * Disconnect from database
     *
     * @access private
     */
    function disconnect()
    {
        if (!isset($GLOBALS['database_connection'])) {
            return false;
        }
//        if (!isset($GLOBALS['database_connection']['connection'])) {
//            return false;
//        }
        $GLOBALS['database_connection']->disconnect();
    }






    /**
     * Get connection status
     *
     * @access private
     */
    function connection()
    {
        g10e_database::connect();
        if (isset($GLOBALS['database_connection'])) {
            return $GLOBALS['database_connection'];
        }
    }






    /**
     * Get next auto increment id - This method requires an entry in a/the
     * settings table
     *
     * @access public
     */
    function next_id($sequence)
    {
        $sql = "SELECT setting_value FROM " . G10E_SETTING_TABLE . "
                WHERE setting_name = ?";
        if ($db = g10e_database::query($sql, array('sequence_' . $sequence))) {
            $res = $db->fetchRow();
            if (PEAR::isError($res)) {
                system_debug::add_message($res->getMessage(), $res->getDebugInfo(), 'error', $res->getBacktrace());
                system_debug::add_message('SQL Statement', $sql, 'error');
                return false;
            }

            if (sizeof($res) > 0) {
                $next_id = $res['setting_value'] + 1;
                // Update sequence
                $data = array('setting_value' => $next_id);
                $where = "setting_name = ?";
                $where_data = array('sequence_' . $sequence);
                g10e_database::update('setting', $data, $where, $where_data);
                return $next_id;
            }

            // Create new sequence
            $data = array(  'setting_name' => 'sequence_' . $sequence,
                            'setting_value' => 1);
            if ($res = g10e_database::insert('setting', $data)) {
                return 1;
            }
        }
    }






    /**
     * Database query
     *
     * @access public
     * @param string $sql SQL statement
     * @param mixed  $params  array, string or numeric data to be used in
     *                         execution of the statement.  Quantity of items
     *                         passed must match quantity of placeholders in
     *                         query:  meaning 1 placeholder for non-array
     *                         parameters or 1 placeholder per array element.
     *
     * @return mixed  a new DB_result object for successful SELECT queries
     *                 or DB_OK for successul data manipulation queries.
     *                 A DB_Error object on failure.
     */
    function query($sql, $data = array(), $result_types = null)
    {
        $is_manip = null;
        if ($result_types === MDB2_PREPARE_MANIP) {
            $is_manip = $result_types;
        }

        if ($db = g10e_database::connection()) {
            $res = $db->prepare($sql, null, $is_manip);

            if (PEAR::isError($res)) {
                system_debug::add_message($res->getMessage(), $res->getDebugInfo(), 'error', $res->getBacktrace());
                system_debug::add_message('SQL Statement', $sql, 'error');
                return false;
            }

            $res = $res->execute($data);

            if (PEAR::isError($res)) {
                system_debug::add_message($res->getMessage(), $res->getDebugInfo(), 'error', $res->getBacktrace());
                system_debug::add_message('SQL Statement', $sql, 'error');
                return false;
            }

            return $res;
        }
    }






    /**
     * Quote input
     *
     * @access public
     *
     */
    function quote($input)
    {
        if ($db = g10e_database::connection()) {
            $input = $db->quoteSmart($input);
        }
        return $input;
    }






    /**
     * Escape input
     *
     * @access public
     */
    function escape($input)
    {
        if ($db = g10e_database::connection()) {
            $input = $db->escape($input);
        }
        return $input;
    }






    /**
     * Prepare insert record into database
     *
     * @param string $table Database table
     * @param array $data Input data
     *
     * @return bool Returns true on success and false on failure
     */
    function insert($table, $data)
    {
        global $g10e;
        if (!isset($g10e['mapping'][$table])) {
            $g10e['mapping'][$table] = array();
        }
        if (!isset($g10e['tables'][$table])) {
            return false;
        }
        if (!isset($g10e['db_fields'][$table])) {
            return false;
        }
        $res = g10e_database::perform_insert($g10e['tables'][$table],
                                                $data,
                                                $g10e['mapping'][$table],
                                                $g10e['db_fields'][$table]);
        return $res;
    }






    /**
     * Insert  record into database
     *
     * @param string 	$table 		Table name
     * @param array 	$data 		Data to be written to database
     * @param array 	$mapping 	Form field table field assignment
     * @param array 	$fields 	Table fields that are allowed to be written
     *
     * @return bool Returns true on success and false on failure
     */
    function perform_insert($table, $data, $mapping, $fields)
    {
        $columns = array();
        $values = array();
        $questionmarks = array();
        reset($fields);
        while (list($key, $val) = each($fields))
        {
            if (isset($mapping[$val]) and isset($data[$mapping[$val]])) {
                $columns[] = $val;
                $values[] = $data[$mapping[$val]];
                $questionmarks[] = '?';
                continue;
            }
            if (isset($data[$val])) {
                $columns[] = $val;
                $values[] = $data[$val];
                $questionmarks[] = '?';
                continue;
            }
        }
        $sql = "INSERT INTO " . $table . " (" . join(', ', $columns) . ")
                VALUES (" . join(', ', $questionmarks) . ")";
        if ($res = g10e_database::query($sql, $values)) {
            return $res;
        }
    }






    /**
     * Update record
     *
     * @param string	$table 		Database table
     * @param array 	$data 		Input data
     * @param string 	$where 		SQL where statement
     * @param array 	$where_data	Values for where statement
     *
     * @return bool Returns true on success and false on failure
     */
    function update($table, $data, $where, $where_data)
    {
        global $g10e;
        if (!isset($g10e['mapping'][$table])) {
            $g10e['mapping'][$table] = array();
        }
        if (!isset($g10e['tables'][$table])) {
            return false;
        }
        if (!isset($g10e['db_fields'][$table])) {
            return false;
        }
        $res = g10e_database::perform_update($g10e['tables'][$table],
                                                $data,
                                                $where,
                                                $where_data,
                                                $g10e['mapping'][$table],
                                                $g10e['db_fields'][$table]);
        return $res;
    }






    /**
     * Perform update record
     *
     * @param string	$table 		Database table name
     * @param array 	$data 		Input data
     * @param string 	$where 		SQL where statement
     * @param array 	$where_data	Values for where statement
     *
     * @return bool Returns true on success and false on failure
     */
    function perform_update($table, $data, $where, $where_data, $mapping, $fields)
    {
        global $g10e;
        $values = array();
        $set    = array();
        while (list($key, $val) = each($fields))
        {
            if (isset($mapping[$val]) and isset($data[$mapping[$val]])) {
                $set[]      = $val . ' = ?';
                $values[]   = $data[$mapping[$val]];
                continue;
            }
            if (isset($data[$val])) {
                $set[]      = $val . ' = ?';
                $values[]   = $data[$val];
                continue;
            }
        }
        $values = array_merge($values, $where_data);
        $sql = "UPDATE " . $table . " SET " . join(', ', $set) . "
                WHERE " . $where;
        if ($res = g10e_database::query($sql, $values, MDB2_PREPARE_MANIP)) {
            return $res;
        }
    }






    /**
     * Perform delete
     *
     * @param string	$table 		Database table name
     * @param string 	$where 		SQL where statement
     * @param array 	$data		Values for where statement
     *
     * @return bool Returns true on success and false on failure
     */
    function delete($table, $where, $data)
    {
        global $g10e;
        $sql = "DELETE FROM " . $table . "
                WHERE " . $where;
        if ($res = g10e_database::query($sql, $data, MDB2_PREPARE_MANIP)) {
            return $res;
        }
    }






    /**
     * Create prefixed field list
     *
     * @access public
     */
    function fields($prefix, $fields)
    {
        while (list($key, $val) = each($fields))
        {
            $list[] = $prefix . '.' . $val;
        }
        return join(', ', $list);
    }






    /**
     * Update database structure using sql files
     *
     * @param String $file File name
     * @return Array
     */
    function update_database_structure($file)
    {

        if (!file_exists($file)) {
            return array('error' => 'FILE_NOT_FOUND');
        }

        $sql = join('', file($file));
        if (strlen($sql) <= 0) {
            return array('error' => 0);
        }
        $sql_error = array();
        if ($statement = $this->sql_statements($sql)) {

            $num = count($statement);
            for ($i = 0; $i < $num; $i++)
            {
                $sql = $this->table_name_replace($statement[$i]);
                if ($this->query($sql) == 1) {
                    $check[] = 1;
                }
            }
            if (!isset($check) or count($check) < $num) {
                return array('error' => 'DB_ERROR');
            }
        }

        return array('error' => 0);
    }

//--------------------------------------------------------------------------




    /**
     * Get sql statements
     *
     * @access private
     */
    function sql_statements($data)
    {
        $inserts    = explode("\n", $data);
        $num        = count($inserts);
        $previous   = '';

        for ($i = 0; $i < $num; $i++)
        {
            $line = trim($inserts[$i]);

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






    /**
     * Replace table name and/or prefix markers in SQL statements with table
     * names
     *
     * @access private
     */
    function table_name_replace($content)
    {
        global $g10e;

        reset($g10e['tables']);
        while (list($key, $val) = each($g10e['tables']))
        {
            $content = str_replace('{' . $key . '}', $val, $content);
        }

        // Replace prefix
        $content = str_replace('{prefix}', $g10e['database_table_prefix'], $content);

        return $content;
    }

    /**
     * Get last insert ID
     */
    function last_insert_id($table = null)
    {
        if ($db = g10e_database::connection()) {
            return $db->lastInsertID($table);
        }
    }






}