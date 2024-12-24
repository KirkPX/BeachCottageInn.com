<?php

/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


$form->addElement('hidden', 'c');
$form->addElement('password', 'password', $g10e['text']['txt_password']);
$form->addElement('password', 'repeat', $g10e['text']['txt_password_repeat']);
$form->addElement('submit', 'save', $g10e['text']['txt_submit']);

$form->addRule('password', $g10e['text']['txt_enter_password'], 'required');
$form->addRule('repeat', $g10e['text']['txt_repeat_password'], 'required');
$form->addRule(array('password', 'repeat'), $g10e['text']['txt_passwords_do_not_match'], 'compare');








?>
