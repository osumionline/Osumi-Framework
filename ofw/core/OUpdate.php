<?php declare(strict_types=1);
/**
 * Utilities for the update process
 */
class OUpdate {
	private ?OColors $colors          = null;
	private string   $base_dir        = '';
	private string   $repo_url        = 'https://raw.githubusercontent.com/igorosabel/Osumi-Framework/';
	//private string $repo_url        = 'https://raw.githubusercontent.com/igorosabel/Osumi-Framework-Test/';
	private array    $version_file    = [];
	private ?string  $current_version = null;
	private ?string  $repo_version    = null;
	private ?int     $version_check   = null;
	private array    $new_updates     = [];

	/**
	 * Loads on start up current version and repo version and checks both
	 */
	function __construct() {
		global $core;
		$this->colors          = new OColors();
		$this->base_dir        = $core->config->getDir('base');
		$this->current_version = trim( OTools::getVersion() );
		$this->repo_version    = $this->getVersion();
		$this->version_check   = version_compare($this->current_version, $this->repo_version);
	}

	/**
	 * Get currently installed version
	 *
	 * @return string Current version number
	 */
	public function getCurrentVersion(): string {
		return $this->current_version;
	}

	/**
	 * Get repository version
	 *
	 * @return string Repository version number
	 */
	public function getRepoVersion(): string {
		return $this->repo_version;
	}

	/**
	 * Get version check
	 *
	 * @return int Get version check (-1 to be updated 0 current 1 newer)
	 */
	public function getVersionCheck(): int {
		return $this->version_check;
	}

	/**
	 * Get file of version updates
	 *
	 * @return array Available updates list array
	 */
	private function getVersionFile(): array {
		if (empty($this->version_file)) {
			$this->version_file = json_decode( file_get_contents($this->repo_url.'master/ofw/core/version.json'), true );
		}
		return $this->version_file;
	}

	/**
	 * Get current version from the repository
	 *
	 * @return string Current version number (eg 5.0.0)
	 */
	private function getVersion(): string {
		$version = $this->getVersionFile();
		return $version['version'];
	}

	/**
	 * Perform the update check
	 *
	 * @return array Array of "to be updated" versions. Includes version number, message and array of files with their status
	 */
	public function doUpdateCheck(): array {
		$version = $this->getVersionFile();
		$updates = $version['updates'];

		$to_be_updated = [];
		foreach ($updates as $update_version => $update) {
			if (version_compare($this->current_version, $update_version)==-1) {
				$to_be_updated[$update_version] = [
					'message' => $update['message'],
					'postinstall' => (array_key_exists('postinstall', $update) && $update['postinstall']===true),
					'files' => []
				];
			}
		}
		asort($to_be_updated);

		foreach (array_keys($to_be_updated) as $version) {
			if (array_key_exists('deletes', $updates[$version])) {
				foreach ($updates[$version]['deletes'] as $delete) {
					$local_delete = $this->base_dir.$delete;
					$status = 2; // delete
					if (!file_exists($local_delete)) {
						$status = 3; // delete not found
					}
					array_push($to_be_updated[$version]['files'], ['file' => $local_delete, 'rel' => $delete, 'status' => $status]);
				}
			}
			if (array_key_exists('files', $updates[$version])) {
				foreach ($updates[$version]['files'] as $file) {
					$local_file = $this->base_dir.$file;
					$status = 0; // new
					if (file_exists($local_file)) {
						$status = 1; // update
					}
					array_push($to_be_updated[$version]['files'], ['file' => $local_file, 'rel' => $file, 'status' => $status]);
				}
			}
		}

		$this->new_updates = $to_be_updated;
		return $this->new_updates;
	}

	/**
	 * Returns status message about a file
	 *
	 * @param array $file Array of information about a file to be updated
	 *
	 * @param string $end Adds ending information (OK/ERROR) to the line
	 *
	 * @return string Information about the file
	 */
	private function getStatusMessage(array $file, string $end=''): string {
		$ret = "    ";
		switch ($file['status']) {
			case 0: {
				$ret .= "[ ".$this->colors->getColoredString("NEW   ", "light_green")." ]";
			}
			break;
			case 1: {
				$ret .= "[ ".$this->colors->getColoredString("UPDATE", "light_blue")." ]";
			}
			break;
			case 2: {
				$ret .= "[ ".$this->colors->getColoredString("DELETE", "light_red")." ]";
			}
			break;
			case 3: {
				$ret .= "[ ".$this->colors->getColoredString("DELETE (NOT FOUND)", "light_purple")." ]";
			}
			break;
		}
		$ret .= " - ".$file['rel'];
		
		if ($end=='ok') {
			$ret = str_pad($ret, 120, ' ');
			$ret .= "[ ".$this->colors->getColoredString("OK", "light_green")." ]";
		}
		if ($end=='error') {
			$ret = str_pad($ret, 120, ' ');
			$ret .= "[ ".$this->colors->getColoredString("ERROR", "light_red")." ]";
		}
		
		$ret .= "\n";

		return $ret;
	}

