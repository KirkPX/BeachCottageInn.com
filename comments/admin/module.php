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

$g10e_detail_template    = 'module.tpl.html';

// -----------------------------------------------------------------------------




// Include
require G10E_ROOT . 'include/core.inc.php';

$data = array('module' => g10e_gpc_vars('m'));
g10e_module::call_module('module_send_file', $data, $g10e['module_additional']);


// Start output handling
$out = new g10e_output($g10e_detail_template);

// -----------------------------------------------------------------------------




if ($module = g10e_gpc_vars('m')) {
//    $out->assign('administration_form', g10e_module::administration($module));
    if ($module_result = g10e_module::administration($module)) {

//    g10e_print_a($module_result['module_form']);
        $out->assign('module_message',      $module_result['module_form']['module_message']);
        $out->assign('administration_form', array_merge($module_result['module_form']['elements']), $module_result['module_form']['module_additional']);
        $out->assign('form_attributes',     $module_result['module_form']['attributes']);
        $out->assign('module_title',        $module_result['module_title']);
        $out->assign('module_description',  $module_result['module_description']);
        $out->assign('module_name',         $module_result['module_name']);
        $out->assign('display_form',        true);
    }
}

// -----------------------------------------------------------------------------



$out->assign('module_list', g10e_module::module_list());

// -----------------------------------------------------------------------------




// Output
$out->assign('display_setting_navigation', true);
$out->finish();






?>
