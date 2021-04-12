<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Log;

/**
 * OLog - Class to log information to a debug log file
 */
class OLog {
	private ?string $class_name  = null;
	private ?string $log_dir     = null;
	private string $log_file     = 'osumi';
	private string $log_file_ext = 'log';
	private string $log_path     = '';
	private int $max_file_size   = 50;
	private int $max_num_files   = 3;
	private string $log_level    = 'ALL';
	private array $levels        = ['ALL', 'DEBUG', 'INFO', 'ERROR'];

	/**
	 * Start up the object by getting the logging configuration from the global config
	 *
	 * @param string $class_name Name of the class where the logger is used
	 */
	function __construct(string $class_name=null) {
		global $core;
		$this->log_dir = $core->config->getDir('logs');
		$this->log_file_name = $core->config->getLog('name');
		$this->log_path = $this->log_dir.$this->log_file_name.'.'.$this->log_file_ext;
		$this->max_file_size = $core->config->getLog('max_file_size');
		$this->max_num_files = $core->config->getLog('max_num_files');
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

		$rotate = false;
		$truncate = false;
		$log_file_size = 0;
		$data_file_size = strlen($data);
		$max_size = $this->max_file_size * 1024 * 1024;
		if (file_exists($this->log_path)) {
			$log_file_size = filesize($this->log_path);
		}

		if (($log_file_size + $data_file_size) > $max_size) {
			if ($this->max_num_files > 1) {
				$rotate = true;
			}
			else {
				$truncate = true;
			}
		}

		if (!$rotate && !$truncate) {
			return (file_put_contents($this->log_path, $data, FILE_APPEND) !== false);
		}
		if ($truncate) {
			$old_log = file_get_contents($this->log_path, false, null, -1 * ($max_size - $data_file_size));
			return (file_put_contents($this->log_path, $old_log.$data) !== false);
		}
		if ($rotate) {
			$check_path = $this->log_dir.$this->log_file_name.'*.'.$this->log_file_ext;
			$current_log_files = glob($check_path);

			$check_max = $this->log_dir.$this->log_file_name.'_'.$this->max_num_files.'.'.$this->log_file_ext;
			if (in_array($check_max, $current_log_files)) {
				unlink($check_max);
				$last = array_pop($current_log_files);
			}
			for ($i=($this->max_num_files-1); $i>0; $i--) {
				$check_from = $this->log_dir.$this->log_file_name.'_'.$i.'.'.$this->log_file_ext;
				$check_to = $this->log_dir.$this->log_file_name.'_'.($i+1).'.'.$this->log_file_ext;

				if (file_exists($check_from)) {
					rename($check_from, $check_to);
				}
			}
			$first_rotate = $this->log_dir.$this->log_file_name.'_1.'.$this->log_file_ext;
			rename($this->log_path, $first_rotate);
			return (file_put_contents($this->log_path, $data) !== false);
		}
	}
}