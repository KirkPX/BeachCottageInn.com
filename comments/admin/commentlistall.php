<?php

/**
 * GentleSource - Comment Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


define('G10E_ROOT', '../');


// Settings
$g10e_detail_template                = 'admin_comment_list_all.tpl.html';
$message                            = array();

define('G10E_ALTERNATIVE_TEMPLATE', 'admin');
define('G10E_LOGIN_LEVEL', 1);


// Include
require G10E_ROOT . 'include/core.inc.php';


// Start output handling
$out = new g10e_output($g10e_detail_template);


// Delete comment
if ($comment_id = g10e_gpc_vars('c')
        and g10e_gpc_vars('do') == 'd') {
    $delete_confirmation = array(
                            'dialogue'      => 1,
                            'comment_id'    => $comment_id,
                            'anchor'        => g10e_gpc_vars('p')
                            );
    $out->assign('delete_confirmation', $delete_confirmation);
}
if ($comment_id = g10e_gpc_vars('c')
        and g10e_gpc_vars('do') == 'dc') {
    if (g10e_comment::delete($comment_id)) {
        $g10e['message'][] = $g10e['text']['txt_delete_entry_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_delete_entry_failed'];
    }
}

// Delete comment list
if (g10e_gpc_vars('submit_delete_comments')) {
    $delete_confirmation = array(
                            'dialogue'      => 1,
                            'list'   =>     g10e_gpc_vars('delete_comment'),
                            );
    $out->assign('delete_list_confirmation', $delete_confirmation);
}
if (g10e_gpc_vars('submit_delete_comments_c')) {
    if (g10e_comment::delete_list(g10e_gpc_vars('delete_comment'))) {
        $g10e['message'][] = $g10e['text']['txt_delete_entries_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_delete_entries_failed'];
    }
}

// Approve/disapprove comment
if ($comment_id = g10e_gpc_vars('c')
        and g10e_gpc_vars('do') == 'a') {
    if (g10e_comment::status($comment_id, $g10e['comment_status']['approved'])) {
        $g10e['message'][] = $g10e['text']['txt_change_status_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_change_status_failed'];
    }
}
if ($comment_id = g10e_gpc_vars('c')
        and g10e_gpc_vars('do') == 'da') {
    if (g10e_comment::status($comment_id, $g10e['comment_status']['unapproved'])) {
        $g10e['message'][] = $g10e['text']['txt_change_status_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_change_status_failed'];
    }
}
if ($comment_id = g10e_gpc_vars('c')
        and g10e_gpc_vars('do') == 'ms') {
    if (g10e_comment::status($comment_id, $g10e['comment_status']['spam'])) {
        $g10e['message'][] = $g10e['text']['txt_change_status_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_change_status_failed'];
    }
}
if ($comment_id = g10e_gpc_vars('c')
        and g10e_gpc_vars('do') == 'ns') {
    if (g10e_comment::status($comment_id, $g10e['comment_status']['approved'])) {
        $g10e['message'][] = $g10e['text']['txt_change_status_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_change_status_failed'];
    }
}


// Get comment data
require 'commentlistadmin.class.inc.php';
$comment = new g10e_comment_list(true, array('limit' => $g10e['comment_list_result_number'], 'identifier' => 'commentlistalladmin'));
if ($comment_data = $comment->get_list_all()) {
    $out->assign('comment_list', $comment_data);
}
$out->assign($comment->values());





// Save limit as setting
if ($limit = g10e_gpc_vars('limit')) {
    g10e_setting::write('comment_list_result_number', $limit);
}

// -----------------------------------------------------------------------------



// Search form
require_once 'HTML/QuickForm.php';


// Start form handler
$form = new HTML_QuickForm('form', 'POST');


// Get list form elements (grouping, sorting, searching)
$search_field_list  = $comment->search_field_list();


// Get form configuration
require 'list_form.inc.php';
$form->addElement('hidden', 'i');
$form->setConstants(array('i' => g10e_gpc_vars('i')));
$form->setDefaults($comment->default_values());


require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($out->get_object, true);

$form->accept($renderer);


// Assign array with form data
$out->assign('form', $renderer->toArray());







// Output
$out->assign('status_approved', $g10e['comment_status']['approved']);
$out->assign('status_spam', $g10e['comment_status']['spam']);
$out->finish();






?>
