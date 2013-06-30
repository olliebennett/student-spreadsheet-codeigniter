<?php // migrate from v3 to codeigniter version

$old_user = 'root';
$old_pass = '';
$old_host = 'localhost';
$old_db = 'stsp_migrate';

$new_user = 'root';
$new_pass = '';
$new_host = 'localhost';
$new_db = 'stspci';

// Connect
$old = new mysqli($old_host, $old_user, $old_pass, $old_db);
if ($old->connect_errno) {
    die("Failed to connect to MySQL: (" . $old->connect_errno . ") " . $old->connect_error);
} else {
	l("Connected to 'old' database on Host: <b>$old_host</b> | User: <b>$old_user</b> | DB: <b>$old_db</b>");
}
$new = new mysqli($new_host, $new_user, $new_pass, $new_db);
if ($new->connect_errno) {
    die("Failed to connect to MySQL: (" . $new->connect_errno . ") " . $new->connect_error);
} else {
	l("Connected to 'new' database on Host: <b>$new_host</b> | User: <b>$new_user</b> | DB: <b>$new_db</b>");
}



// Create fresh new database.
$new_schema = file_get_contents('new_schema.sql');
if (!$new->multi_query($new_schema)) {
    die("Failed: (" . $new->errno . ") " . $new->error);
} else {
	// this is an ugly workaround for "freeing" or "closing"? the multi-query...
	do { 
		$new->use_result();
	} while ($new->next_result());
	l("Created fresh new database");
}


// READ OLD INFORMATION

// Old houses
$houses = array();
$house_count = 1;
if ($result = $old->query("SELECT * FROM `houses`")) {
	h('Reading ' . $result->num_rows . ' houses...');
	
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		//var_dump($row);
		$houses[$row['house_id']] = $row;
		$houses[$row['house_id']]['migration_house_id'] = $house_count++;
	}
	$result->free();
} else {
	die("Failed to get old houses: (" . $new->errno . ") " . $new->error);
}
//var_dump($houses);

// Old users
$users = array();
$user_count = 1;
if ($result = $old->query("SELECT * FROM `users`")) {
	h('Reading ' . $result->num_rows . ' users...');
	
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		//var_dump($row);
		$users[$row['user_id']] = $row;
		$users[$row['user_id']]['migration_user_id'] = $user_count++;
	}
	$result->free();
} else {
	die("Failed to get old users: (" . $new->errno . ") " . $new->error);
}
//var_dump($users);

// Old house-user links
$houseuserlinks = array();
if ($result = $old->query("SELECT * FROM `link_houses_users`")) {
	h('Reading ' . $result->num_rows . ' house/user links...');
	
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$houseuserlinks[] = $row;
	}
	$result->free();
} else {
	die("Failed to get old houseuserlinks: (" . $new->errno . ") " . $new->error);
}

// Old Purchases
$purchases = array();
$purchase_count = 1;
if ($result = $old->query("SELECT * FROM `purchases`")) {
	h('Reading ' . $result->num_rows . ' purchases...');
	
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		//var_dump($row);
		$purchases[$row['purchase_id']] = $row;
		$purchases[$row['purchase_id']]['migration_purchase_id'] = $user_count++;
	}
	$result->free();
} else {
	die("Failed to get old purchases: (" . $new->errno . ") " . $new->error);
}

// Old purchase-user links
$purchaseuserlinks = array();
if ($result = $old->query("SELECT * FROM `link_purchases_users`")) {
	h('Reading ' . $result->num_rows . ' purchase/user links...');
	
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$purchaseuserlinks[] = $row;
	}
	$result->free();
} else {
	die("Failed to get old purchaseuserlinks: (" . $new->errno . ") " . $new->error);
}

// Old Comments
$comments = array();
if ($result = $old->query("SELECT * FROM `comments`")) {
	h('Reading ' . $result->num_rows . ' comments...');
	
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$comments[] = $row;
	}
	$result->free();
} else {
	die("Failed to get old comments: (" . $new->errno . ") " . $new->error);
}

// WRITE NEW INFORMATION

