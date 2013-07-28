<?php
//d($users, 'users');
?>

<?php foreach ($users as $user) : ?>

<h3>User <b>#<?php echo $user['user_id']; ?></b>: <?php echo $user['user_name']; ?></h3>

<p><a href="http://facebook.com/profile.php?id=<?php echo $user['social_identifier_facebook']; ?>">FB#<?php echo $user['social_identifier_facebook']; ?></a></p>

<?php d($user, 'full user details'); ?>

<?php endforeach; ?>