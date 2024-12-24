<?php

/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


$form->addElement('select', 'search_field', $g10e['text']['txt_search_column'], $search_field_list);
$form->addElement('text', 'search_query', $g10e['text']['txt_search_text']);
$form->addElement('submit', 'search', $g10e['text']['txt_search']);
$form->addElement('submit', 'search_delete', $g10e['text']['txt_delete_search']);









?>
