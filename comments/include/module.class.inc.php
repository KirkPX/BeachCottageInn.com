<?php

/**
 * GentleSource Guestbook Script
 *
 * (C) Ralf Stadtaus http://www.gentlesource.com/
 */


/**
 * Manage modules
 *
 * Triggers:
 *
 * frontend_page_header
 * frontend_page_footer
 *
 * backend_source_head 		(include i.e. javascript into the HTML head)
 *
 * frontend_content			(content row after reading from database)
 * backend_content 			(content row after reading from database)
 *
 * frontend_textarea
 * backend_textarea
 *
 * frontend_save_comment
 * frontend_comment_form
 * backend_comment_control	(button/link list in comment list)
 *
 * module_demo
 *
 * standalone
 *
 */
class g10e_module
{




    /**
     * Call module on trigger
     *
     * Use $g10e['module_additional'] if no genuine $additional is at hand.
     *
     * @access public
     */
    function call_module($trigger, &$data, &$additional)
    {
        global $g10e;

        $module_container = g10e_module::container();
        reset($module_container);
        while (list($module, $instance) = each($module_container))
        {
            if (in_array($trigger, $instance->get_property('trigger'))
                    and $instance->get_property('module_active') == true) {
                $instance->process($trigger, $g10e, $data, $additional);
            }
        }
    }

// -----------------------------------------------------------------------------




    /**
     * Call module on trigger with output
     *
     * @access public
     */
    function call_module_output($parameter, &$smarty)
    {
        global $g10e;

        if (!isset($parameter['trigger'])) {
            return false;
        } else {
            $trigger = $parameter['trigger'];
        }

        $module_container = g10e_module::container();
        $output = array();
        reset($module_container);
        while (list($module, $instance) = each($module_container))
        {
            if (in_array($trigger, $instance->get_property('trigger'))
                    and $instance->get_property('module_active') == true) {
                $instance->process($trigger, $g10e, $parameter, $g10e['module_additional']);
                $output[] = $instance->get_output($trigger);
            }
        }
        return join('', $output);
    }

// -----------------------------------------------------------------------------




    /**
     * Create module instance
     *
     * @access public
     */
    function load_module($module)
    {
        global $g10e;

        if (!in_array($module, $g10e['installed_modules'])) {
            system_debug::add_message('Module Failure', 'Module is not listed in $g10e[\'installed_modules\']');
            return false;
        }

        $module_file =  G10E_ROOT .
                        $g10e['module_directory'] .
                        $module . '/' .
                        $module . '.class.inc.php';
        if (!is_file($module_file)) {
            system_debug::add_message('Module Failure', 'Module file not found in: ' . $module_file);
            return false;
        }

        require_once $module_file;

        if (!class_exists($module)) {
            system_debug::add_message('Module Failure', 'Module class "' . $module . '" does not exist.');
            return false;
        }

        $instance = new $module($g10e);
        return $instance;
    }

// -----------------------------------------------------------------------------




    /**
     * Re-instanciate modlue
     */
    function reload_module($module)
    {
        global $g10e;

        if (!in_array($module, $g10e['installed_modules'])) {
            system_debug::add_message('Module Failure', 'Module is not listed in $g10e[\'installed_modules\']');
            return false;
        }

        $instance = new $module($g10e);
        return $instance;
    }

// -----------------------------------------------------------------------------




    /**
     * Contains/creates module instances
     *
     */
    function &container($reload = false)
    {
        global $g10e;
        static $module_data = null;

        if ($reload == false and is_array($module_data)) {
            return $module_data;
        }

        $module_data = array();
        foreach ($g10e['installed_modules'] AS $module)
        {
            trim($module);

            if ($reload == true and $instance = g10e_module::reload_module($module)) {
                $module_data[$module] = $instance;
                continue;
            }

            if ($instance = g10e_module::load_module($module)) {
                $module_data[$module] = $instance;
            }
        }
        return $module_data;
    }

// -----------------------------------------------------------------------------




    /**
     * Administration module list
     *
     * @access public
     */
    function module_list()
    {
        $module_container = g10e_module::container();
        reset($module_container);
        $module_list = array();
        while (list($module, $instance) = each($module_container))
        {
            // Skip hidden modules or modules without administration
            if($instance->get_property('hidden') == true) {
                continue;
            }
            $module_list[] = array( 'name'          => $instance->get_property('name'),
                                    'description'   => $instance->get_property('description'),
                                    'module'    => $module
                                    );
        }
        return $module_list;
    }

// -----------------------------------------------------------------------------




    /**
     * Administration
     *
     * @access public
     */
    function administration($module)
    {
        $module_container = g10e_module::container();
        $admin = $module_container[trim($module)];
        if ($admin->get_property('hidden') == true) {
            return false;
        }
        $result = array(
                    'module_name'           => $module,
                    'module_title'          => $admin->get_property('name'),
                    'module_description'    => $admin->get_property('description'),
                    'module_form'           => g10e_module::administration_form($admin)
                    );
        return $result;
    }

// -----------------------------------------------------------------------------




