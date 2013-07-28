<?php
  //d(isset($purchase_id) ? $purchase_id : 'N/A', 'purchase_id');
  //d($housemates, 'housemates');
  //d($user, 'user');
  //d(isset($edit) ? $edit : 'N/A', 'edit');
  //d(isset($repop) ? $repop : 'N/A', 'repop');
?>


<?php echo form_open('purchases/add', array('id'=>'purchase_form','class'=>'form-horizontal')); ?>

<?php
function form_err($err) {
    return '<div class="alert alert-error">'.$err.'</div>';
}
// TODO - print errors next to the appropriate field, instead of all at top
if (isset($error) && is_array($error)) {
  foreach ($error as $err_type => $err) {
    echo form_err($err);
  }
} ?>

<?php if (isset($edit)) : ?>

  <input type="hidden" name="edit_id" value="<?php echo $purchase_id; ?>" />

<?php endif; ?>

  <div class="control-group">
  	<?php //echo form_err('description'); ?>
    <label class="control-label" for="description">Description</label>
    <div class="controls">
      <input type="text" name="description" id="description" placeholder="e.g. Electricity Bill" value="<?php echo (isset($repop['description']) ? $repop['description'] : ''); ?>" size="50" />
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="payer">Who Paid?</label>
    <div class="controls">
      <?php //echo form_err('payer'); ?>
      <select name="payer" id="payer">
<?php foreach ($housemates as $housemate) : ?>
        <option value="<?php echo $housemate['user_id']; ?>" <?php echo ((isset($repop['payer'])) && (($housemate['user_id'] == $repop['payer']) || ($housemate['user_id'] == $user['user_id']))) ? ' selected="selected"' : ''; ?>><?php echo $housemate['user_name']; ?></option>
<?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="purchase_date">Purchase Date</label>
    <div class="controls">
      <div class="input-prepend">
        <!--<label for="purchase_date">TODO - this breaks the datepicker -->
          <span class="add-on"><i class="icon-calendar"></i></span><input type="text" name="purchase_date" class="datepicker" id="purchase_date" placeholder="Click to select date" value="" />
        <!--</label>-->
      </div>
    </div>
  </div>

  <div class="control-group">
    <div class="controls btn-group" id="btns" data-toggle="buttons-radio">
  	  <input type="button" class="btn active" id="btn-split-even" value="Even Split" />
      <input type="button" class="btn" id="btn-split-custom" value="Custom Split" />
      <!-- TODO <input type="button" class="btn" id="btn-split-date" value="Date Split" />-->
    </div>
  </div>
  <div style="display: none;">
      <input type="radio" name="split_type" id="split_type_even" value="even" checked>
      <input type="radio" name="split_type" id="split_type_custom" value="custom">
  </div>

  <div id="tab-even">
    <div class="control-group">
      <div class="controls">
        <span class="help-inline">Each housemate pays an <strong>equal share</strong> of the total.</span>
      </div>
    </div>
  	<div class="control-group">
  	  <label class="control-label" for="total_price">Total Price</label>
      <div class="controls">
        <div class="input-prepend">
          <label for="total_price">
            <span class="add-on">£</span><input class="input-mini" type="text" name="total_price" id="total_price" placeholder="0.00" value="<?php echo set_value('total_price', (isset($repop['total_price']) && is_numeric($repop['total_price'])) ? number_format($repop['total_price'], 2) : ''); ?>" size="50" />
          </label>
        </div>
      </div>
  	</div>
    <div class="control-group">
      <label class="control-label" for="payees">Payees</label>
      <div class="controls">

<?php foreach ($housemates as $housemate) : ?>
<?php
// Determine whether or not to check this housemate by default
$checked = FALSE;
//(isset($repop['split_type']) && $repop['split_type'] == 'even' && isset($repop['payees']) && is_array($repop['payees']) && in_array($housemate['user_id'], array_values($repop['payees']))) {
if (isset($repop['split_type'])) {
  if ($repop['split_type'] == 'even') {
    if (isset($repop['payees']) && is_array($repop['payees'])) {
      if (in_array($housemate['user_id'], array_keys($repop['payees']))) {
          $checked = TRUE;
      }
    }
  }
} else {
  // check all housemates by default
  $checked = TRUE;
}
?>
      <label class="checkbox inline">
        <input type="checkbox" name="payees[]" id="check_<?php echo $housemate['user_id']; ?>" value="<?php echo $housemate['user_id']; ?>"<?php echo ($checked ? ' checked="checked"' : '') ?>> <?php echo $housemate['user_name']; ?>
      </label>
 <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div id="tab-custom">
    <div class="control-group">
      <div class="controls">
        <span class="help-inline">Each housemate pays a <strong>custom amount</strong>, as specified below.</span>
      </div>
    </div>
