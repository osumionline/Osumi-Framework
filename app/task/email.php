<?php
class emailTask{
	public function __toString(){
		return "email: Función para enviar emails de prueba";
	}

	private $email_list = [
		'welcome'              => 'Email que se envía a un nuevo cliente al registrarse.',
		'lost-password'        => 'Email que se envía a un cliente al solicitar una nueva contraseña.',
		'order-ok'             => 'Email que se envía a un cliente que ha hecho un pedido.',
		'payment-received'     => 'Email que se nos envía al recibir un pedido.',
		'order-shipped'        => 'Email que se envía al cliente cuando el pedido ya se ha enviado.',
		'order-address-change' => 'Email que se nos envía cuando un cliente cambia la dirección de entrega.',
		'contact'              => 'Email que se nos envía cuando un usuario escribe desde contacto.'
	];

	public function run($options=[]){
		if (count($options)==0){
			echo "\nTienes que indicar por lo menos una opción.\n\n";
			echo "  Opciones:\n";
			foreach ($this->email_list as $option => $text){
				echo "  ·  ".$option.": ".$text."\n";
			}
			echo "\nPor ejemplo: php ofw.php email welcome contact\n\n";
			exit;
		}
		foreach ($options as $option){
			if (!array_key_exists($option, $this->email_list)){
				echo "---------------------------------------\n";
				echo "ERROR: La opción ".$option." no existe.\n";
				echo "---------------------------------------\n";
				continue;
			}
			echo "Enviando ".$option.": ".$this->email_list[$option]."\n";
			switch ($option){
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

	private function sendWelcome(){

	}

	private function sendLostPassword(){

	}

	private function sendOrderOk(){

	}

	private function sendPaymentReceived(){

	}

	private function sendOrderShipped(){

	}

	private function sendOrderAddressChange(){

	}

	private function sendContact(){

	}
}