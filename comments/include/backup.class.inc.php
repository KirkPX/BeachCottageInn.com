<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


//require_once 'database.class.inc.php';




/**
 * Handle backups
 */
class g10e_backup
{




    /**
     *
     *
     * @access public
     */
    function g10e_backup()
    {
    }

// -----------------------------------------------------------------------------




    /**
     * Export data
     *
     * @access public
     */
    function export()
    {
        global $g10e;

        $path = G10E_ROOT . $g10e['backup_directory'];

        // Create directory and .htaccess
        if (!is_dir($path)) {
            mkdir($path);
            $htcontent = "deny from all";
            file_put_contents($path . '.htaccess', $htcontent);

        }

//        set_time_limit(600);
        ini_set('max_execution_time', 600);
        ignore_user_abort(true);

        $multi_statement = ' = 0';
        $domain_hash = '';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $domain_hash = gentlesource_module_multi_domain::domain_hash($sub_domain = true);
            $multi_statement = ' = ' . (int)$domain_id;
        }

        //Get database content
        $filename   = $path . $this->filename($domain_hash . '_');
        $filename_dl= $path . $this->filename($domain_hash . '_download_');
        $source     = array("\x00", "\x0a", "\x0d", "\x1a");
        $target     = array('\0', '\n', '\r', '\Z');
        $dump       = array();
        $dump_dl    = array();

        foreach ($g10e['tables'] as $key => $val)
        {
            if ($key == 'domain') {
                continue;
            }
            if ($key == 'setting') {
                $sql = "SELECT * FROM " . $val . " WHERE setting_name != 'administration_login' AND setting_name != 'script_url' AND setting_name != 'database_version' AND " . $key . "_domain_id" . $multi_statement;
            } else {
                $sql = "SELECT * FROM " . $val . " WHERE " . $key . "_domain_id" . $multi_statement;
            }

            if ($res = g10e_database::query($sql, array($domain_id))) {
                while ($row = $res->fetchRow())
                {
                    $tmp   = array();
                    $tmp[] = 'INSERT INTO `' . $val . '` ';
                    $tmp[] = '(`' . join('`, `', array_keys($row)) . '`)';
                    $tmp[] = ' VALUES ';
                    $tmp[] = "('" . str_replace($source, $target, join("', '", array_values($row))) . "');";
                    $dump[]= join('', $tmp);

                    if (isset($row[$key . '_domain_id'])) {
                        unset($row[$key . '_domain_id']);
                    }
                    $tmp_dl   = array();
                    $tmp_dl[] = 'INSERT INTO `' . $val . '` ';
                    $tmp_dl[] = '(`' . join('`, `', array_keys($row)) . '`)';
                    $tmp_dl[] = ' VALUES ';
                    $tmp_dl[] = "('" . str_replace($source, $target, join("', '", array_values($row))) . "');";
                    $dump_dl[]= join('', $tmp_dl);
                }
            }
        }
        $content = join("\n", $dump);
        $content_dl = join("\n", $dump_dl);

        // Write file
        if (file_put_contents($filename, $content)
                and file_put_contents($filename_dl, $content_dl)) {
            return true;
        }
    }

// -----------------------------------------------------------------------------




    /**
     * Create file name
     *
     * @access public
     */
    function filename($id = '')
    {
        global $g10e;
        $filename = $id;
        $filename .= $g10e['backup_file_prefix'];
        $filename .= date('Y-m-d_H-i-s', g10e_time::current_timestamp());
        $filename .= '.sql';
        return $filename;
    }

