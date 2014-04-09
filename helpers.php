<?php
include_once "Querries.php";
include_once "User.php";

define("RANDOM_32_CHAR_KEY", substr(md5("random"), 0, 31).'~');

function makeNewUser($uname, $pass, $name, $sex, $number, $mail, $privileges, $picture, $friendList=null, $pendingList=null, $bio) {
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
	$u->bio = $bio;
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
	$q = new Querries();
	$db = $q->getDB();
	for($users as $user)
	{
		$db->querry(sprintf($q->CREATE_USER, $user->username, $user->passwd, $user->name, $user->gender, $user->phone, $user->mail, $user->admin, $user->pic, $user->bio));
	}
	$db->close();
}

function addPendingUser($user)
{
	$users = array();
	$users[0]=$user;
	writeUsers($user);
	$q = new Querries();
	$db = $q->getDB();
	$db->querry(sprintf($q->ADD_PENDING_USER, $user->username));
	$db->close();
}	

function readUsers() {
	$q = new Querries();
	$db = $q->getDB();
	$array = $db->querry($q->GET_ALL_USER_NAMES);
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
	$array = $db->querry(sprintf($q->GET_USER, $uname));
	if(!($array instanceof Sqlite3Result))
	{
		return;
	}
	$res = $array->fetchArray()
  	$user = makeNewUser($res["username"],$res["password"],$res["name"],$res["gender"],$res["phone"],$res["email"],$res["admin"],$res["pictureLocation"], null, null, $res["bio"]);
	$db->close();
	$user->friends = getFriends($uname);
	$user->pending = getRequestUsers($uname);

	return $user;
}


function getHash($uname) {
	
	getUser($uname);
	return getUser($uname)->passwd;
}

function getUserSummary($username) {
	getUser($username)->summary
}

function readUserSummaries()
{
	$userlist = readUsers();
	$retVal = array();
	for($userlist as $user)
	{
		bio = new UserSummary()
		bio->username=$res["username"];
		bio->summary =$res["bio"];
		array_push($retVal, bio);
	}
	return $retVal;
}

function writeUserSummaries($userSummaries) {
	$q = new Querries();
	$db = $q->getDB();
	for($userSummaries as $bio)
	{
		$array = $db->querry(sprintf($q->WRITE_USER_SUMMARY, $bio->summary, $bio->username));	
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
	$array = $db->querry(sprintf($q->GET_USER_FRIENDS, $uname));
	if(!($array instanceof Sqlite3Result))
	{
		return array();
	}
	$friends=array();
	while($res = $array->fetchArray())
	{
		$user = getUser($res["friend"]);
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
	$array = $db->querry(sprintf($q->GET_PENDING_REQUESTS, $uname));
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
	$res = $db->querry(sprintf($q->IS_FRIEND, $u1, $u2));
	if(!($res instanceof Sqlite3Result))
	{
		return array();
	}
	$friends=$res[0];
	$db->close();
	return $friends;
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

// returns true if $requestor is on $requestee's pending list
function isPending($requestor, $requestee){
	$pend = getRequests($requestee);
	return in_array($requestor, $pend);
}

?>
