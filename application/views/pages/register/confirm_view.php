<?php echo form_open('register',array('id'=>'register_form', 'class'=>'form-horizontal')); ?>


  <div class="control-group">
    <label class="control-label" for="group-name">House Name</label>
    <div class="controls">
      <div class="input-prepend">
        <span class="add-on"><i class="icon-home"></i></span>
        <input id="group-name" type="hidden" value="">
      </div>
    </div>
  </div>


  <div class="control-group">
    <label class="control-label" for="register-select">Friends</label>
    <div class="controls">
<?php foreach ($fb_friends as $fb_friend_id => $fb_friend_name) : ?>
      <div class="media">
        <a class="pull-left" href="#">
          <img class="media-object" src="https://graph.facebook.com/<?php echo $fb_friend_id; ?>/picture?type=square">
        </a>
        <div class="media-body">
          <h4 class="media-heading"><?php echo $fb_friend_name; ?></h4>
          ...
        </div>
      </div>
<?php endforeach; ?>
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
      <input class="btn btn-primary" type="submit" value="Confirm" />
    </div>
  </div>



</form>
