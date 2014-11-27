<?php

class PayPal
{
	public function __construct($APPLICATION_PATH)
	{
//		error_reporting(E_ALL);
//		ini_set("display_errors", "On");
		$this->APPLICATION_PATH = $APPLICATION_PATH;
		include_once($this->APPLICATION_PATH. 'conf/config.php');
		require_once( $this->APPLICATION_PATH. 'plugins/paypal/lib/paypal-digital-goods.class.php' );
		//require_once( $this->APPLICATION_PATH. 'plugins/paypal/lib/paypal-subscription.class.php' );
		require_once( $this->APPLICATION_PATH. 'plugins/paypal/lib/paypal-purchase.class.php' );
	}

	private function setCredentials()
	{
		PayPal_Digital_Goods_Configuration::username( PAYPAL_USERNAME );
		PayPal_Digital_Goods_Configuration::password( PAYPAL_PASSWORD );
		PayPal_Digital_Goods_Configuration::signature( PAYPAL_SIGNATURE );

		PayPal_Digital_Goods_Configuration::return_url( $this->getScriptURI( PAYPAL_RETURN_URL ) );
		PayPal_Digital_Goods_Configuration::cancel_url( $this->getScriptURI( PAYPAL_CANCEL_URL ) );
		PayPal_Digital_Goods_Configuration::business_name( COMPANY_FULL_NAME );

		PayPal_Digital_Goods_Configuration::notify_url( $this->getScriptURI( PAYPAL_NOTIFY_URL ) );

		if(!USE_SANDBOX) {
			PayPal_Digital_Goods_Configuration::environment( 'live' );
		}		

		if( PayPal_Digital_Goods_Configuration::username() == 'your_api_username' || PayPal_Digital_Goods_Configuration::password() == 'your_api_password' || PayPal_Digital_Goods_Configuration::signature() == 'your_api_signature' )
			exit( 'You must set your API credentials' );
	}

	
	/**
	 * Creates and returns a PayPal DG Purchase Object
	 */
	public function setPurchaseDetails() {

		$this->setCredentials();

		$purchase_details = array(
			'name'        => 'Digital Good Purchase Example',
			'description' => 'Example Digital Good Purchase',
			'amount'      => '15.50',
			'tax_amount'  => '2.50',
			'items'       => array(
				array( // First item
					'item_name'        => 'First item name',
					'item_description' => 'This is a description of the first item in the cart, it costs $9.00',
					'item_amount'      => '10.00',
					'item_tax'         => '1.00',
					'item_quantity'    => 1,
					'item_number'      => 'XF100',
				),
				array( // Second item
					'item_name'        => 'Second Item',
					'item_description' => 'This is a description of the SECOND item in the cart, it costs $1.00 but there are 3 of them.',
					'item_amount'      => '1.00',
					'item_tax'         => '0.50',
					'item_quantity'    => 3,
					'item_number'      => 'XJ100',
				),
			)
		);

		return new PayPal_Purchase( $purchase_details );
	}

	/**
	 * Helper function to get the URI for the script
	 */
	public function getScriptURI( $script = 'index.php' ){
		// IIS Fix
		if( empty( $_SERVER['REQUEST_URI'] ) )
			$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];

		// Strip off query string
		$url = preg_replace( '/\?.*$/', '', $_SERVER['REQUEST_URI'] );
		//$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.ltrim(dirname($url), '/').'/';
		$url = 'http://'.$_SERVER['HTTP_HOST'].implode( '/', ( explode( '/', $_SERVER['REQUEST_URI'], -2 ) ) ) . '/';

		return $url . $script;
	}
}

?>