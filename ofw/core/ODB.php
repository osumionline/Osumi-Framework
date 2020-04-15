<?php declare(strict_types=1);
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

/**
 * ODB - Class to interact with the database
 */
class ODB {
	private string        $driver           = 'mysql';
	private ?string       $host             = null;
	private ?string       $user             = null;
	private ?string       $pass             = null;
	private ?string       $name             = null;
	private ?string       $charset          = 'UTF8';
	private ?PDO          $link             = null;
	private ?string       $connection_index = null;
	private ?PDOStatement $stmt             = null;
	private ?int          $fetch_mode       = null;
	private ?string       $last_query       = null;

	/**
	 * Get connection configuration on startup (if given, else get it from the application global configuration)
	 *
	 * @param string $user Username to connect to the database
	 *
	 * @param string $pass Password to connect to the database
	 *
	 * @param string $host Host name where the database is
	 *
	 * @param string $name Name of the database to connect to
	 */
	function __construct(string $user='', string $pass='', string $host='', string $name='') {
		global $core;
		if (empty($user) ||empty($pass) ||empty($host) ||empty($name) ) {
			$this->setDriver( $core->config->getDB('driver') );
			$this->setHost( $core->config->getDB('host') );
			$this->setUser( $core->config->getDB('user') );
			$this->setPass( $core->config->getDB('pass') );
			$this->setName( $core->config->getDB('name') );
			$this->setCharset( $core->config->getDB('charset') );
		}
		else {
			$this->setHost( $host );
			$this->setUser( $user );
			$this->setPass( $pass );
			$this->setName( $name );
		}
	}

	/**
	 * Set the connection driver
	 *
	 * @param string $d Driver name (eg mysql, postgresql, sqlite...)
	 *
	 * @return void
	 */
	public function setDriver(string $d): void {
		$this->driver = $d;
	}

	/**
	 * Get the connection driver
	 *
	 * @return string Driver name
	 */
	public function getDriver(): ?string {
		return $this->driver;
	}

	/**
	 * Set the hostname of the database
	 *
	 * @param string Hostname of the database
	 *
	 * @return void
	 */
	public function setHost(string $h): void {
		$this->host = $h;
	}

	/**
	 * Get the hostname of the database
	 *
	 * @return string Hostname of the database
	 */
	public function getHost(): ?string {
		return $this->host;
	}

	/**
	 * Set the username to connect to the database
	 *
	 * @param string $u Username to stablish a connection
	 *
	 * @return void
	 */
	public function setUser(string $u): void {
		$this->user = $u;
	}

	/**
	 * Get the username to connect to the database
	 *
	 * @return string Username to stablish a connection
	 */
	public function getUser(): ?string {
		return $this->user;
	}

	/**
	 * Set the password to connect to the database
	 *
	 * @param string $p Password to stablish a connection
	 *
	 * @return void
	 */
	public function setPass(string $p): void {
		$this->pass = $p;
	}

	/**
	 * Get the password to connect to the database
	 *
	 * @return string Password to stablish a connection
	 */
	public function getPass(): ?string {
		return $this->pass;
	}

	/**
	 * Set the name of the database to connect to
	 *
	 * @param string $n Name of the database
	 *
	 * @return void
	 */
	public function setName(string $n): void {
		$this->name = $n;
	}

	/**
	 * Get the name of the database to connect to
	 *
	 * @return string Name of the database
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * Set the charset used in the queries to the database
	 *
	 * @param string $c Charset used in the queries
	 *
	 * @return void
	 */
	public function setCharset(string $c): void {
		$this->charset = $c;
	}

	/**
	 * Get the charset used in the queries to the database
	 *
	 * @return string Charset used in the queries
	 */
	public function getCharset(): ?string {
		return $this->charset;
	}

	/**
	 * Set a connection link to the database
	 *
	 * @param PDO $l PDO link connection to the database
	 *
	 * @return void
	 */
	public function setLink(PDO $l): void {
		$this->link = $l;
	}

	/**
	 * Get a connection link to the database
	 *
	 * @return PDO PDO link connection to the database
	 */
	public function getLink(): ?PDO {
		return $this->link;
	}

	/**
	 * Set the connections hashed index in the dbContainer
	 *
	 * @param string $ci Hashed index of the connection
	 *
	 * @return void
	 */
	public function setConnectionIndex(string $ci): void {
		$this->connection_index = $ci;
	}

	/**
	 * Get the hashed index of the connection in the dbContainer
	 *
	 * @return string Hashed index of the connection
	 */
	public function getConnectionIndex(): ?string {
		return $this->connection_index;
	}

	/**
	 * Set the PDO statement used in a query to be reused
	 *
	 * @param PDOStatement $s Last PDO statement used
	 *
	 * @return void
	 */
	public function setStmt(PDOStatement $s): void {
		$this->stmt = $s;
	}

	/**
	 * Get the last PDO statement used
	 *
	 * @return PDOStatement Last PDO statement used
	 */
	public function getStmt(): ?PDOStatement {
		return $this->stmt;
	}

