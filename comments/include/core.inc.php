<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 *
 * @todo Add @
 *
 */

  /*****************************************************
  **
  ** THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY
  ** OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
  ** LIMITED   TO  THE WARRANTIES  OF  MERCHANTABILITY,
  ** FITNESS    FOR    A    PARTICULAR    PURPOSE   AND
  ** NONINFRINGEMENT.  IN NO EVENT SHALL THE AUTHORS OR
  ** COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
  ** OR  OTHER  LIABILITY,  WHETHER  IN  AN  ACTION  OF
  ** CONTRACT,  TORT OR OTHERWISE, ARISING FROM, OUT OF
  ** OR  IN  CONNECTION WITH THE SOFTWARE OR THE USE OR
  ** OTHER DEALINGS IN THE SOFTWARE.
  **
  *****************************************************/


// Prevent hacking attempt
if (!defined('G10E_ROOT')) {
    die();
}


// Define path separator
if (!defined('PATH_SEPARATOR')) {
    if (substr(PHP_OS, 0, 3) == 'WIN') {
        define('PATH_SEPARATOR', ';');
    } else {
        define('PATH_SEPARATOR', ':');
    }
}


// Set include path
$g10e_include_path =
                G10E_ROOT . 'configuration'. PATH_SEPARATOR .
                G10E_ROOT . 'include'. PATH_SEPARATOR .
                G10E_ROOT . 'include/library'. PATH_SEPARATOR .
                './' . PATH_SEPARATOR .
                ini_get('include_path') . PATH_SEPARATOR;

if (function_exists('set_include_path')) {
    set_include_path($g10e_include_path);
} else {
    ini_set('include_path', $g10e_include_path);
}




// Include
require 'functions.inc.php';




// Clean input
$g10e = array();
g10e_unset_globals();

$g10e['_post']   = $_POST;
$g10e['_get']    = $_GET;
$g10e['_cookie'] = $_COOKIE;

array_walk($g10e['_get'],    'g10e_clean_input');
array_walk($g10e['_post'],   'g10e_clean_input');
array_walk($g10e['_cookie'], 'g10e_clean_input');




// Settings
$g10e['software']                = 'GentleSource Guestbook Script';
$g10e['version']                 = '2.1.0';
$g10e['login_status']            = false;
$g10e['alternative_template']    = defined('G10E_ALTERNATIVE_TEMPLATE') ? G10E_ALTERNATIVE_TEMPLATE : '';
$g10e['message']                 = array();
$g10e['module_additional']       = array();
$g10e['output']                  = array();




// Include
require 'system_debug.class.inc.php';
require 'query.class.inc.php';
require 'database.class.inc.php';
require 'setting.class.inc.php';
require 'time.class.inc.php';
require 'module.class.inc.php';
require 'modulecommon.class.inc.php';
require 'language.class.inc.php';
require 'output.class.inc.php';
require 'default.inc.php';
require 'comment.class.inc.php';




// Set path
$g10e['template_path']   = G10E_ROOT . $g10e['template_directory'];
$g10e['cache_path']      = G10E_ROOT . $g10e['cache_directory'];




/**
 * Database field - form field mapping
 * Key:   database field name
 * Value: form field name
 */
$g10e['mapping']['comment'] = array(
                                'comment_id'                => 'id',
                                'comment_author_name'       => 'name',
                                'comment_author_email'      => 'email',
                                'comment_author_homepage'   => 'homepage',
                                'comment_author_city'       => 'city',
                                'comment_author_state'      => 'state',
                                'comment_author_country'    => 'country',
                                'comment_text'              => 'comment',
                                );

$g10e['mapping']['domain'] = array(
                                'domain_name'   => 'domain_name',
                                'domain_hash'   => 'domain_hash',
                                );

$g10e['mapping']['setting'] = array(
                                'setting_name'              => 'setting_name',
                                'setting_value'             => 'setting_value'
                                );




// Table fields to be inserted or updated in database
$g10e['db_fields']['comment'] = array(
                                'comment_id',
                                'comment_author_name',
                                'comment_author_email',
                                'comment_author_homepage',
                                'comment_author_city',
                                'comment_author_state',
                                'comment_author_country',
                                'comment_author_ip',
                                'comment_author_host',
                                'comment_author_user_agent',
                                'comment_text',
                                'comment_hash',
                                'comment_timestamp',
                                'comment_status',
                                'comment_domain_id',
                                );

$g10e['db_fields']['domain'] = array(
                                'domain_name',
                                'domain_hash',
                                );

$g10e['db_fields']['setting'] = array(
                                'setting_name',
                                'setting_value',
                                'setting_domain_id'
                                );




// Allowed form fields to be used for insert and update
$g10e['form_fields']['comment'] = array(
                                    'id',
                                    'name',
                                    'email',
                                    'homepage',
                                    'title',
                                    'comment'
                                    );

$g10e['form_fields']['domain'] = array(
                                    'domain_name',
                                    'domain_hash',
                                    );

$g10e['form_fields']['setting'] = array(
                                    'setting_name',
                                    'setting_value'
                                    );




// Setting names to be written and read
$g10e['setting_names'] = array(
    'database_version',
    'default_language',
    'script_url',
    'frontend_result_number',
    'frontend_order',
    'enable_moderation',
    'publish_delay',
    'display_turn_off_messages',
    'display_comments',
    'display_comment_form',
    'separate_comment_form',
    'url_handling',
    'use_utf8',
    'frontend_language',
    'display_language_selection',
    'comment_list_result_number'
    );

