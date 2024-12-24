<?php /* Smarty version 2.6.13, created on 2010-07-20 08:13:50
         compiled from default/layout.tpl.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'call_module', 'default/layout.tpl.html', 40, false),)), $this); ?>
<?php echo '<?xml'; ?>
 version="1.0"<?php echo '?>'; ?>

<!doctype html public "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $this->_tpl_vars['txt_content_direction']; ?>
">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->_tpl_vars['txt_charset']; ?>
" />
    <title><?php echo $this->_tpl_vars['txt_guestbook_script']; ?>
</title>
        
    <style type="text/css">
    <!--
        @import url("<?php echo $this->_tpl_vars['script_url']; ?>
template/default/style.css");
    -->
    </style>
    
    
    
  </head>
  <body>
    
  <!-- Language selection (link list or dropdown menu) -->
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'default/language.tpl.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    

    <?php $_from = $this->_tpl_vars['system_messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <div class="g10e_system">
      <div class="g10e_index"><?php echo $this->_tpl_vars['item']['title']; ?>
</div>
      <div class="g10e_code"><br /><?php echo $this->_tpl_vars['item']['message']; ?>
</div>
    </div>
    <?php endforeach; endif; unset($_from); ?>

    <?php $_from = $this->_tpl_vars['error_messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <div class="g10e_system">
      <div class="g10e_index"><?php echo $this->_tpl_vars['item']['title']; ?>
</div>
      <div class="g10e_code"><br /><?php echo $this->_tpl_vars['item']['message']; ?>
</div>
    </div>
    <?php endforeach; endif; unset($_from); ?>
    
    <?php echo g10e_module::call_module_output(array('trigger' => 'frontend_page_header'), $this);?>

  
  
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['detail_template'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>    
    
    
    <?php echo g10e_module::call_module_output(array('trigger' => 'frontend_page_footer'), $this);?>

    
    
    
    
    <?php $_from = $this->_tpl_vars['debug_messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <div class="g10e_system">
      <div class="g10e_index"><?php echo $this->_tpl_vars['item']['title']; ?>
</div>
      <div class="g10e_code"><br /><?php echo $this->_tpl_vars['item']['message']; ?>
</div>
    </div>
    <?php endforeach; endif; unset($_from); ?>
  </body>  
</html>