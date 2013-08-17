<?php
//d($houses, 'houses');
?>

<?php foreach ($houses as $house) : ?>

<h3>House <b><a href="<?php echo site_url('admin/houses/' . $house['house_id']); ?>">#<?php echo $house['house_id']; ?></a></b>: <?php echo $house['house_name']; ?></h3>

<p>Created on <?php echo strftime('%a %d %b %Y', strtotime($house['house_joined'])); ?> (<time class="timeago" datetime="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', strtotime($house['house_joined'])); ?>"><?php echo strftime('%a %d %b %Y at %H:%M', strtotime($house['house_joined'])); ?></time>) by <?php echo $house['housemates'][$house['house_created_by']]['user_name']; ?>. Currency = <?php echo $house['house_currency']; ?>.</p>

<?php foreach ($house['housemates'] as $housemate) : ?>
        <img src="https://graph.facebook.com/<?php echo $housemate['social_identifier_facebook']; ?>/picture" class="img-polaroid">
        <strong><?php echo $housemate['user_name']; ?></strong><!-- <?php echo 'ID: ' . $housemate['user_id']; ?> -->
<?php endforeach; ?>

<?php endforeach; ?>