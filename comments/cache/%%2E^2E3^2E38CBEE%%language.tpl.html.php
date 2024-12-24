<?php /* Smarty version 2.6.13, created on 2010-07-19 23:32:00
         compiled from default/language.tpl.html */ ?>

<?php if ($this->_tpl_vars['display_language_selection'] == 'Y'): ?>
    <div id="g10e_language_selector">
        <?php echo $this->_tpl_vars['txt_select_language']; ?>

        
        <?php if ($this->_tpl_vars['language_selector_mode'] == 'links'): ?>
            <?php $_from = $this->_tpl_vars['available_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
              <a href="<?php echo $this->_tpl_vars['script_url']; ?>
?g10e_language_selector=<?php echo $this->_tpl_vars['key']; ?>
&amp;r=<?php echo $this->_tpl_vars['page_url_encoded']; ?>
"><?php echo $this->_tpl_vars['item']; ?>
</a>
            <?php endforeach; endif; unset($_from); ?>
        <?php endif; ?>
        
        
        <?php if ($this->_tpl_vars['language_selector_mode'] == 'form'): ?>
            <form action="<?php echo $this->_tpl_vars['script_url']; ?>
" method="post">
            <input type="hidden" name="r" value="<?php echo $this->_tpl_vars['page_url_encoded']; ?>
" />
            <select name="g10e_language_selector">
            <?php $_from = $this->_tpl_vars['available_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
              <option value="<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['item']; ?>
</a>
            <?php endforeach; endif; unset($_from); ?>
            </select>
            <input type="submit" name="change_language" value="<?php echo $this->_tpl_vars['txt_go']; ?>
" />
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>