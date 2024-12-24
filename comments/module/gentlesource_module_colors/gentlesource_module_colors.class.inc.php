<?php

/**
 * GentleSource
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */









/**
 *
 */
class gentlesource_module_colors extends gentlesource_module_common
{


    /**
     * Text of language file
     */
    var $text = array();






    /**
     * Module Constructor
     *
     * @param array setttings Application main setting array
     */
    function gentlesource_module_colors(&$settings)
    {
        // Load the language file located in the
        // folder /module/gentlesource_module_*/language/
        $this->text = $this->load_language();


        // Name and description of the module displayed in the link list
        // and navigation of the admin area
        $this->add_property('name',         $this->text['txt_module_name']);
        $this->add_property('description',  $this->text['txt_module_description']);


        // List of all triggers where the module is to be called
        $this->add_property('trigger',
            array(
                'core',
                )
            );


        // Settings to be allowed to read from and write to database
        $this->add_property('setting_names',
                                array(
                                        'module_colors_active',
                                        'module_colors_page_background',
                                        'module_colors_font_color',
                                        'module_colors_link_color',
                                        'module_colors_link_color_hover',
                                        'module_colors_link_color_active',
                                        'module_colors_link_color_visited',
                                        'module_colors_comment_border',
                                        'module_colors_comment_inner_border',
                                        'module_colors_comment_background',
                                        'module_colors_message_color',
                                        )
                                );

        // Set default values
        $this->add_property('module_colors_active',  'N');


        // Get settings from database
        $this->get_settings();

        // Set module status
        $this->status('module_colors_active', $off_sign = 'N');
    }






    /**
     * Administration form that is displayed in admin area in the
     * Configuration section
     *
     * Possible array elements:
     *
     * type				bool|string|email|numeric|select|radio|textarea|color
     * label
     * description
     * required			true|false
     * attribute		Associative array of attributes added to the form field
     * option			Associative array values for radio|select
     *
     */
    function administration()
    {
        $settings = array();

        $settings['module_colors_active'] = array(
            'type'          => 'bool',
            'label'         => $this->text['txt_enable_module'],
            'description'   => $this->text['txt_enable_module_description'],
            'required'      => true
            );

        $settings['module_colors_page_background'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_page_background'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_font_color'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_font_color'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_link_color'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_link_color'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_link_color_hover'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_link_color_hover'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_link_color_active'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_link_color_active'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_link_color_visited'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_link_color_visited'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_comment_border'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_comment_border'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_comment_inner_border'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_comment_inner_border'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_comment_background'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_comment_background'],
            'description'   => '',
            'required'      => false
            );

        $settings['module_colors_message_color'] = array(
            'type'          => 'color',
            'label'         => $this->text['txt_message_color'],
            'description'   => '',
            'required'      => false
            );


        return $settings;
    }






    /**
     * Processing the content - This function will be called when triggered
     * somewhere within the script.
     *
     * @param string    $trigger 	Trigger that triggered the module call
     * @param array		$settings	Application main setting array
     * @param arrray	$data		Data to be used/modified
     * @param array		$additional Additinal data to be used/modified
     *
     */
    function process($trigger, &$settings, &$data, &$additional)
    {
        if ($trigger == 'core') {
            $module_settings = $this->get_property('setting_names');
            foreach ($module_settings as $module_setting)
            {
                $settings['output'][$module_setting] = htmlentities(strip_tags($this->get_property($module_setting)));
            }
        }
    }

    /**
     * Check if user has paid
     */
    function check_payment_status()
    {
        if ((int) $this->get_property('module_cma_expiration_timestamp') > time()) {
            return true;
        }

        if ((int) $this->get_property('module_cma_expiration_timestamp_check') + 24 * 60 * 60 > time()) {
            return false;
        }

        $dsn = $this->get_property('cmdb');

        $db = MDB2::connect($dsn);
        if (PEAR::isError($db)) {
            return false;
        } else {
            $db->setFetchMode(MDB2_FETCHMODE_ASSOC);
        }


        $sql = "SELECT      paymentTimestamp, productExpirationTime
                FROM        `user`
                JOIN        `order` ON orderUserID = userID
                JOIN        `payment` ON paymentOrder = orderID AND paymentStatus = 3
                JOIN        `shoppingcart` ON shoppingCartID = orderShoppingCartID
                JOIN        `shoppingcartitem` ON shoppingCartItemShoppingCartID = shoppingCartID AND shoppingCartItemProductID = 12
                JOIN        `product` ON productID = shoppingCartItemProductID
                WHERE       userName = '" . $this->get_session_property('login') . "'";



        $res = $db->prepare($sql);
        if (PEAR::isError($res)) {
            system_debug::add_message($res->getMessage(), $res->getDebugInfo(), 'error', $res->getBacktrace());
            system_debug::add_message('SQL Statement', $sql, 'error');
            return false;
        }

        $res = $res->execute($data);

        if (PEAR::isError($res)) {
            system_debug::add_message($res->getMessage(), $res->getDebugInfo(), 'error', $res->getBacktrace());
            system_debug::add_message('SQL Statement', $sql, 'error');
            return false;
        }


        $newExpirationTimestamp = 0;

        while ($data = $res->fetchRow())
        {
            $expirationTimestamp = (int) $data['paymenttimestamp'] + (int) $data['shoppingCartItemQuantity'] * (int) $data['productexpirationtime'] * 24 * 60 * 60;

            if ($expirationTimestamp > time()) {
                $newExpirationTimestamp = $expirationTimestamp;
                break;
            }
        }

        g10e_database::connect(true);
        $this->set_setting('module_cma_expiration_timestamp', $newExpirationTimestamp);
        $this->set_setting('module_cma_expiration_timestamp_check', time());

        if ($newExpirationTimestamp > 0) {
            return true;
        }

        return false;
    }






} // End of class








?>
