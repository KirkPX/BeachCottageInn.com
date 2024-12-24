<?php

/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */




// Settings
define('G10E_ROOT', '../');
define('G10E_ALTERNATIVE_TEMPLATE', 'admin');
define('G10E_LOGIN_LEVEL', 1);

$g10e_detail_template    = 'configuration.tpl.html';

// -----------------------------------------------------------------------------




// Include
require G10E_ROOT . 'include/core.inc.php';

// Start output handling
$out = new g10e_output($g10e_detail_template);


$out->assign('module_list', g10e_module::module_list());

// -----------------------------------------------------------------------------




// Output
$out->assign('display_setting_navigation', true);
$out->finish();






?>
