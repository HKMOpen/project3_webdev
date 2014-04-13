<?php
class Post
{
	public $messageType;
	public $sender;
	public $reciever;
	public $timeStamp;
	public $message;
	public $repliedTo;

	public Post($messageType, $sender, $reciever, $timeStamp, $message, $repliedTo)
	{
		$this->messageType = $messageType;
 		$this->sender=$sender;
		$this->reciever=$reciever; 
		$this->timeStamp=$timeStamp; 
		$this->message=$message; 
		$this->repliedTo=$repliedTo;
	}

?>
