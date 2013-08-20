<?php 
//d($hashid_decoded, 'hashids_decoded');
//d($hashid_encoded, 'hashids_encoded');
//d($hashid_to_encode, 'hashid_to_encode');
//d($hashid_to_decode, 'hashid_to_decode');
?>


<?php if (ENVIRONMENT == 'demo') : // TODO! ?>

<p>Click <a href="<?php echo site_url('admin/cleardemo'); ?>">Clear DEMO</a> to re-create the demo database sample data.</p>

<?php endif; ?>

<?php echo form_open('admin/other', array('class'=>'form-horizontal')); ?>

  <h4>HashIDs</h4>

  <p>Convert IDs to/from HashIDs.</p>

  <div class="control-group">
    <label class="control-label" for="purchase_date">Decode:</label>
    <div class="controls">
      <div class="input-prepend">
        <label for="admin_other_hashids_decode">
          <input type="text" name="hashid_to_decode" placeholder="Enter a hashid to decode" value="<?php echo $hashid_to_decode; ?>" />
          <span>&rarr; <?php echo $hashid_decoded; ?></span>
        </label>
      </div>
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="purchase_date">Encode:</label>
    <div class="controls">
      <div class="input-prepend">
        <label for="admin_other_hashids_encode">
          <input type="text" name="hashid_to_encode" id="admin_other_hashids_encode" placeholder="Enter an ID to encode" value="<?php echo $hashid_to_encode; ?>" />
          <span>&rarr; <?php echo $hashid_encoded; ?></span>
        </label>
      </div>
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
      <input class="btn btn-primary" type="submit" value="Encode / Decode Hashids!" />
    </div>
  </div>

</form>
