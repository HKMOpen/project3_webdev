<?php
class Post
{
	public $Id;
	public $messageType;
	public $sender;
	public $reciever;
	public $timeStamp;
	public $message;
	public $repliedTo;

	function Post($Id, $messageType, $sender, $reciever, $timeStamp, $message, $repliedTo)
	{
		$this->Id = $Id;
		$this->messageType = $messageType;
 		$this->sender=$sender;
		$this->reciever=$reciever; 
		$this->timeStamp=$timeStamp; 
		$this->message=$message; 
		$this->repliedTo=$repliedTo;
	}
}
?>
