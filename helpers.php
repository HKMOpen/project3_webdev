<?php
include_once "Querries.php";
include_once "User.php";
include_once "post.php";

define("RANDOM_32_CHAR_KEY", substr(md5("random"), 0, 31).'~');

function makeNewUser($uname, $pass, $name, $sex, $number, $mail, $privileges, $picture, $friends, $pending, $bio) {
	$u = new User();
	$u->username = $uname;
	$u->passwd  = $pass;
	$u->name  = $name;
	$u->gender = $sex;
	$u->phone  = $number;
	$u->email  = $mail;
	$u->admin  = $privileges;
	$u->pic = $picture;
	$u->friends = $friends;
	$u->pending = $pending;
	$u->bio = $bio;
	return $u;
}

function setupDefaultUsers() {
	$users = array();
	$users[0] = makeNewUser("blund", "2ba29d51f0a6c701cdaba3d51a9ede42", "Brian", "Male", "7209331750", "blund@email.com", "1", "images/brian.jpg", array(),array(),"");
	$users[1] = makeNewUser("rawlin", saltedHash("rawlin", "rawlin"), "Rawlin", "Male", "5555555555", "blah@gmail.com", "1", "images/rawlin.jpg", array(),array(),"");
	$users[2] = makeNewUser("prady", saltedHash("prady", "prady"), "Prady", "Male", "1111111111", "prady@mail.com", "1", "images/prady.jpg", array(),array(),"");

	writeNewUsers($users);
}

function writeNewUsers($users)
{
	$q = new Querries();
	$db = $q->getDB();
	foreach($users as $user)
	{
		$db->query(sprintf($q->CREATE_USER, $user->username, $user->passwd, $user->name,$user->gender,$user->phone,$user->email,$user->admin,$user->pic,$user->bio));
	}
	$db->close();
}

function writeUsers($users) {
	$q = new Querries();
	$db = $q->getDB();
	foreach($users as $user)
	{
		if(!($user instanceof User))
		{
			echo "<h1>complain loudly</h1>";
		}
		saveUser($user);
	}
	$db->close();
}

function addPendingUser($user, $ipaddress)
{
	$users = array();
	$users[0]=$user;
	writeNewUsers($users);

	$q = new Querries();
	$db = $q->getDB();

	$hash = md5($user->passwd.$user->username."PROUDTOBEACSURAM".$user->email.$user->username);
	$db->query(sprintf($q->ADD_PENDING_USER, $user->username, $hash, $ipaddress, "FALSE"));
	$db->close();
	$emailAddress = $user->email;
	$uname = $user->username;
	mail($emailAddress,"User Request!","A user named $uname has registerd for colostatebook, please visit: https://www.cs.colostate.edu/~rbpeters/project3/authenticate.php?user=$uname&key=$hash to confirm you exist! If this email is in error please ignore it <3 ");
}	

function removePendingUsers($users)
{
	$q = new Querries();
	$db = $q->getDB();
	foreach($users as $user)
	{
		$db->query(sprintf($q->REMOVE_PENDING_USER, $user->username));
	}
	$db->close();
}

function readUsers() {
	if (!file_exists("project3.db")) createInitialDatabse();
	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query($q->GET_ALL_USER_NAMES);
	$retVal=array();
	if(!($array instanceof Sqlite3Result))
	{
		return;
	}

  	while($res = $array->fetchArray())
	{ 
		$user = getUser($res["username"]);
        	array_push($retVal, $user);
        } 
	$db->close();
	return $retVal;
	
}

function getUser($uname) {
	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query(sprintf($q->GET_USER, $uname));
	if(!($array instanceof Sqlite3Result))
	{
		return;
	}
	$res = $array->fetchArray();
  	$user = makeNewUser($res["username"],$res["password"],$res["name"],$res["gender"],$res["phone"],$res["email"],$res["admin"],$res["pictureLocation"], array(), array(), $res["bio"]);
	$db->close();
	$user->friends = getFriends($uname);
	$user->pending = getRequests($uname);
	
	return $user;
}


function getPasswordHash($uname) {
	
	getUser($uname);
	return getUser($uname)->passwd;
}

