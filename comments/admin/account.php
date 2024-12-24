<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */



define('G10E_ROOT', '../');



// Settings
$g10e_detail_template                = 'admin_account.tpl.html';

define('G10E_ALTERNATIVE_TEMPLATE', 'admin');
define('G10E_LOGIN_LEVEL', 1);


// Include
require G10E_ROOT . 'include/core.inc.php';



// Start output handling
$out = new g10e_output($g10e_detail_template);


// Handle and validate form
require_once 'HTML/QuickForm.php';


// Start form handler
$form = new HTML_QuickForm('account', 'POST');


// Get form configuration
require 'account_form.inc.php';


// Validate form
$show_form  = 'yes';
$message    = array();
if ($form->validate()) {
    $show_form = 'no';

    // Write data as settings
    if (false == $g10e['demo_mode']) {
        if ($g10e['alternative_password'] == true) {
            $password = md5(strtolower($g10e['_post']['login_name']) . $g10e['_post']['password']);
        } else {
            $password = md5($g10e['_post']['password']);
        }
        $arr = array(   'login'     => $g10e['_post']['login_name'],
                        'email'     => $g10e['_post']['email'],
                        'password'  => $password
                        );
        $ser = serialize($arr);
        g10e_setting::write('administration_login', $ser);
        $g10e['message'][] = $g10e['text']['txt_update_data_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_disabled_in_demo_mode'];
    }
} else {
    if (sizeof($g10e['_post']) > 0) {
        $g10e['message'][] = $g10e['text']['txt_fill_out_required'];
    }
}


// Get login data
$ser = g10e_setting::read('administration_login');
$login_data = unserialize($ser['setting_value']);
$input_data = array('login_name'    => $login_data['login'],
                    'email'         => $login_data['email']);
$form->setDefaults($input_data);



require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($out->get_object, true);

$form->accept($renderer);


// Assign array with form data
$out->assign('form', $renderer->toArray());




// Output
$out->assign('show_form', $show_form);
$out->assign('message', $message);
$out->finish();






?>
