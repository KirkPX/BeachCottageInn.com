<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */

  /*****************************************************
  **
  ** THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY
  ** OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
  ** LIMITED   TO  THE WARRANTIES  OF  MERCHANTABILITY,
  ** FITNESS    FOR    A    PARTICULAR    PURPOSE   AND
  ** NONINFRINGEMENT.  IN NO EVENT SHALL THE AUTHORS OR
  ** COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
  ** OR  OTHER  LIABILITY,  WHETHER  IN  AN  ACTION  OF
  ** CONTRACT,  TORT OR OTHERWISE, ARISING FROM, OUT OF
  ** OR  IN  CONNECTION WITH THE SOFTWARE OR THE USE OR
  ** OTHER DEALINGS IN THE SOFTWARE.
  **
  *****************************************************/




// Settings
if (!defined('G10E_ROOT')) {
    define('G10E_ROOT', './');
}


$g10e_detail_template        = 'comment.tpl.html';
$frontend_language          = true;
define('G10E_LOGIN_LEVEL', 0);



// Include
require G10E_ROOT . 'include/core.inc.php';
require 'urlconvert.class.inc.php';

// Check for module standalone call
if (g10e_gpc_vars('module')) {
    $module_data = array('data' => g10e_gpc_vars('module'));
    g10e_module::call_module('standalone', $module_data, $g10e['module_additional']);
    exit;
}

// -----------------------------------------------------------------------------

require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
require_once 'HTML/QuickForm.php';


// Start output handling
$g10e_out = new g10e_output($g10e_detail_template);


// Start comment handling
$g10e_comment = new g10e_comment;

// Dynamic comment field value
g10e_comment::field_value($_POST);

// Start form handler
$g10e_form_action = getenv('REQUEST_URI');
if (g10e_gpc_vars('g10e_ssi') or g10e_gpc_vars('g10e_ssi_redirect')) {
    $g10e_form_action = $g10e['script_url'] . 'include.php';
}
$g10e_form = new HTML_QuickForm('form', 'POST', $g10e_form_action . '#g10e_form');




// Add redirect URL
if (g10e_gpc_vars('g10e_ssi') or g10e_gpc_vars('g10e_ssi_redirect')) {
    $g10e_form->addElement('hidden', 'g10e_ssi_redirect');
    if ($g10e_ssi_redirect = g10e_gpc_vars('g10e_ssi_redirect')) {
        $g10e['alternative_template'] = 'standalone';
    } else {
        $g10e_ssi_redirect = getenv('REQUEST_URI');
    }
    $g10e_form->setDefaults(array('g10e_ssi_redirect' => $g10e_ssi_redirect));
}

// -----------------------------------------------------------------------------




// Display or hide form
$g10e_active_form = true;
if ($g10e['display_comment_form'] != 'Y') {
	if ($g10e['display_turn_off_messages'] == 'Y') {
    	$g10e['message'][] = $g10e['text']['txt_entry_form_turned_off'];
	}
    $g10e_active_form = false;
}


// Get form configuration
require 'comment_form.inc.php';



// Validate form
$g10e_message = array();
if ($g10e['display_comment_form'] == 'Y') {
    $g10e_active_form = true;
    if (g10e_gpc_vars('save')) {
        if (isset($g10e['_post']['save']) and $g10e_form->validate()) {
            if ($g10e_comment->put()) {
                $g10e_active_form = false;

//                header('Location: ' . $g10e['server_protocol'] . $g10e['server_name'] . $g10e['script_url'] . '#g10e_top');
//                exit;
            }
            if ($g10e_ssi_redirect = g10e_gpc_vars('g10e_ssi_redirect')) {
                header('Location: ' . $g10e['server_protocol'] . $g10e['server_name'] . $g10e_ssi_redirect);
                exit;
            }
        } else {
            if (sizeof($g10e['_post']) > 0) {
                $g10e['message'][] = $g10e['text']['txt_fill_out_required'];
            }
        }
    }

    $g10e_form_renderer = new HTML_QuickForm_Renderer_ArraySmarty($g10e_out->get_object, true);
    $g10e_form->accept($g10e_form_renderer);
    $g10e_out->assign('form', $g10e_form_renderer->toArray());
} else {
//    $g10e['message'][] = $g10e['text']['txt_entries_disabled'];
//    $g10e_active_form = false;
}

