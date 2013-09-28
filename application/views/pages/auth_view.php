<div id="provider-list">
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
</div>

<br style="clear: both;"/>

<h3>Why Facebook?</h3>

<p>Facebook is used <strong>only</strong> to retrieve your list of friends so you can select your housemates.</p>

<p>Student Spreadsheet never sees your Facebook password.</p>

<p>We <strong>DO NOT</strong> post to your wall / email your friends / send messages from your account or store unnecessary personal information.</p>

<p>Still not happy? Sorry to hear that! Facebook is currently the only option for logging in. If you have comments or questions, please <a href="<?php echo site_url('help/contact'); ?>">get in touch</a>!</p>

<p>There are plans for login using Twitter and other social networks too.</p>

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
          <a href="<?php echo $d['user_profile']->profileURL; ?>"><img src="<?php echo $d['user_profile']->photoURL; ?>" title="<?php echo $d['user_profile']->displayName; ?>" style="height: 120px;"></a>
<?php else : ?>
          <img src="public/avatar.png" title="<?php echo $d['user_profile']->displayName; ?>" >
<?php endif; ?>
        </td>
        <td align="left"><table width="100%" cellspacing="0" cellpadding="3" border="0">
            <tbody>
<?php foreach ($d['user_profile'] as $key => $value) : ?>
<?php if ($value != "") : ?>
              <!-- "<?php echo $key; ?>": "<?php echo $value; ?>"-->
<?php endif; ?>
<?php endforeach; ?>
              <tr>
                <td class="pItem"><strong>Name:</strong> <?php echo $d['user_profile']->displayName; ?></td>
              </tr>
              <tr>
                <td class="pItem"><strong>Email:</strong> <?php echo $d['user_profile']->email; ?></td>
              </tr>
              <tr>
                <td class="pItem"><strong>Profile ID:</strong> <?php echo $d['user_profile']->identifier; ?></td>
              </tr>
              <tr>
                <td class="pItem"><strong>Profile Link:</strong> <a href="<?php echo $d['user_profile']->profileURL; ?>"><?php echo $d['user_profile']->profileURL; ?></a></td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </table>
  </fieldset>
<?php endif; ?>
<?php endforeach; ?>
