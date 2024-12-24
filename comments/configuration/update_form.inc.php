<?php

/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


$form->addElement('text', 'hostname', $g10e['text']['txt_hostname']);
$form->addElement('text', 'database', $g10e['text']['txt_database']);
$form->addElement('text', 'username', $g10e['text']['txt_username']);
$form->addElement('password', 'dbpassword', $g10e['text']['txt_password']);

$form->addElement('text', 'login_name', $g10e['text']['txt_login_name']);
$form->addElement('password', 'password', $g10e['text']['txt_password']);

$form->addElement('submit', 'update', $g10e['text']['txt_update']);

$form->addRule('hostname', $g10e['text']['txt_enter_hostname'], 'required');
$form->addRule('database', $g10e['text']['txt_enter_database'], 'required');
$form->addRule('username', $g10e['text']['txt_enter_username'], 'required');
$form->addRule('dbpassword', $g10e['text']['txt_enter_password'], 'required');

$form->addRule('login_name', $g10e['text']['txt_enter_login_name'], 'required');
$form->addRule('password', $g10e['text']['txt_enter_password'], 'required');






?>
