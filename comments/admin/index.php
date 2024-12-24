<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */




// Settings
define('G10E_ROOT', '../');

$g10e_detail_template                = 'admin_start.tpl.html';

define('G10E_ALTERNATIVE_TEMPLATE', 'admin');
define('G10E_LOGIN_LEVEL', 1);


// Include
require G10E_ROOT . 'include/core.inc.php';






// Start output handling
$out = new g10e_output($g10e_detail_template);


if (g10e_gpc_vars('utf8encode') == 1) {

    $paths = array(
                '../language/',
                '../module/',
                );

    while (list($key, $val) = each($paths))
    {
        if (is_dir($val)) {
            if ($handle = opendir($val)) {
                while (false !== ($file = readdir($handle)))
                {
                    if (strpos($file, '.') === 0) {
                        continue;
                    }
                    if (strpos($file, 'gentlesource_module') !== false) {
                        $paths[] = $val . $file . '/language/';
                    }
                    if (strpos($file, 'language.') === false) {
                        continue;
                    }
                    if (!is_dir($val . 'utf-8/')) {
                        mkdir($val . 'utf-8');
                    }
                    $content = preg_replace("/'txt_charset'(.*?)=> '(.*?)'/", "'txt_charset' => 'utf-8'", file_get_contents($val . $file));
                    file_put_contents($val . 'utf-8/' . $file, g10e_utf8_encode($content, g10e_get_language_file_charset($val . $file)));
                    echo nl2br("$val$file\n");
                }
                closedir($handle);
            }
        }
    }
}


// Output
$out->finish();






?>
