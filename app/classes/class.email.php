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

	public function __construct($APPLICATION_PATH, $FROM_ADDRESS_ID=0)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH;
		include_once($this->APPLICATION_PATH . 'conf/config.php');
		include_once($this->APPLICATION_PATH . 'plugins/PHPMailer/class.phpmailer.php');

		//set SMTP settings
		$this->setSMTPSettings();

		//set FROM address
		$this->setFromAddress($FROM_ADDRESS_ID);
	}

	public function setSMTPSettings()
	{
		$this->host = SMTP_SERVER;
		$this->username = SMTP_USERNAME;
		$this->password = SMTP_PASSWORD;
	}

	public function setFromAddress($FROM_ADDRESS_ID)
	{
		if($FROM_ADDRESS_ID==EMAIL_FROM_SALES) {
			$this->from_address = FROM_SALES_ADDRESS;
			$this->from_name = FROM_SALES_NAME;
		} else if($FROM_ADDRESS_ID==EMAIL_FROM_SUPPORT) {
			$this->from_address = FROM_SUPPORT_ADDRESS;
			$this->from_name = FROM_SUPPORT_NAME;
		} else if($FROM_ADDRESS_ID==EMAIL_FROM_DONOTREPLY) {
			$this->from_address = FROM_DONOTREPLY_ADDRESS;
			$this->from_name = FROM_DONOTREPLY_NAME;
		} else if($FROM_ADDRESS_ID==EMAIL_FROM_INFO) {
			$this->from_address = FROM_INFO_ADDRESS;
			$this->from_name = FROM_INFO_NAME;
		} else {
			$this->from_address = FROM_INFO_ADDRESS;
			$this->from_name = FROM_INFO_NAME;
		}
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
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed";
		$mail = new PHPMailer;

		$mail->isSMTP();	// Set mailer to use SMTP
		$mail->Host = $this->host;	// Specify main and backup SMTP servers
		$mail->SMTPAuth = true;	// Enable SMTP authentication
		$mail->Username = $this->username;	// SMTP username
		$mail->Password = $this->password;	// SMTP password
		$mail->SMTPSecure = $this->smtp_secure;	// Enable encryption, 'ssl' also accepted

		$mail->From = $this->from_address;
		$mail->FromName = $this->from_name;

		//Adding To Addresses
		$to_addr_array = array();
		if(trim($this->to_address) != "") {
			$to_addr_array = explode(",", trim($this->to_address));
			for($t=0; $t < COUNT($to_addr_array); $t++)
			{
				$mail->addAddress($to_addr_array[$t]);	// Add a recipient; Name is optional
			}
		}

		//Adding Reply-To Address
		$mail->addReplyTo($this->reply_to_address, $this->reply_to_name);

		//Adding CC Addresses
		$cc_addr_array = array();
		if(trim($this->cc_address) != "") {
			$cc_addr_array = explode(",", trim($this->cc_address));
			for($c=0; $c < COUNT($cc_addr_array); $c++)
			{
				$mail->addCC($cc_addr_array[$c]);	// Add a recipient; Name is optional
			}
		}

		//Adding BCC Addresses
		$bcc_addr_array = array();
		if(trim($this->bcc_address) != "") {
			$bcc_addr_array = explode(",", trim($this->bcc_address));
			for($b=0; $b < COUNT($bcc_addr_array); $b++)
			{
				$mail->addBCC($bcc_addr_array[$b]);
			}
		}

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

		if(strpos($this->body, 'cid:cs_head_logo') > 0) {
			$mail->addEmbeddedImage($this->APPLICATION_PATH.'images/email/cs_email_head.png', 'cs_head_logo');
		}
		if(strpos($this->body, 'cid:cs_site_text_logo') > 0) {
			$mail->addEmbeddedImage($this->APPLICATION_PATH.'images/email/cs-website-text.png', 'cs_site_text_logo');
		}
		if(strpos($this->body, 'cid:cs_vertical_stripe') > 0) {
			$mail->addEmbeddedImage($this->APPLICATION_PATH.'images/email/vertical_stripe.gif', 'cs_vertical_stripe');
		}
		if(strpos($this->body, 'cid:cs_horizontal_stripe') > 0) {
			$mail->addEmbeddedImage($this->APPLICATION_PATH.'images/email/horizontal_stripe.gif', 'cs_horizontal_stripe');
		}

		if(!$mail->send()) {
			//TODO: Log the success/failure
			$to_return[0] = 0;
			$to_return[1] = "Message sending failed with error : ".$mail->ErrorInfo;
			//echo 'Message could not be sent.';
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			$to_return[0] = 1;
			$to_return[1] = "Message has been sent";
			//echo 'Message has been sent';
		}

		return $to_return;
	}
}

?>