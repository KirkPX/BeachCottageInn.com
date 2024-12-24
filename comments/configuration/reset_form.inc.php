<?php

/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


$form->addElement('hidden', 'd', 'r');
$form->addElement('text', 'login_name', $g10e['text']['txt_login_name']);
$form->addElement('submit', 'send', $g10e['text']['txt_send']);

$form->addRule('login_name', $g10e['text']['txt_enter_login_name'], 'required');








?>
