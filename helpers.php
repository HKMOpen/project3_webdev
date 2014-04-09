<?php

// code based off of CT310 Lecture 10 example

class User {
	public $username; // uname must be > 3 characters (for salt)
	public $passwd;
	public $name; 
	public $gender;
	public $phone;
	public $email;
	public $admin;
	public $pic; // location of profile picture
	public $friends; // list of curerent friends
	public $pending; // list of pending friend requests
	
	/* This function provides a complete tab delimeted dump of the contents/values of an object */
	public function contents() {
		$vals = array_values(get_object_vars($this));
		return( array_reduce($vals, create_function('$a,$b','return is_null($a) ? "$b" : "$a"."\t"."$b";')));
	}
	/* Companion to contents, dumps heading/member names in tab delimeted format */
	public function headings() {
		$vals = array_keys(get_object_vars($this));
		return( array_reduce($vals, create_function('$a,$b','return is_null($a) ? "$b" : "$a"."\t"."$b";')));
	}
	
}

class UserSummary {
	public $username;
	public $summary;
}

define("RANDOM_32_CHAR_KEY", substr(md5("random"), 0, 31).'~');

function makeNewUser($uname, $pass, $name, $sex, $number, $mail, $privileges, $picture, $friendList, $pendingList) {
	$u = new User();
	$u->username = $uname;
	$u->passwd  = $pass;
	$u->name  = $name;
	$u->gender = $sex;
	$u->phone  = $number;
	$u->email  = $mail;
	$u->admin  = $privileges;
	$u->pic = $picture;
	$u->friends  = $friendList;
	$u->pending  = $pendingList;
	return $u;
}

function setupDefaultUsers() {
	$users = array();
	$users[0] = makeNewUser("blund", "2ba29d51f0a6c701cdaba3d51a9ede42", "Brian", "Male", "7209331750", "blund@email.com", "1", "images/brian.jpg", "", "");
	$users[1] = makeNewUser("rawlin", saltedHash("rawlin", "rawlin"), "Rawlin", "Male", "5555555555", "blah@gmail.com", "1", "images/rawlin.jpg", "", "");
	$users[2] = makeNewUser("prady", saltedHash("prady", "prady"), "Prady", "Male", "1111111111", "prady@mail.com", "1", "images/prady.jpg", "", "");
	writeUsers($users);
}

function writeUsers($users) {
	$fh = fopen('users.tsv', 'w+') or die("Can't open file");
	fwrite($fh, $users[0]->headings()."\n");
	for ($i = 0; $i < count($users); $i++) {
		fwrite($fh, $users[$i]->contents()."\n");
	}
	fclose($fh);
}

function readUsers() {
	if (! file_exists('users.tsv')) { setupDefaultUsers(); }
	$contents = file_get_contents('users.tsv');
	$lines    = preg_split("/\r|\n/", $contents, -1, PREG_SPLIT_NO_EMPTY);
	$keys     = preg_split("/\t/", $lines[0]);
	$i        = 0;
	for ($j = 1; $j < count($lines); $j++) {
		$vals = preg_split("/\t/", $lines[$j]);
		if (count($vals) > 1) {
			$u = new User();
			for ($k = 0; $k < count($vals); $k++) {
				$u->$keys[$k] = $vals[$k];
			}
			$users[$i] = $u;
			$i++;
		}
	}
	return $users;
}

function getUser($uname) {
	if ($uname != "") {
		return getUserHelper(readUsers(), $uname);
	}
	else {
		return NULL;
	}
}

function getUserHelper($allUsers, $uname) {

	for ($i = 0; $i < count($allUsers); $i++) {
		if ($allUsers[$i]->username == $uname) {
			return $allUsers[$i];
		}
	}
	return NULL;
}

function getHash($users, $uname) {
	$hash = '';
	foreach ($users as $u ) {
		if ($u->username == $uname) {
			$hash = $u->passwd;
		}
	}
	return $hash;
}

function getUserSummary($username) {
	if (!empty($username)) {
		return getUserSummaryHelper(readUserSummaries(), $username);
	}
	else {
		return NULL;
	}
}