function getUserSummary($username) {
	return getUser($username)->bio;
}

function readUserSummaries()
{
	$userlist = readUsers();
	$retVal = array();
	foreach($userlist as $user)
	{
		$bio = new UserSummary();
		$bio->username=$user->username;
		$bio->bio =$user->bio;
		array_push($retVal, $bio);
	}
	return $retVal;
}

function writeUserSummaries($userSummaries) {
	$q = new Querries();
	$db = $q->getDB();
	foreach($userSummaries as $bio)
	{
		$array = $db->query(sprintf($q->WRITE_USER_SUMMARY, $bio->summary, $bio->username));	
	}
	$db->close();
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

	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query(sprintf($q->GET_USER_FRIENDS, $uname));
	if(!($array instanceof Sqlite3Result))
	{
		return array();
	}
	$friends=array();
	while($res = $array->fetchArray())
	{
		$user = $res["friend"];
		array_push($friends,$user);
	}
	
	$db->close();
	return $friends;
}

// return array of Users that $uname has pending requests from
function getRequestUsers($uname){
	if($uname == 'guest') return array();

	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query(sprintf($q->GET_PENDING_REQUESTS, $uname));
	if(!($array instanceof Sqlite3Result))
	{
		return array();
	}
	$requests=array();
	while($res = $array->fetchArray())
	{
		$user = getUser($res["user"]);
		array_push($requests,$user);
	}
	
	$db->close();
	return $requests;
}

// returns true if user $u2 is on user $u1's friend list
function isFriend($u1, $u2){
	$q = new Querries();
	$db = $q->getDB();
	$res = $db->query(sprintf($q->IS_FRIEND, $u1, $u2));
	if(!($res instanceof Sqlite3Result))
	{
		return array();
	}
	$friendsArray = $res->fetchArray();
	$db->close();
	return $friendsArray[0];
}

// return array of usernames that $uname has pending requests from
function getRequests($uname)
{
	if($uname == 'guest') return array();
	$requests = array();
	foreach(getRequestUsers($uname) as $usr){
		$requests[$usr->username]=$usr->username;
	}
	return $requests;
}

function getPendingSystemUsers()
{
	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query($q->GET_PENDING_USERS);
	if(!($array instanceof Sqlite3Result))
	{
		return array();
	}
	$unames = array();
	while($res = $array->fetchArray())
	{
		$temp = getUser($res["username"]);
		array_push($unames,$temp);
	}
	$db->close();
	return $unames;
}

function disapproveNewUser($username) {
	//same as approveNewUser, except it disapproves them
	$q = new Querries();
	$db = $q->getDB();
	$emailAddress = getUser($username)->email;
	$db->query(sprintf($q->REMOVE_PENDING_USER, $username));
	$db->query(sprintf($q->REMOVE_USER, $username));
	mail($emailAddress,"Disapproved Message!","Saldy $username is not allowed to use colostatebook at this time :(");
}

function requestChangePassword($username, $email, $ip) {
	//generate random key, store it in the DB, send authentication email
	//to the user's email address, using link to chpasswd.php w/ key as a GET variable
	//also store user's IP address in DB to make sure it matches when authenticating
	
	$user = getUser($username);
	
	$q = new Querries();
	$db = $q->getDB();
	$key = md5($user->passwd.$user->username."PROUDTOBEACSURAM".$user->email.	$user->username.$ip);
	$db->query(sprintf($q->ADD_CHANGE_REQUEST, $username,$key, $ip ));
	$emailAddress = getUser($username)->email;
	mail($emailAddress,"Password  Message!","Please copy and paste the following link into your browser to change your password: http://www.cs.colostate.edu/~rbpeters/project3/chpasswd.php?username=$username&key=$key");
	
}

function getPWchangeIP($username) {
	$q = new Querries();
	$db = $q->getDB();
	$res = $db->query(sprintf($q->GET_PW_CHANGE_IP, $username));
	$array = $res->fetchArray();
	if(!($res instanceof Sqlite3Result))
	{
		return "not an ip address";
	}
	return $array['ipaddress'];
}

