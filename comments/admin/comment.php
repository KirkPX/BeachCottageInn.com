<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */



define('G10E_ROOT', '../');


// Settings
$g10e_detail_template                = 'admin_comment.tpl.html';

define('G10E_ALTERNATIVE_TEMPLATE', 'admin');
define('G10E_LOGIN_LEVEL', 1);



// Include
require G10E_ROOT . 'include/core.inc.php';


// Start output handling
$out = new g10e_output($g10e_detail_template);


// Start comment handling
$comment = new g10e_comment;


// Handle and validate form
require_once 'HTML/QuickForm.php';

// Dynamic comment field value
g10e_comment::field_value($_POST);

// Start form handler
$g10e_form = new HTML_QuickForm('form', 'POST', getenv('REQUEST_URI'));


// Get form configuration
require 'comment_form.inc.php';


// Validate form
$message = array();
$show_form = 'yes';
if ($comment_id = g10e_gpc_vars('c')
        and $g10e_form->validate()) {
    if ($comment->update($comment_id)) {
        $g10e['message'][] = $g10e['text']['txt_update_data_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_update_data_failed'];
    }
} else {
    if (sizeof($g10e['_post']) > 0) {
        $g10e['message'][] = $g10e['text']['txt_fill_out_required'];
    }
}
$out->assign('show_form', $show_form);


// Get comment data
if ($comment_id = g10e_gpc_vars('c')
        and $comment_data = $comment->get($comment_id)) {
    //array_walk($comment_data, 'g10e_clean_output');
    $defaults = array(
        'comment_id'=> $comment_data['comment_id'],
        'name'      => $comment_data['comment_author_name'],
        'email'     => $comment_data['comment_author_email'],
        'homepage'  => $comment_data['comment_author_homepage'],
        'city'      => $comment_data['comment_author_city'],
        'state'     => $comment_data['comment_author_state'],
        'country'   => $comment_data['comment_author_country'],
        g10e_comment::field_name()   => $comment_data['comment_text']
        );
    $g10e_form->setDefaults($defaults);
    $out->assign('comment_data', $comment_data);
} else {
    $g10e['message'][] = $g10e['text']['txt_entry_not_found'];
}


require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($out->get_object, true);

$g10e_form->accept($renderer);


// Assign array with form data
$out->assign('form', $renderer->toArray());


// Output
$out->finish();





?>
