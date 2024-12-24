<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


$form->addElement('text', 'login_name', $g10e['text']['txt_login_name'], array('tabindex' => 1));
$form->addElement('password', 'password', $g10e['text']['txt_password'], array('tabindex' => 2));
$form->addElement('submit', 'login', $g10e['text']['txt_login'], array('tabindex' => 3));

$form->addRule('login_name', $g10e['text']['txt_enter_login_name'], 'required');
$form->addRule('password', $g10e['text']['txt_enter_password'], 'required');








?>
