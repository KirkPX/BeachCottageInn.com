<?php

/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */




// Show or hide comments
$display_comments = array();
$display_comments[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_yes'], 'Y');
$display_comments[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_no'], 'N');
$form->addGroup($display_comments, 'display_comments', $g10e['text']['txt_display_entries']);


// Show or hide comment form
$display_comment_form = array();
$display_comment_form[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_yes'], 'Y');
$display_comment_form[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_no'], 'N');
$form->addGroup($display_comment_form, 'display_comment_form', $g10e['text']['txt_display_entry_form']);


// Show or hide turn off messages
$turn_off_messages = array();
$turn_off_messages[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_yes'], 'Y');
$turn_off_messages[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_no'], 'N');
$form->addGroup($turn_off_messages, 'display_turn_off_messages', $g10e['text']['txt_display_turn_off_messages']);


// Toggle separate comment form
$toggle_separate_comment_form = array();
$toggle_separate_comment_form[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_yes'], 'Y');
$toggle_separate_comment_form[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_no'], 'N');
$form->addGroup($toggle_separate_comment_form, 'separate_comment_form', $g10e['text']['txt_separate_comment_form']);

 
// Language
$select =& $form->addElement('select', 'default_language', $g10e['text']['txt_language'], $g10e['available_languages']);
$select->setSize(1); 

$select =& $form->addElement('select', 'frontend_language', $g10e['text']['txt_frontend_language'], $g10e['available_languages']);
$select->setSize(1); 

$use_utf8 = array();
$use_utf8[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_yes'], 'Y');
$use_utf8[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_no'], 'N');
$form->addGroup($use_utf8, 'use_utf8', $g10e['text']['txt_use_utf8_language_files']);

$language_selection = array();
$language_selection[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_yes'], 'Y');
$language_selection[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_no'], 'N');
$form->addGroup($language_selection, 'display_language_selection', $g10e['text']['txt_display_language_selection']);


// Frontend order
$select =& $form->addElement('select', 'frontend_order', $g10e['text']['txt_frontend_order'], $g10e['available_order']);
$select->setSize(1);

// Frontend results
$form->addElement('text', 'frontend_result_number', $g10e['text']['txt_frontend_result_number']);
$form->addRule('frontend_result_number', $g10e['text']['txt_error_required'], 'required');
$form->addRule('frontend_result_number', $g10e['text']['txt_error_number_syntax'],'numeric');



// Script URL
$form->addElement('text', 'script_url', $g10e['text']['txt_script_url']);

// Enable moderation
$enable_moderation = array();
$enable_moderation[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_yes'], 'Y');
$enable_moderation[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_no'], 'N');
$form->addGroup($enable_moderation, 'enable_moderation', $g10e['text']['txt_enable_moderation']);

// Publish delay 
$form->addElement('text', 'publish_delay', $g10e['text']['txt_publish_delay']);
$form->addRule('publish_delay', $g10e['text']['txt_error_required'], 'required');
$form->addRule('publish_delay', $g10e['text']['txt_error_number_syntax'],'numeric');


$select =& $form->addElement('select', 'url_handling', $g10e['text']['txt_url_handling'], $g10e['url_handling_types']);
$select->setSize(1); 

$form->addElement('submit', 'save', $g10e['text']['txt_save_settings']);



//$arr = $form->getRegisteredRules();
//g10e_print_a($arr);




?>
