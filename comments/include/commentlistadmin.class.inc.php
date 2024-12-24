<?php

/**
 * GentleSource Guestbook Script -  commentlist.class.inc.php
 *
 * @copyright   (C) Ralf Stadtaus , {@link http://www.gentlesource.com/}
 *
 */

require_once 'list.class.inc.php';




/**
 * Generate comment list
 */
class g10e_comment_list extends g10e_list
{


    /**
     * Database fields to be selected
     */
    var $fields = array('comment_id',
                        'comment_id AS id',
                        'comment_author_name',
                        'comment_author_email',
                        'comment_author_homepage',
                        'comment_author_city',
                        'comment_author_state',
                        'comment_author_country',
                        'comment_author_ip',
                        'comment_author_ip AS ip_address',
                        'comment_author_host',
                        'comment_author_user_agent',
                        'comment_text',
                        'comment_text AS frontend_text',
                        'comment_timestamp',
                        'comment_status'
                        );


    /**
     * Columns that can be sorted
     */
    var $order_columns = array( 'date'  => 'comment_timestamp');


    /**
     * Identifier to tell different list settings in session apart
     */
    var $identifier = 'commentlistadmin';


    /**
     * Default order direction for SQL statement
     * Possible values: ascending|descending
     */
    var $default_order_direction = 'descending';


    /**
     * Default order field for SQL statement
     */
    var $default_order_field = 'tc.comment_timestamp';

//------------------------------------------------------------------------------




    /**
     * Constructor
     */
    function g10e_comment_list($use_session, $setup = array())
    {
        global $g10e;

        // Search field select menu
        $this->search_field_list = array(
                                    'comment_text'          => $g10e['text']['txt_search_in'] . ' ' . $g10e['text']['txt_entry_text'],
                                    'comment_author_name'   => $g10e['text']['txt_search_in'] . ' ' . $g10e['text']['txt_name'],
                                    'comment_author_email'  => $g10e['text']['txt_search_in'] . ' ' . $g10e['text']['txt_email']
                                    );

        // Search SQL statements
        $this->search_statements = array(
                                    'comment_text'          => " AND tc.comment_text LIKE '%{query}%'",
                                    'comment_author_name'   => " AND tc.comment_author_name LIKE '%{query}%'",
                                    'comment_author_email'  => " AND tc.comment_author_email LIKE '%{query}%'",
                                    );

        // Configuration and setup
        if ($use_session == true) {
            $this->use_session = true;
        }
        $this->g10e_list($setup);
    }

//------------------------------------------------------------------------------




    /**
     * Get comment list
     */
    function get_list_all()
    {
        global $g10e;
        list($where, $data) = $this->where();

        $multi_domain_statement = ' AND tc.comment_domain_id = 0 ';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $multi_domain_statement = ' AND tc.comment_domain_id = ' . (int)$domain_id;
        }

        $cql = "SELECT      COUNT(*) as num_result
                FROM        (" . G10E_COMMENT_TABLE . " AS tc)
                            " .  $where . "
                            " . $multi_domain_statement;

        $sql = "SELECT      " . g10e_database::fields('tc', $this->fields) . "
                FROM        (" . G10E_COMMENT_TABLE . " AS tc)
                            " .  $where . "
                            " . $multi_domain_statement . "
                ORDER BY    " . $this->order_field . " " . $this->order_direction;

        if ($res = $this->query($cql, $sql, array())) {

            // Comment number
            if ($this->order_direction == 'ASC') {
                $comment_number = 0;
            } else {
                $comment_number = $this->num_results + 1 - $this->valid_offset();
            }

            // Last comment id for delete anchor
            $previous_id = 0;

            // Fetch comments
            while ($row = $res->fetchRow())
            {
                g10e_clean_output($row);
                g10e_escape_output($row);

                // Comment number
                if ($this->order_direction == 'ASC') {
                    $comment_number++;
                } else {
                    $comment_number--;
                }

                // Enhance user data
                $row['comment_author_name'] = ($row['comment_author_name'] == '' ? $g10e['text']['txt_anonymous'] : $row['comment_author_name']);

                $enhance = array(
                            'previous_id'       => $previous_id,
                            'comment_date'      => g10e_time::format_date($row['comment_timestamp']),
                            'comment_time'      => g10e_time::format_time($row['comment_timestamp']),
                            'comment_number'    => $comment_number,
                            'hostname_output'   => (strlen($row['comment_author_host']) > $g10e['hostname_length']) ? substr($row['comment_author_host'], 0, $g10e['hostname_length']) . $g10e['cut_off_string'] : $row['comment_author_host'],
                            'user_agent_output' => (strlen($row['comment_author_user_agent']) > $g10e['user_agent_length']) ? substr($row['comment_author_user_agent'], 0, $g10e['user_agent_length']) . $g10e['cut_off_string'] : $row['comment_author_user_agent'],
                            );

                $final  = array_merge(
                                    $row,
                                    $enhance
                                    );

                g10e_module::call_module('backend_content', $final, $g10e['module_additional']);

                $list[] = $final;

                // Last comment number for delete anchor
                $previous_id = $row['comment_id'];
            }
        }
        if (isset($list)) {
            return $list;
        }
    }

//------------------------------------------------------------------------------



}
?>
