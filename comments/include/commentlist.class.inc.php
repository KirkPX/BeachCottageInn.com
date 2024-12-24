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
                        'comment_author_name',
                        'comment_author_email',
                        'comment_author_homepage',
                        'comment_author_city',
                        'comment_author_state',
                        'comment_author_country',
                        'comment_text',
                        'comment_text AS frontend_text',
                        'comment_timestamp'
                        );


    /**
     * Columns that can be sorted
     */
    var $order_columns = array('comment_timestamp');


    /**
     * Identifier to tell different list settings in session apart
     */
    var $identifier = 'commentlist';


    /**
     * Default order direction for SQL statement
     * Possible values: ascending|descending
     */
    var $default_order_direction = 'ascending';


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
                                    'comment_text' => $g10e['text']['txt_entry_text']
                                    );

        // Search SQL statements
        $this->search_statements = array(
                                    'comment_text'  => " AND tc.comment_text LIKE '%{query}%'"
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
    function get_list()
    {
        global $g10e;

        list($where, $data) = $this->where();
        $data[] = $g10e['comment_status']['approved'];

        $delay_statement = '';
        if ((int)$g10e['publish_delay'] < 0) {
            $data[] = g10e_time::current_timestamp() - ((int) $g10e['publish_delay'] * 60);
            $delay_statement = 'AND tc.comment_timestamp <= ?';
        }

        $multi_domain_statement = ' AND tc.comment_domain_id = 0 ';
        if ($g10e['multi_domain'] == true
                and $domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
            $multi_domain_statement = ' AND tc.comment_domain_id = ' . (int)$domain_id;
        }

        $cql = "SELECT      COUNT(*) as num_result
                FROM        (" . G10E_COMMENT_TABLE . " AS tc)
                            " .  $where . "
                AND         tc.comment_status = ?
                " . $delay_statement . "
                " . $multi_domain_statement;

        $sql = "SELECT      " . g10e_database::fields('tc', $this->fields) . "
                FROM        (" . G10E_COMMENT_TABLE . " AS tc)
                            " .  $where . "
                AND         tc.comment_status = ?
                " . $delay_statement . "
                " . $multi_domain_statement . "
                ORDER BY    " . $this->order_field . " " . $this->order_direction;

        if ($res = $this->query($cql, $sql, $data)) {

            // Comment number
            if ($this->order_direction == 'ASC') {
                $comment_number = 0;
            } else {
                $comment_number = $this->num_results + 1 - $this->valid_offset();
            }

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

                $row['comment_author_name'] = ($row['comment_author_name'] == '' ? $g10e['text']['txt_anonymous'] : $row['comment_author_name']);

                // Enhance user data
                $enhance = array(
                            'comment_date'      => g10e_time::format_date($row['comment_timestamp']),
                            'comment_time'      => g10e_time::format_time($row['comment_timestamp']),
                            'comment_number'    => $comment_number,
                            'comment_text'      => nl2br($row['comment_text']),
                            );

                $final  = array_merge(
                                    $row,
                                    $enhance
                                    );

                g10e_module::call_module('frontend_content', $final, $g10e['module_additional']);

                $list[] = $final;
            }
        }
        if (isset($list)) {
            return $list;
        }
    }

//------------------------------------------------------------------------------



}
?>
