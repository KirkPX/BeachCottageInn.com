<?php /* Smarty version 2.6.13, created on 2010-07-19 23:52:30
         compiled from admin/configuration.tpl.html */ ?>
<img src="../template/admin/image/icon/configuration_large.png" width="32" height="32" align="left" style="margin-right:20px;" />
    <h3><?php echo $this->_tpl_vars['txt_configuration']; ?>
</h3>



<table class="g10e_configuration_list">
<tr>
  <td class="g10e_configuration_list_label"><a href="./setting.php"><?php echo $this->_tpl_vars['txt_general_settings']; ?>
</a></td>
  <td></td>
</tr>
<?php $_from = $this->_tpl_vars['module_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<tr>
  <td class="g10e_configuration_list_label"><a href="./module.php?m=<?php echo $this->_tpl_vars['item']['module']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</a></td>
  <td><?php echo $this->_tpl_vars['item']['description']; ?>
</td>
</tr>
<?php endforeach; endif; unset($_from); ?>    
</table>
