<?php declare(strict_types=1);

namespace OsumiFramework\App\Task;

use OsumiFramework\OFW\Core\OTask;

class emailTask extends OTask {
	/**
	 * Returns task's name
	 */
	public function __toString() {
		return 'email: Task to send test emails';
	}

	private array $email_list = [
		'welcome'              => 'Email sent to new customers on registration.',
		'lost-password'        => 'Email sent to a customer that requests a new password.',
		'order-ok'             => 'Email sent to a customer when a purchase is done.',
		'payment-received'     => 'Email sent to a customer when the payment of the purchase is received.',
		'order-shipped'        => 'Email sent to a customer when the order is shipped.',
		'order-address-change' => 'Email sent to a customer when the shipping address is changed.',
		'contact'              => 'Email sent to us when a customer uses the contact form.'
	];

	/**
	 * Runs the task, checks given parameters and sends the appropiate email
	 *
	 * @param array Parameter list given to the task
	 *
	 * @return void
	 */
	public function run(array $options=[]): void {
		if (count($options)==0) {
			echo "\nYou have to choose (at least) an option.\n\n";
			echo "  Options:\n";
			foreach ($this->email_list as $option => $text) {
				echo "  ·  ".$option.": ".$text."\n";
			}
			echo "\Eg: ofw email welcome contact\n\n";
			exit;
		}
		foreach ($options as $option) {
			if (!array_key_exists($option, $this->email_list)) {
				echo "---------------------------------------\n";
				echo "ERROR: Option ".$option." does not exist.\n";
				echo "---------------------------------------\n";
				continue;
			}
			echo "Sending ".$option.": ".$this->email_list[$option]."\n";
			switch ($option) {
				case 'welcome': $this->sendWelcome();
				case 'lost-password': $this->sendLostPassword();
				case 'order-ok': $this->sendOrderOk();
				case 'payment-received': $this->sendPaymentReceived();
				case 'order-shipped': $this->sendOrderShipped();
				case 'order-address-change': $this->sendOrderAddressChange();
				case 'contact': $this->sendContact();
			}
		}
	}

	private function sendWelcome(): void {}

	private function sendLostPassword(): void {}

	private function sendOrderOk(): void {}

	private function sendPaymentReceived(): void {}

	private function sendOrderShipped(): void {}

	private function sendOrderAddressChange(): void {}

	private function sendContact(): void {}
}