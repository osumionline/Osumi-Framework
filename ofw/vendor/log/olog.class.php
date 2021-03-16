<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Log;

/**
 * OLog - Class to log information to a debug log file
 */
class OLog {
	private ?string $class_name = null;
	private string  $log_dir  = '';
	private string  $log_level = 'ALL';
	private array   $levels = ['ALL', 'DEBUG', 'INFO', 'ERROR'];

	/**
	 * Start up the object by getting the logging configuration from the global config
	 *
	 * @param string $class_name Name of the class where the logger is used
	 */
	function __construct(string $class_name=null) {
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
	 * @return bool Returns if the message was written to the log file or not
	 */
	public function debug(string $str): bool {
		if (in_array($this->log_level, ['ALL', 'DEBUG'])) {
			return $this->putLog('DEBUG', $str);
		}
		return false;
	}

	/**
	 * Log a given info string if the log level is in ('ALL', 'DEBUG', 'INFO')
	 *
	 * @param string $str String to be logged
	 *
	 * @return bool Returns if the message was written to the log file or not
	 */
	public function info(string $str): bool {
		if (in_array($this->log_level, ['ALL', 'DEBUG', 'INFO'])) {
			return $this->putLog('INFO', $str);
		}
		return false;
	}

	/**
	 * Log a given error string
	 *
	 * @param string $str String to be logged
	 *
	 * @return bool Returns if the message was written to the log file or not
	 */
	public function error(string $str): bool {
		return $this->putLog('ERROR', $str);
	}

	/**
	 * Write the log level and the data to the debug file
	 *
	 * @param string $level Log importance level ('DEBUG', 'INFO', 'ERROR')
	 *
	 * @param string $str Message to be logged
	 *
	 * @return bool Returns if the message was written to the log file or not
	 */
	private function putLog(string $level, string $str): bool {
		$data = '['.date('Y-m-d H:i:s',time()).'] - ['.$level.'] - ';
		if (!is_null($this->class_name)) {
			$data .= '['.$this->class_name.'] - ';
		}
		$data .= $str."\n";
		return (file_put_contents($this->log_dir, $data, FILE_APPEND) !== false);
	}
}