    /**
     * Administration form
     *
     * $property elements:
     * - type
     * - label
     * - description
     * - required
     * - option (radio|select)
     * - attribute
     *
     * @access public
     */
    function administration_form(&$instance)
    {
        global $g10e;

        require_once 'HTML/QuickForm.php';

        $form       = new HTML_QuickForm('module_admin', 'POST');
        $settings   = $instance->administration($g10e);
        $additional = array();
        foreach ($settings AS $name => $property)
        {
            $skip = false;
            $add_html = '';
            switch ($property['type']) {
				case 'string':
					$form->addElement('text', $name, $property['label']);
					break;

				case 'email':
                    $form->addElement('text', $name, $property['label']);
                    $form->addRule($name, $g10e['text']['txt_syntax_email'], 'email');
					break;

				case 'numeric':
                    $form->addElement('text', $name, $property['label']);
                    $form->addRule($name, $g10e['text']['txt_syntax_numeric'], 'numeric');
					break;

				case 'bool':
                    $bool = array();
                    $bool[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_yes'], 'Y');
                    $bool[] = &HTML_QuickForm::createElement('radio', null, null, $g10e['text']['txt_no'], 'N');
                    $form->addGroup($bool, $name, $property['label'], ' &nbsp; ');
					break;

				case 'select':
                    $bool = array();
                    $select =& $form->addElement('select', $name, $property['label'], $property['option']);
                    if (!isset($property['size']) or $property['size'] == '' or !is_numeric($property['size'])) {
                        $select->setSize(1);
                    } else {
                        $select->setSize($property['size']);
                    }
					break;

				case 'radio':
                    $radio = array();
                    foreach ($property['option'] AS $value => $label)
                    {
                        $radio[] = &HTML_QuickForm::createElement('radio', null, null, $label, $value);
                    }
                    $form->addGroup($radio, $name, $property['label'], '<br />');
					break;

				case 'textarea':
                    $attribute = '';
                    if (isset($property['attribute'])) {
                        $attribute = $property['attribute'];
                    }
                    $form->addElement('textarea', $name, $property['label'], $attribute);

					break;

                case 'color':
                    $color_attribute = array(   'onfocus'   => 'style.backgroundColor = \'\'; style.color = \'\';',
                                                'onblur'    => 'style.backgroundColor = value; style.color = value;');
                    $form->addElement('text', $name, $property['label'], $color_attribute);
                    $add_html = '<script language="javascript">var cp_' . $name . ' = new ColorPicker();cp_' . $name . '.offsetX = 30; document.forms[0].' . $name . '.style.backgroundColor = document.forms[0].' . $name . '.value; document.forms[0].' . $name . '.style.color = document.forms[0].' . $name . '.value;</script><a href="#" onclick="cp_' . $name . '.select(document.forms[0].' . $name . ',\'pick\'); return false;" name="pick" id="pick"><img src="../template/admin/image/icon/color_picker.png" border="0" align="absmiddle" /></a><script language="javascript">cp_' . $name . '.writeDiv()</script>';
                    break;

				default:
                    $skip = true;
					break;
			}
            if ($skip == false) {
                $additional[] = array(  'description'   => $property['description'],
                                        'add_html'      => $add_html
                                        );
            }

            if ($property['required']) {
                $form->addRule($name, $g10e['text']['txt_error_required'], 'required');
            }
        }
        $form->addElement('submit', 'save', $g10e['text']['txt_save_settings']);
        $additional[] = array(  'description'   => '',
                                'add_html'      => '');
        $form->addElement('hidden', 'm', get_class($instance));
        $additional[] = array(  'description'   => '',
                                'add_html'      => '');

        // Validate form
        $message = array();
        if (isset($g10e['_post']['save'])
                and $form->validate()) {

            // Write data as settings
            if (false == $g10e['demo_mode']) {
                foreach ($g10e['_post'] AS $name => $value)
                {
                    if (!in_array($name, $instance->get_property('setting_names'))) {
                        continue;
                    }
                    g10e_setting::write($name, $value);
                    $instance->add_property($name, $value);
                }
                $message[]['message'] = $g10e['text']['txt_update_data_successful'];

                // Reload modules
                g10e_module::container(true);

            } else {
                $message[]['message'] = $g10e['text']['txt_disabled_in_demo_mode'];
            }
        }


        // Get setting data
        $settings = g10e_setting::read_all();
        $input_data = array_merge($instance->get_all_properties(), $settings);
        $form->setDefaults($input_data);


        $result = $form->toArray();
        $merged = array();
        foreach ($result['elements'] AS $key => $item)
        {
            $merged[] = array_merge($item, $additional[$key]);
        }
        $result['elements']             = $merged;
        $result['module_message']       = $message;
        $result['module_additional']    = $additional;
        return $result;
    }

// -----------------------------------------------------------------------------




} // End of class








?>