	/**
	 * Show information about available updates
	 *
	 * @return void Echoes information about updates
	 */
	public function showUpdates(): void {
		$to_be_updated = $this->doUpdateCheck();
		echo "\n";

		foreach ($to_be_updated as $version => $update) {
			echo str_pad("==[ ".$update['message']." ]", 110, "=")."\n\n";
			foreach ($update['files'] as $file) {
				echo $this->getStatusMessage($file);
			}
			echo "\n".str_pad('', 109, '=')."\n\n";
		}
	}

	/**
	 * Restore the backups created during the update
	 *
	 * @param array Array of backuped files
	 *
	 * @return void
	 */
	private function restoreBackups(array $backups): void {
		foreach ($backups as $backup) {
			if (file_exists($backup['new_file'])) {
				unlink($backup['new_file']);
			}
			if (!is_null($backup['backup'])) {
				rename($backup['backup'], $backup['new_file']);
			}
		}
	}

	/**
	 * Download a file by its URL
	 *
	 * @param string URL of the file
	 *
	 * @return string Content of the file
	 */
	private function getFile(string $url): string {
		$file_headers = get_headers($url);
		if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
			return false;
		}
		return file_get_contents($url);
		
	}

	/**
	 * Perform the updates and print update information messages
	 *
	 * @return string Prints information about updates
	 */
	public function doUpdate() {
		$to_be_updated = $this->doUpdateCheck();
		echo "\n";

		$result = true;
		foreach ($to_be_updated as $version => $update) {
			echo str_pad("==[ ".$update['message']." ]", 110, "=")."\n\n";
			$backups = [];
			foreach ($update['files'] as $file) {
				// Update or delete -> make backup
				if ($file['status']==1 || $file['status']==2) {
					$backup_file = $file['file'].'_backup';
					rename($file['file'], $backup_file);
					array_push($backups, ['new_file'=>$file['file'], 'backup'=>$backup_file]);
				}
				// New or update -> download
				if ($file['status']==0 || $file['status']==1) {
					$file_url = $this->repo_url.'v'.$version.'/'.$file['rel'];
					$file_content = $this->getFile($file_url);
					if ($file_content===false) {
						echo "\n\n".$this->colors->getColoredString("ERROR", "white", "red").": ".OTools::getMessage('TASK_UPDATE_NOT_FOUND', [$file_url])."\n\n";
						$this->restoreBackups($backups);
						exit;
					}

					$dir = dirname($file['file']);
					if (!file_exists($dir)) {
						mkdir($dir, 0777, true);
					}

					$result_file = file_put_contents($file['file'], $file_content);
					if ($result_file===false) {
						echo $this->getStatusMessage($file, 'error');
						$result = false;
						break;
					}
					else {
						if ($file['status']==0) {
							array_push($backups, ['new_file'=>$file['file'], 'backup'=>null]);
						}
					}
				}
				
				echo $this->getStatusMessage($file, 'ok');
			}

			if ($update['postinstall']) {
				$file = 'ofw/core/postinstall-'.$version.'.php';
				$file_url = $this->repo_url.'v'.$version.'/'.$file;
				$file_content = $this->getFile($file_url);
				if ($file_content===false) {
					echo "\n\n".$this->colors->getColoredString("ERROR", "white", "red").": ".OTools::getMessage('TASK_UPDATE_NOT_FOUND', [$file_url])."\n\n";
					$this->restoreBackups($backups);
					exit;
				}

				if (file_exists($this->base_dir.$file)){
					unlink($this->base_dir.$file);
				}
				$result_file = file_put_contents($this->base_dir.$file, $file_content);
				
				include $this->base_dir.$file;
				
				$postinstall = new OPostInstall();
				$postinstall->run();
				unlink($this->base_dir.$file);
			}

			if ($result) {
				echo "\n  ".$this->colors->getColoredString(OTools::getMessage('TASK_UPDATE_ALL_UPDATED', [$version]), "light_green")."\n";
				if (count($backups)>0) {
					echo OTools::getMessage('TASK_UPDATE_DELETE_BACKUPS');
					foreach ($backups as $backup) {
						if (!is_null($backup['backup']) && file_exists($backup['backup'])){
							unlink($backup['backup']);
						}
					}
				}
			}
			else {
				echo "  ".$this->colors->getColoredString(OTools::getMessage('TASK_UPDATE_UPDATE_ERROR'), "white", "red")."\n";
				$this->restoreBackups($backups);
			}
			
			echo "\n".str_pad('', 109, '=')."\n\n";
		}
	}
}