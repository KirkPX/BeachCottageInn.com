<?php

/**
 * GentleSource Guestbook Script - comment.class.inc.php
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


//require_once 'database.class.inc.php';




/**
 * Handle comments
 */
class g10e_comment
{




    /**
     * Get comment details
     *
     * @access public
     */
    function get($id)
    {
        global $g10e;

        $data = array((int)$id);
        $multi_statement = ' AND comment_domain_id = 0';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $data['comment_domain_id']  = $domain_id;
            $multi_statement = ' AND comment_domain_id = ' . (int)$domain_id;
        }
        $sql = "SELECT  c.*
                FROM    " . G10E_COMMENT_TABLE . " AS c
                WHERE   c.comment_id = ?" . $multi_statement;
        if ($db = g10e_database::query($sql, $data)) {
            $res = $db->fetchRow();
            if (PEAR::isError($res)) {
                system_debug::add_message($res->getMessage(), $res->getDebugInfo(), 'error', $res->getBacktrace());
                system_debug::add_message('SQL Statement', $sql, 'error');
                return false;
            }

            if (sizeof($res) > 0) {
                g10e_clean_output($res);
                return $res;
            }
        }

    }







    /**
     * Write comment to database
     *
     * @access public
     */
    function put()
    {
        global $g10e;


        $data = array_merge($g10e['_post'], $this->enhance());

        $data['comment_timestamp']  = g10e_time::current_timestamp();

        $page_data = array(
                        'page_allow_comment'    => $g10e['display_comment_form']
                        );


        g10e_module::call_module('frontend_save_content', $data, $page_data);

        // Comment blocked
        if ($page_data['page_allow_comment'] == 'N') {
            return false;
        }

        // Write into comment table
        if (!$comment_id = g10e_database::next_id('comment')) {
            return false;
        }
        $data['comment_id']         = $comment_id;
        $data['comment_domain_id']  = 0;

        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $data['comment_domain_id']  = $domain_id;
        }

        // Trigger moderation
        if ($g10e['enable_moderation'] == 'Y'
                and !isset($data['comment_status'])) {
            $data['comment_status'] = 100;
            $g10e['message'][] = $g10e['text']['txt_thanks_moderation'];
        }

        // Write comment
        if ($res = g10e_database::insert('comment', $data)) {
            if (!isset($data['comment_status']) or $data['comment_status'] == 0) {
                $g10e['message'][] = $g10e['text']['txt_comment_posted'];
            }
            $g10e['message'][] = $g10e['text']['txt_thanks'];
            return true;
        } else {
            $g10e['message'][] = $g10e['text']['txt_error_entry'];
            return false;
        }
    }






    /**
     * Update comment
     *
     * @access public
     */
    function update($id)
    {
        global $g10e;

        $data = array_merge($g10e['_post'], $this->enhance());
        $where_data = array($id);

        $multi_statement = ' AND comment_domain_id = 0';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $multi_statement = ' AND comment_domain_id = ' . (int)$domain_id;
        }

        $where = "comment_id = ?" . $multi_statement;
        if (g10e_database::update('comment', $data, $where, $where_data)) {
            return true;
        }
    }






    /**
     * Update comment status
     *
     * @access public
     */
    function status($id, $status)
    {
        global $g10e;

        $data = array('comment_status' => $status);
        $where_data = array($id);

        $multi_statement = ' AND comment_domain_id = 0';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $multi_statement = ' AND comment_domain_id = ' . (int)$domain_id;
        }

        $where = "comment_id = ?" . $multi_statement;
        if (g10e_database::update('comment', $data, $where, $where_data)) {
            return true;
        }
    }







    /**
     * Delete comment
     *
     * @access public
     */
    function delete($id)
    {
        global $g10e;

        $data = array((int)$id);
        $multi_statement = ' AND comment_domain_id = 0';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $multi_statement = ' AND comment_domain_id = ' . (int)$domain_id;
        }

        $where = " comment_id = ?" . $multi_statement;
        if ($res = g10e_database::delete(G10E_COMMENT_TABLE, $where, $data)) {
            return true;
        }
    }







    /**
     * Delete comment list
     *
     * @access public
     */
    function delete_list($arr)
    {
        global $g10e;

        if (!is_array($arr)) {
            return false;
        }
        $data = array();
        $qm   = array();
        foreach ($arr AS $id)
        {
            if (!is_numeric($id)) {
                continue;
            }
            $data[] = (int) $id;
            $qm[]   = '';
        }
        $multi_statement = ') AND comment_domain_id = 0';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $multi_statement = ') AND comment_domain_id = ' . (int)$domain_id;
        }
        $where = ' (comment_id = ? ' . join(' OR comment_id = ? ', $qm);
        $where .= $multi_statement;
        if ($res = g10e_database::delete(G10E_COMMENT_TABLE, $where, $data)) {
            return true;
        }
    }






    /**
     * Enhance comment data
     * @access private
     */
    function enhance()
    {
        global $g10e;
        $data = array(
                    'comment_hash'              => sha1($g10e['_post']['comment']),
                    'comment_author_ip'         => getenv('REMOTE_ADDR'),
                    'comment_author_host'       => @gethostbyaddr(getenv('REMOTE_ADDR')),
                    'comment_author_user_agent' => getenv('HTTP_USER_AGENT'),
                    );

        return $data;
    }






    /**
     * Translate database fields to form fields
     *
     */
    function form_mapping($data)
    {
        global $g10e;
        if (isset($g10e['mapping']['comment'])) {
            reset($g10e['mapping']['comment']);
            while (list($key, $val) = each($g10e['mapping']['comment']))
            {
                $data[$val] = $data[$key];
            }
        }

        return $data;
    }

    /**
     * Dynamic comment field name
     */
    function field_name()
    {
        global $g10e;
        $comment_field_name = 'comment';
        if ($g10e['dynamic_comment_field_name'] == 'Y') {
            $comment_field_name .= sha1($g10e['dsn']['password'] . date('H'));
        }
        return $comment_field_name;
    }

    /**
     * Dynamic comment field value
     */
    function field_value(&$post_data)
    {
        global $g10e;

        if ($g10e['dynamic_comment_field_name'] != 'Y') {
            return false;
        }
        if (count($_POST) <= 0) {
            return false;
        }

        $value = '';
        for ($i = date('H'); $i >= date('H') - 6; $i--)
        {
            $key = 'comment' . sha1($g10e['dsn']['password'] . $i);
            if (array_key_exists($key, $_POST)) {
                $value = $_POST[$key];
                break;
            }
        }
        $_POST['comment'] = $value;
        $g10e['_post']['comment'] = $value;
        for ($i = date('H'); $i >= date('H') - 6; $i--)
        {
            $key = 'comment' . sha1($g10e['dsn']['password'] . $i);
            $_POST[$key] = $value;
        }
    }






} // End of class


