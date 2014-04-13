<?php
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
	public $bio; //user summary;
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
?>