	/**
	 * Set the mode of obtaining data from a PDO statement
	 *
	 * @param int $fm PDO constant fetch mode for the statement
	 *
	 * @return void
	 */
	public function setFetchMode(int $fm): void {
		$this->fetch_mode = $fm;
	}

	/**
	 * Get the fetch mode of obtaining data from a PDO statement
	 *
	 * @return int PDO constant fetch mode for the statement
	 */
	public function getFetchMode(): ?int {
		return $this->fetch_mode;
	}

	/**
	 * Set the last executed SQL query
	 *
	 * @param string $lq Last executed SQL query
	 *
	 * @return void
	 */
	public function setLastQuery(string $lq): void {
		$this->last_query = $lq;
	}

	/**
	 * Get the last executed SQL query
	 *
	 * @return string Last executed SQL query
	 */
	public function getLastQuery(): ?string {
		return $this->last_query;
	}

	/**
	 * Opens a connection to the database and stores it in dbContainer
	 *
	 * @return bool|string Return true if connected or a string with the given error
	 */
	function connect() {
		global $core;
		if (!is_null($this->getConnectionIndex())) {
			$connection = $core->dbContainer->getConnectionByIndex( $this->getConnectionIndex() );
			$this->setConnectionIndex($connection['index']);
			$this->setLink($connection['link']);
		}
		else {
			try {
				$connection = $core->dbContainer->getConnection($this->getDriver(), $this->getHost(), $this->getUser(), $this->getPass(), $this->getName(), $this->getCharset());
				$this->setConnectionIndex($connection['index']);
				$this->setLink($connection['link']);
			}
			catch (PDOException $e) {
				return 'Connection failed: ' . $e->getMessage();
			}
		}

		return true;
	}

	/**
	 * Close a connection to the database
	 *
	 * @return void
	 */
	function disconnect(): void {
		if (!is_null($this->getLink())) {
			$this->setLink(null);
		}
	}

	/**
	 * Make a query to the database
	 *
	 * @param string $q SQL to be queried to the database
	 *
	 * @param array $params Parameters to be substituted in the SQL query
	 *
	 * @return string|void Returns a string with a message if there was an error connecting to the database or void if everything went ok
	 */
	public function query(string $q, array $params=[]): ?string {
		// Get connection
		$pdo = $this->getLink();
		if (!$pdo) {
			$conn = $this->connect();
			if ($conn===true) {
				$pdo = $this->getLink();
			}
			else {
				return $conn;
			}
		}

		// Save this query as last executed query
		$this->setLastQuery($q);
		try {
			// If there are parameters use a prepared statement
			if (count($params)>0) {
				$stmt = $pdo->prepare($q);
				$stmt->execute($params);
			}
			// If there are not parameters run the query directly
			else {
				$stmt = $pdo->query($q);
			}
		}
		catch(PDOException $e) {
			// In case there is an exception throw a generic exception with the error message
			throw new Exception('SQL ERROR: '.$e->getMessage());
		}

		// If there is a defined mode of obtaining the results, set it to the statement
		if (!is_null($this->getFetchMode())) {
			$stmt->setFetchMode(PDO::FETCH_CLASS, $this->getFetchMode());
		}

		$this->setStmt($stmt);
		return null;
	}

	/**
	 * Set the beginning of a transaction
	 *
	 * @return void
	 */
	public function beginTransaction(): void {
		if (is_null($this->getLink())) {
			$this->connect();
		}
		$this->getLink()->beginTransaction();
	}

	/**
	 * Commit a transaction
	 *
	 * @return void
	 */
	public function commit(): void {
		if (is_null($this->getLink())) {
			$this->connect();
		}
		$this->getLink()->commit();
	}

	/**
	 * Cancel/rollback a transaction
	 *
	 * @return void
	 */
	public function rollback(): void {
		if (is_null($this->getLink())) {
			$this->connect();
		}
		$this->getLink()->rollback();
	}

	/**
	 * Get a result
	 *
	 * @return array|object Return a result from last query as an array or user defined fetch mode
	 */
	public function next() {
		if (is_null($this->getFetchMode())) {
			return $this->getStmt()->fetch(PDO::FETCH_ASSOC);
		}
		return $this->getStmt()->fetch();
	}

	/**
	 * Get all the results as an array
	 *
	 * @return array|object Get all the results from last query as an array or user defined fetch mode
	 */
	public function fetchAll() {
		if (is_null($this->getFetchMode())) {
			return $this->getStmt()->fetchAll(PDO::FETCH_ASSOC);
		}
		return $this->getStmt()->fetchAll();
	}

	/**
	 * Get number of affected rows
	 *
	 * @return int Number of affected rows
	 */
	public function affected(): int {
		return $this->getStmt()->rowCount();
	}

	/**
	 * Get last id of an inserted row if it has auto-increment
	 *
	 * @return int Last id of the inserted row
	 */
	public function lastId(): int {
		return intval($this->getLink()->lastInsertId());
	}
}