function getUserSummaryHelper($allUserSummaries, $uname) {
	foreach ($allUserSummaries as $entry) {
		if ($entry->username == $uname) {
			return $entry->summary;
		}
	}
	return NULL;
}

function readUserSummaries() {
	if (!file_exists("profiles.txt")) { setupDefaultSummary(); }
	$contents = file_get_contents('profiles.txt');
	$entries    = preg_split("/" . RANDOM_32_CHAR_KEY . "/", $contents, -1, PREG_SPLIT_NO_EMPTY);
	$ret = array();
	if (count($entries) % 2 != 0) {
		echo "ERROR: entries should be of even length";
	}
	else {
		for ($i = 0; $i < count($entries); $i += 2) {
			$uSumm = new UserSummary();
			$uSumm->username = $entries[$i];
			$uSumm->summary = $entries[$i+1];
			$ret[] = $uSumm;
		}
	}
	return $ret;
}

function writeUserSummaries($userSummaries) {
	$fh = fopen('profiles.txt', 'w+') or die("Can't open file");
	foreach ($userSummaries as $entry) {
		fwrite($fh, RANDOM_32_CHAR_KEY . $entry->username . RANDOM_32_CHAR_KEY . "\n" . trim($entry->summary) . "\n");
	}
	fclose($fh);
}

function setupDefaultSummary() {
	$default = new UserSummary();
	$default->username = "blund";
	$default->summary = "This is my summary! Pretty cool, right?";
	$default2 = new UserSummary();
	$default2->username = "rawlin";
	$default2->summary = "This is my summary! Pretty cool, right?";
	$default3 = new UserSummary();
	$default3->username = "prady";
	$default3->summary = "This is my summary! Pretty cool, right?";
	$ret =array();
	$ret[] = $default;
	$ret[] = $default2;
	$ret[] = $default3;
	writeUserSummaries($ret);
}

// return password hash given raw entered password and name
function saltedHash($raw, $uname) {
	$salt = substr($uname, 0, 3);
	return md5($salt.$raw);
}

function sanitize($input) {
	$input = trim($input);
	$input = strip_tags($input);
	$input = htmlspecialchars($input);
	return $input;
}

// return array of Users that $uname is friends with
function getFriends($uname){
	if($uname == 'guest') return array();
	$u = getUser($uname);
	$rawFriends = $u->friends;
	if(empty($rawFriends)) return array();
	$friendList = explode(",", $rawFriends);
	$friends = array();
	foreach($friendList as $fname){
		if(!empty($fname)){
			$u = getUser($fname);
			$friends[$fname] = $u;
		}
	}
	return $friends;
}

// returns true if user $u2 is on user $u1's friend list
function isFriend($u1, $u2){
	if($u1 == 'guest' || $u2 == 'guest') return FALSE;
	$friends = getFriends($u1);
	$testUser = getUser($u2);
	return in_array($testUser, $friends);
}

// return array of usernames that $uname has pending requests from
function getRequests($uname){
	if($uname == 'guest') return array();
	$u = getUser($uname);
	$rawRequests = $u->pending;
	if(empty($rawRequests)) return array();
	$requestList = explode(",", $rawRequests);
	$requests = array();
	foreach($requestList as $rname){
		if(!empty($rname)){
			$requests[$rname] = $rname;
		}
	}
	return $requests;
}

// returns true if $requestor is on $requestee's pending list
function isPending($requestor, $requestee){
	$pend = getRequests($requestee);
	return in_array($requestor, $pend);
}

function requestChangePassword($username, $email, $ip) {
	//generate random key, store it in the DB, send authentication email
	//to the user's email address, using link to chpasswd.php w/ key as a GET variable
	//also store user's IP address in DB to make sure it matches when authenticating
	
}

function changePassword($username, $newPassword) {
	//change the user's password in the database to the new password
	
}

function requestRegisterAuthentication($username, $email, $password, $ip) {
	//similar to requestChangePassword...
	//send authentication email to user
	 
}

function authenticateNewUser($username, $key, $ip) {
	//checks that the given key and IP match the key and IP stored in the DB
	//authenticates the user and notifies the admins for approval
	
}

?>