$g10e['hosted_mode_allowed_settings'] = array(
    'default_language',
    'frontend_result_number',
    'frontend_order',
    'enable_moderation',
    'publish_delay',
    'display_turn_off_messages',
    'display_comments',
    'display_comment_form',
    'separate_comment_form',
    'frontend_language',
    'display_language_selection',
    'comment_list_result_number',
    //'use_utf8',

    );

// -----------------------------------------------------------------------------




// Manage installation
include 'installation.class.inc.php';
$g10e_installation = new g10e_installation;
if ($g10e_installation->status() != true) {
    $g10e_installation->start();
}

// -----------------------------------------------------------------------------




// Database tables
require G10E_ROOT . 'dbconfig.php';
define('G10E_COMMENT_TABLE',    $g10e['database_table_prefix'] . 'comment');
define('G10E_DOMAIN_TABLE',     $g10e['database_table_prefix'] . 'domain');
define('G10E_SETTING_TABLE',    $g10e['database_table_prefix'] . 'setting');

$g10e['tables']['comment']  = G10E_COMMENT_TABLE;
$g10e['tables']['domain']   = G10E_DOMAIN_TABLE;
$g10e['tables']['setting']  = G10E_SETTING_TABLE;

// -----------------------------------------------------------------------------



// Multi domain handling
$g10e['multi_domain'] = false;
$g10e['domain_id'] = 0;
if (file_exists(G10E_ROOT . 'module/gentlesource_module_multi_domain/gentlesource_module_multi_domain.class.inc.php')) {
    require_once G10E_ROOT . 'module/gentlesource_module_multi_domain/gentlesource_module_multi_domain.class.inc.php';
    if ($domain_id = gentlesource_module_multi_domain::domain_id($sub_domain = true)) {
        $g10e['domain_id'] = (int) $domain_id;
    }
    $g10e['multi_domain'] = true;
}





// Get setting data
$g10e_global_settings = g10e_setting::read_all($domain_id = 0);
$g10e_settings = g10e_setting::read_all();
$g10e = array_merge($g10e, $g10e_global_settings, $g10e_settings);

// -----------------------------------------------------------------------------




if ($g10e['debug_mode'] == 'Y') {
    ini_set('error_reporting', E_ALL &~ E_DEPRECATED);
} else {
    ini_set('error_reporting', 0);
}

// -----------------------------------------------------------------------------

// Include language file
$g10e_language = $g10e['default_language'];
//if ($language = g10e_setting::read('default_language')) {
//    $g10e_language = $language['setting_value'];
//}
if (isset($frontend_language)) {
    $g10e_language = $g10e['frontend_language'];
//    if ($language = g10e_setting::read('frontend_language')) {
//        $g10e_language = $language['setting_value'];
//    }
}

$g10e['current_language']    = g10e_language::get($g10e_language);
$g10e['text']                = g10e_language::load($g10e['current_language']);


// -----------------------------------------------------------------------------




// Settings
$g10e['available_order'] = array(
    'ascending'     => $g10e['text']['txt_frontend_order_asending'],
    'descending'    => $g10e['text']['txt_frontend_order_desending']
    );

$g10e['comment_status'] = array(
    'approved'      => 0,
    'unapproved'    => 100,
    'spam'          => 200,
    );

$g10e['page_status'] = array(
    'active'        => 0,
    'deactivated'   => 100,
    );

$g10e['url_handling_types'] = array(
    'parameter'     => 'URL Parameter (/?url=page8.html)',
    'querystring'   => 'Query String (/?page8.html)',
    //'pathinfo'      => 'Path Info (index.php/EBHJU/)',
    'modrewrite'    => '.htaccess Mod Rewrite (/page8.html)'
    );

// -----------------------------------------------------------------------------


// Manage update
include 'update.class.inc.php';
$g10e_update = new g10e_update;
if ($g10e_update->status() != true) {
    $g10e_update->start();
}

// -----------------------------------------------------------------------------




// Prepare data for output
$g10e['output'] = array(
                    'software'                      => $g10e['software'],
                    'version'                       => $g10e['version'],
                    'demo_mode'                     => $g10e['demo_mode'],
                    'debug_mode'                    => $g10e['debug_mode'],
                    'hosted_mode'                   => $g10e['hosted_mode'],
                    'shut_down'                     => $g10e['shut_down'],
                    'display_shut_down_message'     => $g10e['display_shut_down_message'],
                    'shut_down_message'             => $g10e['shut_down_message'],
                    'script_url'                    => $g10e['script_url'],
                    'display_language_selection'    => $g10e['display_language_selection'],
                    'language_selector_mode'        => $g10e['language_selector_mode'],
                    'available_languages'           => $g10e['available_languages'],
                    'page_url_encoded'              => urlencode($g10e['server_protocol'] . $g10e['server_name'] . getenv('REQUEST_URI')),
                    'display_setting_navigation'    => false,
                    'comment_field_name'            => g10e_comment::field_name(),
                    );


// -----------------------------------------------------------------------------




g10e_module::call_module('core', $g10e['module_additional'], $g10e['module_additional']);

// -----------------------------------------------------------------------------




// Login
require 'login.class.inc.php';
$g10e_login = new g10e_login(G10E_LOGIN_LEVEL);
if ($g10e_login->status() == true) {
    $g10e['login_status'] = true;
}






?>
