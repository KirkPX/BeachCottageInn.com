<?php /* Smarty version 2.6.13, created on 2010-07-19 23:45:00
         compiled from admin/login.tpl.html */ ?>
    <div class="g10e_login_background">
    <img src="../template/admin/image/icon/login.png" width="64" height="64" align="right" style="margin:20px;" />
    <form <?php echo $this->_tpl_vars['form']['attributes']; ?>
>
    <table class="g10e_login">
      <tr>
        <td colspan="2" class="g10e_error_message">
          <?php $_from = $this->_tpl_vars['message']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
            <div class="g10e_error_message_item"><?php echo $this->_tpl_vars['item']; ?>
</div>
          <?php endforeach; endif; unset($_from); ?>              
        </td>
      </tr>
      <tr>
        <td valign="bottom"><?php echo $this->_tpl_vars['form']['login_name']['label']; ?>
</td>
        <td>
            <?php if ($this->_tpl_vars['form']['login_name']['error']): ?> <span class="g10e_error_message"><?php echo $this->_tpl_vars['form']['login_name']['error']; ?>
</span> <br /><?php endif; ?>
            <?php echo $this->_tpl_vars['form']['login_name']['html']; ?>
 
        </td>
      </tr>
      <tr>
        <td valign="bottom"><?php echo $this->_tpl_vars['form']['password']['label']; ?>
</td>
        <td>
            <?php if ($this->_tpl_vars['form']['password']['error']): ?> <span class="g10e_error_message"><?php echo $this->_tpl_vars['form']['password']['error']; ?>
</span> <br /><?php endif; ?>
            <?php echo $this->_tpl_vars['form']['password']['html']; ?>

        </td>
      </tr>
      <tr>
        <td></td>
        <td><?php echo $this->_tpl_vars['form']['login']['html']; ?>
 (<a href="./?d=r"><?php echo $this->_tpl_vars['txt_forget_password']; ?>
</a>)</td>
      </tr>
    </table>      
    </form>
    </div>