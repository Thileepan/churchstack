<?php
session_start();
@include_once $APPLICATION_PATH .'classes/class.system.php';
if($_SERVER['REQUEST_URI'] == "/app/conf/config.php" || $_SERVER['REQUEST_URI'] == "/conf/config.php") {
	$APPLICATION_PATH = "../";
	@require $APPLICATION_PATH.'error/404';
	exit;
}
//app configurations
define('APP_VERSION', '1.2');
define('APP_BUILD_NUMBER', '20141118');
define('PRODUCT_NAME', 'ChurchStack');
define('PRODUCT_WEBSITE', 'http://www.ChurchStack.com');
define('CS_LOGIN_WEBSITE', 'https://account.churchstack.com');

//Website URLs
define('REFERRAL_PROGRAM_URL', 'http://www.churchstack.com/referral-program.html');
define('FORGOT_PASSWORD_URL', 'https://account.churchstack.com/user/forgotpwd');
define('PRICING_URL', 'http://www.churchstack.com/pricing.html');
define('PRODUCT_CONTACT_US', 'http://www.churchstack.com/contact-us.html');
define('PRODUCT_PRIV_POLICY_EMAIL', "support@churchstack.com");
define('PRODUCT_PRIV_POLICY_URL', "http://www.churchstack.com/privacy-policy.html");
define('PRODUCT_TERMS_CONDITIONS_URL', "http://www.churchstack.com/terms-of-service.html");
define('TWILIO_HELP_DOC_URL', "http://www.churchstack.com/help/twilio-configuration.html");
define('NEXMO_HELP_DOC_URL', "http://www.churchstack.com/help/nexmo-configuration.html");
define('BHASHSMS_HELP_DOC_URL', "http://www.churchstack.com/help/bhashsms-configuration.html");

//Company Related stuff
define('COMPANY_WEBSITE', 'http://www.rapydcloud.com');
define('COMPANY_FULL_NAME', 'RapydCloud Technologies Pvt Ltd');
define('COMPANY_SHORT_NAME', 'RapydCloud Technologies');
define('COMPANY_FULL_ADDRESS', "RapydCloud Technologies Pvt Ltd., Pallavaram, Chennai-600 043, Tamil Nadu, India");
define('COMPANY_CONTACT_US', 'http://www.rapydcloud.com/contact-us.html');

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

//SMTP stuff
define('SMTP_SERVER', 'email-smtp.us-west-2.amazonaws.com');
define('SMTP_USERNAME', 'AKIAJNVL2WHRTLOP7HVA');
define('SMTP_PASSWORD', 'AqJ57BW8cmXKiMqaey1Hu7gGiUhCeXYbpJFtmnU2TOXA');

define('SALES_SMTP_SERVER', 'smtp.gmail.com');
define('SALES_SMTP_USERNAME', 'support@churchstack.com');
define('SALES_SMTP_PASSWORD', 'churchstack123$');

define('SUPPORT_SMTP_SERVER', 'email-smtp.us-west-2.amazonaws.com');
define('SUPPORT_SMTP_USERNAME', 'AKIAJNVL2WHRTLOP7HVA');
define('SUPPORT_SMTP_PASSWORD', 'AqJ57BW8cmXKiMqaey1Hu7gGiUhCeXYbpJFtmnU2TOXA');

define('DONOTREPLY_SMTP_SERVER', 'email-smtp.us-west-2.amazonaws.com');
define('DONOTREPLY_SMTP_USERNAME', 'AKIAJNVL2WHRTLOP7HVA');
define('DONOTREPLY_SMTP_PASSWORD', 'AqJ57BW8cmXKiMqaey1Hu7gGiUhCeXYbpJFtmnU2TOXA');

define('INFO_SMTP_SERVER', 'smtp.gmail.com');
define('INFO_SMTP_USERNAME', 'support@churchstack.com');
define('INFO_SMTP_PASSWORD', 'churchstack123$');

define('NOTIFICATIONS_SMTP_SERVER', 'email-smtp.us-west-2.amazonaws.com');
define('NOTIFICATIONS_SMTP_USERNAME', 'AKIAJNVL2WHRTLOP7HVA');
define('NOTIFICATIONS_SMTP_PASSWORD', 'AqJ57BW8cmXKiMqaey1Hu7gGiUhCeXYbpJFtmnU2TOXA');

//Email addresses
define('FROM_ADDRESS', 'support@churchstack.com');
define('FROM_NAME', 'ChurchStack');
define('FROM_SALES_ADDRESS', 'support@churchstack.com');
define('FROM_SALES_NAME', 'ChurchStack Sales');
define('FROM_SUPPORT_ADDRESS', 'support@churchstack.com');
define('FROM_SUPPORT_NAME', 'ChurchStack Support');
define('FROM_DONOTREPLY_ADDRESS', 'do-not-reply@churchstack.com');
define('FROM_DONOTREPLY_NAME', 'ChurchStack - Do Not Reply');
define('FROM_INFO_ADDRESS', 'support@churchstack.com');
define('FROM_INFO_NAME', 'ChurchStack');
define('FROM_NOTIFICATIONS_ADDRESS', 'notifications@churchstack.com');
define('FROM_NOTIFICATIONS_NAME', 'ChurchStack - Notifications');
define('SUPPORT_EMAIL', 'support@churchstack.com');
define('SALES_EMAIL', 'support@churchstack.com');
define('DONOTREPLY_EMAIL', 'do-not-reply@churchstack.com');
define('INVOICE_COPY_TO_ADDRESS', 'support@churchstack.com');
define('NEW_SIGNUP_COPY_TO_ADDRESS', 'support@churchstack.com,bala.d7868@gmail.com,shijuchintu@gmail.com,sktgthill@gmail.com,nesanjoseph@yahoo.com');

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
define('SESSION_DATA_REFRESH_SECONDS', 300);

//PAYPAL CREDENTIALS
define('USE_SANDBOX', 1);
define('PAYPAL_USERNAME', 'sktgthill-facilitator_api1.gmail.com');
define('PAYPAL_PASSWORD', '1408191544');
define('PAYPAL_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31AyiS2qsbPAQ2oNIRwagSiYeILWk2');
define('PAYPAL_RETURN_URL', 'info?paypal=paid');
define('PAYPAL_CANCEL_URL', 'info?paypal=cancel');
define('PAYPAL_NOTIFY_URL', 'info?paypal=notify');
define("PAYPAL_DEBUG", 1);
define("IPN_LOG_FILE", "plugins/paypal/ipn/ipn.log");
define("PAYPAL_USE_SSL", 1);
define("PAYPAL_SANDBOX_URL" , "www.sandbox.paypal.com");
define("PAYPAL_LIVE_URL" , "www.paypal.com");

?>