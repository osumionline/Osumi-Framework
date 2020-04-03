<?php
/**
 * OLog - Class to log information to a debug log file
 */
class OLog {
	private $class_name = null;
	private $log_dir  = '';
	private $log_level = 0;
	private $levels = ['ALL',	'DEBUG', 'INFO', 'ERROR'];

	/**
	 * Start up the object by getting the logging configuration from the global config
	 *
	 * @return void
	 */
	function __construct($class_name=null) {
		global $core;
		$this->log_dir = $core->config->getLog('dir');
		$this->log_level = array_key_exists($core->config->getLog('level'), $this->levels) ? $core->config->getLog('level') : 'ALL';
		if (!is_null($class_name)) {
			$this->class_name = $class_name;
		}
	}

	/**
	 * Log a given debug string if the log level is in ('ALL', 'DEBUG')
	 *
	 * @param string $str String to be logged
	 *
	 * @return void
	 */
	public function debug($str) {
		if (in_array($this->log_level, ['ALL', 'DEBUG'])) {
			$this->putLog('DEBUG', $str);
		}
	}

	/**
	 * Log a given info string if the log level is in ('ALL', 'DEBUG', 'INFO')
	 *
	 * @param string $str String to be logged
	 *
	 * @return void
	 */
	public function info($str) {
		if (in_array($this->log_level, ['ALL', 'DEBUG', 'INFO'])) {
			$this->putLog('INFO', $str);
		}
	}

	/**
	 * Log a given error string
	 *
	 * @param string $str String to be logged
	 *
	 * @return void
	 */
	public function error($str) {
		$this->putLog('ERROR', $str);
	}

	/**
	 * Write the log level and the data to the debug file
	 *
	 * @param string $level Log importance level ('DEBUG', 'INFO', 'ERROR')
	 *
	 * @param string $str Message to be logged
	 *
	 * @return boolean Returns if the message was written to the log file or not
	 */
	private function putLog($level, $str) {
		$data = '['.date('Y-m-d H:i:s',time()).'] - ['.$level.'] - ';
		if (!is_null($this->class_name)) {
			$data .= '['.$this->class_name.'] - ';
		}
		$data .= $str."\n";
		return file_put_contents($this->log_dir, $data, FILE_APPEND);
	}
}