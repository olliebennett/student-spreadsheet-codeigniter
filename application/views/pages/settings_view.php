<?php //d($user,'user'); ?>
<?php //d(isset($error) ? $error : 'no_error', 'error'); ?>
<?php //d($houses,'houses'); // array[house_id]['house_name']?>
<?php //d($housemates_all,'housemates_all'); // array[house_id][housemate_id]['user_name'] ?>

<?php echo form_open('settings', array('id'=>'purchase_form', 'class'=>'form-horizontal')); ?>

<?php if (isset($error)) : ?>
  <div class="control-group">
    <div class="controls">
<?php foreach ($error as $err_type => $err_str) : ?>
      <span class="help-block error"><?php echo $err_str; ?></span>
<?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

  <fieldset>

    <legend>House Settings</legend>

    <div class="control-group">
      <div class="controls">
        <span>Select your primary/current house from the list below<span id="savereminder-house" class="text-warning" style="display:none;"> (and click "Save Settings")</span>.</span>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="house">House</label>
      <div class="controls">
<?php foreach ($houses as $house_id => $house) : ?>
        <label class="radio" id="house_label_<?php echo $house_id; ?>">
          <input type="radio" name="house_id" id="house_<?php echo $house_id; ?>" value="<?php echo $house['house_id']; ?>" <?php echo (($user['house_id'] == $house_id) ? 'checked' : ''); ?>>
          <strong><?php echo $house['house_name']; ?></strong><!-- <?php echo 'ID: ' . $house_id; ?> --> (created <time class="timeago" datetime="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', strtotime($house['house_joined'])); ?>">on <?php echo $house['house_joined']; ?></time> by <em><?php echo $housemates_all[$house_id][$house['house_created_by']]['user_name']; ?></em>)
        </label>
<?php endforeach; ?>
        <span class="help-block">Click <a href="<?php echo site_url('register'); ?>">here</a> to register a new house.</span>
      </div>
    </div>

    <div class="control-group">
      <span class="control-label">Housemates</span>
<?php foreach ($housemates_all as $house_id => $housemates) : ?>
      <div class="controls" id="housemates_house_<?php echo $house_id; ?>"<?php echo ($user['house_id'] == $house_id) ? '' : ''; ?>>
<?php foreach ($housemates as $housemate) : ?>
        <img src="https://graph.facebook.com/<?php echo $housemate['social_identifier_facebook']; ?>/picture" class="img-polaroid">
        <strong><?php echo $housemate['user_name']; ?></strong><!-- <?php echo 'ID: ' . $housemate['user_id']; ?> -->
<?php endforeach; ?>
      </div>
<?php endforeach; ?>
    </div>

    <script>

      // Show correct housemates for the selected house
      $('input[name=house_id]').change(function() {
        var h_id = $(this).attr('id');
        //console.log(h_id);
        //console.log(typeof h_id);
        $('div[id^=housemates_house_]').hide();
        $('div#housemates_' + h_id).show();
        // Show "save reminder" if necessary.
        if (h_id === "house_<?php echo $user['house_id']; ?>") {
          $('span#savereminder-house').hide();
        } else {
          $('span#savereminder-house').show();
        }
      });

      // Click the current house
      $('div[id^=housemates_house_]').hide();
      $("div#housemates_house_<?php echo $user['house_id']; ?>").show();

    </script>

  </fieldset>



  <fieldset>

    <legend>Purchase Settings</legend>

    <div class="control-group">
      <label class="control-label" for="purchases_order_by">Sorting</label>
      <div class="controls">
        <span class="help-inline">Sort purchases by</span>
        <select name="purchases_order_by" class="span2">
          <option value="added_time"<?php echo ((isset($repop['purchases_order_by']) && ($repop['purchases_order_by'] == 'added_time')) ? ' selected' : (isset($user['conf']['purchases_order_by']) && ($user['conf']['purchases_order_by'] == 'added_time')) ? ' selected' : ''); ?>>Time Added</option>
          <option value="date"<?php echo ((isset($repop['purchases_order_by']) && ($repop['purchases_order_by'] == 'date')) ? ' selected' : (isset($user['conf']['purchases_order_by']) && ($user['conf']['purchases_order_by'] == 'date')) ? ' selected' : ''); ?>>Purchase Date</option>
        </select>
        <span class="help-inline">in</span>
        <select name="purchases_order" class="span2">
          <option value="asc"<?php echo ((isset($repop['purchases_order']) && ($repop['purchases_order'] == 'asc')) ? ' selected' : (isset($user['conf']['purchases_order']) && ($user['conf']['purchases_order'] == 'asc')) ? ' selected' : ''); ?>>ascending</option>
          <option value="desc"<?php echo ((isset($repop['purchases_order']) && ($repop['purchases_order'] == 'desc')) ? ' selected' : (isset($user['conf']['purchases_order']) && ($user['conf']['purchases_order'] == 'desc')) ? ' selected' : ''); ?>>descending</option>
        </select>
        <span class="help-inline"> order.</span>
      </div>
    </div>