// New users
h('Inserting users...');
foreach ($users as $ufbid => $user) {
	$sql = "
	INSERT INTO `users` (`user_id`, `user_id_facebook`, `house_id`, `user_name`)
	VALUES ('".$user['migration_user_id']."','".$new->escape_string($user['user_id'])."','".$user['house_id']."','".$user['user_name']."');
	";
	if (!$result = $new->query($sql, MYSQLI_USE_RESULT)) {
	    die("Could not insert user: (" . $new->errno . ") " . $new->error);
	} else {
		//l("User #" . $user['migration_user_id'] . " created: " . $user['user_name']);
	}
}


// New houses
h('Inserting houses...');
foreach ($houses as $house) {
	$sql = "
	INSERT INTO `houses` (`house_id`, `house_name`, `house_created_by`,`house_joined`)
	VALUES ('".$house['house_id']."','".$house['house_name']."','".$users[$house['house_created_by']]['migration_user_id']."','".$house['house_joined']."');
	";
	if (!$result = $new->query($sql, MYSQLI_USE_RESULT)) {
	    die("Could not insert house: (" . $new->errno . ") " . $new->error);
	} else {
		//l("House #" . $house['house_id'] . " created: " . $house['house_name']);
	}
}

// New house/user links
h('Inserting house/user links...');
foreach ($houseuserlinks as $houseuserlink) {
	$sql = "
	INSERT INTO `link_houses_users` (`house_id`, `user_id`)
	VALUES ('".$houseuserlink['house_id']."','".$users[$houseuserlink['user_id']]['migration_user_id']."');
	";
	if (!$result = $new->query($sql, MYSQLI_USE_RESULT)) {
	    die("Could not insert house/user link: (" . $new->errno . ") " . $new->error);
	} else {
		//l("House/User link: House" . $houseuserlink['house_id'] . " + User" . $users[$houseuserlink['user_id']]['migration_user_id']);
	}
}

// New purchases
h('Inserting purchases...');
foreach ($purchases as $purchase) {
	$sql = "
		INSERT INTO `purchases` (
			`purchase_id`,
			`description`,
			`added_by`,
			`added_time`,
			`house_id`,
			`payer`,
			`date`
		)
		VALUES (
			'".$purchase['purchase_id']."',
			'".$purchase['purchase_description']."',
			'".$users[$purchase['purchase_added_by']]['migration_user_id']."',
			'".$purchase['purchase_added_time']."',
			'".$purchase['purchase_house_id']."',
			'".$users[$purchase['purchase_payer']]['migration_user_id']."',
			'".$purchase['purchase_date']."'
		);
	";
	if (!$result = $new->query($sql, MYSQLI_USE_RESULT)) {
	    die("Could not insert purchase: (" . $new->errno . ") " . $new->error);
	} else {
		//l("Purchase #" . $purchase['purchase_id'] . " created: " . $purchase['purchase_description']);
	}

}

// New purchase/user links
h('Inserting purchase/user links...');
foreach ($purchaseuserlinks as $purchaseuserlink) {
	$sql = "
	INSERT INTO `link_purchases_users` (`purchase_id`, `user_id`, `price`)
	VALUES ('".$purchaseuserlink['purchase_id']."','".$users[$purchaseuserlink['user_id']]['migration_user_id']."','".$purchaseuserlink['value']."');
	";
	if (!$result = $new->query($sql, MYSQLI_USE_RESULT)) {
	    die("Could not insert purchase/user link: (" . $new->errno . ") " . $new->error);
	} else {
		//l("Purchase/User link: Purchase" . $purchaseuserlink['purchase_id'] . " + User" . $users[$purchaseuserlink['user_id']]['migration_user_id'] . ' + ' . $purchaseuserlink['value']);
	}
}

// New comments
h('Inserting comments...');
foreach ($comments as $comment) {
	$sql = "
	INSERT INTO `comments` (`parent_id`, `comment_text`, `comment_added_by`, `comment_added_time`)
	VALUES ('".$comment['comment_parent_id']."','". $comment['comment_text'] . "','" . $users[$comment['comment_added_by']]['migration_user_id']."','".$comment['comment_added_time']."');
	";
	if (!$result = $new->query($sql, MYSQLI_USE_RESULT)) {
	    die("Could not insert comment: (" . $new->errno . ") " . $new->error);
	} else {
		//l("Comment: " . $comment['comment_text'] . " on P#" . $comment['comment_parent_id']);
	}
}

l('All data migrated successfully!');

function l($str) {
	echo $str . '<br />' . "\n";
}
function h($str) {
	echo '<h2>' . $str . '</h2>' . "\n";
}