function changePassword($username, $newPassword) {
	$q = new Querries();
	$db = $q->getDB();
	$res = $db->query(sprintf($q->CHANGE_PASSWORD, $newPassword, $username));
	if(!($res instanceof Sqlite3Result))
	{
		return false;
	}
	$res = $db->query(sprintf($q->REMOVE_CHANGE_REQUEST, $username));
	$db->close();
	return true;
}

function getAllUsersToBeApproved()
{
	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query($q->GET_AUTHENTICATED_PANDING);
	if(!($array instanceof Sqlite3Result))
	{
		return array();
	}
	$unames = array();
	while($res1 = $array->fetchArray())
	{
		$temp = getUser($res1["username"]);
		array_push($unames,$temp);
	}
	$db->close();
	return $unames;
}

function isPendingUser($username) {
	$q= new Querries();
	$db = $q->getDB();
	$res = $db->query(sprintf($q->IS_PENDING_USER, $username));
	if(!($res instanceof Sqlite3Result))
	{
		return false;
	}
	else {
		$array = $res->fetchArray();
		if ($array) {
			return true;
		}
		else {
			return false;
		}
	}
}

function authenticateNewUser($username, $key, $ip) {
	//checks that the given key and IP match the key and IP stored in the DB
	//authenticates the user and notifies the admins for approval
	//returns TRUE on success, else returns FALSE

	$q = new Querries();
	$db = $q->getDB();
	$array=$db->query(sprintf($q->AUTHENTICATE_NEW_USER, $key, $ip, $username));
		
		if(!($array instanceof Sqlite3Result))
		{	
			return FALSE;
		}
		if(!$array)
		{	
			return FALSE;
		}
		$ctr=0;
		while($res=$array->fetchArray())
		{
			$ctr+=1;
			
		}	
		if($ctr!=1)
		{
			
			return false;
		}
	return authenticatePendingUser($username);
	
}

function authenticatePendingUser($uname)
{
	$q = new Querries();
	$db = $q->getDB();
	$res = $db->query(sprintf($q->AUTH_PENDING_USER, $uname));
	if(!($res instanceof Sqlite3Result))
	{	
			return FALSE;
	}
	$db->close();
	return true;
}

function approveNewUser($uname)
{
	//this method is to be used after a user has been authenticated
	//it changes their status in the database to approved and sends
	//them an email saying they've been approved.
	$q = new Querries();
	$db = $q->getDB();
	$res = $db->query(sprintf($q->REMOVE_PENDING_USER, $uname));
	if(!($res instanceof Sqlite3Result))
	{	
			return FALSE;
	}
	$db->close();
	$emailAddress = getUser($uname)->email;
	mail($emailAddress,"User Accepted!","A user named $uname has been approved by the admins addition to the colostatebook family!");
	return true;
}

function saveUser($user)
{
	$q = new Querries();
	$db = $q->getDB();
	if(!($user instanceof User))
	{
		echo "THROW ALL THE F($%ING ERRORS";
		return;
	}
	
	$db->query(sprintf($q->UPDATE_USER, $user->passwd, $user->name, $user->gender, $user->phone, $user->email, $user->admin, $user->pic, $user->bio, $user->username));
	$db->query(sprintf($q->REMOVE_ALL_FRIENDS, $user->username));
	foreach($user->friends as $friend)
	{
			$db->query(sprintf($q->ADD_FRIEND, $user->username, $friend));
	}
	
	$db->query(sprintf($q->REMOVE_ALL_PENDING, $user->username));
	foreach($user->pending as $pending)
	{
			$db->query(sprintf($q->ADD_REQUEST,$pending,$user->username));
	}
	
	$db->close();
	
}

function getUsersWallPosts($user)
{
	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query(sprintf($q->GET_USER_WALL_COMMENTS, $user->username));
	$posts = array();
	while($res = $array->fetchArray())
	{
		$tempDB = $q->getDB();
		$replyRes = $tempDB->query();
		$res2=$db->query(sprintf($q->GET_POST_REPLY, $res["id"]));
		$temp = new Post($res['id'],$res["messageType"], $res["sender"], $res["reciever"], $res["timeStamp"],$res["message"], $res["username"],  $res2["repliedTo"]);
		array_push($posts,$temp);
	}
	$db->close();
	return $posts;
}