<!--
    <div class="control-group">
      <label class="control-label" for="purchases_per_page">Paging</label>
      <div class="controls">
        <span class="help-inline">Show</span>
        <select name="purchases_per_page" class="span1">
<?php foreach ($this->config->item('purchases_per_page') as $ppp) : ?>
          <option value="<?php echo $ppp; ?>"<?php echo ((isset($repop['purchases_per_page']) && ($repop['purchases_per_page'] == $ppp)) ? ' selected' : (isset($user['conf']['purchases_per_page']) && ($user['conf']['purchases_per_page'] == $ppp)) ? ' selected' : ''); ?>><?php echo $ppp; ?></option>
<?php endforeach; ?>
        </select>
        <span class="help-inline">purchases per page.</span>
      </div>
    </div>
-->
  </fieldset>

<!--

  <fieldset>

    <legend>Notification Settings</legend>

    <div class="control-group">
      <div class="controls">
        <span>Enter an email address and/or UK mobile number to enable notifications.</span>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="user_email">Email address</label>
      <div class="controls">
        <div class="input-prepend">
          <span class="add-on"><i class="icon-envelope"></i></span>
          <input class="span2" id="user_email" name="user_email" type="email" placeholder="ollie@stsp.info" value="<?php echo (isset($repop['user_email']) ? $repop['user_email'] : (isset($user['user_email']) ? $user['user_email'] : '')); ?>">>
        </div>
<?php if (($user['user_email_facebook'] != '') && ($user['user_email_facebook'] != $user['user_email'])) : ?>
        <span class="help-inline" id="fb_email_hint"> <i class="icon-caret-left"></i> <a href="#" id="fb_email_fill"><?php echo $user['user_email_facebook']; ?></a> <?php echo helptip('Email address retrieved from Facebook.', 'right'); ?></span>
        <script>
        $('#fb_email_fill').click(function(e) {
          e.preventDefault();
          $('input#user_email').val('<?php echo $user['user_email_facebook']; ?>');
          $('span#fb_email_hint').hide();
        });
        </script>
<?php endif; ?>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="user_mobile">Mobile Number</label>
      <div class="controls">
        <div class="input-prepend">
          <span class="add-on"><i class="icon-phone"></i></span>
          <input class="span2" id="user_mobile" name="user_mobile" type="text" placeholder="+44 7000 000 000" value="<?php
if (isset($repop['user_mobile'])) {
  echo $repop['user_mobile'];
} elseif (isset($user['user_mobile'])) {
  if ($user['user_mobile'] != '') {
    echo '+' . substr($user['user_mobile'], 0, 2) . ' ' . substr($user['user_mobile'], 2, 4) . ' ' . substr($user['user_mobile'], 6, 3) . ' ' . substr($user['user_mobile'], 9, 3);
  }
}
          ?>">
        </div>
        <span class="help-inline"><?php echo helptip('Enter a number beginning \'+447\' or \'07\'. Only UK mobile numbers are currently supported.', 'right'); ?></span>
      </div>
    </div>

    <div class="control-group">
      <div class="controls">
        <span class="help-inline">When a purchase is <em>added</em> by a housemate:</span>
        <label class="checkbox inline">
          <input type="checkbox" name="notification[added][email]" id="abc1"> <i class="icon-envelope"></i>
        </label>
        <label class="checkbox inline">
          <input type="checkbox" name="notification[added][sms]" id="abc2"> <i class="icon-globe"></i>
        </label>
      </div>
      <div class="controls">
        <span class="help-inline">When a purchase is <em>disputed</em> by a housemate:</span>
        <label class="checkbox inline">
          <input type="checkbox" name="notification[disputed][email]" id="abc1"> Email
        </label>
        <label class="checkbox inline">
          <input type="checkbox" name="notification[disputed][sms]" id="abc2"> SMS
        </label>
      </div>
      <div class="controls">
        <span class="help-inline">When a purchase is <em>deleted</em> by a housemate:</span>
        <label class="checkbox inline">
          <input type="checkbox" name="notification[deleted][email]" id="abc1thautode"> Email
        </label>
        <label class="checkbox inline">
          <input type="checkbox" name="notification[deleted][sms]" id="abc2"> SMS
        </label>
      </div>
    </div>

  </fieldset>

-->

  <div class="control-group">
    <div class="controls">
      <input class="btn btn-primary" type="submit" value="Save Settings" />
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
      <span class="help-inline"><a href="<?php echo site_url('settings/refresh'); ?>">Refresh Facebook Data</a> <?php echo helptip('Retrieve any changes to your Facebook details.', 'right'); ?></span>
    </div>
  </div>
</form>

<script>

// Check for changed settings, to avoid accidentally unsaved changes.
function unsavedChangesWarning() {
    
    var warningMsg = 'It looks like you have been editing something - if you leave before submitting your changes will be lost.';
    
    if (FALSE) { // TODO - handle onbeforeunload to stop users navigating away before saving changes
        //return warningMsg;
    } else {
        return; // do not prompt user before leaving page
    }
}

window.onbeforeunload = unsavedChangesWarning;

</script>
