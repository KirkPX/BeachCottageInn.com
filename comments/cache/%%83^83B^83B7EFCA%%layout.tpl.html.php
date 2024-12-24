<?php /* Smarty version 2.6.13, created on 2010-07-19 23:32:00
         compiled from admin/layout.tpl.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'call_module', 'admin/layout.tpl.html', 61, false),)), $this); ?>
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
 <?php echo $this->_tpl_vars['txt_admin_area']; ?>
</title>
    
    <?php echo '
    <style type="text/css">
    <!--
        @import url("../template/default/style.css");
        @import url("../template/admin/style.css");
    -->
    </style>    
    <script type="text/javascript">
        <!--
    '; ?>

    
            var g10e_sure_delete_comment = '<?php echo $this->_tpl_vars['txt_sure_delete_entry']; ?>
';
            
            
    <?php echo '
        
        
        function display(blockid) {
            if (document.getElementById(blockid).style.display==\'block\'){
                document.getElementById(blockid).style.display=\'none\';
            } else {
                document.getElementById(blockid).style.display=\'block\';
            }
        }  
    
        function delete_comment(g10e_url) {
            var answer = confirm(g10e_sure_delete_comment)
            if (answer){
                window.location = g10e_url;
                return false;
            }
            return answer;
        }
        
        function check_all(name, ctl) {
            for (i = 0; i < name.length; i++)
            {
                if (name[i].type == \'checkbox\') {
                    name[i].checked = ctl.checked? true:false
                }
            }
        }
        
        //-->
    </script>
    
    '; ?>

    
    <script language="javascript" src="../template/javascript/ColorPicker2.js"></script>
    
    <?php echo g10e_module::call_module_output(array('trigger' => 'backend_source_head'), $this);?>

    
  </head>
  <body>  
    
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
  
    
    <?php if ($this->_tpl_vars['login_status']): ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/navigation.tpl.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>
    
  <div class="g10e_admin_content_background">
  <div class="g10e_admin_content">
  <!-- Language selection (link list or dropdown menu) -->
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'default/language.tpl.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['detail_template'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    
    
  </div>
  </div>
  
  
    
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