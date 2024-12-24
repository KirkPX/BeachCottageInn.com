<?php /* Smarty version 2.6.13, created on 2010-07-20 08:13:50
         compiled from default/comment.tpl.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'call_module', 'default/comment.tpl.html', 136, false),)), $this); ?>
    
    <!-- Comment List Start ------------------------------------------------ -->
    <?php if ($this->_tpl_vars['show_comments'] == true): ?>
    <a name="g10e_top"></a>
    <h2 class="g10e_headline"><?php echo $this->_tpl_vars['txt_entry_headline']; ?>
 (<?php echo $this->_tpl_vars['result_number']; ?>
)</h2>  
    <?php if ($this->_tpl_vars['show_sign_link'] == true): ?>
      <a href="?d=sign"><?php echo $this->_tpl_vars['txt_sign_guestbook']; ?>
</a>
    <?php endif; ?>
    <div class="g10e_comment_list">
        <?php $_from = $this->_tpl_vars['comment_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
            <div class="g10e_comment_item_background">
            <div class="g10e_comment_item">
                <div class="g10e_comment_item_text"><?php echo $this->_tpl_vars['item']['frontend_text']; ?>
</div>
                <div class="g10e_comment_item_details"> 
                    # <?php echo $this->_tpl_vars['item']['comment_number']; ?>
 -
                    <?php if ($this->_tpl_vars['item']['comment_author_homepage'] == '' || $this->_tpl_vars['item']['comment_author_homepage'] == 'http://'): ?>
                      <?php echo $this->_tpl_vars['item']['comment_author_name']; ?>

                    <?php else: ?>
                      <a href="<?php echo $this->_tpl_vars['item']['comment_author_homepage']; ?>
"><?php echo $this->_tpl_vars['item']['comment_author_name']; ?>
</a> -
                    <?php endif; ?> 
                    <?php echo $this->_tpl_vars['item']['comment_date']; ?>
 - 
                    <?php echo $this->_tpl_vars['item']['comment_time']; ?>
 -
                    <?php echo $this->_tpl_vars['txt_city']; ?>
: <?php echo $this->_tpl_vars['item']['comment_author_city']; ?>
 -  
                    <?php echo $this->_tpl_vars['txt_state']; ?>
: <?php echo $this->_tpl_vars['item']['comment_author_state']; ?>
 -
                    <?php echo $this->_tpl_vars['txt_country']; ?>
: <?php echo $this->_tpl_vars['item']['comment_author_country']; ?>
 
                </div>
            </div>
            </div>
        <?php endforeach; endif; unset($_from); ?>
    </div>
    <?php endif; ?>
    <!-- Comment List End  ------------------------------------------------- -->

    
    
    
    <!-- Pagination Form Start --------------------------------------------- -->
    <?php if ($this->_tpl_vars['show_comments'] == true && $this->_tpl_vars['comment_list'] && $this->_tpl_vars['display_pagination']): ?>
    <div class="g10e_frontend_pagination">
      <?php if ($this->_tpl_vars['browse_previous'] == 2): ?>
        <a href="<?php echo $this->_tpl_vars['start_page_url']; ?>
"><?php echo $this->_tpl_vars['txt_start']; ?>
</a>
        <a href="<?php echo $this->_tpl_vars['previous_page_url']; ?>
">&laquo;&nbsp;<?php echo $this->_tpl_vars['txt_previous_page']; ?>
</a>
      <?php else: ?>
        <span><?php echo $this->_tpl_vars['txt_start']; ?>
</span>
        <span>&laquo;&nbsp;<?php echo $this->_tpl_vars['txt_previous_page']; ?>
</span>
      <?php endif; ?>
      
      <?php if ($this->_tpl_vars['browse_next'] == 2): ?>
        <a href="<?php echo $this->_tpl_vars['next_page_url']; ?>
"><?php echo $this->_tpl_vars['txt_next_page']; ?>
&nbsp;&raquo;</a>
        <a href="<?php echo $this->_tpl_vars['end_page_url']; ?>
"><?php echo $this->_tpl_vars['txt_end']; ?>
</a>
      <?php else: ?>
        <span><?php echo $this->_tpl_vars['txt_next_page']; ?>
&nbsp;&raquo;</span>
        <span><?php echo $this->_tpl_vars['txt_end']; ?>
</span>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <!-- Pagination Form End   --------------------------------------------- -->
    
    
    
    
    


    <!-- Comment Form Start ------------------------------------------------ -->
      <?php if ($this->_tpl_vars['show_form'] == true): ?>
        <a name="g10e_form"></a>
        <h2 class="g10e_headline"><?php echo $this->_tpl_vars['txt_sign_guestbook']; ?>
</h2>
        <?php if ($this->_tpl_vars['show_list_link'] == true): ?>
          <a href="?d=list"><?php echo $this->_tpl_vars['txt_guestbook_entries']; ?>
</a>
        <?php endif; ?>
        
        <div class="g10e_comment_form_background">
        <form <?php echo $this->_tpl_vars['form']['attributes']; ?>
>
        <?php echo $this->_tpl_vars['form']['hidden']; ?>

          <table class="g10e_comment_form_table">

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

            <?php if ($this->_tpl_vars['active_form'] == true): ?>
            <tr>
              <td class="g10e_comment_form_label">
                <?php if ($this->_tpl_vars['form']['name']['error']): ?> <span class="g10e_error_message"><?php echo $this->_tpl_vars['form']['name']['error']; ?>
</span> <?php else: ?> <?php echo $this->_tpl_vars['form']['name']['label']; ?>
 <?php endif; ?>  
              </td>
              <td class="g10e_comment_form_field">
                <?php echo $this->_tpl_vars['form']['name']['html']; ?>

              </td>
            </tr>

            <tr>
              <td class="g10e_comment_form_label">
                <?php if ($this->_tpl_vars['form']['email']['error']): ?> <span class="g10e_error_message"><?php echo $this->_tpl_vars['form']['email']['error']; ?>
</span> <?php else: ?> <?php echo $this->_tpl_vars['form']['email']['label']; ?>
 <?php endif; ?>
              </td>
              <td class="g10e_comment_form_field">
                <?php echo $this->_tpl_vars['form']['email']['html']; ?>

              </td>
            </tr>

            <tr>
              <td class="g10e_comment_form_label"><?php echo $this->_tpl_vars['form']['homepage']['label']; ?>
</td>
              <td class="g10e_comment_form_field">
                <?php echo $this->_tpl_vars['form']['homepage']['html']; ?>

              </td>
            </tr>

            <tr>
              <td class="g10e_comment_form_label"><?php echo $this->_tpl_vars['form']['city']['label']; ?>
</td>
              <td class="g10e_comment_form_field">
                <?php echo $this->_tpl_vars['form']['city']['html']; ?>

              </td>
            </tr>

            <tr>
              <td class="g10e_comment_form_label"><?php echo $this->_tpl_vars['form']['state']['label']; ?>
</td>
              <td class="g10e_comment_form_field">
                <?php echo $this->_tpl_vars['form']['state']['html']; ?>

              </td>
            </tr>

            <tr>
              <td class="g10e_comment_form_label"><?php echo $this->_tpl_vars['form']['country']['label']; ?>
</td>
              <td class="g10e_comment_form_field">
                <?php echo $this->_tpl_vars['form']['country']['html']; ?>

              </td>
            </tr>

            <tr>
              <td></td>
              <td class="g10e_comment_form_field">
                <?php echo g10e_module::call_module_output(array('trigger' => 'frontend_textarea','field' => $this->_tpl_vars['comment_field_name']), $this);?>
                
              </td>
            </tr>
            
            <tr>
              <td class="g10e_comment_form_label">
                <?php if ($this->_tpl_vars['form'][$this->_tpl_vars['comment_field_name']]['error']): ?> <span class="g10e_error_message"><?php echo $this->_tpl_vars['form'][$this->_tpl_vars['comment_field_name']]['error']; ?>
</span> <?php else: ?> <?php echo $this->_tpl_vars['form'][$this->_tpl_vars['comment_field_name']]['label']; ?>
 <?php endif; ?>
              </td>
              <td class="g10e_comment_form_field">
                <?php echo $this->_tpl_vars['form'][$this->_tpl_vars['comment_field_name']]['html']; ?>
                
              </td>
            </tr>
            
            <tr>
              <td></td>
              <td class="g10e_comment_form_field">
                <?php echo g10e_module::call_module_output(array('trigger' => 'frontend_comment_form'), $this);?>

              </td>
            </tr>


            <tr>
              <td class="g10e_comment_form_label"></td>
              <td class="g10e_comment_form_submit"><?php echo $this->_tpl_vars['form']['save']['html']; ?>
</td>
            </tr>

            <?php endif; ?>
          </table>
            
        </form>
        </div>
      <?php endif; ?>
    <!-- Comment Form End   ------------------------------------------------ -->
    
    <br />
    <?php echo g10e_module::call_module_output(array('trigger' => 'frontend_content_footer','data' => ($this->_tpl_vars['page_data'])), $this);?>

    
    
    
    