<?php

/**
 * GentleSource Guestbook Script - comment_form.inc.php
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


$tabindex = 9000;

$g10e_form->addElement('text',       'name',     $g10e['text']['txt_name'],           array('tabindex' => $tabindex++));
$g10e_form->addElement('text',       'email',    $g10e['text']['txt_email_hidden'],   array('tabindex' => $tabindex++));
$g10e_form->addElement('text',       'homepage', $g10e['text']['txt_homepage'],       array('tabindex' => $tabindex++));
$g10e_form->addElement('text',       'city',     $g10e['text']['txt_city'],           array('tabindex' => $tabindex++));
$g10e_form->addElement('text',       'state',    $g10e['text']['txt_state'],          array('tabindex' => $tabindex++));
$g10e_form->addElement('text',       'country',  $g10e['text']['txt_country'],        array('tabindex' => $tabindex++));
$g10e_form->addElement('textarea',   g10e_comment::field_name(),  $g10e['text']['txt_comment'],        array('rows' => 8, 'cols' => 30, 'tabindex' => $tabindex++));
$g10e_form->addElement('hidden',     'page');
$g10e_form->addElement('submit',     'save',     $g10e['text']['txt_submit'],         array('tabindex' => $tabindex++));

$g10e_form->addRule('email',     $g10e['text']['txt_valid_email'],    'email');
//$g10e_form->addRule('email',     $g10e['text']['txt_valid_email'],    'required');
$g10e_form->addRule('name',      $g10e['text']['txt_enter_name'],     'required');
$g10e_form->addRule(g10e_comment::field_name(),   $g10e['text']['txt_enter_comment'],  'required');

$g10e_form->setDefaults(array('homepage' => 'http://'));
