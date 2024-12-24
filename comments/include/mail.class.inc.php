<?php
 
/** 
 * GentleSource Guestbook Script
 * 
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


include 'htmlMimeMail.php';




/**
 * Send mails
 */
class g10e_mail
{




    /**
     * Send mails
     */    
    function send($to, $subject, $body, $from)
    {
        global $g10e;
        
        $mail = new htmlMimeMail();
        
        if ($g10e['mail_type'] == 'smtp') {
            $type = 'smtp';
            $smtp = $g10e['smtp'];
            $mail->setSMTPParams($smtp['host'], $smtp['port'], $smtp['helo'], $smtp['auth'], $smtp['user'], $smtp['pass']); 
        } else {
            $type = 'mail'; 
        }
        
        $mail->setFrom($from);
        $mail->setReturnPath($from);
        $mail->setSubject($subject); 
        $mail->setText($body);
        $result = $mail->send(array($to), $type);
        if ($result) {
            return true;
        } else {
            system_debug::add_message('Sending Mail Failed', join('<br />', $mail->errors), 'system');
        }
    }

    
//------------------------------------------------------------------------------





}








?>
