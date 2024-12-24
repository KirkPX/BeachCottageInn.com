<?php

/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 * 
 * Version 1.1.0
 */




/**
 * Mail comments to admin
 */
class gentlesource_module_comment_mailer extends gentlesource_module_common
{


    /**
     * Text of language file
     */
    var $text = array();

// -----------------------------------------------------------------------------




    /**
     *  Setup
     * 
     * @access public
     */
    function gentlesource_module_comment_mailer()
    {
        $this->text = $this->load_language();
        
        // Configuration
        $this->add_property('name',         $this->text['txt_module_name']);
        $this->add_property('description',  $this->text['txt_module_description']);
        $this->add_property('trigger',  
                                array(  
                                        'frontend_save_content'
                                        )
                                );
        
        // Settings to be allowed to read from and write to database
        $this->add_property('setting_names',  
                                array(  
                                        'module_comment_mailer_active',
                                        'module_comment_mailer_recipient',
                                        'module_comment_mailer_thanks',
                                        )
                                );
        
        // Default values
        $this->add_property('module_comment_mailer_active',  'N');
        $this->add_property('module_comment_mailer_thanks',  'N');
        
        // Get settings from database
        $this->get_settings();

        // Set module status 
        $this->status('module_comment_mailer_active', 'N');
    }

// -----------------------------------------------------------------------------




    /**
     *  Administration
     * 
     * @access public
     */
    function administration()
    {
        $form = array();
        
        $form['module_comment_mailer_active'] = array(
            'type'          => 'bool',
            'label'         => $this->text['txt_enable_mailer'],
            'description'   => $this->text['txt_enable_mailer_description'],
            'required'      => true
            );
        
        $form['module_comment_mailer_recipient'] = array(
            'type'          => 'string',
            'label'         => $this->text['txt_recipient'],
            'description'   => $this->text['txt_recipient_description'],
            'required'      => false
            );
            
        $form['module_comment_mailer_thanks'] = array(
            'type'          => 'bool',
            'label'         => $this->text['txt_enable_mailer_thanks'],
            'description'   => $this->text['txt_enable_mailer_thanks_description'],
            'required'      => true
            );

        return $form;
    }

// -----------------------------------------------------------------------------




    /**
     * Processing the content
     * 
     * @access public
     */
    function process($trigger, &$settings, &$data, &$additional)
    {
        // Skip check if comment has already been blocked
        if ($additional['page_allow_comment'] == 'N') {
            return false;
        }
        if ($trigger == 'frontend_save_content') {
            $enhance = array(
                            'comment_date'      => date($settings['text']['txt_date_format'], $data['comment_timestamp']),
                            'comment_time'      => date($settings['text']['txt_time_format'], $data['comment_timestamp']),
                            );
            $this->notification($settings, array_merge($data, $additional, $enhance));

            // Send thank you mail
            if ($this->get_property('module_comment_mailer_thanks') == 'Y') {
                $this->notification_thanks($settings, array_merge($data, $additional, $enhance));
            }
        }
    }

// -----------------------------------------------------------------------------




    /**
     * Send notification mail
     * 
     */
    function notification(&$settings, &$data)
    {
        $recipient = $this->get_property('module_comment_mailer_recipient');
        if ($recipient == '') {
            return false;
        }
        $recipient_list = explode(',', $recipient);
        
        // Create link
        if (0 === strpos($settings['script_url'], 'http://')) {
            $link = array(  $settings['script_url'] . '/admin/');
        } else {            
            $link = array(  $settings['server_protocol'],
                            $settings['server_name'],
                            str_replace('//', '/', $settings['script_url'] . '/admin/')
                            );
        }

        // Start output handling
        $out = $this->get_output_object();
        $out->set_template_dir($this->get_property('module_path') . 'template/');     
        $out->assign($this->text);
        $out->assign($settings['text']);
        $out->assign('link', join('', $link));         
        $out->assign($data); 
        $mail_body = $out->fetch('notification.tpl.txt');
        $mail_body = strip_tags(stripslashes($mail_body));
        
        // Send mail off
        foreach ($recipient_list AS $address)
        {        
            $this->send_mail(   trim($address), 
                                $this->text['txt_notification_mail_subject'],                            
                                $mail_body, 
                                $settings['mail_from']);
        }
        
    }

// -----------------------------------------------------------------------------




    /**
     * Send notification mail to the comment poster
     * 
     */
    function notification_thanks(&$settings, &$data)
    {
        if (trim($data['email']) == '') {
            return false;
        }
        
        // Start output handling
        $out = $this->get_output_object();
        $out->set_template_dir($this->get_property('module_path') . 'template/');     
        $out->assign($this->text);
        $out->assign($settings['text']);         
        $out->assign($data); 
        $mail_body = $out->fetch('thankyou.tpl.txt');
        $mail_body = htmlentities(strip_tags(stripslashes($mail_body)));
     
        require_once 'mailinputvalidation.class.inc.php';   
        $recipient  = mail_input_validation::check(trim($data['email']));
        
        if (!mail_input_validation::email_syntax($recipient)) {
            return false;
        }
        
        // Send mail off
        $this->send_mail(   $recipient, 
                            $this->text['txt_thank_you_mail_subject'],                            
                            $mail_body, 
                            $settings['mail_from']);
        
    }

// -----------------------------------------------------------------------------




} // End of class








?>
