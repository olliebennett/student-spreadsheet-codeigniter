<?php
//d($users, 'users');
?>

<?php foreach ($users as $user) : ?>

<h3><b>#<?php echo $user['user_id']; ?></b>: <?php echo $user['user_name']; ?></h3>

<p>HashID: '<b><?php echo hashids_encrypt($user['user_id']); ?></b>'</p>


<div class="user_pic_container">
	<a href="http://facebook.com/profile.php?id=<?php echo $user['social_identifier_facebook']; ?>"><img class="user_pic" src="https://graph.facebook.com/<?php echo $user['social_identifier_facebook']; ?>/picture" height="50" width="50" alt="Profile Pic" title="ID=<?php echo $user['social_identifier_facebook']; ?>" />
	<span class="user_pic_social_overlay"><img src="<?php echo base_url(); ?>assets/img/fb_16x16.png" width="16" height="16" alt="" /></span></a> 
</div>


 <div class="clearfix"></div>

<?php d($user, 'full user details'); ?>

<?php endforeach; ?>