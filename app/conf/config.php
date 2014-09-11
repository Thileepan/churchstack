<?php
session_start();
@include_once $APPLICATION_PATH .'classes/class.system.php';
if($_SERVER['REQUEST_URI'] == "/app/conf/config.php" || $_SERVER['REQUEST_URI'] == "/conf/config.php") {
	$APPLICATION_PATH = "../";
	@require $APPLICATION_PATH.'error/404';
	exit;
}
//app configurations
define('APP_VERSION', '1.0');
define('APP_BUILD_NUMBER', '20140118');
define('PRODUCT_NAME', 'ChurchStack');
define('PRODUCT_WEBSITE', 'http://www.ChurchStack.com');
define('CS_LOGIN_WEBSITE', 'https://account.churchstack.com');
define('REFERRAL_PROGRAM_URL', 'http://www.churchstack.com/ref/referralpg.html');
define('FORGOT_PASSWORD_URL', 'https://account.churchstack.com/user/forgotpwd');
define('PRICING_URL', 'http://www.churchstack.com/pricing.html');
define('PRODUCT_CONTACT_US', 'http://www.churchstack.com/contactus');
define('PRODUCT_PRIV_POLICY_EMAIL', "support@churchstack.com");

//Company Related stuff
define('COMPANY_WEBSITE', 'http://www.rcloudtechnologies.com');
define('COMPANY_FULL_NAME', 'RCloud Technologies Pvt Ltd.');
define('COMPANY_SHORT_NAME', 'RCloud Technologies');
define('COMPANY_FULL_ADDRESS', "RCloud Technologies Pvt Ltd., Pallavaram, Chennai-600 043, Tamil Nadu, India");
define('COMPANY_CONTACT_US', 'http://www.rcloudtechnologies.com/contactus');

//mysql database information
define('APPLICATION_PATH', dirname(dirname(_FILE_)));
define('DB_DSN', 'churchstack');
define('DB_USER_NAME', 'root');
define('DB_PASSWORD', 'admin');
define('DB_NAME', 'churchstack');

//email configurations
define('EMAIL_FROM_SALES', 1);
define('EMAIL_FROM_SUPPORT', 2);
define('EMAIL_FROM_DONOTREPLY', 3);
define('EMAIL_FROM_INFO', 4);
define('EMAIL_FROM_NOTIFICATIONS', 5);

define('SMTP_SERVER', 'smtp.gmail.com');
define('SMTP_USERNAME', 'help@churchstack.com');
define('SMTP_PASSWORD', 'churchstack123$');
define('FROM_ADDRESS', 'help@churchstack.com');
define('FROM_NAME', 'ChurchStack');
define('FROM_SALES_ADDRESS', 'sales@churchstack.com');
define('FROM_SALES_NAME', 'ChurchStack.com');
define('FROM_SUPPORT_ADDRESS', 'support@churchstack.com');
define('FROM_SUPPORT_NAME', 'ChurchStack.com');
define('FROM_DONOTREPLY_ADDRESS', 'do-not-reply@churchstack.com');
define('FROM_DONOTREPLY_NAME', 'ChurchStack.com');
define('FROM_INFO_ADDRESS', 'info@churchstack.com');
define('FROM_INFO_NAME', 'ChurchStack.com');
define('FROM_NOTIFICATIONS_ADDRESS', 'notifications@churchstack.com');
define('FROM_NOTIFICATIONS_NAME', 'ChurchStack Notification');
define('SUPPORT_EMAIL', 'support@churchstack.com');
define('SALES_EMAIL', 'sales@churchstack.com');
define('INVOICE_COPY_TO_ADDRESS', 'help@churchstack.com');
define('NEW_SIGNUP_COPY_TO_ADDRESS', 'help@churchstack.com');

if (System::getOS() == 2) {
    define('PHP_EXE_PATH', 'C:/Program Files (x86)/php/php.exe');
} else {
   define('PHP_EXE_PATH', 'php');
}

//MENU SETTINGS
define('SHOW_DASHBOARD_MENU', 1);
define('SHOW_PROFILE_MENU', 1);
define('SHOW_SUBSCRIPTION_MENU', 0);
define('SHOW_FUNDS_MENU', 1);
define('SHOW_EVENTS_MENU', 1);
define('SHOW_GROUPS_MENU', 1);
define('SHOW_SETTINGS_MENU', 1);
define('SHOW_REPORTS_MENU', 1);

define('IDLE_SECONDS_LOGOUT', 3600);
define('SESSION_DATA_REFRESH_SECONDS', 3600);

?>