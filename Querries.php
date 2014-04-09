<?php
class Querries
{
	public CREATE_USER = "insert into users values (username=%s,password=%s,name=%s,gender=%s, phone=%s,email=%s,admin=%s,pictureLocation=%s, bio=%s);";
	public GET_USER = "Select * from users where username=%s;";
	public GET_ALL_USER_NAMES = "Select username from users;";
	public GET_USER_PASSWORD="Select password from users where username=%s;";
	public GET_USER_SUMMARY="Select summary from users where username=%s";
	public WRITE_USER_SUMMARY="Update users set summary=%s where username=%s;";
	public GET_USER_FRIENDS="Select friend from friends where user=%s";
	public IS_FRIEND="SELECT CASE WHEN EXISTS (SELECT * FROM friends WHERE user=%s and friend=%s) THEN CAST(1 AS BIT) ELSE CAST(0 AS BIT) END;";
	public GET_PENDING_REQUESTS="Select user from friendRequests where requestedFriend=%s;";
	public GET_PENDING_USERS="select username from pendingUsers;";
	public GET_USER_WALL_COMMENTS="select * from communications where sender=%s";
	public ADD_PENDING_USER="";
	public REMOVE_PENDING_USER="";
	public REMOVE_FRIEND_REQUEST="";

	function getDB()
	{
		return new SQLite3("./project3.db");
	}
}
?>
