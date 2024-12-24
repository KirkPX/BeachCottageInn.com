<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
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



$g10e['script_url']              = './';
$g10e['template_directory']      = 'template/';
$g10e['cache_directory']         = 'cache/';
$g10e['backup_directory']        = 'cache/backup/';
$g10e['default_template']        = 'default';
$g10e['global_template_file']    = 'layout.tpl.html';
$g10e['mail_template_file']      = 'layout.tpl.txt';
$g10e['time_difference']         = +0; // Time difference in minutes
$g10e['automatic_identifier']    = false; // false = off, true = on
$g10e['identifier_key']          = 'g10e'; // If automatic identifier ?g10e=ID provides identifier
$g10e['session_vars_name']       = 'G10E_SESS';
$g10e['cookie_path']             = '/';
$g10e['cookie_domain']           = '.' . $_SERVER['HTTP_HOST'];
$g10e['backup_file_prefix']      = 'database_backup_';
$g10e['server_protocol']         = 'http://';
$g10e['server_name']             = $_SERVER['HTTP_HOST'];
$g10e['login_redirect']          = $g10e['server_protocol'] . $g10e['server_name'];
$g10e['logout_redirect']         = $g10e['server_protocol'] . $g10e['server_name'];

$g10e['mail_link']                   = array('protocol'  => 'http://',
                                        'server'    => $_SERVER['SERVER_NAME'],
                                        'path'      => dirname($_SERVER['PHP_SELF']) . '/'
                                        );

$g10e['debug_mode']                  = 'N';
$g10e['demo_mode']                   = false;
$g10e['hosted_mode']                 = false;

$g10e['module_directory']            = 'module/';
$g10e['installed_modules']           = array(
                                        'gentlesource_module_akismet',
                                        'gentlesource_module_boilerplate',
                                        'gentlesource_module_captcha',
                                        'gentlesource_module_colors',
                                        'gentlesource_module_content_block',
                                        'gentlesource_module_content_replace',
                                        'gentlesource_module_dummy',
                                        'gentlesource_module_flood_protection',
                                        'gentlesource_module_ip_block',
                                        'gentlesource_module_keyword_highlighting',
                                        'gentlesource_module_nl2br',
                                        'gentlesource_module_referrer_log',
                                        'gentlesource_module_smiley',
                                        'gentlesource_module_social_links',
                                        'gentlesource_module_word_filter',
                                        'gentlesource_module_comment_mailer',
                                        );

$g10e['mail_type']                   = 'mail'; // (mail, smtp)
$g10e['mail_from']                   = 'postmaster@' . $_SERVER['SERVER_NAME'];
$g10e['smtp']['host']                = 'example.com';
$g10e['smtp']['port']                = 25;
$g10e['smtp']['helo']                = $_SERVER['SERVER_NAME'];
$g10e['smtp']['auth']                = false;
$g10e['smtp']['user']                = '';
$g10e['smtp']['pass']                = '';

$g10e['language_directory']          = 'language/';
$g10e['language_directory_utf8']     = 'language/utf-8/';
$g10e['use_utf8']                    = 'Y';
$g10e['language_cookie_name']        = 'G10E_LANG';
$g10e['default_language']            = 'en';
$g10e['frontend_language']           = 'en';
$g10e['display_language_selection']  = 'Y';
$g10e['language_selector_mode']      = 'links'; // links, form
$g10e['available_languages']         = array(
                                        'en' => 'English',
                                        'de' => 'German'
                                        );
$g10e['domain_language']             = array(
                                        'de'    => 'de',
                                        );


$g10e['shut_down']                   = 'N';
$g10e['display_shut_down_message']   = 'Y';
$g10e['shut_down_message']           = '';


$g10e['frontend_result_number']      = 5;
$g10e['frontend_order']              = 'descending';
$g10e['comment_list_result_number']  = 20;
$g10e['hostname_length']             = 20;
$g10e['user_agent_length']           = 30;
$g10e['cut_off_string']              = '&nbsp;...';
$g10e['enable_moderation']           = 'N';
$g10e['publish_delay']               = 0;
$g10e['display_turn_off_messages']   = 'Y';
$g10e['display_comments']            = 'Y';
$g10e['display_comment_form']        = 'Y';
$g10e['separate_comment_form']       = 'Y';
$g10e['url_handling']                = 'querystring'; // parameter, querystring, modrewrite
$g10e['output_ignore_tags']          = '';
$g10e['output_htmlentities']         = true;
$g10e['dynamic_comment_field_name']  = 'Y';
$g10e['include_url_active']          = 'N';
$g10e['include_url']                 = '';
$g10e['alternative_password']        = true;