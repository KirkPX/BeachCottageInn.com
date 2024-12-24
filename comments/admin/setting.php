<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */



define('G10E_ROOT', '../');



// Settings
$g10e_detail_template                = 'setting.tpl.html';

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
require 'setting_form.inc.php';


// Validate form
$message = array();
if ($form->validate()) {

    // Write data as settings
    if (false == $g10e['demo_mode']) {
        foreach ($g10e['_post'] AS $name => $value)
        {
            if (!in_array($name, $g10e['setting_names'])) {
                continue;
            }
            if ($g10e['hosted_mode'] == true
                    and !in_array($name, $g10e['hosted_mode_allowed_settings'])) {
                continue;
            }
            g10e_setting::write($name, $value);
        }
        $g10e['message'][] = $g10e['text']['txt_update_data_successful'];
    } else {
        $g10e['message'][] = $g10e['text']['txt_disabled_in_demo_mode'];
    }
}


// Write or delete .htaccess file for mod_rewrite URL handling
if (false == $g10e['demo_mode']
        and false == $g10e['hosted_mode']
        and isset($g10e['_post']['url_handling'])
        and $g10e['_post']['url_handling'] == 'modrewrite'
        and !is_file(G10E_ROOT . '.htaccess')) {
    require_once 'installation.class.inc.php';
    $content = "Options +FollowSymLinks
RewriteEngine on
RewriteBase " . str_replace('//', '/', str_replace('\\', '/', str_replace('admin', '', dirname($_SERVER['PHP_SELF']))) . '/') . "

RewriteCond %{REQUEST_URI} page(.*)\.html$
RewriteRule (.*)$ index.php?url=$1 [L]
";
    file_put_contents(G10E_ROOT . '.htaccess', $content);
}
if (false == $g10e['demo_mode']
        and false == $g10e['hosted_mode']
        and isset($g10e['_post']['url_handling'])
        and $g10e['_post']['url_handling'] != 'modrewrite'
        and is_file(G10E_ROOT . '.htaccess')) {
    unlink(G10E_ROOT . '.htaccess');
}


// Get setting data
$settings = g10e_setting::read_all();
$input_data = array_merge($g10e, $settings);
$form->setDefaults($input_data);


require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($out->get_object, true);
$form->accept($renderer);


// Assign array with form data
$out->assign('form', $renderer->toArray());


// Current script server path
if (false == $g10e['demo_mode']) {
    $script_server_path = str_replace('admin', '', str_replace('\\', '/', getenv('DOCUMENT_ROOT') . dirname($_SERVER['PHP_SELF'])));
} else {
    $script_server_path = '/example/path/to/comment/script/';
}
$out->assign('script_server_path', $script_server_path);

// -----------------------------------------------------------------------------



// Module list
$out->assign('module_list', g10e_module::module_list());

// -----------------------------------------------------------------------------


// Output
$out->assign('display_setting_navigation', true);
$out->finish();






?>