<?php foreach ($housemates as $housemate) : ?>
  <div class="control-group">
    <label class="control-label" for="price_custom_<?php echo $housemate['user_id']; ?>"><?php echo $housemate['user_name']; ?></label>
    <div class="controls">
      <div class="input-prepend">
        <label for="price_custom_<?php echo $housemate['user_id']; ?>">
          <span class="add-on">£</span><input class="input-mini" type="text" name="price_custom[<?php echo $housemate['user_id']; ?>]" id="price_custom_<?php echo $housemate['user_id']; ?>" placeholder="0.00" value="<?php echo (isset($repop['payees'][$housemate['user_id']]) ? number_format((double)$repop['payees'][$housemate['user_id']], 2) : ''); ?>" />
        </label>
      </div>
    </div>
  </div>
<?php endforeach; ?>
  </div>

  <script>

    $('#btn-split-even').click(function (e) {
      e.preventDefault();
      $('#tab-even').show();
      $('#tab-custom').hide();
      $('#split_type_custom').prop('checked', false);
      $('#split_type_even').prop('checked', true);
      //console.log($(this).attr('id'));
    });
    $('#btn-split-custom').click(function (e) {
      e.preventDefault();
      $('#tab-even').hide();
      $('#tab-custom').show();
      $('#split_type_custom').prop('checked', true);
      $('#split_type_even').prop('checked', false);
      //console.log($(this).attr('id'));
    });

<?php if (isset($repop['split_type']) && ($repop['split_type'] == 'custom')) : ?>
    // Initially show only "custom" split
    //console.log('triggering btn-split-custom');
    $('#btn-split-custom').trigger('click');
<?php else : ?>
    // Initially show only "even" split
    //console.log('triggering btn-split-even');
    $('#btn-split-even').trigger('click');
<?php endif; ?>

    // Enhance Date input with date picker
    var purchase_date_input = $('#purchase_date').pickadate({
      format: 'dddd, dd mmm, yyyy', // visible format
      formatSubmit: 'yyyy/mm/dd' // submitted format
    });

    // Set date to the current or pre-set value
    var purchase_date = purchase_date_input.data('pickadate');
<?php $d = (isset($repop['date']) && $repop['date'] != '') ? $repop['date'] : date('Y/m/d'); ?>
    purchase_date.setDate( <?php echo substr($d, 0, 4); ?>, <?php echo substr($d, 5, 2); ?>, <?php echo substr($d, 8, 2); ?> );
  </script>

  <div class="control-group">
  	<?php //echo form_err('comment'); ?>
    <label class="control-label" for="comment">Extra Comments</label>
    <div class="controls">
      <textarea rows="3" name="comment" id="comment" class="span5" placeholder="(optional)" ><?php echo set_value('comment'); ?></textarea>
      <!--<input type="text" name="comment" id="comment" placeholder="(optional)" value="<?php echo set_value('comment'); ?>" size="250" />-->
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
<?php if (isset($edit)) : ?>
      <a href="<?php echo site_url('purchases/view/' . $purchase_id); ?>" class="btn btn-inverse">Cancel</a>
<?php endif; ?>
      <input class="btn btn-primary" type="submit" value="<?php echo (isset($edit) ? 'Save Changes' : 'Add Purchase'); ?>" />
    </div>
  </div>

  <script>

    // On form reset, manually reset the "split_type" buttons to defaults

  $("#btn-reset").click(function() {

    "use strict";

<?php if (isset($repop['split_type']) && ($repop['split_type'] == 'custom')) : ?>

    $('#btn-split-custom').trigger('click');

<?php else : ?>

    $('#btn-split-even').trigger('click');

<?php endif; ?>

  });

  </script>

</form>
