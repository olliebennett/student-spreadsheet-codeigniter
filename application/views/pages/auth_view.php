<ul id="provider-list">
<?php
  // Output the enabled services and change link/button if the user is authenticated.
  $this->load->helper('url');
  foreach($providers as $provider => $data) {
    echo '<p>';
    if ($data['connected']) {
	   echo '<a class="btn btn-inverse" href="' . site_url('auth/logout/'.$provider) . '"><i class="icon-' . strtolower($provider) . '"></i> Logout of ' . $provider . '</a>';
    } else {
	   echo '<a class="btn btn-success" href="' . site_url('auth/login/'.$provider) . '"><i class="icon-' . strtolower($provider) . '"></i> Login with ' . $provider . '</a>';
    }
    echo '</p>';
  }
?>
</ul>
<br style="clear: both;"/>

<p class="footer">
<?php
// Output the profiles of each logged in service
foreach ($providers as $provider => $d) :
  if (!empty($d['user_profile'])) :
    $profile[$provider] = (array)$d['user_profile'];
    ?>
  <fieldset>
    <legend><strong><?php echo $provider; ?></strong> Profile</legend>
    <table width="100%">
      <tr>
        <td width="150" valign="top" align="center">
<?php if ( !empty($d['user_profile']->profileURL) ) : ?>
      <a href="<?php echo $d['user_profile']->profileURL; ?>"><img src="<?php echo $d['user_profile']->photoURL; ?>" title="<?php echo $d['user_profile']->displayName; ?>" border="0" style="height: 120px;"></a>
<?php else : ?>
    <img src="public/avatar.png" title="<?php echo $d['user_profile']->displayName; ?>" border="0" >
<?php endif; ?>
        </td>
        <td align="left"><table width="100%" cellspacing="0" cellpadding="3" border="0">
            <tbody>
<?php foreach ($d['user_profile'] as $key=>$value) : ?>
<?php if ($value != "") : ?>
              <tr>
                <td class="pItem"><strong><?php echo ucfirst($key); ?>:</strong> <?php echo (filter_var($value, FILTER_VALIDATE_URL) !== false) ?  '<a href="'.$value.'" target="_blank">'.$value.'</a>' : $value; ?></td>
              </tr>
<?php endif; ?>
<?php endforeach; ?>
            </tbody>
          </table>
        </td>
      </tr>
    </table>
  </fieldset>
<?php endif; ?>
<?php endforeach; ?>
</p>