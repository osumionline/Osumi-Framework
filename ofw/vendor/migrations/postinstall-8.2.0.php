<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Migrations;

use OsumiFramework\OFW\Core\OConfig;
use OsumiFramework\OFW\Tools\OColors;
use OsumiFramework\OFW\Tools\OTools;

class OPostInstall {
	private ?OColors $colors = null;
	private ?OConfig $config = null;
	private array    $messages = [
		'es' => [
			'TITLE'           => "\nPOST INSTALL 8.2.0\n\n",
			'UPDATING_MODELS' => "  Actualizando modelos...\n",
			'UPDATED_MODEL'   => "    Modelo \"%s\" actualizado.\n",
			'MODELS_UPDATED'  => "  Modelos actualizados.\n",
			'END_TITLE'       => "\nPOST INSTALL 8.2.0 finalizado.\n\n"
		],
		'en' => [
			'TITLE'           => "\nPOST INSTALL 8.2.0\n\n",
			'UPDATING_MODELS' => "  Updating models...\n",
			'UPDATED_MODEL'   => "    Model \"%s\" updated.",
			'MODELS_UPDATED'  => "  Models updated.\n",
			'END_TITLE'       => "\nPOST INSTALL 8.1.1 finished.\n\n"
		],
		'eu' => [
			'TITLE'           => "\nPOST INSTALL 8.2.0\n\n",
			'UPDATING_MODELS' => "  Eguneratzen modeloak...\n",
			'UPDATED_MODEL'   => "    \"%s\" modeloa eguneratu da.",
			'MODELS_UPDATED'  => "  Modeloak eguneratu dira.\n",
			'END_TITLE'       => "\nPOST INSTALL 8.1.1 bukatu du.\n\n"
		]
	];

	/**
	 * Store global configuration locally
	 */
	public function __construct() {
		global $core;
		$this->config = $core->config;
		$this->colors = new OColors();
	}

  /**
   * Given a field name and its value, return the value between single quotes if necessary
   *
   * @param string $type Type of the whole OModelField
   *
   * @param string $key Name of the field
   *
   * @param string $value Value of the field
   *
   * @return string Value of the field between single quotes or not
   */
  private function formatFieldValue(string $type, string $key, string $value): string {
	if ($key === 'name' || $key === 'comment' || $key === 'ref') {
		return "'".$value."'";
	}
	if ($key === 'default') {
	  if ($type === 'OMODEL_PK_STR' || $type === 'OMODEL_TEXT' || $type === 'OMODEL_DATE' || $type === 'OMODEL_LONGTEXT') {
			if (is_null($value)) {
				return 'null';
			}
			return "'".$value."'";
	  }
	}
	return $value;
  }

	/**
	 * Function to update the models and change all array models to OModelGroup and OModelFields
	 *
	 * @return string Result messages returned on every model updated
	 */
	private function updateModels(): string {
		$ret = '';

		if (file_exists($this->config->getDir('app_model'))) {
			if ($model = opendir($this->config->getDir('app_model'))) {
				while (false !== ($entry = readdir($model))) {
					if ($entry != '.' && $entry != '..') {
						$model_path = $this->config->getDir('app_model').$entry;
						$model_content = file_get_contents($model_path);

						// Find where the model data begins and ends
						$start_ind = stripos($model_content, '$'.'model = [');
						$end_ind = stripos($model_content, '];', $start_ind);

						// Clean line breaks and spaces/tabs
			$models = substr($model_content, $start_ind, ($end_ind - $start_ind));
			$models = trim(str_ireplace("$"."model = [\n", "", $models));
			$models = preg_replace('/[ \t]{2,}/i', "", $models);

						// Replace old OModel constants with globaly defined contants
			$models = str_ireplace('OModel::PK',       'OMODEL_PK',       $models);
			$models = str_ireplace('OModel::PK_STR',   'OMODEL_PK_STR',   $models);
			$models = str_ireplace('OModel::CREATED',  'OMODEL_CREATED',  $models);
			$models = str_ireplace('OModel::UPDATED',  'OMODEL_UPDATED',  $models);
			$models = str_ireplace('OModel::NUM',      'OMODEL_NUM',      $models);
			$models = str_ireplace('OModel::TEXT',     'OMODEL_TEXT',     $models);
			$models = str_ireplace('OModel::DATE',     'OMODEL_DATE',     $models);
			$models = str_ireplace('OModel::BOOL',     'OMODEL_BOOL',     $models);
			$models = str_ireplace('OModel::LONGTEXT', 'OMODEL_LONGTEXT', $models);
			$models = str_ireplace('OModel::FLOAT',    'OMODEL_FLOAT',    $models);

						// Find all ids
			preg_match_all("/'(.*?)' => \[/", $models, $match);

			$ids = [];
			foreach ($match[1] as $id) {
			  array_push($ids, $id);
			}

			$new_fields = [];

						// Iterate every id / field
			foreach ($ids as $id) {
			  preg_match_all("/'".$id."' => \[(.*?)\]/s", $models, $match);

							// Break field data into key / value pairs
			  $field_content = str_ireplace("\n", "", $match[1][0]);
			  $field_content = str_ireplace("'=>", "' =>", $field_content);
			  $fields = explode(",'", $field_content);

			  $new_field = ['name' => $id];

			  foreach ($fields as $field) {
				$field = str_ireplace("'", "", $field);
				$field_data = explode(' => ', $field);

				$new_field[$field_data[0]] = $field_data[1];
			  }

			  array_push($new_fields, $new_field);
			}

						// Build new OModelGroup / OModelField structure
			$new_models = "$"."model = new OModelGroup(\n";
			$new_model_fields = [];

						// For every field create a OMOdelField
			foreach ($new_fields as $new_field) {
			  $new_model_field = "\t\t\tnew OModelField(\n";
			  $fields = [];
			  $type = '';
			  foreach ($new_field as $key => $value) {
				if ($key === 'type') {
				  $type = $value;
				}
				array_push($fields, "\t\t\t\t".$key.': '.$this->formatFieldValue($type, $key, $value));
			  }
			  $new_model_field .= implode(",\n", $fields);
			  $new_model_field .= "\n\t\t\t)";
			  array_push($new_model_fields, $new_model_field);
			}
			$new_models .= implode(",\n", $new_model_fields);
			$new_models .= "\n\t\t);\n";

						// Get original models data
			$models = substr($model_content, $start_ind, ($end_ind - $start_ind +2));
						// Replace model data with new model structure
			$model_content = substr($model_content, 0, $start_ind).$new_models.substr($model_content, ($end_ind +3));

						// Add OModelGroup and OModelField to class import declarations
			$model_content = str_ireplace(
			  "use OsumiFramework\OFW\DB\OModel;",
			  "use OsumiFramework\OFW\DB\OModel;\nuse OsumiFramework\OFW\DB\OModelGroup;\nuse OsumiFramework\OFW\DB\OModelField;",
			  $model_content
			);

						// Save model file
						file_put_contents($model_path, $model_content);

						$ret .= sprintf($this->messages[$this->config->getLang()]['UPDATED_MODEL'],
							$this->colors->getColoredString($model_path, 'light_green')
						);
					}
				}
				closedir($model);
			}
		}

		return $ret;
	}

	/**
	 * Runs the v8.2.0 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';

		// Start
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		// Update models

		$ret .= $this->messages[$this->config->getLang()]['UPDATING_MODELS'];

		$ret .= $this->updateModels();

		$ret .= $this->messages[$this->config->getLang()]['MODELS_UPDATED'];

		// End
		$ret .= $this->messages[$this->config->getLang()]['END_TITLE'];

		return $ret;
	}
}