// -----------------------------------------------------------------------------




// Get comment data
$g10e_comment_list_values = array('result_number' => 0);
if ($g10e['display_comments'] == 'Y'
        and $page = g10e_url_convert::page_input()) {
    require 'commentlist.class.inc.php';
    $g10e_list_setup = array('direction' => $g10e['frontend_order'],
                            'limit'     => 0,
                            'page'      => $page);
    if ((int) $g10e['frontend_result_number'] >= 1) {
        $g10e_list_setup['limit'] = (int) $g10e['frontend_result_number'];
        // Pagination does not work with SSI
        if (g10e_gpc_vars('g10e_ssi')) {
            $g10e_list_setup['limit'] = 0;
        }
        $g10e_out->assign('display_pagination', true);
    }
    $g10e_comment_list = new g10e_comment_list(false, $g10e_list_setup);
        if ($g10e_comment_data = $g10e_comment_list->get_list()) {
            $g10e_out->assign('comment_list', $g10e_comment_data);
        }
    $g10e_comment_list_values = $g10e_comment_list->values();
    $g10e_comment_list_values['start_page_url']      = g10e_url_convert::page_output(1);
    $g10e_comment_list_values['end_page_url']        = g10e_url_convert::page_output($g10e_comment_list_values['result_pages']);
    $g10e_comment_list_values['next_page_url']       = g10e_url_convert::page_output($g10e_comment_list_values['next_page']);
    $g10e_comment_list_values['previous_page_url']   = g10e_url_convert::page_output($g10e_comment_list_values['previous_page']);
    $g10e_out->assign($g10e_comment_list_values);

    if ($g10e_comment_list_values['result_limit'] > 0){
        $g10e_page = ceil(($g10e_comment_list_values['result_number'] + 1) / $g10e_comment_list_values['result_limit']);
    } else {
        $g10e_page = 1;
    }
    $g10e_form->setConstants(array('page' => $g10e_page));
}
$g10e_out->assign($g10e_comment_list_values);

// Page not found
if (!g10e_url_convert::page_input()) {
    $g10e_turned_off = array('frontend_text'         => $g10e['text']['txt_no_entries_found'],
                            'comment_author_name'   => $g10e['text']['txt_administrator'],
                            'comment_number'        => 1,
                            'comment_date'          => g10e_time::format_date(g10e_time::current_timestamp()),
                            'comment_time'          => g10e_time::format_time(g10e_time::current_timestamp())
                            );
    $g10e_out->assign('comment_list', array($g10e_turned_off));
}

// Entry display has been turned off
if ($g10e['display_comments'] != 'Y' and $g10e['display_turn_off_messages'] == 'Y') {
    $g10e_turned_off = array('frontend_text'         => $g10e['text']['txt_entry_display_turned_off'],
                            'comment_author_name'   => $g10e['text']['txt_administrator'],
                            'comment_number'        => 1,
                            'comment_date'          => g10e_time::format_date(g10e_time::current_timestamp()),
                            'comment_time'          => g10e_time::format_time(g10e_time::current_timestamp())
                            );
    $g10e_out->assign('comment_list', array($g10e_turned_off));
}

// -----------------------------------------------------------------------------




// Show or hide comment form and comment list according to settings
$g10e_show_comments  = true;
$g10e_show_form      = true;
$g10e_show_list_link = false;
$g10e_show_sign_link = false;

if ($g10e['separate_comment_form'] == 'Y') {
    $g10e_show_form      = false;
    $g10e_show_sign_link = true;
}

if (g10e_gpc_vars('d') == 'sign') {
    $g10e_show_form      = true;
    $g10e_show_comments  = false;
    $g10e_show_list_link = true;
}

// -----------------------------------------------------------------------------


$page_data = array('page_title' => $g10e['text']['txt_guestbook_script']);
$g10e_out->assign('page_data', $page_data);



// Output
$g10e_out->assign('active_form', $g10e_active_form);
$g10e_out->assign('show_form', $g10e_show_form);
$g10e_out->assign('show_comments', $g10e_show_comments);
$g10e_out->assign('show_list_link', $g10e_show_list_link);
$g10e_out->assign('show_sign_link', $g10e_show_sign_link);
$g10e_output = $g10e_out->finish(false);


