<?php declare(strict_types=1);

namespace OsumiFramework\OFW\DB;

use \PDO;

/**
 * ODBContainer - Class to store all the opened connections to the databases and methods to create new connections or close existing ones
 */
class ODBContainer {
	private array $connections = [];

	/**
	 * Get a previously stablished connection or create a new one and store it
	 *
	 * @param string $driver Driver used to connect to the database (eg mysql, postgresql, sqlite...)
	 *
	 * @param string $host Host name where the database is
	 *
	 * @param string $user Username to connect to the database
	 *
	 * @param string $pass Password to connect to the database
	 *
	 * @param string $name Name of the database to connect to
	 *
	 * @param string $charset Charset used in the database connection
	 *
	 * @return array Connection data, array with the connection index and the PDO connection link
	 */
	public function getConnection(string $driver, string $host, string $user, string $pass, string $name, string $charset): array {
		$index = sha1($driver.$host.$user.$pass.$name.$charset);

		if (!array_key_exists($index, $this->connections)) {
			$conn = new PDO(
				$driver.':host='.$host.';dbname='.$name.';charset='.$charset,
				$user,
				$pass,
				[PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
			);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connections[$index] = $conn;
		}

		return [ 'index' => $index, 'link' => $this->connections[$index] ];
	}

	/**
	 * Get a connection by its hashed index
	 *
	 * @param string $index Hashed index of the connection
	 *
	 * @return array Connection data, array with the connection index and the PDO connection link
	 */
	public function getConnectionByIndex(string $index): array {
		if (array_key_exists($index, $this->connections)){
			return [ 'index' => $index, 'link' => $this->connections[$index] ];
		}
		else{
			return null;
		}
	}

	/**
	 * Close a connection to the database
	 *
	 * @param string $index Hashed index of the connection
	 *
	 * @return bool Returns connection was closed or not
	 */
	public function closeConnection(string $index): bool {
		if (array_key_exists($index, $this->connections)){
			$this->connections[$index] = null;
			unset($this->connections[$index]);

			return true;
		}

		return false;
	}

	/**
	 * Close all stored connections to the databases
	 *
	 * @return void
	 */
	public function closeAllConnections(): void {
		foreach ($this->connections as $index => $link){
			$this->connections[$index] = null;
			unset($this->connections[$index]);
		}
	}
}