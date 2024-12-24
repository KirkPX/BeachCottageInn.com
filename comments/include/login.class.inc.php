<?php

/**
 * GentleSource Guestbook Script - login.class.inc.php
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */

require_once 'session.class.inc.php';




/**
 * Handle logins
 */
class g10e_login
{




    /**
     * Start login process
     *
     */
    function g10e_login($level = 1)
    {
        global $g10e;


        if ($level <= 0) {
            return true;
        }

        if ($this->status() == true) {
            $this->login_exists();
        } else {
            if (g10e_gpc_vars('d') == 'r') {
                $this->reset_form();
            } elseif (g10e_gpc_vars('c')) {
                $this->reset_password();
            } else {
                $this->login_starts();
            }
        }

        //Log user out
        if (g10e_gpc_vars('l') == 'o') {
            g10e_session::destroy();
            header('Location: ' . $g10e['logout_redirect'] . dirname($_SERVER['PHP_SELF']) . '/');
            exit;
        }

    }

// -----------------------------------------------------------------------------




    /**
     * Return login status
     *
     */
    function status()
    {
        if ($data = g10e_session::get()
                and isset($data['login_status'])
                and $data['login_status'] == true) {
            return true;
        }
    }

// -----------------------------------------------------------------------------




    /**
     * Manage existing login
     *
     */
    function login_exists()
    {
        return true;
    }

// -----------------------------------------------------------------------------




    /**
     * Start login
     *
     */
    function login_starts()
    {
        global $g10e;

        // Configuration
        $detail_template                = 'login.tpl.html';
        $g10e['alternative_template']    = 'admin';
        $message                        = array();

        // Includes
        require_once 'HTML/QuickForm.php';

        // Start output handling
        $out = new g10e_output($detail_template);

        // Start form field handling
        $form = new HTML_QuickForm('login', 'POST');
        require_once 'login_form.inc.php';


        // Validate form
        if ($form->validate()) {
            // Get login data
            if ($ser = g10e_setting::read('administration_login')) {
                $login_data = unserialize($ser['setting_value']);
                if ($g10e['alternative_password'] == true) {
                    $password = md5(strtolower($g10e['_post']['login_name']) . $g10e['_post']['password']);
                } else {
                    $password = md5($g10e['_post']['password']);
                }
                if (g10e_gpc_vars('login_name') == $login_data['login']
                        and $password == $login_data['password']) {
                    $login_data['login_status'] = true;
                    g10e_session::add($login_data);
                    header('Location: ' . $g10e['login_redirect'] . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $g10e['message'][] = $g10e['text']['txt_login_failed'];
                }
            }
        }


        require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($out->get_object, true);

        $form->accept($renderer);


        // Assign array with form data
        $out->assign('form', $renderer->toArray());


        // Output
        $out->finish();
        exit;

    }

// -----------------------------------------------------------------------------




    /**
     * Reset
     *
     */
    function reset_form()
    {
        global $g10e;

        // Configuration
        $detail_template                = 'reset.tpl.html';
        $message                        = array();
        $show_form                      = true;

        // Includes
        require_once 'HTML/QuickForm.php';

        // Start output handling
        $out = new g10e_output($detail_template);

        // Start form field handling
        $form = new HTML_QuickForm('login', 'POST');
        require_once 'reset_form.inc.php';


        // Validate form
        if ($form->validate()) {
            // Get login data
            if ($ser = g10e_setting::read('administration_login')) {
                $login_data = unserialize($ser['setting_value']);
                if (isset($g10e['_post']['login_name'])
                        and $g10e['_post']['login_name'] == $login_data['login']) {
                    if ($this->reset_mail() == true) {
                        $g10e['message'][] = $g10e['text']['txt_reset_mail_sent'];
                        $show_form = false;
                    } else {
                        $g10e['message'][] = $g10e['text']['txt_reset_mail_not_sent'];
                    }
                } else {
                    $g10e['message'][] = $g10e['text']['txt_login_name_not_exists'];
                }
            }

        }


        require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($out->get_object, true);

        $form->accept($renderer);


        // Assign array with form data
        $out->assign('form', $renderer->toArray());


        // Output
        $g10e['alternative_template'] = 'admin';
        $out->assign('show_form', $show_form);
        $out->finish();
        exit;

    }

// -----------------------------------------------------------------------------




    /**
     * Send reset mail
     *
     */
    function reset_mail()
    {
        global $g10e;

        // Create link
        $random = g10e_create_random(20);
        $part   = $g10e['mail_link'];
        $link[] = $part['protocol'];
        $link[] = $part['server'];
        $link[] = $part['path'];
        $link[] = '?c=' .  $random;

        // Add code to admin account
        if ($ser = g10e_setting::read('administration_login')) {
            $login_data = unserialize($ser['setting_value']);
            $arr = array(   'login'         => $login_data['login'],
                            'email'         => $login_data['email'],
                            'password'      => $login_data['password'],
                            'reset_code'    => $random
                            );
            $ser = serialize($arr);
            g10e_setting::write('administration_login', $ser);
        } else {
            return false;
        }


        // Send reset mail
        $detail_template                = 'reset.tpl.txt';
        $g10e['alternative_template']    = 'mail';

        // Start output handling
        $out = new g10e_output($detail_template);
        $out->assign('reset_link', join('', $link));
        $coutput = $out->finish_mail();

        // Send mail off
        include 'mail.class.inc.php';
        if (g10e_mail::send( $login_data['email'],
                            $g10e['text']['txt_reset_mail_subject'],
                            $coutput,
                            $g10e['mail_from'])) {
            return true;
        }

    }

// -----------------------------------------------------------------------------




    /**
     * Reset user password
     *
     */
    function reset_password()
    {
        global $g10e;

        // Configuration
        $detail_template                = 'reset_password.tpl.html';
        $g10e['alternative_template']    = 'admin';
        $message                        = array();

        // Includes
        require_once 'HTML/QuickForm.php';

        // Start output handling
        $out = new g10e_output($detail_template);

        // Start form field handling
        $form = new HTML_QuickForm('login', 'POST');
        require_once 'reset_password_form.inc.php';
        $form->setDefaults(array('c' => g10e_gpc_vars('c')));


        // Validate form
        $show_form = true;
        if ($form->validate()) {
            // Get login data
            if ($ser = g10e_setting::read('administration_login')) {
                // Change admin password
                if ($ser = g10e_setting::read('administration_login')) {
                    $login_data = unserialize($ser['setting_value']);
                    if (isset($login_data['reset_code'])
                            and $login_data['reset_code'] == $g10e['_post']['c']) {
                        $password = md5($login_data['login'] . $g10e['_post']['password']);
                        $arr = array(   'login'         => $login_data['login'],
                                        'email'         => $login_data['email'],
                                        'password'      => $password
                                        );
                        $ser = serialize($arr);
                        g10e_setting::write('administration_login', $ser);
                        $g10e['message'][] = $g10e['text']['txt_new_password_set'];
                        $show_form = false;
                    } else {
                        $g10e['message'][] = $g10e['text']['txt_reset_code_not_exists'];
                    }
                }
            }

        }


        require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($out->get_object, true);

        $form->accept($renderer);


        // Assign array with form data
        $out->assign('form', $renderer->toArray());


        // Output
        $out->assign(array('show_form' => $show_form));
        $out->finish();
        exit;

    }

// -----------------------------------------------------------------------------


} // End of class







?>
