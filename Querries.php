<?php
class Querries
{
	public $CREATE_USER = "insert into users values ('%s','%s','%s','%s', '%s','%s','%s','%s','%s');";
	
	public $UPDATE_USER = "update users set password='%s',name='%s',gender='%s', phone='%s',email='%s',admin='%s',pictureLocation='%s', bio='%s' where username='%s';";
	public $GET_USER = "Select * from users where username='%s';";
	
	public $GET_ALL_USER_NAMES = "Select username from users;";
	
	public $GET_USER_PASSWORD="Select password from users where username='%s';";
	public $GET_USER_SUMMARY="Select bio from users where username='%s'";
	public $WRITE_USER_SUMMARY="Update users set bio='%s' where username='%s';";
	public $GET_USER_FRIENDS="Select friend from friends where user='%s'";
	
	public $IS_FRIEND="SELECT CASE WHEN EXISTS (SELECT * FROM friends WHERE user=%s and friend=%s) THEN CAST(1 AS BIT) ELSE CAST(0 AS BIT) END;";
	
	public $GET_PENDING_REQUESTS="Select user from friendRequests where requestedFriend='%s';";
	
	public $GET_PENDING_USERS="select username from pendingUsers where authenticated='FALSE';";
	
	public $GET_USER_WALL_COMMENTS="select * from communications where sender='%s';";
	
	public $GET_AUTHENTICATED_PANDING="select username from pendingUsers where authenticated='TRUE';";
	
	public $ADD_PENDING_USER="INSERT INTO pendingUsers values '%s','%s','%s','%s';";
	
	public $AUTH_PENDING_USER="UPDATE pendingUsers set authenticated='TRUE' where username='%s';";
	
	public $REMOVE_PENDING_USER="remove from pendingUsers where username='%s';";
	
	public $REMOVE_FRIEND_REQUEST="remove from friend requests where user='%s';";
	
	public $ADD_FRIEND = "insert into friends values '%s','%s';";
	
	public $REMOVE_ALL_FRIENDS ="REMOVE from friends where user='%s';";
	
	public $REMOVE_ALL_PENDING ="REMOVE from friendRequests where requestedFriend='%s';";

	public $ADD_REQUEST = "insert into friendRequests values '%s','%s'";
	
	public $GET_REPLIES = "select * from communications where id in (select repliedTo from commentReplies where replyId='%s');";

	function getDB()
	{
		return new SQLite3("./project3.db");
	}
}
?>