// -----------------------------------------------------------------------------




    /**
     * List available backup files
     *
     * @access public
     */
    function file_list()
    {
        global $g10e;
        $list = array();
        if (!is_dir(G10E_ROOT . $g10e['backup_directory'])) {
//            return $list;
        }
        $domain_hash = '_';
        $replace_hash = '';
        if ($g10e['multi_domain'] == true
                and gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $domain_hash = gentlesource_module_multi_domain::domain_hash($sub_domain = true);
            $domain_hash .= '_';
            $replace_hash = $domain_hash;
        }
        require_once 'Find.php';
        if ($items = &File_Find::glob( '#^' . $domain_hash . $g10e['backup_file_prefix'] . '(.*?)\.sql#', G10E_ROOT . $g10e['backup_directory'], "perl" )) {
            if (PEAR::isError($items)) {
                system_debug::add_message($items->getMessage(), $items->getDebugInfo(), 'error', $items->getBacktrace());
                return $list;
            }
            arsort($items);
            while (list($key, $val) = each($items))
            {
                $date = $this->file_date($val);
                $time = str_replace('-', ':', substr($val, strrpos($val, '_') +1, 8));
                $val = str_replace($replace_hash, '', $val);
                $list[] = array('file' => $val,
                                'path' => G10E_ROOT . $g10e['backup_directory'],
                                'date' => $date,
                                'time' => $time
                                );
            }
        }
        return $list;

    }

// -----------------------------------------------------------------------------




    /**
     * Create formatted date from file name
     *
     * @access public
     */
    function file_date($val)
    {
        global $g10e;
        $date = substr($val, strpos($val, $g10e['backup_file_prefix']) + strlen($g10e['backup_file_prefix']), 10);
        $date = strtotime($date, g10e_time::current_timestamp());
        $date = g10e_time::format_date($date);
        return $date;

    }

// -----------------------------------------------------------------------------




    /**
     * Delete file
     *
     * @access public
     */
    function delete($file)
    {
        global $g10e;

        $file_name = trim($file);
        $download_file_name = '_download' . trim($file);
        if ($g10e['multi_domain'] == true
                and gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $domain_hash = gentlesource_module_multi_domain::domain_hash($sub_domain = true);
            $file_name = $domain_hash . '_' . trim($file);
            $download_file_name = $domain_hash . '_download_' . trim($file);
        }
        if (is_file(G10E_ROOT . $g10e['backup_directory'] . $file_name)) {
            if (unlink(G10E_ROOT . $g10e['backup_directory'] . $file_name)) {
                if (unlink(G10E_ROOT . $g10e['backup_directory'] . $download_file_name)) {
                    return true;
                }
            }
        }

    }

// -----------------------------------------------------------------------------




    /**
     * Import file
     *
     * @access public
     */
    function import($file)
    {
        global $g10e;

        $file_name = trim($file);
        $domain_statement = ' = 0';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $domain_hash = gentlesource_module_multi_domain::domain_hash($sub_domain = true);
            $file_name = $domain_hash . '_' . trim($file);
            $domain_statement = ' = ' . (int)$domain_id;
        }

        $file = G10E_ROOT . $g10e['backup_directory']. $file_name;
        $error = false;
//        set_time_limit(600);
        ini_set('max_execution_time', 600);
        ignore_user_abort(true);
        if (is_file($file)) {
            if (!$sql = g10e_installation::parse_sql(file($file))) {
                return false;
            }
            reset($sql);

            // Truncate tables
            foreach ($g10e['tables'] AS $table => $name)
            {
                if ($table == 'domain') {
                    continue;
                }
                if ($table == 'setting') {
                    $del = 'DELETE FROM `' . $name . '` WHERE `setting_name` != ? AND `setting_name` != ? AND `setting_name` != ? AND setting_domain_id ' . $domain_statement;
                    g10e_database::query($del, array('administration_login', 'script_url', 'database_version'));
                } else {
                    $del = 'DELETE FROM `' . $name . '` WHERE ' . $table . '_domain_id ' . $domain_statement;
                    g10e_database::query($del);
                }
            }
            foreach ($sql AS $statement)
            {
                // Replace prefix
                if (!$res =& g10e_database::query($statement)) {
                    $error = true;
                }
            }
        }
        if ($error == false) {
            return true;
        }

    }

// -----------------------------------------------------------------------------




} // End of class








?>
