<?php if (ENVIRONMENT != 'demo') : ?>
<p>To see this site in action before you register, see the <a href="http://demo.studentspreadsheet.com/">demo</a>.</p>
<?php endif; ?>

<?php echo form_open('register',array('id'=>'register_form', 'class'=>'form-horizontal')); ?>


<div class="control-group">
  <label class="control-label" for="housename">House Name</label>
  <div class="controls">
    <div class="input-prepend">
      <span class="add-on"><i class="icon-home"></i></span>
      <input class="span3" id="housename" name="housename" type="text" placeholder="eg. 10 Downing Street">
    </div>
  </div>
</div>


<div class="control-group">
  <label class="control-label" for="register-select">Friends</label>
  <div class="controls">
      <select multiple="" class="span3" placeholder="Select Friends" name="register[]" id="register-select">
<?php foreach ($fb_friends as $fb_friend_id => $fb_friend_name) : ?>
        <option value="<?php echo $fb_friend_id; ?>"><?php echo $fb_friend_name; ?></option>
<?php endforeach; ?>
      </select>
  </div>
</div>

  <div class="control-group">
    <div class="controls">
      <input class="btn btn-primary" type="submit" value="Create House" />
    </div>
  </div>

  <p>If you have any issues or questions about registering, please <a href="<?php echo site_url('help/contact'); ?>">get in touch</a>!</p>



</form>