function getPostsOnUserWall($user)
{
	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query(sprintf($q->GET_COMMENTS_ON_USER_WALL, $user->username));
	$posts = array();
	while($res = $array->fetchArray())
	{
		$topLevelPost = new Post($res['id'],$res["messageType"], $res["sender"], $res["reciever"], $res["time"],$res["message"],"");
		array_push($posts,$topLevelPost);

	}
	$db->close();
	sortWallPosts($posts);
	return $posts;
}

function sortWallPosts($posts)
{
	$sorted=array();
	for($i=0; $i<count($posts); ++$i)
	{
	    if($posts[$i]->repliedTo=="")
	    {	
		$sorted[]=$posts[$i];
		$indexOfLowest=0;
		while($indexOfLowest!=-1)
		{
			$indexOfLowest=-1;
			for($j=0; $j<count($posts); ++$j)
			{
				if($posts[$j]->repliedTo==$posts[$j] and ($indexOfLowest==-1 or $posts[$j]->Id < $posts[$indexOfLowest]->Id))
				{
					$indexOfLowest=$j;
				}
			}
			if(!$indexOfLowest==-1)
			{
				$sorted[]=$posts[$indexOfLowest];
				unset($posts[$indexOfLowest]);
				$posts=array_values($posts);
				--$j;
			}
		}
		unset($posts[$i]);
		$posts=array_values($posts);
		--$i;
	    }
	}
}
function savePost($post)
{
	$q = new Querries();
	$db = $q->getDB();
	$messageType;
	if($post->repliedTo=="")
	{
		$messageType="NEW";
	}
	else
	{
		$messageType="REPLY";
	}
	
	//$db->query("insert into communications (messageType, sender, reciever, time, message) values ('$messageType','$post->sender','$post->reciever',datetime(current_timestamp,'localtime'),'$post->message');");
	$db->query(sprintf($q->SAVE_POST, $messageType, $post->sender, $post->reciever, $post->message));
	if($messageType=="REPLY")
	{
		$db->query(sprintf($q->SAVE_POST_REPLY, $post->sender, $post->timeStamp, $post->repliedTo));
	}

	$db->close();
}

function requestRegisterAuthentication($username, $email, $password, $ipaddr)
{
	$user = new User();
	$user->username=$username;
	$user->passwd = saltedHash($password,$username);
	$user->name = $email;
	$user->gender="Unknown";
	$user->phone="XXX-XXX-XXXX";
	$user->email=$email;
	$user->admin="0";
	$user->pic="./images/default.jpg";
	$user->bio="Tell us something about yourself!";
	addPendingUser($user,$ipaddr);
}

// returns true if $requestor is on $requestee's pending list
function isPending($requestor, $requestee){
	$pend = getRequests($requestee);
	return in_array($requestor, $pend);
}

function removePendingFriend($pendingFriends, $usernameToRemove) {
	$newPending = array();
	foreach ($pendingFriends as $pendingFriend) {
		if ($pendingFriend != $usernameToRemove) {
			$newPending[] = $pendingFriend;
		}
	}
	return $newPending;
}

function createInitialDatabse() {
	$q = new Querries();
	$db = $q->getDB();
	$db->query($q->CREATE_DATABASE);
	$db->close();
	setupDefaultUsers();
}

function nameExists($uname) {
	$q = new Querries();
	$db = $q->getDB();
	$array = $db->query(sprintf($q->NAME_TAKEN, $uname));
	if(!($array instanceof Sqlite3Result))
	{	
		return TRUE;
	}
	if(!$array)
	{	
		return TRUE;
	}
	$ctr=0;
	while($res=$array->fetchArray())
	{
		$ctr+=1;
		
	}
	$db->close();
	if($ctr!=0)
	{
			
		return TRUE;
	}
	return FALSE;
}

function countUsersToBeApproved() {
	$q = new Querries();
	$db = $q->getDB();
	$res = $db->query($q->COUNT_TO_BE_APPROVED);
	$array = $res->fetchArray();
	return $array[0];
}

?>

