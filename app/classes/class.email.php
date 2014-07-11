<?php

class Email
{
	private $is_smtp = true;
	private $is_html = true;
	private $host = '';
	private $username = '';
	private $password = '';
	private $smtp_secure = 'tls';
	private $from_address = '';
	private $from_name = '';
	private $to_address = '';
	private $reply_to_address = '';
	private $reply_to_name = '';
	private $cc_address = '';
	private $bcc_address = '';
	private $subject = '';
	private $body = '';
	private $attachments = array();
	private $APPLICATION_PATH;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH;
		include_once($this->APPLICATION_PATH . 'conf/config.php');
		include_once($this->APPLICATION_PATH . 'plugins/PHPMailer/class.phpmailer.php');

		//set SMTP settings
		$this->setSMTPSettings();

		//set FROM address
		$this->setFromAddress();
	}

	public function setSMTPSettings()
	{
		$this->host = SMTP_SERVER;
		$this->username = SMTP_USERNAME;
		$this->password = SMTP_PASSWORD;
	}

	public function setFromAddress()
	{
		$this->from_address = FROM_ADDRESS;
		$this->from_name = FROM_NAME;
	}

	public function setRecipients($recipients)
	{
		$this->to_address = $recipients['to_address'];
		$this->reply_to_address = $recipients['reply_to_address'];
		$this->reply_to_name = $recipients['reply_to_name'];
		$this->cc_address = $recipients['cc_address'];
		$this->bcc_address = $recipients['bcc_address'];
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	public function setBody($body)
	{
		$this->body = $body;
	}

	public function setAttachments($attachments)
	{
		$this->attachments[] = $attachments;
	}

	public function sendEmail()
	{
		$mail = new PHPMailer;

		$mail->isSMTP();	// Set mailer to use SMTP
		$mail->Host = $this->host;	// Specify main and backup SMTP servers
		$mail->SMTPAuth = true;	// Enable SMTP authentication
		$mail->Username = $this->username;	// SMTP username
		$mail->Password = $this->password;	// SMTP password
		$mail->SMTPSecure = $this->smtp_secure;	// Enable encryption, 'ssl' also accepted

		$mail->From = $this->from_address;
		$mail->FromName = $this->from_name;
		$mail->addAddress($this->to_address);	// Add a recipient; Name is optional
		$mail->addReplyTo($this->reply_to_address, $this->reply_to_name);
		$mail->addCC($this->cc_address);
		$mail->addBCC($this->bcc_address);

		$mail->WordWrap = 50;	// Set word wrap to 50 characters

		if($this->attachments > 0)
		{
			$total_attachments = COUNT($this->attachments);
			for($i=0; $i<$total_attachments; $i++)
			{
				$mail->addAttachment($this->attachments[$i]);
			}
		}		

		$mail->isHTML($this->is_html);
		$mail->Subject = $this->subject;
		$mail->Body    = $this->body;
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$mail->send()) {
			//TODO: Log the success/failure
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			echo 'Message has been sent';
		}
	}
}

?>