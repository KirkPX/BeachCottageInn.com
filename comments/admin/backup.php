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

$g10e_detail_template    = 'backup.tpl.html';

// -----------------------------------------------------------------------------




// Include
require G10E_ROOT . 'include/core.inc.php';

// Start output handling
$out = new g10e_output($g10e_detail_template);

// -----------------------------------------------------------------------------




require 'backup.class.inc.php';
$backup = new g10e_backup;


// Export database into file
if (g10e_gpc_vars('do') == 'ex') {
    if (false == $g10e['demo_mode']) {
        if ($backup->export()) {
            header('Location: ' . $g10e['server_protocol'] . $g10e['server_name'] . dirname($_SERVER['PHP_SELF']) . '/backup.php?e=s');
            exit;
        }
    } else {
        $g10e['message'][] = $g10e['text']['txt_disabled_in_demo_mode'];
    }
}
if (g10e_gpc_vars('e') == 's') {
    $g10e['message'][] = $g10e['text']['txt_export_successful'];
}

// -----------------------------------------------------------------------------




// Delete backup file
if ($file = g10e_gpc_vars('f')
        and g10e_gpc_vars('do') == 'de') {
    $delete_confirmation = array(
                            'dialogue'      => 1,
                            'file' => $file
                            );
    $out->assign('delete_confirmation', $delete_confirmation);
}
if (g10e_gpc_vars('f')
        and g10e_gpc_vars('do') == 'dec') {
    if (false == $g10e['demo_mode']) {
        if ($backup->delete($file)) {
            $g10e['message'][] = $g10e['text']['txt_delete_file_successful'];
        } else {
            $g10e['message'][] = $g10e['text']['txt_delete_file_failed'];
        }
    } else {
        $g10e['message'][] = $g10e['text']['txt_disabled_in_demo_mode'];
    }
}

// -----------------------------------------------------------------------------




// Import backup file
if ($file = g10e_gpc_vars('f')
        and g10e_gpc_vars('do') == 'im') {
    $import_confirmation = array(
                            'dialogue'      => 1,
                            'file'          => $file
                            );
    $out->assign('import_confirmation', $import_confirmation);
}
if (g10e_gpc_vars('f')
        and g10e_gpc_vars('do') == 'imc') {
    if (false == $g10e['demo_mode']) {
        if ($backup->import($file)) {
            header('Location: ' . $g10e['server_protocol'] . $g10e['server_name'] . dirname($_SERVER['PHP_SELF']) . '/backup.php?i=s');
            exit;
        } else {
            $g10e['message'][] = $g10e['text']['txt_import_failed'];
        }
    } else {
        $g10e['message'][] = $g10e['text']['txt_disabled_in_demo_mode'];
    }
}
if (g10e_gpc_vars('i') == 's') {
    $g10e['message'][] = $g10e['text']['txt_import_successful'];
}

// -----------------------------------------------------------------------------




// Download backup file
if ($file = g10e_gpc_vars('f')
        and g10e_gpc_vars('do') == 'dl') {
    $domain_hash = '_download';
    if ($g10e['multi_domain'] == true
                and gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
        $domain_hash = gentlesource_module_multi_domain::domain_hash($sub_domain = true);
            $domain_hash .= '_download_';
    }
    require_once 'download.class.inc.php';
    if (is_file(G10E_ROOT . $g10e['backup_directory'] . $domain_hash . $file)){
        g10e_download::send(G10E_ROOT . $g10e['backup_directory'] . $domain_hash . $file, '_' . $file);
    }
}

// -----------------------------------------------------------------------------




// Manually remove /backup/.htaccess and /backup/ (safe mode, uid etc.)
if (g10e_gpc_vars('g10e_dbf')) {
    $delete_backup_folder = true;
    if (!is_dir(G10E_ROOT . $g10e['backup_directory'])) {
        $delete_backup_folder = false;
    }
    if ($delete_backup_folder and is_file(G10E_ROOT . $g10e['backup_directory'] . '.htaccess')) {
        unlink(G10E_ROOT . $g10e['backup_directory'] . '.htaccess');
    }
    if ($delete_backup_folder) {
        rmdir(G10E_ROOT . $g10e['backup_directory']);
    }
}

// -----------------------------------------------------------------------------




// List available backup files
$out->assign('backup_files', $backup->file_list());

// -----------------------------------------------------------------------------




// Output
$out->finish();






?>
