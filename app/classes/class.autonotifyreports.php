<?php

class AutoNotifyReports
{
	protected $db_conn;
	private $APPLICATION_PATH;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, false);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
        }
	}

	public function insertNotificationReport($notification_type, $subject_internal_id, $for_occurrence)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		if($this->db_conn)
		{
			$query = 'insert into AUTO_NOTIFICATIONS_REPORT (NOTIFICATION_TYPE, SUBJECT_INTERNAL_ID, FOR_OCCURRENCE, UPDATED_ON) values(?,?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isNotificationSent($notification_type, $subject_internal_id, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		$notification_type = "#EMAIL_TRIAL_EXPIRING_CHURCH#";
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from AUTO_NOTIFICATIONS_REPORT where NOTIFICATION_TYPE=? and SUBJECT_INTERNAL_ID=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}

	public function insertTrialExpiringNotifyReport($subject_internal_id, $for_occurrence)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		$notification_type = "#EMAIL_TRIAL_EXPIRING_CHURCH#";
		if($this->db_conn)
		{
			$query = 'insert into AUTO_NOTIFICATIONS_REPORT (NOTIFICATION_TYPE, SUBJECT_INTERNAL_ID, FOR_OCCURRENCE, UPDATED_ON) values(?,?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isTrialExpiringNotificationSent($subject_internal_id, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		$notification_type = "#EMAIL_TRIAL_EXPIRING_CHURCH#";
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from AUTO_NOTIFICATIONS_REPORT where NOTIFICATION_TYPE=? and SUBJECT_INTERNAL_ID=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}

	public function insertLicenseExpiringNotifyReport($subject_internal_id, $for_occurrence)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		$notification_type = "#EMAIL_LICENSE_EXPIRING_CHURCH#";
		if($this->db_conn)
		{
			$query = 'insert into AUTO_NOTIFICATIONS_REPORT (NOTIFICATION_TYPE, SUBJECT_INTERNAL_ID, FOR_OCCURRENCE, UPDATED_ON) values(?,?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isLicenseExpiringNotificationSent($subject_internal_id, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		$notification_type = "#EMAIL_LICENSE_EXPIRING_CHURCH#";
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from AUTO_NOTIFICATIONS_REPORT where NOTIFICATION_TYPE=? and SUBJECT_INTERNAL_ID=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}

	public function insertTrialExpiredNotifyReport($subject_internal_id, $for_occurrence)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		$notification_type = "#EMAIL_TRIAL_EXPIRED_CHURCH#";
		if($this->db_conn)
		{
			$query = 'insert into AUTO_NOTIFICATIONS_REPORT (NOTIFICATION_TYPE, SUBJECT_INTERNAL_ID, FOR_OCCURRENCE, UPDATED_ON) values(?,?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isTrialExpiredNotificationSent($subject_internal_id, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		$notification_type = "#EMAIL_TRIAL_EXPIRED_CHURCH#";
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from AUTO_NOTIFICATIONS_REPORT where NOTIFICATION_TYPE=? and SUBJECT_INTERNAL_ID=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}

	public function insertLicenseExpiredNotifyReport($subject_internal_id, $for_occurrence)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		$notification_type = "#EMAIL_LICENSE_EXPIRED_CHURCH#";
		if($this->db_conn)
		{
			$query = 'insert into AUTO_NOTIFICATIONS_REPORT (NOTIFICATION_TYPE, SUBJECT_INTERNAL_ID, FOR_OCCURRENCE, UPDATED_ON) values(?,?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isLicenseExpiredNotificationSent($subject_internal_id, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		$notification_type = "#EMAIL_LICENSE_EXPIRED_CHURCH#";
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from AUTO_NOTIFICATIONS_REPORT where NOTIFICATION_TYPE=? and SUBJECT_INTERNAL_ID=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}


	public function insertMonthlyRecurringPaymentReceivedReport($subject_internal_id, $for_occurrence)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		$notification_type = "#CHARGE_MONTHLY_RECURRING_BILL_OF_CHURCH#";
		if($this->db_conn)
		{
			$query = 'insert into AUTO_NOTIFICATIONS_REPORT (NOTIFICATION_TYPE, SUBJECT_INTERNAL_ID, FOR_OCCURRENCE, UPDATED_ON) values(?,?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isMonthlyRecurringPaymentReceived($subject_internal_id, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		$notification_type = "#CHARGE_MONTHLY_RECURRING_BILL_OF_CHURCH#";
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from AUTO_NOTIFICATIONS_REPORT where NOTIFICATION_TYPE=? and SUBJECT_INTERNAL_ID=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $subject_internal_id, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}
}

?>