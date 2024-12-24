<?php

/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


$form->addElement('text', 'login_name', $g10e['text']['txt_login_name']);
$form->addElement('text', 'email', $g10e['text']['txt_email']);
$form->addElement('password', 'password', $g10e['text']['txt_password']);
$form->addElement('password', 'repeat', $g10e['text']['txt_password_repeat']);
$form->addElement('submit', 'save', $g10e['text']['txt_save_account']);

$form->addRule('login_name', $g10e['text']['txt_enter_login_name'], 'required');
$form->addRule('login_name', $g10e['text']['txt_syntax_alphanumeric'], 'alphanumeric');
$form->addRule('email', $g10e['text']['txt_enter_email'], 'required');
$form->addRule('email', $g10e['text']['txt_syntax_email'], 'email');
$form->addRule('password', $g10e['text']['txt_enter_password'], 'required');
$form->addRule('repeat', $g10e['text']['txt_repeat_password'], 'required');
$form->addRule(array('password', 'repeat'), $g10e['text']['txt_passwords_do_not_match'], 'compare');








?>
