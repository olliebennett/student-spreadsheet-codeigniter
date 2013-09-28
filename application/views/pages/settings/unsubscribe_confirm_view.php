

<?php echo form_open(uri_string()); ?>

<input type="hidden" name="confirm" value="confirm" />

  <p>You are unsubscribing <b><?php echo $email; ?></b>.</p>

  <p class="help-block">You will stop receiving <em>ALL</em> notifications from The Student Spreadsheet.<br />To instead change the frequency and type of emails you receive, review your <a href="<?php echo site_url('settings'); ?>">settings</a>.</p>

  <a href="<?php echo site_url(); ?>" class="btn btn-primary">Cancel</a>
  <button type="submit" class="btn btn-danger">Unsubscribe</button>

</form>
