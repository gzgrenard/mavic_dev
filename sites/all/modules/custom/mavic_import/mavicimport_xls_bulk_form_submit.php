<?php

/*
 * submit/confirm action and ajax callback are handled the same way : 
 * they all call this page without any of the form arguments except for the first submit action (the file(s) to be processed), 
 * all other argument are set before the response is sent to the user and stored in session var or are handled on the user's side (js), while
 * any needed data are temporarily stored in tables. Both of them, argument and data, are set and retrieved with
 * a set of functions defined in mavicimport.module :
 * - mavicimport_ajaxsubmit_set_step() : a next step to be automatically called
 * - mavicimport_ajaxsubmit_set_confirm() : a next step which will need the end user to confirm he wants to proceed or not
 * - mavicimport_ajaxsubmit_set_callback() : parameters to pass to the js function called on ajax submit, usually used to set the message displayed to the end user
 * - mavicimport_ajaxsubmit_set_file : temporarily store or retrieve the status and related data (warnings, errors) of a new (set of) uploaded file(s)
 * - mavicimport_ajaxsubmit_set_data : temporarily store or retrieve the data needed for the import process, e.g. nodes objects build during the checking process, nodes' nid to ignore, etc.
 * - mavicimport_ajaxsubmit_clearAll : clear all temporary settings and data, immediately or at the end of a process
 * 
 * N.B. : some steps may be called subsequently several times, eg. case 'file_xml_only_checking'
 */

//retrieved the step (the first submit action has not yet set any : switch to default)
$step = (mavicimport_ajaxsubmit_set_step() === NULL) ? '' : key(mavicimport_ajaxsubmit_set_step());
//if it is a step which needs the end user to confirm he wants to proceed, the step is null to prevent automatic callback : needs to set it
$confirm = (mavicimport_ajaxsubmit_set_confirm() === NULL) ? '' : key(mavicimport_ajaxsubmit_set_confirm());

switch ($confirm) {

	case 'warning_xml_only':
		$step = 'file_xml_only_checking';
		break;

	case 'import_xlsx_data':
		$step = 'import_xlsx_data';
		break;

	default:
		break;
}

switch ($step) {
	/*	 * **************************************************************************************************************
	 * 
	 * XML FILE(S)
	 */
	case 'file_xml_only_checking' :
		//retrieve the file(s)
		$xmlT = mavicimport_ajaxsubmit_set_step();
		$newXmls = $xmlT['file_xml_only_checking'];
		//if $newXmls is an array, it means the extension has not yet been checked : go for it
		if (is_array($newXmls) && !empty($newXmls)) {
			$validators = array(
				'mavicimport_file_validate_ext' => array('xml')
			);
			$xmlFiles = array();
			foreach ($newXmls as $newXml) {
				$file = file_save_upload($newXml, $validators, FALSE, FILE_EXISTS_REPLACE);
				if ($file != 0) {
					array_push($xmlFiles, $file);
				}
			}
			if (!empty($xmlFiles) && count($newXmls) == count($xmlFiles)) {
				mavicimport_ajaxsubmit_set_step(FALSE, $xmlFiles, 'xlm_season_checking');
				$callbackParams = array(
					'beforeSubmit' => 'XML seasons checking...',
				);
				mavicimport_ajaxsubmit_set_callback($callbackParams);
			} else {
				//drupal_set_message(print_r($xmlFiles, true), 'status');
				drupal_set_message('<strong>Failed to upload the file(s), process aborted.</strong>', 'error');
				mavicimport_ajaxsubmit_clearAll(true);
			}
			//stop here : it will proceed further if the file has been successfully uploaded
			break;
		}
		//files' extensions are correct and they have been successfully uploaded in a temporary directory
		unset($newXmls);
		$newXmls = mavicimport_ajaxsubmit_set_file();
		//foreach new xml file
		foreach ($newXmls as $key => $newXml) {
			/*
			 * set the var wich tells what is the "sub"step : 
			 *  -file not yet checked
			 *  -file checked but warnings have been raised : the end user has confirmed he wants to proceed to the import 
			 */
			$ignoreWarning = false;
			//if this file has been checked
			if (isset($newXml->imported)) {
				//if this file has not yet been imported because only warnings were raised. set it as ready to be imported as the end user allows it
				if (!$newXml->imported && $confirm == 'warning_xml_only' && empty($newXml->errors)) {
					$ignoreWarning = true;
					//if no warnings were raised during the checking and it has thus been allready imported, skip it
				} elseif ($newXml->imported && $confirm == 'warning_xml_only') {
					continue;
				}
			}
			//(re)load xml
			if (!$xml = simplexml_load_file($newXml->filepath)) {
				$newXml->errors = array(array('line' => 0, 'message' => "Failed to open the file" . $newXml->filename . "."));
			} else {
				if (!$ignoreWarning) {
					$notes = mavicimport_ajaxsubmit_checkXml($xml, $newXml->season);
					$newXml->statusMsg = $notes['statusMsg'];
					$newXml->warnings = $notes['warnings'];
					$newXml->errors = $notes['errors'];
					$newXml->features = $notes['features'];
					$newXml->macromodels = $notes['macromodels'];
					$newXml->typeCarac = $notes['typeCarac'];
					$newXml->technoSup = $notes['technoSup'];
					$newXml->filterList = $notes['filterList'];
					$newXml->technoSeason = $notes['technoSeason'];
					$newXml->imported = false;
				}
				if ((empty($newXml->errors) && (empty($newXml->warnings) && !$newXml->imported) || $ignoreWarning)) {//if no error and no warning, try to directly import data
					$seasonUpdate = $form_state['values']['seasons'];
					$notes = mavicimport_ajaxsubmit_importXml($xml, $newXml, $seasonUpdate);
					$newXml->statusMsg = $notes['statusMsg'];
					$newXml->warnings = $notes['warnings'];
					$newXml->errors = $notes['errors'];

					if (empty($newXml->errors)) {
						$newXml->imported = true;
						$newXml->errors = array();
						$destination = file_directory_path() . '/mavicimport/';
						if (file_move($newXml->filepath, $destination, FILE_EXISTS_REPLACE)) {
							$newXml->filepath = $destination;

							$notes['season'] = $newXml->season;
							$notes['range'] = $newXml->range;
							$notes['fileshortname'] = $newXml->fileshortname;
							$notes['type'] = 'xml';

							unset($notes['errors']);
							$newXml->errors = array();
							$sheet = (string) $newXml->fileshortname;
							if (db_query("SELECT * FROM {mavicimport_files_warnings} WHERE sheet = '%s'", $sheet)) {
								db_query("DELETE FROM {mavicimport_files_warnings} WHERE sheet = '%s'", $sheet);
							}
							$warningStatusMsg = array_merge($newXml->statusMsg, $newXml->warnings);
							if (!empty($warningStatusMsg)) {
								foreach ($warningStatusMsg as $warning) {
									$warningsTobestored = new stdClass();
									$warningsTobestored->sheet = $newXml->fileshortname;
									$warningsTobestored->line = $warning['line'];
									$warningsTobestored->message = $warning['message'];
									if (drupal_write_record('mavicimport_files_warnings', $warningsTobestored) == FALSE) {
										switch ($warning['line']) {
											case 0:
												$wLine = 'warning';
												break;

											case -1:
												$wLine = 'message';
												break;

											default:
												$wLine = 'warning at line ' . $warning['line'];
												break;
										}
										array_push($newXml->errors, array('line' => 0, 'message' => 'The folowing ' . $wLine . ' has not been stored in the DB : ' . $warning['message']));
									}
								}
							}
							unset($notes['warnings']);
							unset($notes['statusMsg']);

							$tobestored = clone $newXml;
							$tobestored->notes = serialize($notes);
							unset($notes);
							$tobestored->path = $newXml->filepath;
							$tobestored->upload = $newXml->timestamp;

							unset($tobestored->statusMsg);
							unset($tobestored->warnings);
							unset($tobestored->errors);
							unset($tobestored->source);
							unset($tobestored->fid);
							unset($tobestored->timestamp);
							unset($tobestored->filepath);
							unset($tobestored->season);
							unset($tobestored->range);
							unset($tobestored->fileshortname);
							if (db_query("SELECT * FROM {mavicimport_files} WHERE filename = '%s'", $newXml->filename)) {
								db_query("DELETE FROM {mavicimport_files} WHERE filename = '%s'", $newXml->filename);
							}
							if (drupal_write_record('mavicimport_files', $tobestored) === FALSE) {
								$newXml->errors[] = array(array('line' => 0, 'message' => "The file " . $newXml->filename . " has been imported but an error occured while storing its status into the DB"));
							}
						} else {
							$newXml->errors[] = array(array('line' => 0, 'message' => "The file " . $newXml->filename . " has been imported but an error occured while moving this file to its final directory"));
						}
					} else {
						$newXml->errors[] = array(array('line' => 0, 'message' => "Errors were raised during the import of the file " . $newXml->filename . "."));
					}
				}
			}
			$newXmls[$key] = $newXml;
		}
		$fileErrors = array();
		$fileWarnings = array();
		$fileSuccess = array();
		$fileImportedErrrors = array();
		foreach ($newXmls as $newXml) {
			if (!empty($newXml->errors)) {
				if ($newXml->imported) {
					array_push($fileImportedErrrors, $newXml->filename);
				} else {
					array_push($fileErrors, $newXml->filename);
				}
				$newXml->status = 0;
			} elseif (!empty($newXml->warnings)) {
				if ($newXml->imported) {
					array_push($fileSuccess, $newXml->filename);
				}
				array_push($fileWarnings, $newXml->filename);
				$newXml->status = 2;
			} else {
				array_push($fileSuccess, $newXml->filename);
				$newXml->status = 1;
			}
			mavicimport_ajaxsubmit_set_file(FALSE, $newXml->fileshortname, $newXml);
		}
		if (!empty($fileSuccess) && (count($fileSuccess) == count($newXmls)) && !$ignoreWarning) {
			drupal_set_message("<strong>The import process is a success, please check the report below</strong>", 'status');
			//drupal_set_message('(Please reload the page before submitting any new files)', 'status');
			mavicimport_ajaxsubmit_clearAll(true);
		} elseif ($ignoreWarning) {
			drupal_get_messages('', true);
			if (!empty($fileImportedErrrors)) {
				drupal_set_message("<strong>The import of " . implode(', ', $fileImportedErrrors) . " raised errors : please correct and submit it(them) again ASAP !!</strong>", 'error');
			}
			if (!empty($fileSuccess)) {
				drupal_set_message("The import of " . implode(', ', $fileSuccess) . " <br /><strong/>was successfull,", 'status');
			}
			if (!empty($fileWarnings)) {
				drupal_set_message("but warnings have been ignored for the import of " . implode(', ', $fileWarnings) . " :  <br /><strong/>some new warnings may have been raised : please check the report carrefully !!</strong>", 'warning');
			}

			drupal_set_message('<strong>Process ended.</strong>', 'status');
			mavicimport_ajaxsubmit_clearAll(true);
		} else {
			if (!empty($fileSuccess)) {
				drupal_set_message("<strong>The data of " . implode(', ', $fileSuccess) . " have been succesfully imported</strong>", 'status');
			}
			if (!empty($fileErrors)) {
				drupal_set_message("<strong>The file(s) " . implode(', ', $fileErrors) . " contain(s) error(s) : import not allowed.</strong>", 'error');
			}
			if (!empty($fileWarnings)) {
				drupal_set_message("If you choose to ignore the warnings, you can import the data of " . implode(', ', $fileWarnings) . ".", 'warning');
				$callbackParams = array(
					'beforeSubmit' => 'Importing xml...',
				);
				mavicimport_ajaxsubmit_set_callback($callbackParams);
				mavicimport_ajaxsubmit_set_step(true);
				mavicimport_ajaxsubmit_set_confirm(FALSE, 'import_xml_confirmed', 'warning_xml_only');
			} else {
				drupal_set_message('<strong>Process ended.</strong>', 'status');
				mavicimport_ajaxsubmit_clearAll(true);
			}
		}

		break;
	/*	 * **************************************************************************************************************
	 * 
	 * XLSX FILE
	 */

	case 'file_xlsx_checking' :
		/*
		 * xlsx
		 */

		$validators = array(
			'mavicimport_file_validate_ext' => array('xlsx')
		);

		$file = file_save_upload('xls_file_upload', $validators, FALSE, FILE_EXISTS_REPLACE);

		if ($file != 0) {

			mavicimport_ajaxsubmit_set_step(FALSE, $file, 'xlsx_checking');
			$callbackParams = array(
				'beforeSubmit' => 'XLSX file checking...',
			);
			mavicimport_ajaxsubmit_set_callback($callbackParams);
			break;
		} else {
			drupal_set_message('<strong>Process aborted.</strong>', 'error');
			mavicimport_ajaxsubmit_clearAll(true);
			break;
		}


		break;
	/*	 * **************************************************************************************************************
	 * 
	 * XLSX FILE CHECKING
	 */

	case 'xlsx_checking' :
		require_once('lib/PHPExcel/IOFactory.php');
		$fileT = mavicimport_ajaxsubmit_set_step();
		$file = $fileT['xlsx_checking'];
		$inputFileType = 'Excel2007';
		$inputFileName = $file->filepath;

		//  Create a new Reader of the type defined in $inputFileType
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);

		//  Load $inputFileName to a PHPExcel Object 
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);

		//properties
		$PropertyList = $objPHPExcel->getProperties();
		$file->author = $PropertyList->getCreator();
		$file->created = $PropertyList->getCreated();
		$file->modified = $PropertyList->getModified();

		//check missing sheet
		$sheetNames = $objPHPExcel->getSheetNames();
		$sheetNamesVerif = mavicimport_ajaxsubmit_checkSheetNames($sheetNames);
		if (is_array($sheetNamesVerif)) {
			$objPHPExcel->disconnectWorksheets();
			unset($objPHPExcel);

			//reload only needed sheets
			$sheetnamesFirev = array_keys($sheetNamesVerif);
			$objReader->setLoadSheetsOnly($sheetnamesFirev);

			//limit data according to params
			require_once('lib/mavicimport_ajaxsubmit_limitFilter.php');
			$filterSubset = new mavicimport_ajaxsubmit_limitFilter($parameters['sheets']);
			$objReader->setReadFilter($filterSubset);

			$objPHPExcel = $objReader->load($inputFileName);

			
			$file->xlsxReport = mavicimport_ajaxsubmit_checkXlsxData($objPHPExcel);

			$objPHPExcel->disconnectWorksheets();
			unset($objPHPExcel);
			$sheetsErrors = array();
			$sheetsWarnings = array();
			$sheetsSuccess = array();
			foreach ($file->xlsxReport as $sheetName => $sheetData) {
				if (!empty($sheetData['errors'])) {
					array_push($sheetsErrors, $sheetName);
				} elseif (!empty($sheetData['warnings'])) {
					array_push($sheetsWarnings, $sheetName);
				} else {
					array_push($sheetsSuccess, $sheetName);
				}
			}
			if (!empty($sheetsErrors)) {
				drupal_set_message("<strong>The file(s) " . implode(', ', $sheetsErrors) . " contain(s) error(s) : please correct them before submitting the file again.</strong>", 'error');
				drupal_set_message('<strong>Process aborted.</strong>', 'error');
				mavicimport_ajaxsubmit_clearAll(true);
				$file->status = 0;
			} else {
				$file->status = 1;
				if (!empty($sheetsSuccess))
					drupal_set_message("<strong>The data of <em>" . implode(', ', $sheetsSuccess) . " </em> have been succesfully checked</strong>", 'status');
				if (!empty($sheetsWarnings)) {
					drupal_set_message("If you choose to ignore the warnings, you can import the data of " . implode(', ', $sheetsWarnings) . ".", 'warning');
					$file->status = 2;
				}
				$callbackParams = array(
					'beforeSubmit' => 'Importing XLSX data...',
				);
				mavicimport_ajaxsubmit_set_callback($callbackParams);
				mavicimport_ajaxsubmit_set_step(true);
				mavicimport_ajaxsubmit_set_confirm(FALSE, 'import_confirmed', 'import_xlsx_data');
			}
			
			mavicimport_ajaxsubmit_set_file(FALSE, 'xlsx', $file);		//ask for confirmation
		} else {
			drupal_set_message('<strong>Process aborted.</strong>', 'error');

			mavicimport_ajaxsubmit_clearAll(true);
		}

		break;
	/*	 * **************************************************************************************************************
	 * 
	 * XML SEASON CHECKING
	 */

	case 'xlm_season_checking' :
		//set xml season and store them in session files
		$filesT = mavicimport_ajaxsubmit_set_step();
		$files = $filesT['xlm_season_checking'];
		foreach ($files as $key => $xmlfile) {
			$pattern = '/[^_\.]+/';
			preg_match_all($pattern, $xmlfile->filename, $matchIt);
			if ($matchIt[0][0] == 'product') {
				$xmlfile->season = $matchIt[0][1];
				$xmlfile->range = 'single_product';
			} else {
				$xmlfile->season = $matchIt[0][0];
				$xmlfile->range = $matchIt[0][1];
			}
			$xmlfile->fileshortname = substr($xmlfile->filename, 0, -4);
			mavicimport_ajaxsubmit_set_file(FALSE, $xmlfile->fileshortname, $xmlfile);
		}

		if (!empty($_FILES['files']['name']['xls_file_upload'])) {
			mavicimport_ajaxsubmit_set_step(FALSE, true, 'file_xlsx_checking');
			$callbackParams = array(
				'beforeSubmit' => 'checking XLSX file...',
			);
			mavicimport_ajaxsubmit_set_callback($callbackParams);
		} else {
			mavicimport_ajaxsubmit_set_step(FALSE, true, 'file_xml_only_checking');
			$callbackParams = array(
				'beforeSubmit' => 'checking XML files&rsquo; data...',
			);
			mavicimport_ajaxsubmit_set_callback($callbackParams);
		}

		break;
	/*	 * **************************************************************************************************************
	 * 
	 * XLSX IMPORT/DELETE CONFIRMATION
	 */

	case 'import_xlsx_data' :

		$confirmation = mavicimport_ajaxsubmit_set_confirm(true);
		$xlsx_file = mavicimport_ajaxsubmit_set_file(FALSE, 'xlsx');

		if (($confirmation['import_xlsx_data'] == 'import_confirmed') && !isset($xlsx_file->imported)) {
			//first backup !!
			if (mavicimport_dump_db($xlsx_file->filename) !== false) {
				//then delete all old nodes and/or remove old filters/technologies
				mavicimport_ajaxsubmit_deleteXlsxData();
				//delete all features if asked
				if ($form_state['values']['delete_prodFeatures']) {
					$prodval = 'prodvalcarac';
					$resTechno = db_query("	SELECT n.`nid`
											FROM {node} n 
											WHERE n.type = '%s'", $prodval);
					while ($result = db_fetch_array($resTechno)) {
						node_delete($result['nid']);
					}
				}
				//set the container which will pass the data retrieved during the import process over to a future step (e.g. nid for node references fields)
				$xlsx_file->xlsxReport['next'] = array();
				mavicimport_ajaxsubmit_importXlsxData($xlsx_file);
				break;
			} else {
				$xlsx_file->status = 0;
				drupal_set_message('The file ' . $xlsx_file->filename . ' has not been imported because the system was unable to create a recovery backup of the database, please contact your administrator.', 'error');
				drupal_set_message('<strong>Process aborted.</strong>', 'error');
				mavicimport_ajaxsubmit_clearAll(true);
			}
		}

		if (isset($xlsx_file->imported)) {
			if ($xlsx_file->status == 0) {
				drupal_set_message('<strong>The import of data contained within <em>' . $xlsx_file->filename . '<em/> raised errors and may have corrupted the database,</strong>', 'error');
				if (mavicimport_restore_db() !== false) {
					drupal_set_message('but the system has successfully restored all initial data.', 'error');
				} else {
					drupal_set_message('<strong>Please contatact your administrator.</strong>', 'error');
				}
				drupal_set_message('<strong>Process aborted.</strong>', 'error');
				mavicimport_ajaxsubmit_clearAll(true);
				
			} else {
				if ($xlsx_file->status == 2) {
					$msg = '<strong>Warnings have been ignored for the import of data contained within <em>' . $xlsx_file->filename . '<em/>';
					if ($xlsx_file->morewarnings) {
						$msg .= ' and additionnal warnings were raised during the process';
						unset($xlsx_file->morewarnings);
					}
					$msg .= ' : please check the report carrefully !!</strong>';
					drupal_set_message($msg, 'warning');
				} else {
					drupal_set_message('<strong>The import of data contained within<em>' . $xlsx_file->filename . '<em/> was successfull</strong>.', 'status');
				}
				drupal_set_message('<strong>Process ended.</strong>', 'status');
				mavicimport_ajaxsubmit_clearAll(true);
			}
		}

		//store the imported file and warning report after the import process if no error
		if (isset($xlsx_file->imported) && $xlsx_file->status) {
			unset($xlsx_file->imported);
			$destination = file_directory_path() . '/mavicimport/';
			if (file_move($xlsx_file->filepath, $destination, FILE_EXISTS_REPLACE)) {
				$xlsx_file->filepath = $destination;

				$notes = array();
				$notes['type'] = 'xlsx';

				foreach ($xlsx_file->xlsxReport as $sheetName => $sheetData) {
					if (db_query("SELECT * FROM {mavicimport_files_warnings} WHERE sheet = '%s'", $sheetName)) {
						db_query("DELETE FROM {mavicimport_files_warnings} WHERE sheet = '%s'", $sheetName);
					}
					if (!empty($sheetData['statusMsg'])) {
						$warningStatusMsg = array_merge($sheetData['statusMsg'], $sheetData['warnings']);
					} else {
						$warningStatusMsg = $sheetData['warnings'];
					}
					if (!empty($warningStatusMsg)) {
						foreach ($warningStatusMsg as $warning) {
							$warningsTobestored = new stdClass();
							$warningsTobestored->sheet = $sheetName;
							$warningsTobestored->line = $warning['line'];
							$warningsTobestored->message = $warning['message'];
							if (drupal_write_record('mavicimport_files_warnings', $warningsTobestored) == FALSE) {
								switch ($warning['line']) {
									case 0:
										$wLine = 'warning';
										break;
									case -1:
										$wLine = 'message';
										break;

									default:
										$wLine = 'warning at line ' . $warning['line'];
										break;
								}
								array_push($xlsx_file->errors, array('line' => 0, 'message' => 'The folowing ' . $wLine . ' has not been stored in the DB : ' . $warning['message']));
							}
						}
					}
				}
				$tobestored = clone $xlsx_file;
				$tobestored->notes = serialize($notes);
				unset($notes);
				$tobestored->path = $xlsx_file->filepath;
				$tobestored->upload = $xlsx_file->timestamp;
				unset($tobestored->xlsxReport);
				unset($tobestored->source);
				unset($tobestored->fid);
				unset($tobestored->timestamp);
				unset($tobestored->filepath);
				if (db_query("SELECT * FROM {mavicimport_files} WHERE filename = '%s'", $xlsx_file->filename)) {
					db_query("DELETE FROM {mavicimport_files} WHERE filename = '%s'", $xlsx_file->filename);
				}
				if (drupal_write_record('mavicimport_files', $tobestored) === FALSE) {
					drupal_set_message('The file ' . $xlsx_file->filename . ' has been imported but an error occured while storing its status into the DB', 'error');
				}
			} else {
				drupal_set_message('The file ' . $xlsx_file->filename . ' has been imported but an error occured while moving this file to its final directory', 'error');
			}
		}
		break;
	/*	 * **************************************************************************************************************
	 * 
	 * XLSX FILE : IMPORT STEPS 
	 */

	case 'import_xlsx_data_step' :

		//retieve the step
		$xlsx_file_stepT = mavicimport_ajaxsubmit_set_step();
		$xlsx_file_step = $xlsx_file_stepT['import_xlsx_data_step'];

		//retrieve the file object
		$xlsx_file = mavicimport_ajaxsubmit_set_file(FALSE, 'xlsx');

		//launch the next import step
		mavicimport_ajaxsubmit_importXlsxData($xlsx_file, $xlsx_file_step);

		break;
	/*	 * **************************************************************************************************************
	 * 
	 * DEFAULT : XLSX OR XML ?
	 */

	//this is actually the first step, when the form is submitted for the first time with a new file(s) :
	//the user can submit either one xlsx file or several xml files at once
	default :
		$num_xml_files = $form_state['values']['num_xml_files'] + 1;
		$seasonUpdate = $form_state['values']['seasons'];
		$newXml = array();
		for ($i = 0; $i <= $num_xml_files; $i++) {
			$xmlField = 'xml_file_' . $i;
			if (!empty($_FILES['files']['name'][$xmlField]))
				array_push($newXml, $xmlField);
		}
		// if new xlsx and new xml : check the xlsx and ignore the xmls
		if (!empty($_FILES['files']['name']['xls_file_upload']) && !empty($newXml) && ($form_state['values']['seasons'] != 2)) {
			drupal_set_message('One new XLSX file and ' . count($newXml) . 'new XML submitted : only the Excel file will be checked, please submit again the XML files afterwards', 'status');
			mavicimport_ajaxsubmit_set_step(false, $newXml, 'file_xlsx_checking');
			$callbackParams = array(
				'beforeSubmit' => 'Checking XML and XLSX files to upload...',
			);
			mavicimport_ajaxsubmit_set_callback($callbackParams);
			// if new xlsx, no new xml	
		} else if (empty($newXml) && !empty($_FILES['files']['name']['xls_file_upload'])) {
			drupal_set_message('One new XLSX file submitted', 'status');
			mavicimport_ajaxsubmit_set_step(false, $_FILES['files']['name']['xls_file_upload'], 'file_xlsx_checking');
			$callbackParams = array(
				'beforeSubmit' => 'Checking XLSX file to upload...',
			);
			mavicimport_ajaxsubmit_set_callback($callbackParams);
			// if new xml only	
		} else if (!empty($newXml) && ($form_state['values']['seasons'] != 2)) {
			drupal_set_message(count($newXml) . ' new XML submitted', 'status');
			mavicimport_ajaxsubmit_set_step(false, $newXml, 'file_xml_only_checking');
			$callbackParams = array(
				'beforeSubmit' => 'Checking XML files to upload...',
			);
			mavicimport_ajaxsubmit_set_callback($callbackParams);
		} else if (!empty($newXml) && ($form_state['values']['seasons'] == 2)) {
			drupal_set_message('You muste specify a season when importing xml (even if this update concerns only yearly ranges).', 'error');
			drupal_set_message('<strong>Process aborted.</strong>', 'error');
			mavicimport_ajaxsubmit_clearAll(true);
		} else {
			drupal_set_message('No new files were submitted', 'error');
			drupal_set_message('<strong>Process aborted.</strong>', 'error');
			mavicimport_ajaxsubmit_clearAll(true);
		}
		break;
}

/*
 * **********************************************************************************************************************************************************
 */

/**
 * File extension validator
 *
 * @param string $file to check
 * @param array $extensions which are allowed
 * @return error if any
 * 
 */
function mavicimport_file_validate_ext($file, $extensions) {
	$errors = array();
	$regex = '/\.(' . ereg_replace(' +', '|', preg_quote($extensions)) . ')$/i';
	if (!preg_match($regex, $file->filename)) {
		$errors[] = t('Only files with the following extensions are allowed: %files-allowed.', array('%files-allowed' => $extensions));
	}
	return $errors;
}

/**
 * Raise error/warning msg in case of missing/unrecognized sheet
 *
 * @global $parameters Mavicimport parameters
 * @param array of sheet's names
 * @return array/null
 *   array of expected sheets'name if ok or warnings, 
 *   Null if error
 */
function mavicimport_ajaxsubmit_checkSheetNames($sheetNames) {
	global $parameters;
	$recognized = array();
	foreach ($sheetNames as $sheetKey => $sheetName) {
		if (!is_array($parameters['sheets'][$sheetName])) {
			drupal_set_message('The sheet named <em>' . $sheetName . '</em> is not recognized.', 'warning');
		} else {
			$recognized[$sheetName] = $sheetKey;
		}
	}
	if (count($recognized) === count($parameters['sheets'])) {
		return $recognized;
	}
	$missings = array_diff_key($parameters['sheets'], $recognized);
	foreach ($missings as $missing => $value) {
		drupal_set_message('The sheet <em>' . $missing . '</em> is missing.', 'error');
	}
	return NULL;
}

/**
 * Check xml and ret
 * 
 * @global $parameters $parameters
 * @param object $xml
 * @param string $season
 * @return array $result = array(
 * 		'statusMsg' => status messages \n
 * 		'warnings' => warnings messages,
 * 		'errors' => errors message,
 * 		'macromodels' => list of macromodel code concerned by this season,
 * 		'features' => list of features and technologies concerned by this season,
 * 		'typeCarac' => list of typeCarac,
 * 		'technoSup' => list of technologies which may not be concerned by this season but are linked to macromodels of this season,
 *              'filterList' => list of features used as filters,
 *              'technoSeason' => list of all technologies nid with their according seasons
 * 	);
 */
function mavicimport_ajaxsubmit_checkXml($xml, $season) {
	global $parameters;

	//based language for comparaison
	$lang = 'en';

	//status, errors and warnings collectors
	$statusMsg = array();
	$errors = array();
	$warnings = array();

	// list of existing macromodels in DB (en) : $macro['b1234'] = 'macromodel_name'
	$macro = array();

	//list of features and technologies to be updated : $foundFilteredFeature[(int) $feature->caracnu] = (string) $feature->libvalcaraclb;
	$foundFilteredFeature = array();


	/*
	 * Listing of Macromodels according to the season
	 */

	//DB : retrieve the list of macromodel code for the specified season

	$macromo = 'macromodel';
	$resMacro = db_query("	SELECT DISTINCT c.`field_modelco_value`, n.title
							FROM {node} n 
							INNER JOIN {content_type_macromodel} c using (nid) 
							WHERE n.`type` = '%s' 
							AND c.`field_macro_season_value`='%s' 
							AND n.`language` = '%s'", $macromo, $season, $lang);
	while ($result = db_fetch_array($resMacro)) {
		$macroLower = strtolower($result['field_modelco_value']);
		if (empty($macro[$macroLower]))
			$macro[$macroLower] = $result['title'];
	}

	//if macro concerned by this xml's season exist :	
	if (!empty($macro)) {
		$macroTrad = array();
		$featureTrad = array();
		$typeCarac = array();

		foreach ($parameters['langs'] as $langAll) {
			$macroTrad[$langAll] = array();
			$featureTrad[$langAll] = array();
			$typeCarac[$langAll] = array();
		}
		foreach ($xml->table_prodtypcarac->featuretype as $featuretype) {
			$langC = $parameters['filiale'][(int) $featuretype->filialenu];
			$typeCarac[$langC][(int) $featuretype->typcaracco] = array('label' => (string) $featuretype->typcaraclb, 'class' => (int) $featuretype->classtypcaracnu);
			if ($langC == 'en')
				$typeCarac['lower'][(int) $featuretype->typcaracco] = mb_strtolower((string) $featuretype->typcaraclb);
		}
		//XML : retrieve the list of associated features
		// list of associated feature in current xml :
		//$featureList['en'][1234(caracnu-featurecode)][i] = array('b1234', 'macromodel_name')
		$featureList = array();
		$featureList[$lang] = array();
		foreach ($xml->table_prodvalmacro->macromodel as $macromodel) {
			$tempMacroModco = strtolower($macromodel->macromodco);
			if (!empty($macro[$tempMacroModco])) {
				$macroName = $macro[$tempMacroModco];
				$macroTrad[$parameters['filiale'][(int) $macromodel->filialenu]][$tempMacroModco] = $macroName;
				if ($parameters['filiale'][(int) $macromodel->filialenu] == $lang) {
					$caracnuListTemp = $xml->xpath('/prologue_data/table_prodcarac/assoc-model-feature[macronu=' . $macromodel->macronu . ']/caracnu');
					if (is_array($caracnuListTemp)) { // link features - macromodel
						foreach ($caracnuListTemp as $caracnu) {
							//$lang = $parameters['filiale'][(int)$macromodel->filialenu];
							if (!isset($featureList[$lang][(int) $caracnu])) {
								$featureList[$lang][(int) $caracnu] = array();
							}
							$infoMacro = array($tempMacroModco, $macroName);
							array_push($featureList[$lang][(int) $caracnu], $infoMacro);
						}
					}
				}
			}
		}
		$macroNumBase = count($macroTrad[$lang]);
		$missingEnMacro = array();
		$macroLangOk = $macroTrad; //array();//
		/* foreach ($macroTrad as $macroLang => $macroList) { // $macrotrad['en'][b1234] = 'macro name'
		  if (macroLang != $lang && !empty($macroList)){
		  $macroNumOther = count($macroList);
		  if ($macroNumOther < $macroNumBase){
		  $diff = array_diff_key($macroTrad[$lang], $macroList);
		  $msg = 'The <em>'. $macroLang .'</em> version is missing for the macromodels <em>'. implode(', ', $diff).'</em>';
		  array_push($errors, array('line' =>0, 'message' => $msg));
		  } elseif ($macroNumOther > $macroNumBase) {
		  $diff = array_diff_key($macroList, $macroTrad[$lang]);
		  $missingEnMacro = array_merge($missingEnMacro, $diff);
		  } else {
		  $macroLangOk[] = $macroLang;
		  }
		  } else {
		  $msg = 'The <em>'. $macroLang .'</em> version is missing for the macromodel(s) <em>'. implode(', ', $macroTrad[$lang]).'</em>';
		  array_push($errors, array('line' =>0, 'message' => $msg));

		  }
		  }
		  if (!empty($missingEnMacro)){
		  $msg = 'The <em>english</em> version is missing for the macromodels <em>'. implode(', ', $missingEnMacro).'</em>';
		  array_push($errors, array('line' =>0, 'message' => $msg));
		  } */
		/*
		 * Features identified as filter not linked to any macromodel 
		 */

		//DB : retrieve the list of all feature nid used as filter
		$filterTypeNid = array();
		$nodeType = 'filter_type';

		$allQuery = db_query("	SELECT c.`field_filter_value_list_nid` 
                                        FROM {node} n
                                        INNER JOIN {content_field_filter_value_list} c USING (nid)
                                        WHERE n.type = '%s'", $nodeType);
		while ($result = db_fetch_array($allQuery)) {
			if (!in_array($result['field_filter_value_list_nid'], $filterTypeNid))
				array_push($filterTypeNid, $result['field_filter_value_list_nid']);
		}
		//DB : retrieve the list of all filter's nodes nid of all feature codes actually used as filter
		$filterList = array();
		foreach ($parameters['langs'] as $langFilter) {
			$filterTypeNidLang = $filterTypeNid;
			array_push($filterTypeNidLang, $langFilter);
			$filtersLang = array();
			$allQuery = db_query("	SELECT DISTINCT n.`nid`, n.`language`, c.`field_feature_codes_value`
                                                FROM {node} n 
                                                INNER JOIN {content_field_feature_codes} c USING (nid)
                                                WHERE n.`nid` IN (" . db_placeholders($filterTypeNid, 'int') . ")
                                                AND n.`language` = '%s'", $filterTypeNidLang);
			while ($result = db_fetch_array($allQuery)) {
				$filtersLang[$result['field_feature_codes_value']] = $result['nid'];
			}
			$filterList[$langFilter] = $filtersLang;
		}
		$totalFeatureXmlMacro = array();

		foreach ($xml->table_prodvalcarac->feature as $feature) {
			if (!in_array($feature->caracnu, $totalFeatureXmlMacro)) {
				$totalFeatureXmlMacro[] = $feature->caracnu;
			}
		}



		//XML : check which features taken as filters are part of the DB filters list and which are not
		$foundFilterList = array();
		if (!empty($filterList)) {
			foreach ($parameters['langs'] as $langFoundFilter) {
				$filterListlang = $filterList[$langFoundFilter];
				foreach ($filterListlang as $filterCode => $filterNid) {
					if (isset($featureList['en'][$filterCode])) {
						$foundFilterList[$filterCode] = $filterNid;
					} elseif (in_array($filterCode, $totalFeatureXmlMacro)) {
						$msg = 'The feature with the nid <em>' . $filterNid . '</em> (feature_code :<em> ' . $filterCode . '</em>) has been identified as filter but is attributed to none of the macromodels concerned by this file';
						array_push($warnings, array('line' => 0, 'message' => $msg));
					}
				}
			}
		}
	}
	/*
	 * Listing of Technologies according to the season
	 */

	//DB : retrieve the list of features which are technologies belongint to the current season => ignore them when updating the features linked to current macromodel
	$prodval = 'prodvalcarac';
	$technoNid = array(); //list of nid of techno for the current season
	$techno = array(); // list of feature codses for the current season
	$resTechno = db_query("	SELECT DISTINCT c.`field_feature_codes_value`, n.`nid`
							FROM {node} n 
							INNER JOIN {content_field_feature_codes} c using (nid) 
							INNER JOIN {content_type_prodvalcarac} p using (nid)
							WHERE n.type = '%s' 
							AND p.`field_feature_season_value` = '%s'
							AND p.`field_technologie_value` IN (" . db_placeholders(range(1, 3), 'int') . ")", array($prodval, $season, 1, 2, 3));
	while ($result = db_fetch_array($resTechno)) {
		if (!in_array($result['field_feature_codes_value'], $techno))
			array_push($techno, (int) $result['field_feature_codes_value']);
		if (!in_array($result['nid'], $technoNid))
			array_push($technoNid, (int) $result['nid']);
	}

	//DB : retrieve the list of all technologies 
	$allTechnoF = array(); //all feature codes of all technologies
	$technoSup = array(); //all technologies associated to current macromodels but not part of the current seasons
	$allTechnoS = array(); //all features codes of all technologies with their associated season : they wiil be updated for all season before or equal to the one they belong too
	foreach ($parameters['langs'] as $langTechno) {
		$allTechnoSLang = array();
		$technoSupLang = array();
		$resTechno = db_query("	SELECT DISTINCT c.`field_feature_codes_value`, n.`nid`, p.`field_poids_value`, n.`language`, p.`field_feature_season_value`
                                                            FROM {node} n 
                                                            INNER JOIN {content_field_feature_codes} c using (nid) 
                                                            INNER JOIN {content_type_prodvalcarac} p using (nid)
                                                            WHERE n.type = '%s'
                                                            AND n.`language` = '%s'
                                                            AND p.`field_technologie_value` IN (" . db_placeholders(range(1, 3), 'int') . ")", array($prodval, $langTechno, 1, 2, 3));
		while ($result = db_fetch_array($resTechno)) {
			if (!in_array($result['field_feature_codes_value'], $allTechnoF))
				array_push($allTechnoF, (int) $result['field_feature_codes_value']);
			//if not allready stored and if has a season,

			if (!isset($allTechnoSLang[$result['field_feature_codes_value']]))
				$allTechnoSLang[$result['field_feature_codes_value']] = array('season' => $result['field_feature_season_value'], 'nid' => $result['nid']);
			if (!empty($macro)) {
				//if in the list of xml features belonging to macromodel AND if not in the list of techno belonging to the current season or below
				//keep them asside to link them to the macromodel without updating them (need their nid and rank order)
				$allCurentFeatures = array_keys($featureList['en']);
				$techno_seasons = (int) $result['field_feature_season_value'];
				if (in_array($result['field_feature_codes_value'], $allCurentFeatures) && !in_array($result['nid'], $technoNid) && $techno_seasons > $season) {

					foreach ($featureList['en'][$result['field_feature_codes_value']] as $macroHasTechno) {
						if (!isset($technoSupLang[$macroHasTechno[0]]))
							$technoSupLang[$macroHasTechno[0]] = array();
						//$technoSup['en']['b1234'][i][nid] = nid
						//$technoSup['en']['b1234'][i][poids] = rank order
						$technoSupLang[$macroHasTechno[0]][$result['nid']] = array('nid' => $result['nid'], 'poids' => $result['field_poids_value']);
					}
				}
			}
		}
		$allTechnoS[$langTechno] = $allTechnoSLang;
		$technoSup[$langTechno] = $technoSupLang;
	}
	//list of all features codes of technologies which are not part of the current season
	$ignoreTechnosF = array_diff($allTechnoF, $techno);


	if (!empty($techno) || !empty($macro)) {

		/*
		 * Sum up features listing and check translation / missing nodes : errors
		 */
		if (!empty($techno) && !empty($macro)) {//list all feature/technos exept the techno that are not part of this season update
			//combine features codes of current macromodel + feature codes of current techno
			$macroFeatureX = array_merge(array_keys($featureList['en']), $techno);
			$macroFeature = array();
			foreach ($macroFeatureX as $unique) {
				if (!in_array($unique, $macroFeature)) {
					$macroFeature[] = $unique;
				}
			}


			//retrieve features codes belonging to technologie of other seasons
			$totalFeatureDb = array_diff($macroFeature, $ignoreTechnosF);
		} elseif (!empty($techno)) {
			$totalFeatureDb = array();
			foreach ($techno as $unique) {
				if (!in_array($unique, $totalFeatureDb)) {
					$totalFeatureDb[] = $unique;
				}
			}
		} else {
			$macroFeatureX = array_keys($featureList['en']);
			$macroFeature = array();
			foreach ($macroFeatureX as $unique) {
				if (!in_array($unique, $macroFeature)) {
					$macroFeature[] = $unique;
				}
			}


			$totalFeatureDb = array_diff($macroFeature, $ignoreTechnosF);
		}
		//some of the feature codes we have now belongs all to the current season, but some of them may not belongs to the current range :
		//technologies may have feature codes belonging to another range : filter them by storing only feature codes concerned by this file :
		$foundFeature = array();
		foreach ($xml->table_prodvalcarac->feature as $feature) {
			//if not in the selected one, skip it
			if (!in_array($feature->caracnu, $totalFeatureDb))
				continue;
			//if not in english, skip it
			if ($parameters['filiale'][(int) $feature->filialenu] != 'en')
				continue;
			//if not a feature and not a techno, skip it
			if (!($featureCode = isFeature($feature, $xml, $typeCarac)) && !in_array($feature->caracnu, $techno))
				continue;
			$foundFeature[] = (int) $feature->caracnu;
			$foundFilteredFeature[(int) $feature->caracnu] = (string) $feature->libvalcaraclb;
			$totalFeatureXml = $xml->xpath('/prologue_data/table_prodvalcarac/feature[caracnu=' . $feature->caracnu . ']/filialenu');
			$langInXml = array();
			foreach ($totalFeatureXml as $langXml) {
				if (isset($parameters['filiale'][(int) $langXml]) && !in_array($parameters['filiale'][(int) $langXml], $langInXml))
					$langInXml[] = $parameters['filiale'][(int) $langXml];
			}
			if (count($langInXml) != $parameters['nb_Lang']) {
				$diff = array_diff($parameters['langs'], $langInXml);
				$msg = 'The feature <em>' . $feature->caracnu . '</em> : <em>' . $feature->libvalcaraclb . '</em> is missing in the following language(s) : <em>' . implode(', ', $diff) . '</em>.';
				array_push($errors, array('line' => 0, 'message' => $msg));
			}
		}
		$notFoundFeature = array_diff($totalFeatureDb, $foundFeature);
		if (!empty($notFoundFeature)) {
			$msg = 'The features <em>' . implode(', ', $notFoundFeature) . '</em> <br /> can either be : <ul><li>not a technology and not a feature : will not be created or updated</li><li> a technology belonging to another season : will be updated only if their season is older or equal to the season covered by the file</li></ul>';
			array_push($warnings, array('line' => 0, 'message' => $msg));
		}
		//$diffTechnoMacroReverse = array_diff($techno, array_keys($featureList[$lang]));
		//$diffTechnoMacro = array_diff($techno, $diffTechnoMacroReverse);
	}

	/*
	 * Final report
	 */

	if (!empty($macro) || !empty($techno)) {
		if (!empty($macro)) {
			//if(count($macroLangOk) == $parameters['nb_Lang']){
			if ($macroNumBase > 1) {
				$msg = $macroNumBase . ' macromodels are ready to be imported : <ul>';
			} else {
				$msg = 'The folowing macromodels is ready to be imported : <ul>';
			}
			foreach ($macroTrad[$lang] as $macroCode => $macroName) {
				$msg .= '<li><em>' . $macroCode . '</em> : <em>' . $macroName . '</em> </li>';
			}
			$msg .= '</ul>';
			array_push($statusMsg, array('line' => -1, 'message' => $msg));

			/* } elseif (!empty($missingEnMacro)){
			  $msg = 'The <em>english</em> version is missing for the macromodel(s) <em>'. implode(', ', array_unique($macroTrad[$lang])).'</em>';
			  array_push($errors, array('line' =>0, 'message' => $msg));
			  } */
		} else {
			$msg = 'No existing macromodel is present in this file.';
			array_push($warnings, array('line' => 0, 'message' => $msg));
		}
		foreach ($foundFilteredFeature as $featCode => $featValue) {
			$msg = 'The feature <em>' . $featCode . '</em> : <em>' . $featValue . '</em> is ready to be imported<ul>';

			if (!empty($foundFilterList[$featCode])) {
				$msg .= '<li>as the filter <em>' . $foundFilterList[$featCode] . '</em></li>';
			}
			if (in_array($featCode, $techno)) {
				$msg .= '<li>as a technology</li>';
			}
			if (is_array($featureList['en'][$featCode])) {
				$macrosList = array();
				foreach ($featureList['en'][$featCode] as $macros) {
					//array_push($featureList[$lang][(int)$caracnu], array($tempMacroModco,$macro[$tempMacroModco]);
					$mstr = '<li>' . $macros[1] . '(model code : ' . $macros[0] . ')</li>';
					array_push($macrosList, $mstr);
				}
				if (!empty($macrosList)) {
					$msg .= '<li>linked to macromodel(s) : <ul>' . implode(' ', $macrosList) . '</ul></li>';
				}
			}
			$msg .= '</ul>';
			array_push($statusMsg, array('line' => -1, 'message' => $msg));
		}
	} else {
		$msg = 'No existing macromodel nore actual technology is present in this file : technologies present in this file will be downgraded to the <em>' . $season . '</em> season. ';
		array_push($warnings, array('line' => 0, 'message' => $msg));
	}

	$result = array(
		'statusMsg' => $statusMsg,
		'warnings' => $warnings,
		'errors' => $errors,
		'macromodels' => $macro,
		'features' => $foundFilteredFeature,
		'typeCarac' => $typeCarac,
		'technoSup' => $technoSup,
		'filterList' => $filterList,
		'technoSeason' => $allTechnoS
	);

	return $result;
}

//
// factorisation algo calcul feature
//
function isFeature($feature, $xml, $typeCarac) {
	global $parameters;
	$minLabel = mb_strtolower((string) $feature->libvalcaraclb);
	if (strcmp('ssc', $minLabel) == 0)
		return 2;
	if (strcmp('altium', $minLabel) == 0)
		return 3;
	if (strcmp('rim profile', $typeCarac['lower'][(int) $feature->typcaracco]) == 0)
		return 0;
	if (strcmp('oem availability', $typeCarac['lower'][(int) $feature->typcaracco]) == 0)
		return 0;
	if (!empty($parameters['soft'][(string) $xml['typprod']])) {
		if (strcmp('label', $typeCarac['lower'][(int) $feature->typcaracco]) == 0)
			return 0;
	} else {
		if (strcmp('technologies', $typeCarac['lower'][(int) $feature->typcaracco]) == 0)
			return 0;
	}
	return 1;
}

function mavicimport_ajaxsubmit_importXml($xml, $newXml, $seasonUpdate) {
	global $parameters;


	//data from previous checking
	$statusMsg = array();
	$warnings = $newXml->warnings;
	$errors = array();
	$exFilter = $newXml->filterList; //$filterList['en'][1234(caracnu-featurecode)] = 1324(nid)
	$technoSeason = $newXml->technoSeason; //$allTechnoS['en'][1234(caracnu-featurecode)] = array('season' => $result['field_feature_season_value'], 'nid' => $result['nid']);
	$foundMacromodelCode = $newXml->macromodels; //'model code' => 'model name'
	$foundFilteredFeature = $newXml->features; // 'feature code' => 'feature name'
	$typeCarac = $newXml->typeCarac; // 'lang/lower' => 'typcaracco' => 'label'
	$technoSup = $newXml->technoSup; // 'lang' => 'model code' => i => 'nid'
	//new data                                                     => 'poids'
	$listTaille = array();
	$featureList = array();
	$multiFeatureList = array();

	foreach ($parameters['langs'] as $lang) {
		$listTaille[$lang] = array();
		$featureList[$lang] = array();
	}

	$syncFields = i18nsync_node_fields('prodvalcarac');
	variable_set('i18nsync_nodeapi_prodvalcarac', array());
	$a = 0;
	$xml_season = (int) $xml['mil'];
	$featureFromDb = array();
	$featureFromXml = array();
	foreach ($xml->table_prodvalcarac->feature as $feature) {
		$a++;
		if (!empty($parameters['filiale'][(int) $feature->filialenu]) && in_array($parameters['filiale'][(int) $feature->filialenu], $parameters['langs'])) {

			$lang = $parameters['filiale'][(int) $feature->filialenu];
			$techno_season = (is_array($technoSeason[$lang][(int) $feature->caracnu])) ? (int) $technoSeason[$lang][(int) $feature->caracnu]['season'] : 0;
			if (!isset($featureList[$lang][(int) $feature->caracnu]) && (is_string($foundFilteredFeature[(int) $feature->caracnu]) || $xml_season <= $techno_season)) {
				//
				// add feature and complete technologie and filters
				//
			//update le node prodvalcarac ou le crée
				if ($xml_season <= $techno_season) {
					$newNode = build_mavic_node((string) $feature->libvalcaraclb, false, (string) $feature->consbeneflb, $lang, $technoSeason[$lang][(int) $feature->caracnu]['nid'], 0, false, true, true, false, false, true, true, true);
				} else {
					$newNode = build_mavic_node((string) $feature->libvalcaraclb, 'prodvalcarac', (string) $feature->consbeneflb, $lang, $featureList['en'][(int) $feature->caracnu]->nid, 0, ' INNER JOIN {content_field_feature_codes} c USING (vid)' .
							' INNER JOIN {content_type_prodvalcarac} p USING (vid)' .
							' WHERE c.`field_feature_codes_value`="' . (string) $feature->caracnu . '"' .
							' AND p.`field_feature_season_value` = ' . (string) $xml_season . '', true);
					if ($newNode == 'error') {
						$msg = "English version not found for prodvalcarac ($lang) " . $feature->caracnu . " : " . $feature->libvalcaraclb;
						array_push($errors, array('line' => (int) $feature->caracnu, 'message' => $msg));
						continue;
					}
				}
				$typeFeature = $typeCarac[$lang][(int) $feature->typcaracco];
				if (!isset($newNode->field_technologie[0]['value']))
					$newNode->field_technologie = array(array('value' => 0));
				if (($newNode->field_technologie[0]['value'] > 1) && ($newNode->field_technologie[0]['value'] < 4)) {
					$newNode->pathauto_perform_alias = TRUE; // techno parent ou techno direct
					$newNode->field_page_title = array(array('value' => $newNode->title));
					$newNode->menu['link_title'] = (string) $feature->libvalcaraclb;
					$newNode->menu['weight'] = (100 * $typeFeature['class']) + (int) $feature->classvalcaracnu;
					// le menu existe deja on ne fait que le modifier
				} else {//unset path and pathauto to create a redirection if needed
					$newNode->pathauto_perform_alias = FALSE;
					$newNode->path = '';
				}
				$typOffeature = isFeature($feature, $xml, $typeCarac);
				if (array_key_exists((int) $feature->caracnu, $foundFilteredFeature)) {
					$newNode->field_feature_season = array(array('value' => (string) $newXml->season));
				}
				$newNode->field_feature = array(array('value' => $typOffeature));
				$business_proof2 = array();
				$conso = (string) $feature->consoarglb;
				if (!empty($conso)) {
					$business_proof = explode("\n-", (string) $feature->consoarglb);
					$business_proof[0] = preg_replace('/^-/i', '', $business_proof[0]); // supprime tiret en trop
					foreach ($business_proof as $data) {
						$data = trim($data);
						if (!empty($data))
							$business_proof2[] = array('value' => $data);
					}
				}
				$newNode->field_consoarglb = $business_proof2;
				$newNode->field_logo = array(array('value' => (string) $feature->logocaracco));
				//n'écrase pas le feature code pour un noeud existant, au cas ou il y en aurait d'autres :
				if (!isset($newNode->field_feature_codes)) {
					$newNode->field_feature_codes = array(array('value' => (string) $feature->caracnu));
				} else { //mais récupère les éventuels caracnu associés
					if (count($newNode->field_feature_codes) > 1) {
						foreach ($newNode->field_feature_codes as $field_feature_code) {
							//$multiFeatureList[$feature->caracnu] = array();
							if ($field_feature_code['value'] != (int) $feature->caracnu)
								$featureList[$lang][$field_feature_code['value']] = (int) $feature->caracnu;
						}
					}
				}
				$newNode->field_type = array(array('value' => $typeFeature['label']));
				$newNode->field_poids = array(array('value' => (100 * $typeFeature['class']) + (int) $feature->classvalcaracnu));
				$newNode->field_import_file = array(array('value' => $newXml->filename));
				// passe la techo (ou la feature) en simple filtre à ne pas afficher si elle appartient au type feature 'filter'
				$filtype = trim(strtolower($typeCarac['en'][(int) $feature->typcaracco]['label']));
				if ($filtype == 'filters')
					$newNode->field_technologie = array(array('value' => 4));
				
				//save it

				$msg = save_mavic_node($newNode);
				
				
				array_push($statusMsg, array('line' => -1, 'message' => $msg));
				//if (array_key_exists((int)$feature->caracnu, $foundFilteredFeature)) {
				$featureList[$lang][(int) $feature->caracnu] = $newNode;
				//}
				if ($newNode->field_technologie[0]['value'] == 1) {
					$featureFromXml[$newNode->nid] = array('delta' => (100 * $typeFeature['class']) + (int) $feature->classvalcaracnu, 'title' => $newNode->title);
				}
				if ($newNode->field_technologie[0]['value'] == 3) {
					$tab = array();
					$query = db_query("SELECT * FROM {content_field_child} WHERE nid = %d", $newNode->nid);
					while ($records = db_fetch_array($query)) {
						$tab[$records['field_child_nid']] = $records['delta'];
					}
					$featureFromDb[$newNode->nid] = $tab;
				}
			}
		}
	}

	/**
	 * Ordering feature by prologue rank
	 */
	// Order all feature by their own techno parent
	$featureByTechno = array();
	foreach ($featureFromDb as $technoId => $featureTab) {
		foreach ($featureTab as $featureId => $deltaDb) {
			if (isset($featureFromXml[$featureId])) {
				$featureByTechno[$technoId][$featureId] = array('nid' => $featureId, 'delta' => $featureFromXml[$featureId]['delta'], 'title' => $featureFromXml[$featureId]['title']);
			}
		}
	}
	// Clean feature list from XML / DATABASE
	unset($featureFromXml);
	unset($featureFromDb);
	$orderTab = array();
	// Sort by DELTA, TITLE ASC for each feature
	foreach ($featureByTechno as $technoId => $featureTab) {
		$ordered = $featureTab;
		uasort($ordered, 'cmp_prologue');
		$orderTab[$technoId] = $ordered;
	}
	unset($featureByTechno);

	// Save in DATABASE
	foreach ($orderTab as $technoId => $featureTab) {
		db_query("UPDATE {content_field_child} SET delta = delta+10000 WHERE nid = %d", $technoId);
		$rankBegin = 0;
		foreach ($featureTab as $key => $featureData) {
			if (db_query("UPDATE {content_field_child} SET delta = %d WHERE field_child_nid = %d", $rankBegin, $featureData['nid']) === false) {
			} else {
				//error_log("       Feature ID : " . $featureData['nid'] . " / DELTA ".$featureData['delta']." => $rankBegin\n", 3, './logs/mavicimport_prologue_feature_rank.log');
			}
			$rankBegin++;
		}
	}
	//reactivate the syncronise for the node type :  prodvalcarac
	variable_set('i18nsync_nodeapi_prodvalcarac', $syncFields);

	//
	// vidage memoire
	//
	unset($typeCarac);
	unset($typeCaracClass);
	//
	// complete macro model
	//
	
	
	// save syncronise fields for the node type :  macromodel	
	$syncFields = i18nsync_node_fields('macromodel');
	//deactivate the syncronise for the node type :  macromodel
	variable_set('i18nsync_nodeapi_macromodel', array());
	$b = 0;
	foreach ($xml->table_prodvalmacro->macromodel as $macromodel) {
		$tempMacroModco = strtolower((string) $macromodel->macromodco);
		if (array_key_exists($tempMacroModco, $foundMacromodelCode) && !empty($parameters['filiale'][(int) $macromodel->filialenu])) {
			$b++;
			$lang = $parameters['filiale'][(int) $macromodel->filialenu];
			if (!$nid = db_result(db_query('select n.nid from {node} n INNER JOIN {content_type_macromodel} c using (vid) where n.type="macromodel" and c.`field_modelco_value`="' . $macromodel->macromodco . '" and n.`language`="' . $lang . '"'))) {
				continue;
			}
			$newNode = load_mavic_node($nid);
			if (isset($parameters['body'][(int) $macromodel->milutilco][(string) $macromodel->typprodco]))
				$newNode->body = (string) $macromodel->$parameters['body'][(int) $macromodel->milutilco][(string) $macromodel->typprodco];
			else
				$newNode->body = (string) $macromodel->consumerclaimlb;
			$newNode->field_usp = array(array('value' => (string) $macromodel->descmacrolb));
			$newNode->field_page_description = array(array('value' => (string) $macromodel->descmacrolb));
			$newNode->field_killerpointmacrolb = array(array('value' => (string) $macromodel->killerpoint1macrolb), array('value' => (string) $macromodel->killerpoint2macrolb), array('value' => (string) $macromodel->killerpoint3macrolb));
			$newNode->field_kcbarglb = array(array('value' => (string) $macromodel->kcbarg1lb), array('value' => (string) $macromodel->kcbarg2lb), array('value' => (string) $macromodel->kcbarg3lb));
			$newNode->field_sscnode = array();
			$newNode->field_altiumnode = array();
			$newNode->field_technologienode = array();
			$newNode->field_featurenode = array();
			$newNode->field_weight = array();
			$newNode->field_import_file = array(array('value' => (string) $newXml->filename));

			/** PROLOGUE NEW/UNCHANGED */
			//now defined in the excel file
			if ($macromodel->statuslb == 'Unchanged') {
				$newNode->field_new_product = array(array('value' => 0));
			} else {
				$newNode->field_new_product = array(array('value' => 1));
			}
			/** END PROLOGUE NEW/UNCHANGED */
			$newNode->field_weight_label = array();
			$newNode->pathauto_perform_alias = TRUE;
			$newNode->menu['weight'] = (int) $macromodel->classrangemacronu;
			if ($xml['typprod'] == 'mty')
				$newNode->menu['weight'] -= 2000;
			else if (!empty($parameters['soft'][(string) $xml['typprod']])) {
				$milesim = (int) $macromodel->milutilco;
				// pour import saison hiver les prod hiver passe en 1er
				// mettre 0 quand mise à jour du site pour l'hiver, 1 quand mise à jour du site pour l'été
				if (($milesim >= 10000) && (($milesim % 2) == $seasonUpdate))
					$newNode->menu['weight'] -= 1000;
			}
			if (!empty($newNode->menu['nodesymlinks']['items']))
				foreach ($newNode->menu['nodesymlinks']['items'] as $key => $value)
					$newNode->menu['nodesymlinks']['items'][$key]['weight'] = (int) $macromodel->classrangemacronu;

			// weight
			switch ($xml['typprod']) {
				case 'maf' : { // footwear
						$brace1 = strpos((string) $macromodel->poidsco, '(');
						$brace2 = strpos((string) $macromodel->poidsco, ')');
						$weight = substr((string) $macromodel->poidsco, 0, $brace1);

						$weight = $weight / 2;
						$size = substr((string) $macromodel->poidsco, $brace1 + 1, $brace2 - $brace1 - 1);

						if ($weight != 0) {
							if (empty($parameters['size'][$size])) {
								$msg = 'cannot found size for :' . $size;
								array_push($errors, array('line' => $nid, 'message' => $msg));
							} else {
								$newNode->field_weight = array(array('value' => $weight . $parameters['translate'][' grams (size '][$lang] . $parameters['size'][$size][$lang] . $parameters['translate'][' UK)'][$lang]));
								$newNode->field_weight_label = array(array('value' => 'Weight'));
							}
						}
						break;
					}
				case 'mpe' : { // pedal
						$weight = (string) $macromodel->poidsco;
						$weight = $weight / 2;
						if ($weight != 0) {
							$newNode->field_weight = array(array('value' => $weight . $parameters['translate'][' grams / pedal'][$lang]));
							$newNode->field_weight_label = array(array('value' => 'Weight'));
						}
						break;
					}
				case 'mhe' :  // helmets
				case 'mty' : { // tyres
						$weight = (string) $macromodel->poidsco;
						if ($weight != 0) {
							$newNode->field_weight = array(array('value' => $weight . $parameters['translate'][' grams'][$lang]));
							$newNode->field_weight_label = array(array('value' => 'Weight'));
						}
						break;
					}
				case 'mja' : { // rims
						$listweight700 = $xml->xpath('/prologue_data/table_techspecs/techspecs-macro/macromodel[@macronu=' . $macromodel->macronu . ']/techspec[@techdefnu=85]');
						$listweight650 = $xml->xpath('/prologue_data/table_techspecs/techspecs-macro/macromodel[@macronu=' . $macromodel->macronu . ']/techspec[@techdefnu=86]');
						$listweight700[0] = (string) $listweight700[0];
						$listweight650[0] = (string) $listweight650[0];
						if (!empty($listweight700[0]) || !empty($listweight650[0])) {
							$newNode->field_weight[] = array('value' => '&nbsp;'); // retour a la ligne apres le weight:
							$newNode->field_weight_label[] = array('value' => 'Weight');
							if (!empty($listweight700[0])) {
								$newNode->field_weight[] = array('value' => $listweight700[0] . $parameters['translate'][' grams'][$lang]);
								$newNode->field_weight_label[] = array('value' => 'Ø 700');
							}
							if (!empty($listweight650[0])) {
								$newNode->field_weight[] = array('value' => $listweight650[0] . $parameters['translate'][' grams'][$lang]);
								$newNode->field_weight_label[] = array('value' => 'Ø 650');
							}
						}
						break;
					}
				case 'mro' : { // wheels
						$techdefnu = $newNode->field_default_weight[0]['value'];
						if (empty($techdefnu)) {
							drupal_set_message('cannot found default weight for :' . $newNode->title, 'error');
						} else {
							$defaultWeight = $xml->xpath('/prologue_data/table_techspecs/techspecs-macro/macromodel[@macronu=' . $macromodel->macronu . ']/techspec[@techdefnu=' . $techdefnu . ']');
							$defaultWeight[0] = (string) $defaultWeight[0];
							if (!empty($defaultWeight[0])) {
								$brace1 = strpos($defaultWeight[0], '(');
								$brace2 = strpos($defaultWeight[0], ')');
								if ($brace1 !== false && $brace2 !== false) {
									$weight = substr($defaultWeight[0], 0, $brace1);
									$version = t('(' . substr($defaultWeight[0], $brace1 + 1, $brace2 - $brace1 - 1) . ')');
								} else {
									$weight = $defaultWeight[0];
									$version = '';
								}
								$newNode->field_weight[] = array('value' => $weight . $parameters['translate'][' grams'][$lang] . ' ' . $version);
								$deflabel = $parameters['techdefnu'][$techdefnu];
								$newNode->field_weight_label[] = array('value' => 'Weight (' . $deflabel . ')');
							}
							foreach ($parameters['techdefnu'] as $techkey => $label) {
								if ($techkey != $techdefnu) {
									$weight = $xml->xpath('/prologue_data/table_techspecs/techspecs-macro/macromodel[@macronu=' . $macromodel->macronu . ']/techspec[@techdefnu=' . $techkey . ']');
									$weight = (string) $weight[0];
									if (!empty($weight)) {
										$newNode->field_weight[] = array('value' => $weight . $parameters['translate'][' grams'][$lang]);
										$newNode->field_weight_label[] = array('value' => $label);
									}
								}
							}
						}
						break;
					}
			}
			$caracnuList = $xml->xpath('/prologue_data/table_prodcarac/assoc-model-feature[macronu=' . $macromodel->macronu . ']/caracnu');
			if (is_array($caracnuList)) { // link features - macromodel
				$feature_arr = array();
				$techno_arr = array();
				$filter_arr = array();
				foreach ($caracnuList as $caracnu) {
					$featureNodeTmp = $featureList[$lang][(int) $caracnu];
					if (!empty($featureNodeTmp)) {
						//vérifie si la techno associée est un doublon, auquel cas l'attribue à l'original
						if (is_int($featureNodeTmp)) {
							$featureNode = $featureList[$lang][$featureNodeTmp];
						} else {
							$featureNode = $featureNodeTmp;
						}
						$tempFilterArray = array('nid' => $exFilter[$lang][(int) $caracnu]);
						if (isset($exFilter[$lang][(int) $caracnu]))
							array_push($filter_arr, $tempFilterArray);
						if ($featureNode->field_feature[0]['value'] == 2)
							$newNode->field_sscnode[] = array('nid' => $featureNode->nid);
						if ($featureNode->field_feature[0]['value'] == 3)
							$newNode->field_altiumnode[] = array('nid' => $featureNode->nid);
						if ($featureNode->field_feature[0]['value'] == 1)
							$feature_arr[] = array('nid' => $featureNode->nid, 'poids' => $featureNode->field_poids[0]['value']);
						if ($featureNode->field_technologie[0]['value'] == 1 || $featureNode->field_technologie[0]['value'] == 2)
							$techno_arr[] = array('nid' => $featureNode->nid, 'poids' => $featureNode->field_poids[0]['value']);
					}
				}
				//list of nodes being filters:
				$newNode->field_filter_value = $filter_arr;
				//only technologies of the same season has been updated and thus linked to the new macromodels => need to add linked techno of other seasons if any
				//'lang' => 'model code' => i => 'nid'
				//                            => 'poids'
				$currentTechnoSup = (array) $technoSup[$lang][$tempMacroModco];
				$allMacroTechnoTmp = array_merge($techno_arr, $currentTechnoSup);
				//some feature code changes along seasons, needs to remove eventual duplicates 
				$allMacroTechno = array();
				$allMacroTechnoTmpNid = array();
				foreach ($allMacroTechnoTmp as $almtt) {
					if (!in_array($almtt['nid'], $allMacroTechnoTmpNid)) {
						$allMacroTechnoTmpNid[] = $almtt['nid'];
						$allMacroTechno[] = $almtt;
					}
				}
				usort($feature_arr, 'cmp_feature');
				usort($allMacroTechno, 'cmp_feature');
				$newNode->field_featurenode = $feature_arr;
				$newNode->field_technologienode = $allMacroTechno;
			}
			

			$msg = save_mavic_node($newNode);
			array_push($statusMsg, array('line' => -1, 'message' => $msg));
			unset($newNode);
		} else {
			//error_log("Les macros ne sont pas passé XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX \n\r", 3, "D:/projects/mavic/trunk/www/logs/debug_log.log");
		}
	}
	//reactivate the syncronise for the node type :  macromodel
	variable_set('i18nsync_nodeapi_macromodel', $syncFields);
	//
	// vidage memoire
	//
	unset($featureList);
	//
	// complete article : size
	//
	get_mavic_size($xml, $warnings, $listTaille, $newXml->filename);
	/*
	 * TODO: clear unlinked / unused anymore feature (old season)
	 */
	$result = array(
		'statusMsg' => $statusMsg,
		'warnings' => $warnings,
		'errors' => $errors
	);


	return $result;
}

function cmp_prologue($a, $b) {
	if ($a['delta'] == $b['delta']) {
		return strcmp($a['title'], $b['title']);
	}
	return ($a['delta'] < $b['delta']) ? -1 : 1;
}

function cmp_feature($a, $b) {
	if ($a['poids'] == $b['poids'])
		return 0;
	return ($a['poids'] < $b['poids']) ? -1 : 1;
}

function get_mavic_size(&$xml, &$errors, $listTaille, $newXmlFileName) {
	global $parameters;

	switch ($xml['typprod']) {
		case 'maa' : { // apparel
				$func_file = 'file_maa_size';
				break;
			}
		case 'mag' : {
				// apparel
				$func_file = 'file_maa_size';
				break;
			}
		case 'maf' : { // footwear
				$func_file = 'file_maf_size';
				break;
			}
		case 'mhe' : { // helmet
				$func_file = 'file_mhe_size';
				break;
			}
	}

	foreach ($xml->table_prodvaltaille->taille as $taille) {
		$lang = $parameters['filiale'][(int) $taille->filialenu];
		if (empty($listTaille[$lang][(string) $taille->modelenu]))
			$listTaille[$lang][(string) $taille->modelenu] = array();
		if (empty($parameters['size'][(string) $taille->tailleco])) {
			$msg = 'cannot found size for :' . $taille->tailleco;
			array_push($errors, array('line' => 0, 'message' => $msg));
			continue;
		}
		if ($func_file == 'file_mhe_size') {//same size in all lang for helmet
			$listTaille[$lang][(string) $taille->modelenu][(int) $taille->tritaillenu] = $taille->tailleco;
		} elseif ($xml['typprod'] == 'mag' && $lang == 'ja') {//keep en translation size for gloves in ja
			$listTaille[$lang][(string) $taille->modelenu][(int) $taille->tritaillenu] = $parameters['size'][(string) $taille->tailleco]['en'];
		} else {
			$listTaille[$lang][(string) $taille->modelenu][(int) $taille->tritaillenu] = $parameters['size'][(string) $taille->tailleco][$lang];
		}
	}
	// save syncronise fields		
	$syncFields = i18nsync_node_fields('article');
	//deactivate the syncronise
	variable_set('i18nsync_nodeapi_article', array());
	foreach ($listTaille as $lang => $listModele) {
		foreach ($listModele as $modeleNu => $model) {
			ksort($model);
			$modeleCo = $xml->xpath('/prologue_data/table_prodmodel/model[modelenu=' . $modeleNu . ']/modeleco');
			if (!$nid = db_result(db_query('select n.nid from {node} n where n.type="article" and n.`title`="' . $modeleCo[0] . '" and n.`language`="' . $lang . '"'))) {
				continue;
			}
			$newNode = node_load($nid);
			$newNode->pathauto_perform_alias = FALSE;
			if ($xml['typprod'] == 'maa' || $xml['typprod'] == 'mag' || $xml['typprod'] == 'maf' || $xml['typprod'] == 'mhe') {
				$func_file($newNode, $model);
			}
			$newNode->field_import_file = array(array('value' => $newXmlFileName));

			node_save($newNode);
		}
	}
	//reactivate the syncronise for the node type :  article
	variable_set('i18nsync_nodeapi_article', $syncFields);

	return $errors;
}

function file_maf_size(&$newNode, $model) {
	if (count($model) > 0)
		$newNode->field_size = array(array('value' => reset($model) . ' - ' . array_pop($model)));
	else
		$newNode->field_size = array(array('value' => @reset($model)));
}

function file_maa_size(&$newNode, $model) {
	$newNode->field_size = array(array('value' => implode(' / ', $model)));
}

function file_mhe_size(&$newNode, $model) {
	$newNode->field_size = array(array('value' => implode(' / ', $model)));
}

function mavicimport_cleanCell($cell, $type = 'string', $lower = false, $forceTriming = false) {
	$tempcell = trim(strval($cell));
	/*
	 * TODO ; pour  les features codes
	 */
	if (!empty($tempcell)) {
		if ($forceTriming)
			$tempcell = trim($tempcell);
		//comme ça ne se voit pas : cette regexp cible les espace insécable entré en dur : " "
			$tempcell = preg_replace('/^( )*([^ ]*)( )*$/u', '$2', $tempcell);
		switch ($type) {
			case 'string':
				$tempcell = str_replace('""', '"', $tempcell);
				if ($lower) {

					$tempcell = strtolower($tempcell);
				}
				$tempcell = (string) $tempcell;
				break;

			case 'int':
				if (is_numeric($tempcell)) {
					$tempcell = ((int) $tempcell == $tempcell) ? (int) $tempcell : (string) $tempcell;
				}
				break;

			default:

				break;
		}
	} else {
		$tempcell = (string) "0";
	}
	return $tempcell;
}

function mavicimport_ajaxsubmit_checkXlsxData(&$objPHPExcel) {

	global $parameters;
	require_once('lib/PHPExcel/IOFactory.php');
	require_once('lib/mavicimport_ajaxsubmit_limitFilter.php');

	//while checking, build and store by type nodes to be either created, updated or deleted :				
	$allSheets = array();

	//RANGE_RANK_LANDSCAPE CHECKING
	$lineNodes = array();
	$catNodes = array();
	$tabNodes = array();
	//RANGE_FILTER
	$filterTypeNodes = array();
	$filterNodes = array();
	//LINELIST
	$macroNodes = array();
	$articleNodes = array();
	//TECHNOLOGIES
	$technoLineNodes = array();
	$technoCatNodes = array();
	$technoNodes = array();
	$technoNidDone = array();

	foreach ($parameters['langs'] as $lang) {
		$filterTypeNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'delete' => array()
		);
		$filterNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'filterRemove' => array(),
		);
		$lineNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'delete' => array()
		);
		$catNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'delete' => array()
		);
		$tabNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'delete' => array()
		);
		$macroNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'delete' => array()
		);
		$articleNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'delete' => array()
		);
		$technoLineNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'delete' => array()
		);
		$technoCatNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'delete' => array()
		);
		$technoNodes[$lang] = array(
			'create' => array(),
			'update' => array(),
			'technoRemove' => array()
		);
		$technoNidDone[$lang] = array();
	}


	/*	 * **************************************************************************************************************
	 * 
	 * RANGE_RANK_LANDSCAPE CHECKING
	 */
	$rangeErrors = array();
	$rangeWarnings = array();
	$rangeStatus = array();

	//DB : list existing line
	$exLine = array();
	$nodeType = 'line';
	$allQuery = db_query("	SELECT  n.`nid`, n.`language`, r.`body`, r.`title`
							FROM {node} n 
							INNER JOIN {node_revisions} r USING (nid) 
							WHERE n.type = '%s'", array($nodeType));
	while ($result = db_fetch_array($allQuery)) {
		if (!isset($exLine[$result['language']]))
			$exLine[$result['language']] = array();
		$exLine[$result['language']][$result['body']] = array('nid' => $result['nid'], 'title' => $result['title']);
	}

	//DB : list existing cat
	$exCat = array();
	$nodeType = 'category';
	$allQuery = db_query("	SELECT  n.`nid`, n.`language`, r.`body`
							FROM {node} n 
							INNER JOIN {node_revisions} r USING (nid) 
							WHERE n.type = '%s'", array($nodeType));
	while ($result = db_fetch_array($allQuery)) {
		if (!isset($exCat[$result['language']]))
			$exCat[$result['language']] = array();
		$exCat[$result['language']][$result['body']] = array('nid' => $result['nid']);
	}

	//DB : list existing tab
	$exTab = array();
	$nodeType = 'family';
	$allQuery = db_query("	SELECT  n.`nid`, n.`language`, r.`body`
							FROM {node} n 
							INNER JOIN {node_revisions} r USING (nid) 
							WHERE n.type = '%s'", array($nodeType));
	while ($result = db_fetch_array($allQuery)) {
		if (!isset($exTab[$result['language']]))
			$exTab[$result['language']] = array();
		$exTab[$result['language']][$result['body']] = array('nid' => $result['nid']);
	}

	//load xls data
	$objRangeSheet = $objPHPExcel->getSheetByName('RANGE_RANK_LANDSCAPE');
	$highestRow = $parameters['sheets']['RANGE_RANK_LANDSCAPE']['max_row'];
	$highestColumnIndex = count(range('A', $parameters['sheets']['RANGE_RANK_LANDSCAPE']['col_range']));
	$rangelistColumn = array_flip($parameters['sheets']['RANGE_RANK_LANDSCAPE']['col_name']);

	for ($row_nb = 2; $row_nb <= $highestRow; ++$row_nb) {

		//LANDSCAPE
		$landsape = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn['LANDSCAPE'], $row_nb)->getValue(), 'string', true, true);
		if (empty($parameters['landscape'][$landsape])) {
			$msg = 'Unrecognized landscape <em>' . $landsape . '</em>, allowed values are : <ul>';
			$msg .= '<li><em>all</em></li>';
			$msg .= '<li><em>company</em></li>';
			$msg .= '<li><em>road</em></li>';
			$msg .= '<li><em>triathlon</em></li>';
			$msg .= '<li><em>roadaero</em> or <em>road_aero</em></li>';
			$msg .= '<li><em>roadmountain </em>or <em>road_mountain</em></li>';
			$msg .= '<li><em>mtb</em></li>';
			$msg .= '<li><em>mtbextreme </em>or <em>mtb_extreme</em></li>';
			$msg .= '<li><em>mtbcross-country </em>or <em>mtb_cross-country</em></li>';
			$msg .= '<li><em>mtballmountain </em>or <em>mtb_all_mountain</em></li>';
			$msg .= '<li><em>track</em></li>';
			$msg .= '</ul>)';
			array_push($rangeErrors, array('line' => $row_nb, 'message' => $msg));
			$landscape = 'all';
		}

		//LINE, CATEGORY-FAMILY(node-type: category), Tab (node-type : family...)
		$msgA = array();
		if ('0' === $sysLine = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn['LINE_SYSTEM'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>line system name</em>';
		if ('0' === $sysTabS = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn['TAB_SYSTEM'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>family-category system name</em>';
		if ('0' === $sysCatS = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn['CATEGORY_SYSTEM'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>tab system name</em>';

		if ('0' === $orderLine = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn['LINE_ORDER'], $row_nb)->getValue(), 'int', false, true))
			$msgA[] = '<em>line order</em>';
		if ('0' === $orderTab = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn['TAB_ORDER'], $row_nb)->getValue(), 'int', false, true))
			$msgA[] = '<em>family-category order</em>';
		if ('0' === $orderCat = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn['CATEGORY_ORDER'], $row_nb)->getValue(), 'int', false, true))
			$msgA[] = '<em>tab order</em>';

		if (!empty($msgA)) {
			array_push($rangeErrors, array('line' => $row_nb, 'message' => "Empty cell on col. : " . implode(', ', $msgA) . "."));
			continue;
		}
		$sysCat = $sysLine . '/' . $sysCatS;
		$sysTab = $sysCat . '/' . $sysTabS;
		$tabPict = str_replace('/', '', $sysLine . '_' . $sysCatS);
		foreach ($parameters['langs'] as $lang) {
			//
			// LINE
			//
			if (!isset($lineNodes[$lang][$sysLine])) {
				$lineTradCol = 'LINE_' . $lang;
				$lineTrad = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn[$lineTradCol], $row_nb)->getValue(), 'string', false, true);
				if (!empty($lineTrad)) {
					if (isset($exLine[$lang][$sysLine])) {//update : status
						$lineNode = build_mavic_node($lineTrad, false, $sysLine, $lang, $exLine[$lang][$sysLine]['nid'], 0, false, true, false, array('weight' => $orderLine, 'link_title' => $lineTrad, 'menu_name' => 'menu-primary-links-' . $lang, 'expanded' => 1), false, true, false, true);
						$lineNodes[$lang]['update'][$sysLine] = $lineNode;
						$msg = 'The line </em>' . $lineTrad . '</em>, nid[</em>' . $exLine[$lang][$sysLine]['nid'] . '</em>] (<em>' . $lang . '</em>) is ready to be updated';
						array_push($rangeStatus, array('line' => $row_nb, 'message' => $msg));
					} elseif (isset($exLine['en'][$sysLine])) {//we never know... error
						$msg = 'The <em>' . $lang . '</em> version of line </em>' . $lineTrad . '</em> has not been found while the english one exists...';
						array_push($rangeErrors, array('line' => $row_nb, 'message' => $msg));
					} else {//definitely not existing : error
						$msg = 'The line </em>' . $sysLine . '</em> (<em>' . $lang . '</em>) does not exist in DB : please create it throught the admin interface or correct this sheet.';
						array_push($rangeErrors, array('line' => $row_nb, 'message' => $msg));
					}
				} else {
					array_push($rangeErrors, array('line' => $row_nb, 'message' => "The <em>" . $lang . "</em> translation for line is missing"));
				}
			}

			//
			// CATEGORY-Family
			//
			$catTradCol = 'CATEGORY_' . $lang;
			$catTrad = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn[$catTradCol], $row_nb)->getValue(), 'string', false, true);
			if (!empty($catTrad)) {
				if (isset($exCat[$lang][$sysCat])) {//update : status
					$catNode = build_mavic_node($catTrad, false, $sysCat, $lang, $exCat[$lang][$sysCat]['nid'], 0, false, true, false, array('weight' => $orderCat, 'link_title' => $catTrad, 'menu_name' => 'menu-primary-links-' . $lang, 'expanded' => 1, 'plid' => $lineNodes[$lang]['update'][$sysLine]->menu['mlid']), false, true, false, true);
					$catNode->menu['customized'] = 1;
					$catNode->menu['options'] = array('attributes' => array('title' => $tabPict)); // utilise pour calculer l'image
					$catNodes[$lang]['update'][$sysCat] = $catNode;
					$msg = 'The category(family) </em>' . $catTrad . '</em>, nid[</em>' . $exCat[$lang][$sysCat]['nid'] . '</em>] (<em>' . $lang . '</em>) is ready to be updated';
					array_push($rangeStatus, array('line' => $row_nb, 'message' => $msg));
				} elseif (isset($exCat['en'][$sysCat])) {//we never know... error
					$msg = 'The <em>' . $lang . '</em> version of category(family) </em>' . $catTrad . '</em> has not been found while the english one exists...';
					array_push($rangeErrors, array('line' => $row_nb, 'message' => $msg));
				} else {//definitely not existing : error
					$msg = 'The category(family) </em>' . $sysCat . '</em> (<em>' . $lang . '</em>) does not exist in DB : please create it throught the admin interface or correct this sheet.';
					array_push($rangeErrors, array('line' => $row_nb, 'message' => $msg));
				}
			} else {
				array_push($rangeErrors, array('line' => $row_nb, 'message' => "The <em>" . $lang . "</em> translation for line is missing"));
			}

			//
			// TAB
			//
			$tabTradCol = 'TAB_' . $lang;
			$tabTrad = mavicimport_cleanCell($objRangeSheet->getCellByColumnAndRow($rangelistColumn[$tabTradCol], $row_nb)->getValue(), 'string', false, true);
			if (!empty($tabTrad)) {
				if (isset($exTab[$lang][$sysTab])) {//update : status
					$tabNode = build_mavic_node($tabTrad, false, $sysTab, $lang, $exTab[$lang][$sysTab]['nid'], 1, false, true, false, array('weight' => $orderTab, 'link_title' => $tabTrad, 'menu_name' => 'menu-primary-links-' . $lang, 'expanded' => 1, 'plid' => $catNodes[$lang]['update'][$sysCat]->menu['mlid']), false, true, false, true);
					$tabNode->field_landscape = $parameters['landscape'][$landsape];
					$tabNode->field_filter_macro = array();
					$tabNodes[$lang]['update'][$sysTab] = $tabNode;
					$msg = 'The tab </em>' . $tabTrad . '</em>, nid[</em>' . $exTab[$lang][$sysTab]['nid'] . '</em>] (<em>' . $lang . '</em>) is ready to be updated';
					array_push($rangeStatus, array('line' => $row_nb, 'message' => $msg));
				} elseif (isset($exTab['en'][$sysTab])) {//we never know... error
					$msg = 'The <em>' . $lang . '</em> version of tab </em>' . $tabTrad . '</em> has not been found while the english one exists...';
					array_push($rangeErrors, array('line' => $row_nb, 'message' => $msg));
				} else {//definitely not existing : error
					$msg = 'The tab </em>' . $sysTab . '</em> (<em>' . $lang . '</em>) does not exist in DB : please create it throught the admin interface or correct this sheet.';
					array_push($rangeErrors, array('line' => $row_nb, 'message' => $msg));
				}
			} else {
				array_push($rangeErrors, array('line' => $row_nb, 'message' => "The <em>" . $lang . "</em> translation for line is missing"));
			}
		}
	}

	/*
	 * RANGE_RANK_LANDSCAPE REPORT
	 */

	//list of node found in the db but not in the file:
	$msgA = array();
	foreach ($parameters['langs'] as $lang) {
		foreach ($exLine[$lang] as $sysName => $nid) {
			if (!isset($lineNodes[$lang]['update'][$sysName])) {
				$lineNodes[$lang]['delete'][$sysName] = $nid['nid'];
				//node_delete($nid['nid']);
				$msgA[] = '<li>line <em>' . $sysName . '</em>, nid <em>' . $nid['nid'] . '</em> </li>';
			}
		}
		foreach ($exCat[$lang] as $sysName => $nid) {
			if (!isset($catNodes[$lang]['update'][$sysName])) {
				$catNodes[$lang]['delete'][$sysName] = $nid['nid'];
				$msgA[] = '<li>category(family) <em>' . $sysName . '</em>, nid <em>' . $nid['nid'] . '</em> </li>';
				//node_delete($nid['nid']);
			}
		}
		foreach ($exTab[$lang] as $sysName => $nid) {
			if (!isset($tabNodes[$lang]['update'][$sysName])) {
				$tabNodes[$lang]['delete'][$sysName] = $nid['nid'];
				$msgA[] = '<li>Tab <em>' . $sysName . '</em>, nid <em>' . $nid['nid'] . '</em> </li>';
				//node_delete($nid['nid']);
			}
		}
	}
	if (!empty($msgA)) {
		$msg = 'The folowing nodes found in the DB do not belong to the data submitted : <ul>';
		$msg .= implode(' ', $msgA);
		$msg .= '</ul><strong>They will be deleted before the import process begins.. </strong>'; //deleted.</strong>';
		array_push($rangeWarnings, array('line' => 0, 'message' => $msg));
	}

	//sum up	
	$allSheets['RANGE_RANK_LANDSCAPE'] = array(
		'statusMsg' => $rangeStatus,
		'warnings' => $rangeWarnings,
		'errors' => $rangeErrors,
		'nodes' => array(
			'line' => $lineNodes,
			'category' => $catNodes,
			'family' => $tabNodes
		)
	);
	//garbage col
	unset($lineNodes);
	unset($catNodes);
	unset($tabNodes);
	unset($rangeStatus);
	unset($rangeWarnings);
	unset($rangeErrors);


	/*	 * **************************************************************************************************************
	 * 
	 * RANGE_FILTER + RANGE_FILTER_TRANSLATION CHECKING 
	 */
	$filterErrors = array();
	$filterWarnings = array();
	$filterStatus = array();


	//DB : retrieve the list of all filters type
	$filterTypeNid = array();
	$exFilterType = array();
	$nodeType = 'filter_type';
	foreach ($parameters['langs'] as $langL) {
		$allQuery = db_query("	SELECT n.`nid`, r.`body`, c.`field_filter_value_list_nid` 
					FROM {node} n
					INNER JOIN {node_revisions} r USING (nid)
					INNER JOIN {content_field_filter_value_list} c USING (nid)
					WHERE n.type = '%s'
					AND n.`language` = '%s'", array($nodeType, $langL));
		while ($result = db_fetch_array($allQuery)) {
			if (!in_array($result['field_filter_value_list_nid'], $filterTypeNid))
				array_push($filterTypeNid, $result['field_filter_value_list_nid']);
			if (!isset($exFilterType[$langL]))
				$exFilterType[$langL] = array();
			$exFilterType[$langL][$result['body']] = array('nid' => $result['nid']);
		}
	}
	//DB : retrieve the list of all feature codes actually used as filter
	$exFilter = array();
	$exFilterNids = array();
	if (!empty($filterTypeNid)) {
		foreach ($parameters['langs'] as $langL) {
			$quePar = $filterTypeNid;
			array_push($quePar, $langL);
			$allQuery = db_query("	SELECT n.`nid`, c.`field_feature_codes_value`, p.`field_filter_title_value`, p.`field_filter_main_value`
						FROM {node} n 
						INNER JOIN {content_field_feature_codes} c USING (nid)
						INNER JOIN {content_type_prodvalcarac} p USING (nid)
						WHERE n.`nid` IN (" . db_placeholders($filterTypeNid, 'int') . ")
						AND n.`language` = '%s'", $quePar);

			while ($result = db_fetch_array($allQuery)) {

				if (!isset($exFilter[$langL]))
					$exFilter[$langL] = array();
				if (!isset($exFilterNids[$langL]))
					$exFilterNids[$langL] = array();
				if (!isset($exFilterNids[$langL][$result['nid']]))
					$exFilterNids[$langL][$result['nid']] = array();
				if ($result['field_filter_main_value'] == NULL)
					$result['field_filter_main_value'] = 0;
				if (!in_array($result['field_feature_codes_value'], $exFilterNids[$langL][$result['nid']]))
					$exFilterNids[$langL][$result['nid']][] = $result['field_feature_codes_value'];
				$exFilter[$langL][$result['field_feature_codes_value']] = array('nid' => $result['nid'], 'filterTitle' => $result['field_filter_title_value'], 'isMain' => $result['field_filter_main_value']);
			}
		}
	}

	//DB: retrieve the list of all features
	$exFeatures = array();
	$nodeType = 'prodvalcarac';
	foreach ($parameters['langs'] as $langL) {
		$allQuery = db_query("	SELECT n.`nid`, c.`field_feature_codes_value`, p.`field_filter_title_value`, p.`field_filter_main_value`
					FROM {node} n 
					INNER JOIN {content_field_feature_codes} c USING (nid)
					INNER JOIN {content_type_prodvalcarac} p USING (nid)
					WHERE n.`type` = '%s'
					AND n.`language` = '%s'", array($nodeType, $langL));

		while ($result = db_fetch_array($allQuery)) {

			if (!isset($exFeatures[$langL]))
				$exFeatures[$langL] = array();
			if ($result['field_filter_main_value'] == NULL)
				$result['field_filter_main_value'] = 0;
			if (!isset($exFeatures[$langL][$result['field_feature_codes_value']]))
				$exFeatures[$langL][$result['field_feature_codes_value']] = array();
			$exFeatures[$langL][$result['field_feature_codes_value']][] = array('nid' => $result['nid'], 'filterTitle' => $result['field_filter_title_value'], 'isMain' => $result['field_filter_main_value']);
		}
	}

	//load xls data
	$objFilterSheet = $objPHPExcel->getSheetByName('RANGE_FILTER');
	$highestRow = $parameters['sheets']['RANGE_FILTER']['max_row'];
	$highestColumnIndex = count(range('A', $parameters['sheets']['RANGE_FILTER']['col_range']));
	$filterlistColumn = array_flip($parameters['sheets']['RANGE_FILTER']['col_name']);

	$objFilterTradSheet = $objPHPExcel->getSheetByName('RANGE_FILTER_TRANSLATION');
	$highestTradRow = $parameters['sheets']['RANGE_FILTER_TRANSLATION']['max_row'];
	$highestTradColumnIndex = count(range('A', $parameters['sheets']['RANGE_FILTER_TRANSLATION']['col_range']));
	$filterlistTradColumn = array_flip($parameters['sheets']['RANGE_FILTER_TRANSLATION']['col_name']);

	//Build trad assoc array
	$filterTrad = array();
	for ($row_nb = 2; $row_nb <= $highestTradRow; ++$row_nb) {
		$trad = array();
		foreach ($parameters['langs'] as $langU) {
			$tradtemp = mavicimport_cleanCell($objFilterTradSheet->getCellByColumnAndRow($filterlistTradColumn[$langU], $row_nb)->getValue());
			if (empty($tradtemp)) {
				$trad[$langU] = 'tradmissing';
				$msg = "<em>" . $langU . "</em> translation missing.";
				array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
			} else {
				$trad[$langU] = $tradtemp;
			}
		}
		$filterTrad[strtolower(mavicimport_cleanCell($objFilterTradSheet->getCellByColumnAndRow($filterlistTradColumn['systemName'], $row_nb)->getValue()))] = $trad;
	}


	$tempFilters = array(); //store featureId of created/updated new node : also needed to updtade categories
	$i = 0; //current filter type

	/*
	 * Start checking RANGE_FILTER
	 */
	for ($row_nb = 2; $row_nb <= $highestRow; ++$row_nb) {
		$filNum = 0; //count the number of filters
		$lineN = '';
		$familyN = '';
		$tabN = '';
		$filterN = '';
		$label = ''; //filter type system name : $lineN/$familyN/$tabN/filtertypeName
		$filtersN = ''; //filter system name
		$filterOrder = array();
		$lineControle = array_keys($allSheets['RANGE_RANK_LANDSCAPE']['nodes']['line']['en']['update']);
		$catControle = array_keys($allSheets['RANGE_RANK_LANDSCAPE']['nodes']['category']['en']['update']);
		$tabControle = array_keys($allSheets['RANGE_RANK_LANDSCAPE']['nodes']['family']['en']['update']);

		$mayBeASiblingsFeatureCodeCreate = FALSE; //true if an identical NEW filter system name is found on another row
		$mayBeASiblingsFeatureCodeUpdate = FALSE; //true if an identical EXISTING filter system name is found on another row
		for ($col_nb = 0; $col_nb < $highestColumnIndex; $col_nb++) {
			$cell_value = mavicimport_cleanCell($objFilterSheet->getCellByColumnAndRow($col_nb, $row_nb)->getValue());
			if ('0' === $cell_value) {
				if ($col_nb < 3) {
					$msg = "Missing line, family or tab data on column " . $col_nb . ".";
					array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
					continue;
				}
				if ($col_nb > 3 && $col_nb % 2) {
					$msg = "Missing feature id on column " . $col_nb . ".";
					array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
					continue;
				} else {
					break;
				}
			}
			if ($col_nb > 3) {
				$col_nbN = ($col_nb % 2) ? 5 : 4; //switch to a filter value or a filter code checking
			} else {
				$col_nbN = $col_nb; //switch to line, family, tab or filter type checking
			}

			switch ($col_nbN) {


				case $filterlistColumn['LINE'] :
					$lineN = mavicimport_cleanCell($cell_value, 'string', true, true);
					if (!in_array($lineN, $lineControle)) {
						$msg = "Unrecognized line : <em>" . $cell_value . "</em>.";
						array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
					}
					break;

				case $filterlistColumn['FAMILY'] :
					$familyN = mavicimport_cleanCell($cell_value, 'string', true, true);
					if (!in_array($lineN . '/' . $familyN, $catControle)) {
						$msg = "Unrecognized family : <em>" . $cell_value . "</em>.";
						array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
					}
					break;

				case $filterlistColumn['TAB'] :
					$tabN = mavicimport_cleanCell($cell_value, 'string', true, true);
					if (!in_array($lineN . '/' . $familyN . '/' . $tabN, $tabControle)) {
						$msg = "Unrecognized tab : <em>" . $cell_value . "</em>.";
						array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
					}
					break;

				case $filterlistColumn['FILTER_NAME'] :
					//check translations
					if (!isset($filterTrad[strtolower($cell_value)])) {//avoid parsing excel for nothing
						$msg = "Translations not found for filter type <em>" . $cell_value . "</em>.";
						array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
						$col_nb = $highestColumnIndex;
						break;
					} else {
						//build filter type node
						$filterNameSystem = strtolower(str_replace(array(' ', '(', ')'), array('', '', ''), $cell_value));
						$label = $lineN . '/' . $familyN . '/' . $tabN . '/' . $filterNameSystem;
						$belongsTo = $lineN . '/' . $familyN . '/' . $tabN;
						$filterN = $filterNameSystem;
						foreach ($filterTrad[strtolower($cell_value)] as $lang => $filterTypeTrad) {
							if (isset($exFilterType[$lang][$label]['nid']) && isset($exFilterType['en'][$label]['nid'])) {
								$filterTypeNode = build_mavic_node($filterTypeTrad, false, $label, $lang, $exFilterType[$lang][$label]['nid'], 0, false, false, false, false, false, true, false, true);
								$filterTypeNode->field_filter_value_list = array();
								$filterTypeNode->belongsTo = array($belongsTo); //to link to category node when saved
								$filterTypeNodes[$lang]['update'][$label] = $filterTypeNode;
							} elseif (isset($exFilterType['en'][$label]['nid'])) {//we never know... error
								$msg = 'The <em>' . $lang . '</em> version of filter type <em>' . $filterTypeTrad . '</em> has not been found while the english one exists.';
								array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
								break;
							} elseif (isset($exFilterType[$lang][$label]['nid'])) {//error too
								$msg = 'The <em>' . $lang . '</em> version of filterTypre <em>' . $filterTypeTrad . '</em> has been found in the DB while the english one has not.';
								array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
								break;
							} else {//definitely not existing : create
								$filterTypeNode = build_mavic_node($filterTypeTrad, 'filter_type', $label, $lang, '', 0, false, false, false, false, false, true, false, true);
								$filterTypeNode->field_multi[0]['value'] = 1;
								$filterTypeNode->field_filter_value_list = array();
								$filterTypeNode->belongsTo = array($belongsTo); //to link to category node when saved
								$filterTypeNodes[$lang]['create'][$label] = $filterTypeNode;
							}
						}
					}

					break;

				case $filterlistColumn['VALUE']:
					//check translations
					if (!isset($filterTrad[strtolower($cell_value)])) {//avoid parsing excell for nothing
						$msg = "Translations not found for filter type <em>" . $cell_value . "</em>.";
						array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
						$col_nb += 2;
						break;
					}
					$filtersN = $cell_value;
					$filtersSysName = $lineN . strtolower(str_replace(array(' ', '(', ')'), array('', '', ''), $filterN)) . '_' . $filtersN;
					if (isset($filterNodes['en']['create'][$filtersSysName]))
						$mayBeASiblingsFeatureCodeCreate = true;
					if (isset($filterNodes['en']['update'][$filtersSysName]))
						$mayBeASiblingsFeatureCodeUpdate = true;
					break;

				case $filterlistColumn['FEATURE_ID']:
					$tmp = explode(';', $cell_value); //may have several feature code for the same filter
					$tmpC = count($tmp);
					$theOne = array();
					$storedFeatureCodes = array(); //featureCode field to add/merge to/with the new/existing node
					$featureCodeAllreadyDefined = array();
					$featureCodeInDb = array();
					$isAllRight = true;
					foreach ($tmp as $featureCode) {
						//
						if (isset($tempFilters[$featureCode]) && !$mayBeASiblingsFeatureCodeCreate && !$mayBeASiblingsFeatureCodeUpdate) {
							$msg = 'The filter <em>' . $filtersSysName . '</em> has the same feature code (<em>' . $featureCode . '</em> as the new or updated filter <em>' . $tempFilters[$featureCode]['filtersSysName'] . '</em> but their system name are differents.';
							array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
							$isAllRight = false; // UNCommented because it IS NOT ok if it's the same Id but different name AS FILTER TYPE AND FILTERS ARE IDENTIFIED BY THEIR NAME
						}
//						else {
						if (isset($tempFilters[$featureCode])) {
							$featureCodeAllreadyDefined[] = $featureCode;
						}
						if (isset($exFeatures['en'][$featureCode])) {
							$featureCodeInDb[] = $featureCode;
						}
//						}
					}
					if ($isAllRight) {
						$mustBeUpdatedNeverDefined = false;
						if ($mayBeASiblingsFeatureCodeUpdate || $mayBeASiblingsFeatureCodeCreate) {
							$toCreateUpdate = $mayBeASiblingsFeatureCodeUpdate ? 'update' : 'create';
							if ($mayBeASiblingsFeatureCodeCreate && !empty($featureCodeInDb)) {
								$oldfilterBelon = $filterNodes['en']['create'][$filtersSysName];
								$retrievedFilterType = (array) $oldfilterBelon->belongsTo;
								$otherFeatureCode = array();
								$tempOtherFeatureCode = $oldfilterBelon->field_feature_codes;
								foreach ($tempOtherFeatureCode as $tempfte) {
									$otherFeatureCode[] = $tempfte['value'];
								}

								//delete them 
								foreach ($filterTrad[strtolower($filtersN)] as $langH => $filterTradN) {
									$cleanFiltercreate = array();
									$allFiltersNode = $filterNodes[$langH]['create'];
									foreach ($allFiltersNode as $filterKey => $createdFilter) {//unset not working on 'string' offset and array_splice needs numeric offset : easier than retrieving the offset
										$filterVA = explode('_', $filterKey);

										if ($filterVA[1] != $filtersN)
											$cleanFiltercreate[$filterKey] = $createdFilter;
									}
									$filterNodes[$langH]['create'] = $cleanFiltercreate;
								}
								//and raise a warning
								$compare1 = array_diff($otherFeatureCode, $tmp);
								$compare2 = array_diff($tmp, $otherFeatureCode);
								if (!empty($compare1) || !empty($compare2)) {
									$msg = 'The filter </em>' . $filtersSysName . '</em> has feature code(s) <em>' . implode(', ', $featureCodeAllreadyDefined) . '</em>) which have not been systematically associated to the existing feature (featureCode : ' . implode(', ', $featureCodeInDb) . ').';
									array_push($filterWarnings, array('line' => $row_nb, 'message' => $msg));
								}
								//set it to be updated
								$mustBeUpdatedNeverDefined = true;
								$isAllRight = false;
							}
							if (!empty($featureCodeAllreadyDefined)) {
								//check that allready used feature codes are linked accordingly to the same filter name
								$filterSysNameA = array();
								foreach ($featureCodeAllreadyDefined as $fcode) {
									$filterSysNameA[] = $tempFilters[$fcode]['filtersSysName'];
								}
								if (count(array_unique($filterSysNameA)) > 1 || (!in_array($filtersSysName, $filterSysNameA))) {
									$msg = 'The filter </em>' . $filtersSysName . '</em> share the same feature code(s) (<em>' . implode(', ', $featureCodeAllreadyDefined) . '</em> as the new or updated filter(s) <em>' . implode(', ', $filterSysNameA) . ' but their system name are differents.';
									array_push($filterErrors, array('line' => $row_nb, 'message' => $msg));
									$isAllRight = false;
								}
							}
							//retrieve the allready build node
							if ($isAllRight) {
								foreach ($filterTrad[strtolower($filtersN)] as $langH => $filterTradN) {
									$newNode = $filterNodes[$langH][$toCreateUpdate][$filtersSysName];
									//belongs to
									$belongsToFTB = array($label);

									$belongsToNew = (array) $newNode->belongsTo;
									$belongsToFT = array_merge($belongsToFTB, $belongsToNew);
									$belongsToFTC = array();
									foreach ($belongsToFT as $unique) {
										if (!in_array($unique, $belongsToFTC)) {
											$belongsToFTC[] = $unique;
										}
									}
									$newNode->belongsTo = $belongsToFTC;
									//compare featureCodes
									$storedFeatureCodesOld = array();
									foreach ($newNode->field_feature_codes as $oldFeatureCodes) {
										$storedFeatureCodesOld[] = $oldFeatureCodes['value'];
									}
									if ($langH == 'en') {
										$compare1 = array_diff($storedFeatureCodesOld, $tmp);
										$compare2 = array_diff($tmp, $storedFeatureCodesOld);
										if (!empty($compare1) || !empty($compare2)) {
											$msg = 'The feature codes of the filter </em>' . $filtersSysName . '</em> have not been systematically associated to the existing feature : <ul>';
											if (!empty($compare1)) {
												$msg .= '<li>feature code(s) which should have been declared in this cell : ' . implode(", ", $compare1) . '</li>';
											}
											if (!empty($compare2)) {
												$msg .= '<li>feature code(s) which should have been declared previously: ' . implode(", ", $compare2) . '</li>';
											}
											$msg .= '</ul>';
											array_push($filterWarnings, array('line' => $row_nb, 'message' => $msg));
										}
									}
									$storedFeatureCodesM = array_merge($storedFeatureCodesOld, $tmp);


									//delete identical feature codes
									$storedFeatureCodes = array();
									$testAr = array();
									foreach ($storedFeatureCodesM as $featureT) {
										if (!in_array($featureT, $testAr)) {
											$testAr[] = $featureT;
											$storedFeatureCodes[] = array('value' => $featureT);
										}
									}
									$newNode->field_feature_codes = $storedFeatureCodes;
									//add it to filter list
									$filterNodes[$langH][$toCreateUpdate][$filtersSysName] = $newNode;
								}
								$belongsToNew = (array) $newNode->belongsTo;
								foreach ($tmp as $storedFeatureCodesM) {
									$tempFilters[$storedFeatureCodesM] = array('filtersSysName' => $filtersSysName, 'belongsTo' => $belongsToNew);
								}
							}
						}
						if ((!$mayBeASiblingsFeatureCodeUpdate && !$mayBeASiblingsFeatureCodeCreate) || $mustBeUpdatedNeverDefined) {
							if (!empty($featureCodeInDb)) {
								foreach ($filterTrad[strtolower($filtersN)] as $langB => $filterTradN) {
									$theOne = '';
									foreach ($featureCodeInDb as $featureCode) {//try to identify existing filter
										if (!empty($exFilter[$langB][$featureCode])) {
											if ($exFilter[$langB][$featureCode]['isMain'])
												$theOne = $exFilter[$langB][$featureCode]['nid'];
										}
									}
									$theOne = (!empty($theOne)) ? $theOne : $exFeatures[$langB][max($featureCodeInDb)]; //if found in the existing filters take this one
									if (is_array($theOne)) {//if not found in the existing filters, take the latest node sharing this feature code
										$featureNidA = array();
										foreach ($exFeatures[$langB][max($featureCodeInDb)] as $featureNid) {
											$featureNidA[] = $featureNid['nid'];
										}
										$theOne = max($featureNidA);
									}
									$newNode = build_mavic_node($filterTradN, false, '', $langB, $theOne, 0, false, false, false, false, false, false, false, true);

									$belongsToFT = array($label);
									if (isset($retrievedFilterType)) {//add the eventual other filter type
										$belongsToFT = array_merge($belongsToFT, $retrievedFilterType);
										$belongsToFTX = array();
										foreach ($belongsToFT as $unique) {
											if (!in_array($unique, $belongsToFTX)) {
												$belongsToFTX[] = $unique;
											}
										}
										$newNode->belongsTo = $belongsToFTX;
									} else {
										$newNode->belongsTo = $belongsToFT;
									}

									$storedFeatureCodesM = $tmp;
									if (isset($otherFeatureCode)) {//add the eventual other feature codes
										$storedFeatureCodesM = array_merge($storedFeatureCodesM, $otherFeatureCode);
									}

									//delete identical feature codes
									$storedFeatureCodes = array();
									$testAr = array();
									foreach ($storedFeatureCodesM as $featureT) {
										if (!in_array($featureT, $testAr)) {
											$testAr[] = $featureT;
											$storedFeatureCodes[] = array('value' => $featureT);
										}
									}
									$newNode->field_feature_codes = $storedFeatureCodes;
									//add it to filter list
									$newNode->field_filter_main = array(array('value' => 1));
									$newNode->field_filter_title = array(array('value' => $filterTradN));
									$filterNodes[$langB]['update'][$filtersSysName] = $newNode;
								}
								$belongsToNew = (array) $newNode->belongsTo;
								foreach ($storedFeatureCodes as $featureCode) {
									$tempFilters[$featureCode['value']] = array('filtersSysName' => $filtersSysName, 'belongsTo' => $belongsToNew);
								}
							} else {
								foreach ($filterTrad[strtolower($filtersN)] as $lang => $filterTradN) {
									$newNode = build_mavic_node($filterTradN, 'prodvalcarac', '', $lang, '', 0, false, false, false, false, false, false, false, true);
									$newNode->field_technologie = array(array('value' => 0));
									$newNode->field_filter_main = array(array('value' => 1));
									$newNode->belongsTo = array($label);
									$newNode->field_filter_title = array(array('value' => $filterTradN));
									$storedFeatureCodes = array();
									foreach ($tmp as $featureT) {
										$storedFeatureCodes[] = array('value' => $featureT);
									}

									$newNode->field_feature_codes = $storedFeatureCodes;
									$filterNodes[$lang]['create'][$filtersSysName] = $newNode;
								}
								$belongsToNew = (array) $newNode->belongsTo;
								foreach ($tmp as $featureCode) {
									$tempFilters[$featureCode] = array('filtersSysName' => $filtersSysName, 'belongsTo' => $belongsToNew);
								}
							}
						}
					}
					$mayBeASiblingsFeatureCodeCreate = false;
					$mayBeASiblingsFeatureCodeUpdate = false;

					//store filter order in filterType
					$filnode = 'update';
					if (isset($filterNodes['en']['create'][$filtersSysName])) {
						$filnode = 'create';
					}
					$filtypenode = 'update';
					if (isset($filterTypeNodes['en']['create'][$label])) {
						$filtypenode = 'create';
					}
					foreach ($parameters['langs'] as $langU) {
						$tmpnid = isset($filterNodes[$langU][$filnode][$filtersSysName]->nid) ? $filterNodes[$langU][$filnode][$filtersSysName]->nid : $filterNodes[$langU][$filnode][$filtersSysName]->field_feature_codes;
						if (!isset($filterTypeNodes[$langU][$filtypenode][$label]->filterOrder))
							$filterTypeNodes[$langU][$filtypenode][$label]->filterOrder = array();
						array_push($filterTypeNodes[$langU][$filtypenode][$label]->filterOrder, $tmpnid);
					}
					break;
				default:
					break;
			}
			$i++; //next filter type
		}//end of cell
	}//end of line



	/*	 * **************************************************************************************************************
	 * 
	 * TECHNOLOGIES CHECKING
	 */

	//existing data for comparaison
	$technoLineCat = array('technoline', 'technocat');
	$exListLines = array();
	$exTechno = array();
	$technoNid = array();

	//Reports
	$technoErrors = array();
	$technoWarnings = array();
	$technoStatus = array();

	foreach ($technoLineCat as $technoLineT) {
		$exListLines[$technoLineT] = array();
		foreach ($parameters['langs'] as $eslang) {
			$exListLines[$technoLineT][$eslang] = array();

			$resLine = db_query("	SELECT r.`title`, r.`body`, n.`nid`
							FROM {node_revisions} r 
							INNER JOIN {node} n USING (vid)
							WHERE n.`type` = '%s'
							AND n.`language` = '%s'", array($technoLineT, $eslang));
			while ($result = db_fetch_array($resLine)) {
				if (empty($exListLines[$technoLineT][$eslang][$result['body']]))
					$exListLines[$technoLineT][$eslang][$result['body']] = array('title' => $result['title'], 'nid' => $result['nid']);
			}
		}
	}
	foreach ($parameters['langs'] as $lang) {
		//list existing technologies
		$prodval = 'prodvalcarac';
		$resTechno = db_query("	SELECT c.`field_feature_codes_value`, n.`nid`, p.`field_feature_season_value`
								FROM {node} n 
								INNER JOIN {content_field_feature_codes} c using (nid) 
								INNER JOIN {content_type_prodvalcarac} p using (nid)
								WHERE n.type = '%s'
								AND n.language = '%s'
								AND p.`field_technologie_value` IN (" . db_placeholders(range(1, 3), 'int') . ")", array($prodval, $lang, 1, 2, 3));
		while ($result = db_fetch_array($resTechno)) {
			/* if(!in_array($result['field_feature_codes_value'], $techno)) array_push($techno, (int) $result['field_feature_codes_value']); */
			if (!isset($technoNid[$lang]))
				$technoNid[$lang] = array();
			if (!isset($technoNid[$lang][$result['nid']]))
				$technoNid[$lang][$result['nid']] = array();
			array_push($technoNid[$lang][$result['nid']], $result['field_feature_codes_value']);
			if (!isset($exTechno[$lang]))
				$exTechno[$lang] = array();
			if (!isset($exTechno[$lang][$result['field_feature_codes_value']])) {
				$exTechno[$lang][$result['field_feature_codes_value']] = array('nid' => $result['nid'], 'season' => $result['field_feature_season_value']);
			} elseif ($exTechno[$lang][$result['field_feature_codes_value']]['nid'] != $result['nid']) {
				$msg = 'The system found the feature code ' . $result['field_feature_codes_value'] . ' identified as a technology but associated to more than one node : node <em>' . $exTechno[$lang][$result['field_feature_codes_value']]['nid'] . '</em> and node <em>' . $result['nid'] . '</em>';
				array_push($technoErrors, array('line' => $row_nb, 'message' => $msg));
			}
		}
	}

	//load xls data
	$objTechnoSheet = $objPHPExcel->getSheetByName('TECHNO_IMPORT');
	$highestRow = $parameters['sheets']['TECHNO_IMPORT']['max_row'];
	$highestColumnIndex = count(range('A', $parameters['sheets']['TECHNO_IMPORT']['col_range']));
	$technolistColumn = array_flip($parameters['sheets']['TECHNO_IMPORT']['col_name']);

	for ($row_nb = 2; $row_nb <= $highestRow; ++$row_nb) {
		$msgA = array();
		if ('0' === $technoLine = mavicimport_cleanCell($objTechnoSheet->getCellByColumnAndRow($technolistColumn['LINE'], $row_nb)->getValue(), 'string', true))
			$msgA[] = '<em>line system name</em>';
		if ('0' === $technoSeason = mavicimport_cleanCell($objTechnoSheet->getCellByColumnAndRow($technolistColumn['SEASON'], $row_nb)->getValue()))
			$msgA[] = '<em>season</em>';
		$classification = mavicimport_cleanCell($objTechnoSheet->getCellByColumnAndRow($technolistColumn['CLASSIFICATION'], $row_nb)->getValue(), 'string', false, true);
		//$feature_parent = mavicimport_cleanCell($objTechnoSheet->getCellByColumnAndRow($technolistColumn['FEATURE_PARENT'], $row_nb)->getValue());
		$feature_parent_code = mavicimport_cleanCell($objTechnoSheet->getCellByColumnAndRow($technolistColumn['FEATURE_PARENT_CODE'], $row_nb)->getValue());
		if ('0' === $feature_code = mavicimport_cleanCell($objTechnoSheet->getCellByColumnAndRow($technolistColumn['FEATURE_CODE'], $row_nb)->getValue()))
			$msgA[] = '<em>feature code</em>';

		if (!empty($msgA)) {
			array_push($technoErrors, array('line' => $row_nb, 'message' => "Empty cell on col. : " . implode(', ', $msgA) . "."));
			continue;
		}


		foreach ($parameters['langs'] as $lang) {
			if (!isset($exLine[$lang][$technoLine]['title'])) {
				$msg = 'cannot found line for : ' . $technoLine . ' in ' . $lang;
				array_push($technoErrors, array('line' => $row_nb, 'message' => $msg));
				continue;
			} else {
				$title = $exLine[$lang][$technoLine]['title'];
			}


			if (!empty($classification)) {
				//
				// technoline
				//
				if (!isset($technoLineNodes[$lang]['update'][$technoLine]) && !isset($technoLineNodes[$lang]['create'][$technoLine])) { // pour ne pas refaire ce qu on vient de faire
					$updateIt = 'create';
					if (isset($exListLines['technoline'][$lang][$technoLine])) {
						$newNode = build_mavic_node($title, false, $technoLine, $lang, $exListLines['technoline'][$lang][$technoLine]['nid'], 1, false, true, false, false, false, true, false, true);
						$newNode->temp_menu = array('weight' => $row_nb, 'link_title' => $title, 'menu_name' => 'menu-menu-technologies-' . $lang, "plid" => 0);
						$technoLineNodes[$lang]['update'][$technoLine] = $newNode; //array('#node'=>$newNode->nid, '#menu'=>$newNode->menu['mlid'], '#title'=>$title);
					} else {
						$newNode = build_mavic_node($title, 'technoline', $technoLine, $lang, '', 1, false, false, false, false, false, true, false, true);
						$newNode->temp_menu = array('weight' => $row_nb, 'link_title' => $title, 'menu_name' => 'menu-menu-technologies-' . $lang, "plid" => 0);

						$technoLineNodes[$lang]['create'][$technoLine] = $newNode; //array('#node'=>$newNode->nid, '#menu'=>$newNode->menu['mlid'], '#title'=>$title);
					}
				}

				//
				// technologie category info : technocat	
				//
				$catEn = strtolower(str_replace(' ', '', $technoLine . '_' . $classification)); // pour generer un nom d image correct
				if (!isset($technoCatNodes[$lang]['update'][$catEn]) && !isset($technoCatNodes[$lang]['create'][$catEn])) { // pour ne pas refaire ce qu on vient de faire
					$catDesc = '|||keep|||';
					$catTitle = $classification;
					$catPlid = $technoLine;
				}
			} else {
				//
				// technologie category info
				//
				$catEn = $technoLine;
				if (!isset($technoCatNodes[$lang]['update'][$catEn]) && !isset($technoCatNodes[$lang]['create'][$catEn])) { // pour ne pas refaire ce qu on vient de faire
					$catTitle = $title;
					$catDesc = '';
					$catPlid = 0;
				}
			}
			//
			// technologie categorie build node
			//
			if (!isset($technoCatNodes[$lang]['update'][$catEn]) && !isset($technoCatNodes[$lang]['create'][$catEn])) { // pour ne pas refaire ce qu on vient de faire
				//update
				$updateIt = 'create';
				if (isset($exListLines['technocat'][$lang][$catEn])) {
					$newNode = build_mavic_node($catTitle, false, $catEn, $lang, $exListLines['technocat'][$lang][$catEn]['nid'], 0, false, true, false, false, false, true, false, true);
					$newNode->temp_menu = array('weight' => $row_nb, 'link_title' => $catTitle, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catPlid, 'expanded' => 1);

					$updateIt = 'update';
					//create	
				} else {
					$newNode = build_mavic_node($catTitle, 'technocat', $catEn, $lang, '', 0, false, false, false, false, false, false, false, true);
					$newNode->temp_menu = array('weight' => $row_nb, 'link_title' => $catTitle, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catPlid, 'expanded' => 1);
				}
				if ($catDesc != '|||keep|||')
					$newNode->field_description = array(array('value' => $catDesc));
				$technoCatNodes[$lang][$updateIt][$catEn] = $newNode; // array('#node'=>$newNode->nid, '#node_mlid'=>$newNode->menu['mlid']);
			}

			//
			// technologie
			//
			
			
			//for tmp title and array
			$tmpRow5 = (string) str_replace(array(' ', ';'), array('', '_'), $feature_code);
			if (!empty($feature_parent_code)) {
				//for tmp title and array's index

				$tmpRow4 = (string) str_replace(array(' ', ';'), array('', '_'), $feature_parent_code);
				//
				// technologie parent
				//
				if (!isset($technoNodes[$lang]['update'][$tmpRow4]) && !isset($technoNodes[$lang]['create'][$tmpRow4])) { // pour ne pas refaire ce qu on vient de faire
					$featureId = $feature_parent_code;
					//season  : for techno parent, season is not defined => take the latest :
					for ($row = 2; $row <= $highestRow; ++$row) {
						if (mavicimport_cleanCell($objTechnoSheet->getCellByColumnAndRow($technolistColumn['FEATURE_PARENT_CODE'], $row)->getValue()) == $featureId) {
							if (mavicimport_cleanCell($objTechnoSheet->getCellByColumnAndRow($technolistColumn['SEASON'], $row)->getValue(), 'int') > $technoSeason)
								$technoSeason = $row[1];
						}
					}
					$tmp = explode(';', $featureId); //maybe several codes for the same techno
					$tmpC = count($tmp);
					$allreadyFilter = array();
					$inDB = array();
					$featureIdA = array();
					foreach ($tmp as $featureCode) {//
						$featureIdA[] = array('value' => $featureCode);
						if (isset($tempFilters[$featureCode]))
							$allreadyFilter[] = $featureCode; //if allready build because also a filter
						if (isset($exTechno[$lang][$featureCode]))
							$inDB[] = $featureCode; // if in DB
					}
					$updateIt = 'create';
					// if allready created as filter
					if (!empty($allreadyFilter)) {

						//create or update ?
						$createUpdate = (isset($filterNodes[$lang]['create'][$tempFilters[$allreadyFilter[0]]['filtersSysName']])) ? 'create' : 'update';
						//retrieve the filter node
						$filterNode = $filterNodes[$lang][$createUpdate][$tempFilters[$allreadyFilter[0]]['filtersSysName']];
						//compare featurecodes
						$storedFeatureCodesOld = array();
						$filterFeatureCodes = $filterNode->field_feature_codes;
						foreach ($filterNode->field_feature_codes as $oldFeatureCodes) {
							$storedFeatureCodesOld[] = $oldFeatureCodes['value'];
						}
						if ($lang == 'en') {
							$compare1 = array_diff($storedFeatureCodesOld, $tmp);
							$compare2 = array_diff($tmp, $storedFeatureCodesOld);
							if (!empty($compare1) || !empty($compare2)) {
								$msg = 'The feature codes of the filter/technology </em>' . $filterNode->field_filter_title[0]["value"] . '</em> - </em>' . $tmpRow4 . '</em> have not been systematically associated : <ul>';
								if (!empty($compare1)) {
									$msg .= '<li>feature code(s) which should have been declared for this technology : ' . implode(", ", $compare1) . '</li>';
								}
								if (!empty($compare2)) {
									$msg .= '<li>feature code(s) which should have been declared previously for the filter : ' . implode(", ", $compare2) . '</li>';
								}
								$msg .= '</ul>';
								array_push($technoWarnings, array('line' => $row_nb, 'message' => $msg));
							}
						}
						$storedFeatureCodesM = array_merge($storedFeatureCodesOld, $tmp);

						//delete identical feature codes
						$storedFeatureCodes = array();
						$testAr = array();
						foreach ($storedFeatureCodesM as $featureT) {
							if (!in_array($featureT, $testAr)) {
								$testAr[] = $featureT;
								$storedFeatureCodes[] = array('value' => $featureT);
							}
						}
						//if was going to be created as a filter and does not exist in the db as a techno
						if ($createUpdate == 'create' && empty($inDB)) {

							$newNode = build_mavic_node($tmpRow4, 'prodvalcarac', '', $lang, '', 0, false, false, false, false, false, false, false, true);
							//set the fake property which will tell the import process to retrieve the techno node first and then update it
							$newNode->temp_menu = array('weight' => $row_nb, 'link_title' => $tmpRow4, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);
							$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
							$newNode->field_child = array();
							$newNode->field_feature_codes = $storedFeatureCodes;
							$newNode->field_technologie = array(array('value' => 3));
							$newNode->field_feature_season = array(array('value' => $technoSeason));
							$technoNodes[$lang]['update'][$tmpRow4] = $newNode;
							//if a filter was wrongly listed as to be created 	
						} elseif ($createUpdate == 'create' && !empty($inDB)) {
							//delete the "tobecreated" one
							$cleanFiltercreate = array();
							foreach ($filterNodes[$lang]['create'] as $filterKey => $createdFilter) { //unset not working on 'string' offset and array_splice needs numeric offset : easier than retrieving the offset
								if ($filterKey != $tempFilters[$allreadyFilter[0]]['filtersSysName'])
									$cleanFiltercreate[$filterKey] = $createdFilter;
							}
							$filterNodes[$lang]['create'] = $cleanFiltercreate;
							//add it to "tobeupdated" list
							$newFilterNode = build_mavic_node($filterTradN, false, '', $lang, $exTechno[$lang][$inDB[0]]['nid'], 0, false, false, false, false, false, false, false, true);
							$belongsToNew = (array) $filterNode->belongsTo;
							$newFilterNode->belongsTo = $belongsToNew;
							$newFilterNode->field_feature_codes = $storedFeatureCodes;
							$newFilterNode->field_filter_main = (array) $filterNode->field_filter_main;
							$newFilterNode->field_filter_title = (array) $filterNode->field_filter_title;
							$filterNodes[$lang]['update'][$tempFilters[$allreadyFilter[0]]['filtersSysName']] = $newFilterNode;
							//create the techno node							
							$nid = $exTechno[$lang][$inDB[0]]['nid'];
							$newNode = build_mavic_node($tmpRow4, false, '', $lang, $nid, 0, false, true, false, false, false, false, false, true);
							$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
							$currentWeight = $newNode->menu['weight'];
							$currentTitle = $newNode->menu['link_title'];
							$newNode->temp_menu = array('weight' => $currentWeight, 'link_title' => $currentTitle, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);
							$newNode->field_child = array();
							$newNode->field_feature_codes = $storedFeatureCodes;
							$newNode->field_technologie = array(array('value' => 3));
							$newNode->field_feature_season = array(array('value' => $technoSeason));
							$technoNodes[$lang]['update'][$tmpRow4] = $newNode;
						} else { //if ($createUpdate == 'update' && (empty($inDB)or not) {
							//check same nid if also detected as being part of the DB
							if ((!empty($inDB) && $filterNode->nid == $exTechno[$lang][$inDB[0]]['nid']) || empty($inDB)) {
								$nid = $filterNode->nid;
								$newNode = build_mavic_node($tmpRow4, false, '', $lang, $nid, 0, false, true, false, false, false, false, false, true);
								$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
								$currentWeight = $newNode->menu['weight'];
								$currentTitle = $newNode->menu['link_title'];
								$newNode->temp_menu = array('weight' => $currentWeight, 'link_title' => $currentTitle, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);
								$newNode->field_child = array();
								$newNode->field_feature_codes = $storedFeatureCodes;
								$newNode->field_technologie = array(array('value' => 3));
								$newNode->field_feature_season = array(array('value' => $technoSeason));

								$technoNodes[$lang]['update'][$tmpRow4] = $newNode;
							} else {
								$msg = 'Duplicated filter-technology </em>' . $filterNode->field_filter_title[0]["value"] . '</em> - </em>' . $tmpRow4 . '</em> detected : nid </em>' . $filterNode->nid . '</em> and </em>' . $exTechno[$lang][$inDB[0]]["nid"] . '</em>';
								array_push($technoErrors, array('line' => $row_nb, 'message' => $msg));
							}
						}
					} else {
						if (!empty($inDB)) {
							$nid = $exTechno[$lang][$inDB[0]]['nid'];
							$newNode = build_mavic_node($tmpRow4, false, '', $lang, $nid, 0, false, true, false, false, false, false, false, true);
							$currentWeight = $newNode->menu['weight'];
							$currentTitle = $newNode->menu['link_title'];
							$newNode->temp_menu = array('weight' => $currentWeight, 'link_title' => $currentTitle, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);

							$updateIt = 'update';
							$nidInDB = $technoNid[$lang][$nid];
							$oldFeatureCode = array_diff($nidInDB, $tmp);
							if (!empty($oldFeatureCode)) {
								$msg = 'The feature code(s) <em>' . implode(', ', $oldFeatureCode) . '</em> is(are) currently related to the technology <em>' . $tmpRow4 . '</em> (nid : <em>' . $nid . '</em>) but is(are) not present in this file : it(they) will be overwritten.';
								array_push($technoWarnings, array('line' => $row_nb, 'message' => $msg));
							}
							$newFeatureCode = array_diff($tmp, $inDB);
							if (!empty($newFeatureCode)) {
								$msg = 'The new feature code(s) <em>' . implode(', ', $newFeatureCode) . '</em> will be added to the technology <em>' . $tmpRow4 . '</em> (nid : <em>' . $nid . '</em>)';
								array_push($technoStatus, array('line' => -1, 'message' => $msg));
							}
						} else {
							$newNode = build_mavic_node($tmpRow4, 'prodvalcarac', '', $lang, '', 0, false, false, false, false, false, false, false, true);
							$newNode->temp_menu = array('weight' => $row_nb, 'link_title' => $tmpRow4, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);
						}
						$newNode->field_child = array();
						$newNode->field_feature_codes = $featureIdA;
						$newNode->field_technologie = array(array('value' => 3));
						$newNode->field_feature_season = array(array('value' => $technoSeason));
						$technoNodes[$lang][$updateIt][$tmpRow4] = $newNode;
					}
				}

				//
				// technologie child
				//
				$featureId = $feature_code;
				$tmp = explode(';', $featureId); //several code for same techno
				$tmpC = count($tmp);
				$inDB = array();
				$allreadyFilter = array();
				$featureIdA = array();
				foreach ($tmp as $featureCode) { //
					$featureIdA[] = array('value' => $featureCode);
					if (isset($tempFilters[$featureCode]))
						$allreadyFilter[] = $featureCode; //if allready build because also a filter
					if (isset($exTechno[$lang][$featureCode]))
						$inDB[] = $featureCode; // if in DB
				}
				$updateIt = 'create';
				// if allready created as filter
				if (!empty($allreadyFilter)) {

					//create or update ?
					$createUpdate = (isset($filterNodes[$lang]['create'][$tempFilters[$allreadyFilter[0]]['filtersSysName']])) ? 'create' : 'update';
					//retrieve the filter node
					$filterNode = $filterNodes[$lang][$createUpdate][$tempFilters[$allreadyFilter[0]]['filtersSysName']];
					//compare featurecodes
					$storedFeatureCodesOld = array();
					$filterFeatureCodes = (array) $filterNode->field_feature_codes;
					foreach ($filterFeatureCodes as $oldFeatureCodes) {
						$storedFeatureCodesOld[] = $oldFeatureCodes['value'];
					}
					if ($lang == 'en') {
						$compare1 = array_diff($storedFeatureCodesOld, $tmp);
						$compare2 = array_diff($tmp, $storedFeatureCodesOld);
						if (!empty($compare1) || !empty($compare2)) {
							$msg = 'The feature codes of the filter/technology </em>' . $filterNode->field_filter_title[0]["value"] . ' </em>' . $tmpRow5 . '</em> have not been systematically associated : <ul>';
							if (!empty($compare1)) {
								$msg .= '<li>feature code(s) which should have been declared for this technology : ' . implode(", ", $compare1) . '</li>';
							}
							if (!empty($compare2)) {
								$msg .= '<li>feature code(s) which should have been declared previously for the filter : ' . implode(", ", $compare2) . '</li>';
							}
							$msg .= '</ul>';
							array_push($technoWarnings, array('line' => $row_nb, 'message' => $msg));
						}
					}
					$storedFeatureCodesM = array_merge($storedFeatureCodesOld, $tmp);

					//delete identical feature codes
					$storedFeatureCodes = array();
					$testAr = array();
					foreach ($storedFeatureCodesM as $featureT) {
						if (!in_array($featureT, $testAr)) {
							$testAr[] = $featureT;
							$storedFeatureCodes[] = array('value' => $featureT);
						}
					}
					//if was going to be created as a filter and does not exist in the db as a techno
					if ($createUpdate == 'create' && empty($inDB)) {
						$newNode = build_mavic_node($tmpRow5, 'prodvalcarac', '', $lang, '', 0, false, false, false, false, false, false, false, true);
						$technoParent = isset($technoNodes[$lang]['create'][$tmpRow4]) ? 'create' : 'update';
						$parentTechnoNode = $technoNodes[$lang][$technoParent][$tmpRow4];
						if (!isset($parentTechnoNode->temp_field_child))
							$parentTechnoNode->temp_field_child = array();
						$tempArray = $parentTechnoNode->temp_field_child;
						array_push($tempArray, $tmpRow5);
						$parentTechnoNode->temp_field_child = $tempArray;
						$technoNodes[$lang][$technoParent][$tmpRow4] = $parentTechnoNode;
						//set the fake property which will tell the import process to retrieve the filter node first and then update it
						$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
						$newNode->field_child = array();
						$newNode->field_feature_codes = $storedFeatureCodes;
						$newNode->field_technologie = array(array('value' => 1));
						$newNode->field_feature_season = array(array('value' => $technoSeason));
						$technoNodes[$lang]['update'][$tmpRow5] = $newNode;
						//if a filter was wrongly listed as to be created 	
					} elseif ($createUpdate == 'create' && !empty($inDB)) {
						//delete the "tobecreated" one
						$cleanFiltercreate = array();
						foreach ($filterNodes[$lang]['create'] as $filterKey => $createdFilter) { //unset not working on 'string' offset and array_splice needs numeric offset : easier than retrieving the offset
							if ($filterKey != $tempFilters[$allreadyFilter[0]]['filtersSysName'])
								$cleanFiltercreate[$filterKey] = $createdFilter;
						}
						$filterNodes[$lang]['create'] = $cleanFiltercreate;
						//add it to "tobeupdated" list
						$newFilterNode = build_mavic_node($filterTradN, false, '', $lang, $exTechno[$lang][$inDB[0]]['nid'], 0, false, false, false, false, false, false, false, true);
						$belongsToNew = (array) $filterNode->belongsTo;
						$newFilterNode->belongsTo = $belongsToNew;
						$newFilterNode->field_feature_codes = $storedFeatureCodes;
						$newFilterNode->field_filter_main = (array) $filterNode->field_filter_main;
						$newFilterNode->field_filter_title = (array) $filterNode->field_filter_title;
						$filterNodes[$lang]['update'][$tempFilters[$allreadyFilter[0]]['filtersSysName']] = $newFilterNode;
						//create the techno node							
						$nid = $exTechno[$lang][$inDB[0]]['nid'];
						$newNode = build_mavic_node($tmpRow5, 'prodvalcarac', '', $lang, '', 0, false, true, false, false, false, false, false, true);
						$technoParent = isset($technoNodes[$lang]['create'][$tmpRow4]) ? 'create' : 'update';
						$parentTechnoNode = $technoNodes[$lang][$technoParent][$tmpRow4];
						if (!isset($parentTechnoNode->temp_field_child))
							$parentTechnoNode->temp_field_child = array();
						$tempArray = $parentTechnoNode->temp_field_child;
						array_push($tempArray, $tmpRow5);
						$parentTechnoNode->temp_field_child = $tempArray;
						$technoNodes[$lang][$technoParent][$tmpRow4] = $parentTechnoNode;
						$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
						$newNode->field_child = array();
						$newNode->field_feature_codes = $storedFeatureCodes;
						$newNode->field_technologie = array(array('value' => 1));
						$newNode->field_feature_season = array(array('value' => $technoSeason));
						$technoNodes[$lang]['update'][$tmpRow5] = $newNode;
					} else { //if ($createUpdate == 'update' && (empty($inDB)or not) {
						//check same nid if also detected as being part of the DB
						if ((!empty($inDB) && $filterNode->nid == $exTechno[$lang][$inDB[0]]['nid'] || empty($inDB))) {
							$nid = $filterNode->nid;
							$newNode = build_mavic_node($tmpRow5, false, '', $lang, $nid, 0, false, false, false, false, false, false, false, true);
							$technoParent = isset($technoNodes[$lang]['create'][$tmpRow4]) ? 'create' : 'update';
							$parentTechnoNode = $technoNodes[$lang][$technoParent][$tmpRow4];
							if (!isset($parentTechnoNode->temp_field_child))
								$parentTechnoNode->temp_field_child = array();
							$tempArray = $parentTechnoNode->temp_field_child;
							array_push($tempArray, $tmpRow5);
							$parentTechnoNode->temp_field_child = $tempArray;
							$technoNodes[$lang][$technoParent][$tmpRow4] = $parentTechnoNode;
							$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
							$newNode->field_child = array();
							$newNode->field_feature_codes = $storedFeatureCodes;
							$newNode->field_technologie = array(array('value' => 1));
							$newNode->field_feature_season = array(array('value' => $technoSeason));
							$technoNodes[$lang]['update'][$tmpRow5] = $newNode;
						} else {
							$msg = 'Duplicated filter-technology </em>' . $filterNode->field_filter_title['value'] . '</em> - </em>' . $tmpRow5 . '</em> detected : nid </em>' . $filterNode->nid . '</em> and </em>' . $exTechno[$lang][$inDB[0]]["nid"] . '</em>';
							array_push($technoErrors, array('line' => $row_nb, 'message' => $msg));
						}
					}
				} else {
					if (!empty($inDB)) {
						$nid = $exTechno[$lang][$inDB[0]]['nid'];
						$newNode = build_mavic_node($tmpRow5, false, '', $lang, $nid, 0, false, false, false, false, false, false, false, true);
						$technoParent = isset($technoNodes[$lang]['create'][$tmpRow4]) ? 'create' : 'update';
						$parentTechnoNode = $technoNodes[$lang][$technoParent][$tmpRow4];
						if (!isset($parentTechnoNode->temp_field_child))
							$parentTechnoNode->temp_field_child = array();
						$tempArray = $parentTechnoNode->temp_field_child;
						array_push($tempArray, $tmpRow5);
						$parentTechnoNode->temp_field_child = $tempArray;
						$technoNodes[$lang][$technoParent][$tmpRow4] = $parentTechnoNode;
						$updateIt = 'update';
						$nidInDB = $technoNid[$lang][$nid];
						$oldFeatureCode = array_diff($nidInDB, $tmp);
						if (!empty($oldFeatureCode)) {
							$msg = 'The feature code(s) <em>' . implode(', ', $oldFeatureCode) . '</em> is(are) currently related to the technology <em>' . $tmpRow4 . '</em> (nid : <em>' . $nid . '</em>) but is(are) not present in this file : it(they) will be overwritten.';
							array_push($technoWarnings, array('line' => $row_nb, 'message' => $msg));
						}
						$newFeatureCode = array_diff($tmp, $inDB);
						if (!empty($newFeatureCode)) {
							$msg = 'The new feature code(s) <em>' . implode(', ', $newFeatureCode) . '</em> will be added to the technology <em>' . $tmpRow4 . '</em> (nid : <em>' . $nid . '</em>)';
							array_push($technoStatus, array('line' => -1, 'message' => $msg));
						}
					} else {
						$newNode = build_mavic_node($tmpRow5, 'prodvalcarac', '', $lang, '', 0, false, false, false, false, false, false, false, true);
						$technoParent = isset($technoNodes[$lang]['create'][$tmpRow4]) ? 'create' : 'update';
						$parentTechnoNode = $technoNodes[$lang][$technoParent][$tmpRow4];
						if (!isset($parentTechnoNode->temp_field_child))
							$parentTechnoNode->temp_field_child = array();
						$tempArray = $parentTechnoNode->temp_field_child;
						array_push($tempArray, $tmpRow5);
						$parentTechnoNode->temp_field_child = $tempArray;
						$technoNodes[$lang][$technoParent][$tmpRow4] = $parentTechnoNode;
					}
					$newNode->field_child = array();
					$newNode->field_feature_codes = $featureIdA;
					$newNode->field_technologie = array(array('value' => 1));
					$newNode->field_feature_season = array(array('value' => $technoSeason));
					$technoNodes[$lang][$updateIt][$tmpRow5] = $newNode;
				}
			} else {
				//
				// technologie direct
				//
				$allreadyFilter = array();
				$featureId = $feature_code;
				$tmp = explode(';', $featureId); //il peut y avoir plusieurs codes pour une même techno
				$tmpC = count($tmp);
				$inDB = array();
				$featureIdA = array();
				foreach ($tmp as $featureCode) {
					$featureIdA[] = array('value' => $featureCode);
					if (isset($tempFilters[$featureCode]))
						$allreadyFilter[] = $featureCode; //if allready build because also a filter
					if (isset($exTechno[$lang][$featureCode]))
						$inDB[] = $featureCode; // if in DB
				}
				$updateIt = 'create';
				// if allready created as filter
				if (!empty($allreadyFilter)) {

					//create or update ?
					$createUpdate = (isset($filterNodes[$lang]['create'][$tempFilters[$allreadyFilter[0]]['filtersSysName']])) ? 'create' : 'update';
					//retrieve the filter node
					$filterNode = $filterNodes[$lang][$createUpdate][$tempFilters[$allreadyFilter[0]]['filtersSysName']];
					//compare featurecodes
					$storedFeatureCodesOld = array();
					$filterFeatureCodes = (array) $filterNode->field_feature_codes;
					foreach ($filterFeatureCodes as $oldFeatureCodes) {
						$storedFeatureCodesOld[] = $oldFeatureCodes['value'];
					}
					if ($lang == 'en') {
						$compare1 = array_diff($storedFeatureCodesOld, $tmp);
						$compare2 = array_diff($tmp, $storedFeatureCodesOld);
						if (!empty($compare1) || !empty($compare2)) {
							$msg = 'The feature codes of the filter/technology </em>' . $filterNode->field_filter_title[0]["value"] . '</em> - </em>' . $tmpRow5 . '</em> have not been systematically associated : <ul>';
							if (!empty($compare1)) {
								$msg .= '<li>feature code(s) which should have been declared for this technology : ' . implode(", ", $compare1) . '</li>';
							}
							if (!empty($compare2)) {
								$msg .= '<li>feature code(s) which should have been declared previously for the filter : ' . implode(", ", $compare2) . '</li>';
							}
							$msg .= '</ul>';
							array_push($technoWarnings, array('line' => $row_nb, 'message' => $msg));
						}
					}
					$storedFeatureCodesM = array_merge($storedFeatureCodesOld, $tmp);

					//delete identical feature codes
					$storedFeatureCodes = array();
					$testAr = array();
					foreach ($storedFeatureCodesM as $featureT) {
						if (!in_array($featureT, $testAr)) {
							$testAr[] = $featureT;
							$storedFeatureCodes[] = array('value' => $featureT);
						}
					}
					//if was going to be created as a filter and does not exist in the db as a techno
					if ($createUpdate == 'create' && empty($inDB)) {
						$newNode = build_mavic_node($tmpRow5, 'prodvalcarac', '', $lang, '', 0, false, false, false, false, false, false, false, true);
						$newNode->temp_menu = array('weight' => $row_nb, 'link_title' => $tmpRow5, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);

						//set the fake property which will tell the import process to retrieve the filter node first and then update it
						$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
						$newNode->field_child = array();
						$newNode->field_feature_codes = $storedFeatureCodes;
						$newNode->field_technologie = array(array('value' => 2));
						$newNode->field_feature_season = array(array('value' => $technoSeason));
						$technoNodes[$lang]['update'][$tmpRow5] = $newNode;
						//if a filter was wrongly listed as to be created 	
					} elseif ($createUpdate == 'create' && !empty($inDB)) {
						//delete the "tobecreated" one
						$cleanFiltercreate = array();
						foreach ($filterNodes[$lang]['create'] as $filterKey => $createdFilter) {  //unset not working on 'string' offset and array_splice needs numeric offset : easier than retrieving the offset
							if ($filterKey != $tempFilters[$allreadyFilter[0]]['filtersSysName'])
								$cleanFiltercreate[$filterKey] = $createdFilter;
						}
						$filterNodes[$lang]['create'] = $cleanFiltercreate;
						//add it to "tobeupdated" list
						$newFilterNode = build_mavic_node($filterTradN, false, '', $lang, $exTechno[$lang][$inDB[0]]['nid'], 0, false, false, false, false, false, false, false, true);
						$belongsToNew = (array) $filterNode->belongsTo;
						$newFilterNode->belongsTo = $belongsToNew;
						$newFilterNode->field_feature_codes = $storedFeatureCodes;
						$newFilterNode->field_filter_main = (array) $filterNode->field_filter_main;
						$newFilterNode->field_filter_title = (array) $filterNode->field_filter_title;
						$filterNodes[$lang]['update'][$tempFilters[$allreadyFilter[0]]['filtersSysName']] = $newFilterNode;
						//create the techno node							
						$nid = $exTechno[$lang][$inDB[0]]['nid'];
						$newNode = build_mavic_node($tmpRow5, false, '', $lang, $nid, 0, false, true, false, false, false, false, false, true);
						$currentWeight = (!empty($newNode->menu['weight'])) ? $newNode->menu['weight'] : $row_nb;
						$currentTitle = (!empty($newNode->menu['link_title'])) ? $newNode->menu['link_title'] : $tmpRow5;
						$newNode->temp_menu = array('weight' => $currentWeight, 'link_title' => $currentTitle, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);
						$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
						$newNode->field_child = array();
						$newNode->field_feature_codes = $storedFeatureCodes;
						$newNode->field_technologie = array(array('value' => 2));
						$newNode->field_feature_season = array(array('value' => $technoSeason));
						$technoNodes[$lang]['update'][$tmpRow5] = $newNode;
					} else { //if ($createUpdate == 'update' && (empty($inDB)or not) {
						//check same nid if also detected as being part of the DB
						if ((!empty($inDB) && $filterNode->nid == $exTechno[$lang][$inDB[0]]['nid']) || empty($inDB)) {
							$nid = $filterNode->nid;
							$newNode = build_mavic_node($tmpRow5, false, '', $lang, $nid, 0, false, true, false, false, false, false, false, true);
							$currentWeight = (!empty($newNode->menu['weight'])) ? $newNode->menu['weight'] : $row_nb;
							$currentTitle = (!empty($newNode->menu['link_title'])) ? $newNode->menu['link_title'] : $tmpRow5;
							$newNode->temp_menu = array('weight' => $currentWeight, 'link_title' => $currentTitle, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);
							$newNode->is_allready_filter = $tempFilters[$allreadyFilter[0]]['filtersSysName'];
							$newNode->field_child = array();
							$newNode->field_feature_codes = $storedFeatureCodes;
							$newNode->field_technologie = array(array('value' => 2));
							$newNode->field_feature_season = array(array('value' => $technoSeason));
							$technoNodes[$lang]['update'][$tmpRow5] = $newNode;
						} else {
							$msg = 'Duplicated filter-technology </em>' . $filterNode->field_filter_title[0]["value"] . '</em> - </em>' . $tmpRow5 . '</em> detected : nid </em>' . $filterNode->nid . '</em> and </em>' . $exTechno[$lang][$inDB[0]]["nid"] . '</em>';
							array_push($technoErrors, array('line' => $row_nb, 'message' => $msg));
						}
					}
				} else {
					if (!empty($inDB)) {
						$nid = $exTechno[$lang][$inDB[0]]['nid'];
						$newNode = build_mavic_node($tmpRow5, false, '', $lang, $nid, 0, false, true, false, false, false, false, false, true);
						$currentWeight = (!empty($newNode->menu['weight'])) ? $newNode->menu['weight'] : $row_nb;
						$currentTitle = (!empty($newNode->menu['link_title'])) ? $newNode->menu['link_title'] : $tmpRow5;
						$newNode->temp_menu = array('weight' => $currentWeight, 'link_title' => $currentTitle, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);

						$updateIt = 'update';
						$nidInDB = $technoNid[$lang][$nid];
						$oldFeatureCode = array_diff($nidInDB, $tmp);
						if (!empty($oldFeatureCode)) {
							$msg = 'The feature code(s) <em>' . implode(', ', $oldFeatureCode) . '</em> is(are) currently related to the technology <em>' . $tmpRow4 . '</em> (nid : <em>' . $nid . '</em>) but is(are) not present in this file : it(they) will be overwritten.';
							array_push($technoWarnings, array('line' => $row_nb, 'message' => $msg));
						}
						$newFeatureCode = array_diff($tmp, $inDB);
						if (!empty($newFeatureCode)) {
							$msg = 'The new feature code(s) <em>' . implode(', ', $newFeatureCode) . '</em> will be added to the technology <em>' . $tmpRow4 . '</em> (nid : <em>' . $nid . '</em>)';
							array_push($technoStatus, array('line' => -1, 'message' => $msg));
						}
					} else {
						$newNode = build_mavic_node($tmpRow5, 'prodvalcarac', '', $lang, '', 0, false, false, false, false, false, false, false, true);
						$newNode->temp_menu = array('weight' => $row_nb, 'link_title' => $tmpRow5, 'menu_name' => 'menu-menu-technologies-' . $lang, 'plid' => $catEn);
					}
					$newNode->field_child = array();
					$newNode->field_feature_codes = $featureIdA;
					$newNode->field_technologie = array(array('value' => 2));
					$newNode->field_feature_season = array(array('value' => $technoSeason));
					$technoNodes[$lang][$updateIt][$tmpRow5] = $newNode;
				}
			}
		}
	}

	/*
	 * RANGE_FILTER REPORT (we do it after having built the techno nodes as they may have discovered some "toBeCreated" filters being in fact "toBeUpdated")
	 */
	foreach ($filterTypeNodes['en']['create'] as $filterSysName => $fNode) {
		$msg = 'The Filter Type </em>' . $filterSysName . '</em>  does not exist in DB and is ready to be created.';
		array_push($filterStatus, array('line' => -1, 'message' => $msg));
	}
	foreach ($filterTypeNodes['en']['update'] as $filterSysName => $fNode) {
		$msg = 'The Filter Type </em>' . $filterSysName . '</em>, nid[</em>' . $fNode->nid . '</em>]  is ready to be updated';
		array_push($filterStatus, array('line' => -1, 'message' => $msg));
	}
	foreach ($filterNodes['en']['create'] as $filterSysName => $fNode) {
		$msg = 'The Filter </em>' . $filterSysName . '</em> does not exist in DB and is ready to be created.';
		array_push($filterStatus, array('line' => -1, 'message' => $msg));
	}

	//list of node found in the db but not in the file:
	$filtermsgA = array();
	$filterDeletNids = array();
	$tempFiltersNids = array(); //keep nid to filter the feature to delete once technos are done
	foreach ($parameters['langs'] as $lang) {
		if (isset($filterNodes[$lang]['update'])) {
			foreach ($filterNodes[$lang]['update'] as $filerSysName => $fNode) {
				if ($lang == 'en') {
					$msg = 'The Filter </em>' . $filerSysName . '</em>, nid[</em>' . $fNode->nid . '</em>]  is ready to be updated';
					array_push($filterStatus, array('line' => -1, 'message' => $msg));
				}
				$tempFiltersNids[] = $fNode->nid;
			}
		}
		if (isset($exFilterType[$lang])) {
			foreach ($exFilterType[$lang] as $sysName => $nid) {
				if (!isset($filterTypeNodes[$lang]['update'][$sysName])) {
					$filterTypeNodes[$lang]['delete'][] = $nid['nid'];
					$filtermsgA[$nid['nid']] = '<li>Filter Type <em>' . $sysName . '</em>, nid <em>' . $nid['nid'] . '</em> </li>';
					//node_delete($nid['nid']);
				}
			}
		}
	}
	if (!empty($filtermsgA)) {
		$filtermsgB = array_values($filtermsgA);
		$msg = 'The folowing nodes found in the DB do not belong to the data submitted : <ul>';
		$msg .= implode(' ', $filtermsgB);
		$msg .= '</ul><strong>They will be deleted before the import process begins..</strong>';
		array_push($filterWarnings, array('line' => 0, 'message' => $msg));
	}







	/*
	 * TECHNOLOGIES REPORT
	 */
	foreach ($technoLineNodes['en']['update'] as $filterSysName => $fNode) {
		$msg = 'The technoLine </em>' . $filterSysName . '</em>, nid[</em>' . $fNode->nid . '</em>]  is ready to be updated';
		array_push($technoStatus, array('line' => -1, 'message' => $msg));
	}
	foreach ($technoLineNodes['en']['create'] as $filterSysName => $fNode) {
		$msg = 'The technoLine </em>' . $filterSysName . '</em>  does not exist in DB and is ready to be created.';
		array_push($technoStatus, array('line' => -1, 'message' => $msg));
	}
	foreach ($technoCatNodes['en']['update'] as $filterSysName => $fNode) {
		$msg = 'The techno Category </em>' . $filterSysName . '</em>, nid[</em>' . $fNode->nid . '</em>]  is ready to be updated';
		array_push($technoStatus, array('line' => -1, 'message' => $msg));
	}
	foreach ($technoCatNodes['en']['create'] as $filterSysName => $fNode) {
		$msg = 'The techno Category </em>' . $filterSysName . '</em> does not exist in DB and is ready to be created.';
		array_push($technoStatus, array('line' => -1, 'message' => $msg));
	}

	foreach ($technoNodes['en']['create'] as $filterSysName => $fNode) {
		$msg = 'The technology </em>' . $filterSysName . '</em> does not exist in DB and is ready to be created.';
		array_push($technoStatus, array('line' => -1, 'message' => $msg));
	}

	//list of node found in the db but not in the file (filter/technologies nodes are changed to features (only)):
	$msgA = array();
	$technoRemoved = array();
	$filterRemoved = array();
	foreach ($parameters['langs'] as $lang) {
		foreach ($technoNodes[$lang]['update'] as $filterSysName => $fNode) {
			if ($lang == 'en') {
				$msg = 'The technology </em>' . $filterSysName . '</em>, nid[</em>' . $fNode->nid . '</em>]  is ready to be updated';
				array_push($technoStatus, array('line' => -1, 'message' => $msg));
			}
			$technoNidDone[$lang][] = $fNode->nid;
		}

		foreach ($exListLines['technoline'][$lang] as $sysName => $nid) {
			if (!isset($technoLineNodes[$lang]['update'][$sysName])) {
				$technoLineNodes[$lang]['delete'][] = $nid['nid'];
				//node_delete($nid['nid']);
				$msgA[] = '<li>TechnoLine <em>' . $sysName . '</em>, nid <em>' . $nid['nid'] . '</em> </li>';
			}
		}
		foreach ($exListLines['technocat'][$lang] as $sysName => $nid) {
			if (!isset($technoCatNodes[$lang]['update'][$sysName])) {
				$technoCatNodes[$lang]['delete'][] = $nid['nid'];
				$msgA[] = '<li>TechnoCategory <em>' . $sysName . '</em>, nid <em>' . $nid['nid'] . '</em> </li>';
				//node_delete($nid['nid']);
			}
		}
		if (isset($technoNid[$lang])) {
			foreach ($technoNid[$lang] as $allNid => $fca) {
				if (!in_array($allNid, $technoNidDone[$lang])) {//if not belonging to technologies
					$technoNodes[$lang]['technoRemove'][] = $allNid;
					$technoRemoved[] = '<li>Technology, nid <em>' . $allNid . '</em> </li>';
				}
			}
		}
		if (isset($exFilterNids[$lang])) {
			foreach ($exFilterNids[$lang] as $filterNid => $filterFeaturesArray) {//if not belonging to filters
				if (!in_array($filterNid, $tempFiltersNids)) {
					$filterNodes[$lang]['filterRemove'][] = $filterNid;
					$filterRemoved[] = '<li>Filter <em>' . $exFilter[$langL][$filterFeaturesArray[0]]['filterTitle'] . '</em>, nid <em>' . $filterNid . '</em> </li>';
				}
			}
		}
	}
	//technoCat / technoline warnings
	if (!empty($msgA)) {
		$msg = 'The folowing nodes found in the DB do not belong to the data submitted : <ul>';
		$msg .= implode(' ', $msgA);
		$msg .= '</ul><strong>They will be deleted before the import process begins..</strong>';
		array_push($technoWarnings, array('line' => 0, 'message' => $msg));
	}
	//Technologies warnings
	if (!empty($featureRemoved)) {
		$msg = 'The folowing nodes found in the DB do not belong anymore to technologies : <ul>';
		$msg .= implode(' ', $technoRemoved);
		$msg .= '</ul><strong>They will be switched to features only (or deleted if you choose to...) </strong>';
		array_push($technoWarnings, array('line' => 0, 'message' => $msg));
	}
	//Filters / technologies warnings
	if (!empty($featureRemoved)) {
		$msg = 'The folowing nodes found in the DB do not belong anymore to filters : <ul>';
		$msg .= implode(' ', $filterRemoved);
		$msg .= '</ul><strong>They will be switched to features only (or deleted if you choose to...) </strong>';
		array_push($filterWarnings, array('line' => 0, 'message' => $msg));
	}

	//sum up Technos
	$allSheets['TECHNO_IMPORT'] = array(
		'statusMsg' => $technoStatus,
		'warnings' => $technoWarnings,
		'errors' => $technoErrors,
		'nodes' => array(
			'technoline' => $technoLineNodes,
			'technocat' => $technoCatNodes,
			'prodvalcarac' => $technoNodes
		)
	);
	unset($technoNidDone);
	unset($technoNodes);
	unset($technoDone);
	unset($technoLineDone);
	unset($technoErrors);
	unset($technoWarnings);
	unset($technoStatus);




	//sum up Filters
	$allSheets['RANGE_FILTER'] = array(
		'statusMsg' => $filterStatus,
		'warnings' => $filterWarnings,
		'errors' => $filterErrors,
		'nodes' => array(
			'filter_type' => $filterTypeNodes,
			'prodvalcarac' => $filterNodes
		)
	);
	unset($exFeatures);
	unset($exFilterNids);
	unset($exFilter);
	unset($exFilterType);
	unset($filterTypeNid);
	unset($filterNodes);
	unset($filterTypeNodes);
	unset($filterStatus);
	unset($filterWarnings);
	unset($filterErrors);



	/*	 * **************************************************************************************************************
	 * 
	 * LINELIST CHECKING
	 */



	$macroErrors = array();
	$macroWarnings = array();
	$macroStatus = array();

	//DB : retrieve the list of existing macromodels

	$exMacro = array();
	$macromo = 'macromodel';
	foreach ($parameters['langs'] as $lang) {
		$resMacro = db_query("	SELECT c.`field_modelco_value`, n.`title`, n.`nid`
								FROM {node} n 
								INNER JOIN {content_type_macromodel} c using (nid) 
								WHERE n.`type` = '%s'
								AND  n.`language` = '%s'", $macromo, $lang);
		while ($result = db_fetch_array($resMacro)) {
			if (!isset($exMacro[$lang]))
				$exMacro[$lang] = array();
			$macroLower = strtolower($result['field_modelco_value']);
			$exMacro[$lang][$macroLower] = array('nid' => $result['nid'], 'macroTitle' => $result['title']);
		}
	}
	//DB : retrieve the list of articles
	$exArticle = array();
	$artic = 'article';
	foreach ($parameters['langs'] as $lang) {
		$resMacro = db_query("	SELECT n.`title`, n.`nid`
								FROM {node} n  
								WHERE n.`type` = '%s'
								AND  n.`language` = '%s'", $artic, $lang);
		while ($result = db_fetch_array($resMacro)) {
			if (!isset($exArticle[$lang]))
				$exArticle[$lang] = array();
			$exArticle[$lang][$result['title']] = array('nid' => $result['nid']);
		}
	}


	//load xls data
	$objLinelistSheet = $objPHPExcel->getSheetByName('LINELIST');
	$highestRow = $parameters['sheets']['LINELIST']['max_row'];
	$highestColumnIndex = count(range('A', $parameters['sheets']['LINELIST']['col_range']));
	$linelistColumn = array_flip($parameters['sheets']['LINELIST']['col_name']);
	//load trad
	$objLinelistTradSheet = $objPHPExcel->getSheetByName('LINELIST_TRANSLATION');
	$highestTradRow = $parameters['sheets']['LINELIST_TRANSLATION']['max_row'];
	$highestTradColumnIndex = count(range('A', $parameters['sheets']['LINELIST_TRANSLATION']['col_range']));
	$linelistTradColumn = array_flip($parameters['sheets']['LINELIST_TRANSLATION']['col_name']);

	//comparaison checking var
	$sameModel = array();
	$createMacro = FALSE;
	$previous_model_code = '';
	$updateCreate = '';

	for ($row_nb = 2; $row_nb <= $highestRow; ++$row_nb) {
		$msgA = array();
		$msgW = array();
		if ('0' === $line = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['LINE'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>line system name</em>';
		if ('0' === $family = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['FAMILY'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>family-category system name</em>';
		if ('0' === $tab = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['TAB'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>tab system name</em>';
		if ('0' === $art_code = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['ARTICLE_CODE'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>article code</em>';
		if ('0' === $macroNameEn = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['MODEL_NAME_EN'], $row_nb)->getValue(), 'string', false, true))
			$msgA[] = '<em>model system name</em>';
		if ('0' === $model_code = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['MODEL_CODE'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>model code</em>';
		if ('0' === $season = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['SEASON'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>season</em>';
		if ('0' === $def_color = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['DEF_COLOR'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>default color</em>';
		if ('0' === $assoc_art_1 = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['ASSOC_ARTICLE_1'], $row_nb)->getValue(), 'string', true, true))
			$msgW[] = '<em>associated article 1</em>';
		if ('0' === $assoc_art_2 = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['ASSOC_ARTICLE_2'], $row_nb)->getValue(), 'string', true, true))
			$msgW[] = '<em>associated article 2</em>';
		if ('0' === $assoc_art_3 = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['ASSOC_ARTICLE_3'], $row_nb)->getValue(), 'string', true, true))
			$msgW[] = '<em>associated article 3</em>';
		$def_weight = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['DEF_WEIGHT'], $row_nb)->getValue());
		if ('0' === $articl_status = mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['STATUS'], $row_nb)->getValue(), 'string', true, true))
			$msgA[] = '<em>status</em>';

		if (!empty($msgA)) {
			$listPb = implode(', ', $msgA);
			array_push($macroErrors, array('line' => $row_nb, 'message' => 'Empty cell on col. : ' . $listPb . '.'));
			continue;
		}
		if (!empty($msgW)) {
			$listPb = implode(', ', $msgW);
			array_push($macroWarnings, array('line' => $row_nb, 'message' => 'Empty cell on col. : ' . $listPb . '.'));
		}

		$sysFamilyName = $line . '/' . $family . '/' . $tab;


		//clean landscape
		$list_land = explode(';', mavicimport_cleanCell($objLinelistSheet->getCellByColumnAndRow($linelistColumn['LANDSCAPE'], $row_nb)->getValue(), 'string', true, true));
		$list_land_clean = array();
		foreach ($list_land as $land) {
			if (!empty($land)) {
				if (empty($parameters['landscape'][$land])) {
					$msg = 'Unrecognized landscape <em>' . $land . '</em>, allowed values are : <ul>';
					$msg .= '<li><em>all</em></li>';
					$msg .= '<li><em>company</em></li>';
					$msg .= '<li><em>road</em></li>';
					$msg .= '<li><em>triathlon</em></li>';
					$msg .= '<li><em>roadaero</em> or <em>road_aero</em></li>';
					$msg .= '<li><em>roadmountain </em>or <em>road_mountain</em></li>';
					$msg .= '<li><em>mtb</em></li>';
					$msg .= '<li><em>mtbextreme </em>or <em>mtb_extreme</em></li>';
					$msg .= '<li><em>mtbcross-country </em>or <em>mtb_cross-country</em></li>';
					$msg .= '<li><em>mtballmountain </em>or <em>mtb_all_mountain</em></li>';
					$msg .= '<li><em>track</em></li>';
					$msg .= '</ul>';

					array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
					break;
				}
				$list_land_clean = $list_land_clean + $parameters['landscape'][$land];
			}
		}
		if (empty($list_land_clean)) {
			$msg = 'Missing landscape definition.';
			array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
			$list_land_clean = array(array('value' => 'empty'));
		}

		//compare macroName and model code
		if ($model_code != $previous_model_code && $sameModel[$previous_model_code][0] != $macroNameEn) {
			$sameModel[$model_code] = array($macroNameEn, array($line, $family, $tab, $season, $list_land_clean));
			$createMacro = true;

			//get corresponding line in the translations' file
			for ($row_trad = 2; $row_trad <= $highestTradRow; ++$row_trad) {
				if ($macroNameEn == mavicimport_cleanCell($objLinelistTradSheet->getCellByColumnAndRow($linelistTradColumn['en'], $row_trad)->getValue(), 'string', false, true)) {
					$currentRowTrad = $row_trad;
					break;
				}
			}
			if (!isset($currentRowTrad)) {
				$msg = 'Translations not found for model <em>' . $macroNameEn . '</em>.';
				array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
				$currentRowTrad = 1;
			}
		} elseif ($sameModel[$model_code] != array($macroNameEn, array($line, $family, $tab, $season, $list_land_clean))) {
			$msg = 'A difference has been detected between articles supposed to belong to the same macrocmodel : ';
			if ($model_code != $previous_model_code) {
				$msg .= 'the model code is not identical to the previous one although its name is.';
			} elseif ($sameModel[$model_code][0] != $macroNameEn) {
				$msg .= 'the name of the macromodel is not identical to the previous one although its model code is.';
			} else {
				$temptdi = array_diff($sameModel[$model_code][1], array($line, $family, $tab, $season, $list_land_clean));
				if (!empty($temptdi)) {
					$diffP = implode(', ', $temptdi);
					$msg .= 'the following data are different from the previous ones although the model code ant the model name are identical: ' . $diffP . '.';
				}
			}
			array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
		}
		$previous_model_code = $model_code;


		foreach ($parameters['langs'] as $lang) {

			//
			// MACROMODEL
			//
			
			
			if ($createMacro) {
				if ($lang == 'ja') {
					$createMacro = FALSE;
				}
				//check trad
				$macroName = mavicimport_cleanCell($objLinelistTradSheet->getCellByColumnAndRow($linelistTradColumn[$lang], $currentRowTrad)->getValue(), 'string', false, true);
				if (empty($macroName)) {
					$msg = "Translations is missing in <em>" . $lang . "</em> for model <em>" . $macroNameEn . "</em>.";
					array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
					$macroName = 'translationIsMissing(' . lang . ')';
				}
				//check family tab
				$familyUpdated = isset($allSheets['RANGE_RANK_LANDSCAPE']['nodes']['family'][$lang]['update'][$sysFamilyName]); //they cannot be created as the system doesn't handle it.
				if ($familyUpdated) {
					$nodeParent = $allSheets['RANGE_RANK_LANDSCAPE']['nodes']['family'][$lang]['update'][$sysFamilyName]->nid;
					if (!$menuParent = db_result(db_query('select m.mlid from {menu_links} m where m.`link_path`="node/' . $nodeParent . '"'))) {
						$msg = 'Cannot found family menu (node:' . $nodeParent . ') (language: ' . $lang . ').';
						array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
						continue;
					}
					//if exist
					if (isset($exMacro[$lang][$model_code]['nid']) && isset($exMacro['en'][$model_code]['nid'])) {
						$updateCreate = 'update';
						$macroNode = build_mavic_node($macroName, false, '', $lang, $exMacro[$lang][$model_code]['nid'], 0, false, true, true, array('link_title' => $macroName, 'menu_name' => 'menu-primary-links-' . $lang, 'plid' => $menuParent), false, true, false, true);
						$macroNode->field_landscape = $list_land_clean;
						$macroNode->field_modelco = array(array('value' => $model_code));
						if (!empty($def_weight) && $def_weight != 'NA')
							$macroNode->field_default_weight = array(array('value' => $def_weight));
						$macroNode->field_page_title = array(array('value' => $macroNode->title));
						$macroNode->field_macro_season = array(array('value' => $season));
						if ($articl_status == 'new') {
							$macroNode->field_new_product = array(array('value' => 1));
						} else {
							$macroNode->field_new_product = array(array('value' => 0));
						}
						//remove eventual old technologies/filters
						$filteredFilters = array();
						$filteredtechno = array();

						foreach ($macroNode->field_filter_value as $filterLink) {
							if (!in_array($filterLink['nid'], $allSheets['RANGE_FILTER']['prodvalcarac'][$lang]['filterRemove'])) {
								$filteredFilters[] = $filterLink;
							}
						}
						$macroNode->field_filter_value = $filteredFilters;
						foreach ($macroNode->field_technologienode as $technoLink) {
							if (!in_array($technoLink['nid'], $allSheets['TECHNO_IMPORT']['prodvalcarac'][$lang]['technoRemove'])) {
								$filteredtechno[] = $technoLink;
							}
						}
						$macroNode->field_technologienode = $filteredtechno;
						$macroNodes[$lang]['update'][$model_code] = $macroNode;
						$msg = 'The macromodel </em>' . $macroName . '</em>, </em>' . $model_code . '</em>, nid[<em>' . $exMacro[$lang][$model_code]['nid'] . '</em>] (<em>' . $lang . '</em>) is ready to be updated';
						array_push($macroStatus, array('line' => $row_nb, 'message' => $msg));

						$updateCreate = 'update';

						//we never know... error	
					} elseif (isset($exMacro['en'][$model_code]['nid'])) {
						$msg = 'The <em>' . $lang . '</em> version of macromodel </em>' . $model_code . '</em> has not been found while the english one exists.';
						array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
						continue;
						//error too
					} elseif (isset($exMacro[$lang][$model_code]['nid'])) {
						$msg = 'The <em>' . $lang . '</em> version of macromodel </em>' . $model_code . '</em> has been found in the DB while the english one has not.';
						array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
						continue;
						//definitely not existing : create
					} else {
						$updateCreate = 'create';
						$macroNode = build_mavic_node($macroName, 'macromodel', '', $lang, '', 0, false, true, true, array('weight' => $row_nb, 'link_title' => $macroName, 'menu_name' => 'menu-primary-links-' . $lang, 'plid' => $menuParent), false, true, false, true);
						$macroNode->field_landscape = $list_land_clean;
						$macroNode->field_modelco = array(array('value' => $model_code));
						if (!empty($def_weight) && $def_weight != 'NA')
							$macroNode->field_default_weight = array(array('value' => $def_weight));
						$macroNode->field_page_title = array(array('value' => $macroNode->title));
						$macroNode->field_macro_season = array(array('value' => $season));
						$macroNode->field_otherarticle = array();
						if ($articl_status == 'new') {
							$macroNode->field_new_product = array(array('value' => 1));
						} else {
							$macroNode->field_new_product = array(array('value' => 0));
						}
						$macroNodes[$lang]['create'][$model_code] = $macroNode;
						$msg = 'The macromodel </em>' . $macroName . '</em> (<em>' . $lang . '</em>) is ready to be created';
						array_push($macroStatus, array('line' => $row_nb, 'message' => $msg));
					}
				} else {
					if ($lang == 'en') {
						$msg = 'The path line/family/tab <em>' . $sysFamilyName . '</em> does not exist.';
						array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
					}
				}
			}

			//status (set on the macromodel level although it is defined on the article level)
			if ($articl_status == 'new') {
				$macroNodes[$lang][$updateCreate][$model_code]->field_new_product = array(array('value' => 1));
			}

			//
			// ARTICLE
			//
			
			//if exist
			if (isset($exArticle[$lang][$art_code]['nid']) && isset($exArticle['en'][$art_code]['nid'])) {
				if ($lang == 'en') {
					if ($articl_status != 'online') {
						$msg = 'The article </em>' . $art_code . '</em> (<em>' . $lang . '</em>) has not been tagged as online but has actually been found in the DB : it will be updated';
						array_push($macroWarnings, array('line' => $row_nb, 'message' => $msg));
					}
				}
				$articleNode = build_mavic_node($art_code, false, '', $lang, $exArticle[$lang][$art_code]['nid'], 0, false, false, false, false, false, true, false, true);
				$articleNode->field_season = array(array('value' => $season));
				$articleNode->field_associated = array($assoc_art_1, $assoc_art_2, $assoc_art_3);
				//associate to macro model
				$articleNode->belongsTo = array('model_code' => $model_code, 'def_color' => $def_color);
				$articleNodes[$lang]['update'][$art_code] = $articleNode;
				$msg = 'The article </em>' . $art_code . '</em>, nid[<em>' . $exArticle[$lang][$art_code]['nid'] . '</em>] (<em>' . $lang . '</em>) is ready to be updated';
				array_push($macroStatus, array('line' => $row_nb, 'message' => $msg));
			} elseif (isset($exArticle['en'][$art_code]['nid'])) {//we never know... error
				$msg = 'The <em>' . $lang . '</em> version of article </em>' . $art_code . '</em> has not been found while the english one exists.';
				array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
				break;
			} elseif (isset($exArticle[$lang][$art_code]['nid'])) {//error too
				$msg = 'The <em>' . $lang . '</em> version of article </em>' . $art_code . '</em> has been found in the DB while the english one has not.';
				array_push($macroErrors, array('line' => $row_nb, 'message' => $msg));
				break;
			} else {//definitely not existing : create
				if ($lang == 'en') {
					if ($articl_status != 'new') {
						$msg = 'The article </em>' . $art_code . '</em> (<em>' . $lang . '</em>) has not been tagged as new but has not been found in the DB : it will be created';
						array_push($macroWarnings, array('line' => $row_nb, 'message' => $msg));
					}
				}
				$articleNode = build_mavic_node($art_code, 'article', '', $lang, '', 0, false, false, false, false, false, true, false, true);
				$articleNode->field_associated = array($assoc_art_1, $assoc_art_2, $assoc_art_3);
				$articleNode->belongsTo = array('model_code' => $model_code, 'def_color' => $def_color); //to link to macromodel node when saved
				$articleNode->field_season = array(array('value' => $season));
				$articleNodes[$lang]['create'][$art_code] = $articleNode;
				$msg = 'The article </em>' . $art_code . '</em> (<em>' . $lang . '</em>) is ready to be created';
				array_push($macroStatus, array('line' => $row_nb, 'message' => $msg));
			}
		}
	}

	/*
	 * LINELIST REPORT
	 */

	//list of node found in the db but not in the file:
	$msgA = array();
	foreach ($parameters['langs'] as $lang) {
		foreach ($exMacro[$lang] as $sysNameX => $nid) {
			if (!isset($macroNodes[$lang]['update'][$sysNameX])) {
				$macroNodes[$lang]['delete'][$sysNameX] = $nid['nid'];
				$msgA[] = '<li>Macromodel <em>' . $sysNameX . '</em>, nid <em>' . $nid['nid'] . '</em> </li>';
				//node_delete($nid['nid']);
			}
		}
		foreach ($exArticle[$lang] as $sysName => $nid) {
			if (!isset($articleNodes[$lang]['update'][$sysName])) {
				$articleNodes[$lang]['delete'][$sysName] = $nid['nid'];
				$msgA[] = '<li>Article <em>' . $sysName . '</em>, nid <em>' . $nid['nid'] . '</em> </li>';
				//node_delete($nid['nid']);
			}
		}
	}
	if (!empty($msgA)) {
		$msg = 'The folowing nodes found in the DB do not belong to the data submitted : <ul>';
		$msg .= implode(' ', $msgA);
		$msg .= '</ul><strong>They will be deleted before the import process begins..</strong>';
		array_push($macroWarnings, array('line' => 0, 'message' => $msg));
	}

	//sum up	
	$allSheets['LINELIST'] = array(
		'statusMsg' => $macroStatus,
		'warnings' => $macroWarnings,
		'errors' => $macroErrors,
		'nodes' => array(
			'macromodel' => $macroNodes,
			'article' => $articleNodes
		)
	);

	unset($macroNodes);
	unset($articleNodes);
	unset($macroStatus);
	unset($macroWarnings);
	unset($macroErrors);

	/*	 * **************************************************************************************************************
	 * 
	 * SUMMARY
	 */



	$transData = array();
	$transData = array();
	foreach ($allSheets as $sheets => $data) {
		$transData[$sheets] = array(
			'statusMsg' => $data['statusMsg'],
			'warnings' => $data['warnings'],
			'errors' => $data['errors'],
		);
		mavicimport_ajaxsubmit_set_data(false, $sheets, $data['nodes']);
	}

	unset($allSheets);
	return $transData;
}

/**
 * Delete or remove from technologies or filters all nodes collected during the XLSX's checking  and 
 * delete all technologies menuItem (easier to rebuild it from scratch than deleting/inserting individual menu items)
 * @global $parameters $parameters
 * @return boolean
 */
function mavicimport_ajaxsubmit_deleteXlsxData() {
	global $parameters;
	//retrieve the liste of nodes
	$NodeallSheets = mavicimport_ajaxsubmit_set_data(false);
	foreach ($NodeallSheets as $sheet) {
		foreach ($sheet as $NodeTypesList) {
			foreach ($NodeTypesList as $langD => $nodeType) {
				//to delete
				if (isset($nodeType['delete'])) {
					$toDeleteList = $nodeType['delete'];
					foreach ($toDeleteList as $toDelete) {
						node_delete($toDelete);
					}
					//technoRemove AND filterRemove
				} else if (isset($nodeType['technoRemove']) || isset($nodeType['filterRemove'])) {
					$toRemoveList = isset($nodeType['technoRemove']) ? $nodeType['technoRemove'] : $nodeType['filterRemove'];
					foreach ($toRemoveList as $toRemove) {
						$newNode = node_load($toRemove);
						if (isset($nodeType['technoRemove'])) {
							$newNode->field_technologie = array(array('value' => 0));
							//settings to call mavic_search_404 module for deleting alias and preparing a redirection on update
							if (isset($newNode->path) && $newNode->path != 'node/'.$toRemove) {
								$newNode->pathauto_perform_alias = FALSE;
								unset($newNode->path);
							}
							//remove techno reference (not very clean as it should be deleted and delta of other ref. fields updated, but this is done when importing xml files)
							$resFilValues = db_query(" UPDATE {content_field_technologienode} SET field_technologienode_nid = NULL WHERE field_technologienode_nid = %d", $toRemove);
						} else {
							$newNode->field_filter_main = array(array('value' => 0));
							//remove filter reference (not very clean as it should be deleted and delta of other ref. fields updated, but this is done when importing xml files)
							$resFilValues = db_query(" UPDATE {content_field_filter_value} SET field_filter_value_nid = NULL WHERE field_filter_value_nid = %d", $toRemove);
						}
						node_save($newNode);
					}
				}
			}
		}
	}
	//delete techonlogies' menu items
	$prodval = 'prodvalcarac';
	$technoLineCat = array('technoline', 'technocat');
	$exListLines = array();
	$techNid = array();
	foreach ($technoLineCat as $technoLineT) {
		$resLine = db_query("	SELECT n.`nid`
						FROM {node_revisions} r 
						INNER JOIN {node} n USING (vid)
						WHERE n.`type` = '%s'", array($technoLineT));
		while ($result = db_fetch_array($resLine)) {
			if (!isset($exListLines[$result['nid']]))
				$exListLines[] = 'node/' . $result['nid'];
		}
	}
	$resTechno = db_query("	SELECT n.`nid`
				FROM {node} n
				INNER JOIN {content_type_prodvalcarac} p using (nid)
				WHERE n.type = '%s'
				AND p.`field_technologie_value` IN (" . db_placeholders(range(1, 3), 'int') . ")", array($prodval, 1, 2, 3));
	while ($result = db_fetch_array($resTechno)) {
		if (!isset($techNid[$result['nid']]))
			$techNid[] = 'node/' . $result['nid'];
	}
	$phmenu = array_merge($techNid, $exListLines);
	$resTechMenu = db_query("	SELECT m.`mlid`, m.`plid`, m.`link_path`, m.`link_title`
				FROM {menu_links} m
				WHERE m.`link_path` IN (" . db_placeholders($phmenu, 'varchar') . ")", $phmenu);
	while ($result = db_fetch_array($resTechMenu)) {
		menu_link_delete($result['mlid']);
	}

	return true;
}

/**
 * 
 * Set the differents steps for importing xlsx data and launch the next one
 * @param int $step undefined for the the first one
 */
function mavicimport_ajaxsubmit_importXlsxData($xlsx_file, $step = null) {

	//set the last step (1 based)
	$maxStep = 17;

	//first run
	if ($step === null) {

		$callbackParams = array(
			'beforeSubmit' => 'importing data : creating technoline',
		);
		mavicimport_ajaxsubmit_set_callback($callbackParams);
		mavicimport_ajaxsubmit_set_step(false, 1, 'import_xlsx_data_step'); //
		//next step switch the import params	
	} else {
		$nextStepMsg = 'importing data : ';
		switch ($step) {
			case 1 :// create technoline
				$import_params = array(
					'sheet' => 'TECHNO_IMPORT',
					'strNodeType' => 'technoline',
					'nodeType' => 'technoline',
					'action' => 'create',
					'keepData' => false,
					'associate' => true,
				);
				$nextStepMsg .= 'creating technoline...';
				$nextStep = 2;
				break;
			case 2 :// update technoline
				$import_params = array(
					'sheet' => 'TECHNO_IMPORT',
					'strNodeType' => 'technoline',
					'nodeType' => 'technoline',
					'action' => 'update',
					'keepData' => false,
					'associate' => false,
				);
				$nextStepMsg .= 'creating technocat...';
				$nextStep = 3;
				break;

			case 3 :// create technocat
				$import_params = array(
					'sheet' => 'TECHNO_IMPORT',
					'strNodeType' => 'technocat',
					'nodeType' => 'technocat',
					'action' => 'create',
					'keepData' => false,
					'associate' => true,
				);
				$nextStepMsg .= 'updating technocat...';
				$nextStep = 4;
				break;

			case 4 :// update technocat
				$import_params = array(
					'sheet' => 'TECHNO_IMPORT',
					'strNodeType' => 'technocat',
					'nodeType' => 'technocat',
					'action' => 'update',
					'keepData' => false,
					'associate' => false,
				);
				$nextStepMsg .= 'creating technologies...';
				$nextStep = 5;
				break;


			case 5 :// create technologies
				//set technologies menu
				//if also a filter, keep nid
				$import_params = array(
					'sheet' => 'TECHNO_IMPORT',
					'strNodeType' => 'prodvalcarac',
					'nodeType' => 'prodvalcarac',
					'action' => 'create',
					'keepData' => false,
					'associate' => true,
				);
				$nextStepMsg .= 'updatings technologies...';
				$nextStep = 6;
				break;

			case 6 :// update technologies
				//set technologies menu
				//if also a filter, keep nid
				$import_params = array(
					'sheet' => 'TECHNO_IMPORT',
					'strNodeType' => 'prodvalcarac',
					'nodeType' => 'prodvalcarac',
					'action' => 'update',
					'keepData' => true,
					'associate' => false,
				);
				$nextStepMsg .= 'updatings filters categories and linking associated filters...';
				$nextStep = 7;
				break;

			case 7 :// create filters
				//collect nid for the node ref field of filtertype
				$import_params = array(
					'sheet' => 'RANGE_FILTER',
					'strNodeType' => 'filter',
					'nodeType' => 'prodvalcarac',
					'action' => 'create',
					'keepData' => false,
					'associate' => false,
				);
				$nextStepMsg .= 'updating filters...';
				$nextStep = 8;
				break;

			case 8 :// update filters
				//collect nid for the node ref field of filtertype
				$import_params = array(
					'sheet' => 'RANGE_FILTER',
					'strNodeType' => 'filter',
					'nodeType' => 'prodvalcarac',
					'action' => 'update',
					'keepData' => true,
					'associate' => false,
				);
				$nextStepMsg .= 'creating filters categories...';
				$nextStep = 9;
				break;

			case 9 :// create filterTypes
				//collect nid for the node ref field of tab
				$import_params = array(
					'sheet' => 'RANGE_FILTER',
					'strNodeType' => 'filterType',
					'nodeType' => 'filter_type',
					'action' => 'create',
					'keepData' => false,
					'associate' => false,
				);
				$nextStepMsg .= 'updatings filters categories and linking associated filters...';
				$nextStep = 10;
				break;

			case 10 :// update filterTypes
				//collect nid for the node ref field of tab
				$import_params = array(
					'sheet' => 'RANGE_FILTER',
					'strNodeType' => 'filterType',
					'nodeType' => 'filter_type',
					'action' => 'update',
					'keepData' => true,
					'associate' => false,
				);
				$nextStepMsg .= 'updatings lines...';
				$nextStep = 11;
				break;

			case 11 :// update line
				//
				$import_params = array(
					'sheet' => 'RANGE_RANK_LANDSCAPE',
					'strNodeType' => 'line',
					'nodeType' => 'line',
					'action' => 'update',
					'keepData' => true,
					'associate' => false,
				);
				$nextStepMsg .= 'updatings family (category)...';
				$nextStep = 12;
				break;

			case 12 :// update category
				//
				$import_params = array(
					'sheet' => 'RANGE_RANK_LANDSCAPE',
					'strNodeType' => 'category',
					'nodeType' => 'category',
					'action' => 'update',
					'keepData' => true,
					'associate' => false,
				);
				$nextStepMsg .= 'updatings tab (family) and linking associated filters types...';
				$nextStep = 13;
				break;

			case 13 :// update tab
				//set filters type
				$import_params = array(
					'sheet' => 'RANGE_RANK_LANDSCAPE',
					'strNodeType' => 'family',
					'nodeType' => 'family',
					'action' => 'update',
					'keepData' => false,
					'associate' => false,
				);
				$nextStepMsg .= 'creating articles...';
				$nextStep = 14;
				break;

			case 14 :// update articles
				$import_params = array(
					'sheet' => 'LINELIST',
					'strNodeType' => 'article',
					'nodeType' => 'article',
					'action' => 'create',
					'keepData' => false,
					'associate' => true,
				);
				$nextStepMsg .= 'updatings articles...';
				$nextStep = 15;
				break;

			case 15 :// update filterTypes
				//collect nid for the node ref field of Line
				$import_params = array(
					'sheet' => 'LINELIST',
					'strNodeType' => 'article',
					'nodeType' => 'article',
					'action' => 'update',
					'keepData' => true,
					'associate' => false,
				);
				$nextStepMsg .= 'creating macromodels...';
				$nextStep = 16;
				break;

			case 16 :// update filterTypes
				//collect nid for the node ref field of Line
				$import_params = array(
					'sheet' => 'LINELIST',
					'strNodeType' => 'macromodel',
					'nodeType' => 'macromodel',
					'action' => 'create',
					'keepData' => false,
					'associate' => false,
				);
				$nextStepMsg .= 'updatings macromodels...';
				$nextStep = 17;
				break;

			case 17 :// update filterTypes
				//collect nid for the node ref field of Line
				$import_params = array(
					'sheet' => 'LINELIST',
					'strNodeType' => 'macromodel',
					'nodeType' => 'macromodel',
					'action' => 'update',
					'keepData' => true,
					'associate' => false,
				);
				$nextStepMsg .= '';
				$nextStep = 18;
				break;

			default:

				break;
		}

		//launch the import for data defined in this step
		mavicimport_ajaxsubmit_save_node($xlsx_file, $import_params);

		//check result
		$sheetsErrors = array();
		$sheetsWarnings = array();
		$sheetsSuccess = array();

		foreach ($xlsx_file->xlsxReport as $sheetName => $sheetData) {
			if (!empty($sheetData['errors'])) {
				array_push($sheetsErrors, $sheetName);
			} elseif (!empty($sheetData['warnings'])) {
				array_push($sheetsWarnings, $sheetName);
			} else {
				array_push($sheetsSuccess, $sheetName);
			}
		}

		if (!empty($sheetsErrors)) {
			$xlsx_file->status = 0;
			$endmsg = 'Error : process interrupted.';
		} else {
			$xlsx_file->status = 1;
			$endmsg = 'Process is complete : buildings report...';
			if (!empty($sheetsWarnings)) {
				$xlsx_file->status = 2;
			}
		}
		//if process not finished and no errors :
		if ($step < $maxStep && $xlsx_file->status) {
			//got to next step
			$callbackParams = array(
				'beforeSubmit' => $nextStepMsg,
			);
			mavicimport_ajaxsubmit_set_callback($callbackParams);
			mavicimport_ajaxsubmit_set_step(false, $nextStep, 'import_xlsx_data_step');
		} else {
			//set the file as imported and end process,
			$xlsx_file->imported = true;
			$callbackParams = array(
				'beforeSubmit' => $endmsg,
			);
			mavicimport_ajaxsubmit_set_callback($callbackParams);
			mavicimport_ajaxsubmit_set_step(false, 'import_confirmed', 'import_xlsx_data');
		}
	}
	//store the temporary data
	mavicimport_ajaxsubmit_set_file(FALSE, 'xlsx', $xlsx_file);
}

function mavicimport_ajaxsubmit_save_node(&$xlsx_file, $import_params) {

	global $parameters;

	cache_clear_all();

	//retrieve current step params
	$sheet = $import_params['sheet'];
	$strNodeType = $import_params['strNodeType'];
	$nodeType = $import_params['nodeType'];
	$action = $import_params['action'];
	$keepData = $import_params['keepData'];
	$associate = $import_params['associate'];


	//retrieve the liste of nodes

	$NodeTypesList = mavicimport_ajaxsubmit_set_data(false, $sheet);
	$NodesList = $NodeTypesList[$nodeType];

	//set arrays for reporting
	$statusA = array();
	$warningsA = array();
	$errorsA = array();

	// save syncronise fields for the node type :  	
	$syncFields = i18nsync_node_fields($nodeType);
	//deactivate the syncronise for the node type :
	$syncFieldsVar = 'i18nsync_nodeapi_' . $nodeType;
	variable_set($syncFieldsVar, array());

	if ($keepData) {
		if (isset($xlsx_file->xlsxReport['next']['belongsTo'])) {
			$belongToArray = (array) $xlsx_file->xlsxReport['next']['belongsTo'];
		} else {
			$belongToArray = array();
			foreach ($parameters['langs'] as $lang) {
				$belongToArray[$lang] = array();
			}
		}

		if (isset($xlsx_file->xlsxReport['next']['refTo'])) {
			$refToArray = (array) $xlsx_file->xlsxReport['next']['refTo'];
		} else {
			//error_log("Le tableau refToArray n'existe pas.\n\r", 3, "D:/projects/mavic/trunk/www/logs/debug_log.log");
		}
	} else {
		$belongToArray = array();
		foreach ($parameters['langs'] as $lang) {
			$belongToArray[$lang] = array();
		}
		if (isset($xlsx_file->xlsxReport['next']['belongsTo'])) {
			$refToArray = $xlsx_file->xlsxReport['next']['belongsTo'];
		} else {
			//error_log("le tableau belongToArray n'a pas été défini avant.\n\r", 3, "D:/projects/mavic/trunk/www/logs/debug_log.log");
		}
	}

	foreach ($parameters['langs'] as $lang) {
		$toSaves = $NodesList[$lang][$action];
		$a = 0;

		if (!empty($toSaves)) {

			foreach ($toSaves as $key => $toSave) {
				$belongsTo = array();

				//TECHNO
				if ($strNodeType == 'prodvalcarac' || $nodeType == 'technocat' || $nodeType == 'technoline') {
					if (isset($toSave->is_allready_filter)) {
						$belongsTo = array($toSave->is_allready_filter);
						unset($toSave->is_allready_filter);
					}
					if ($action == 'update') {
						if (isset($toSave->temp_field_child)) {
							$fieldChild = (array) $toSave->temp_field_child;
							unset($toSave->temp_field_child);
						} else {
							$fieldChild = array();
						}
						if (!empty($fieldChild)) {
							foreach ($fieldChild as $ref) {
								$tempNode = $NodesList[$lang]['update'][$ref];
								//if(!isset($toSave->field_child)) $toSave->field_child = array();
								$toSave->field_child[] = array('nid' => $tempNode->nid);
								//unset($toSave->temp_field_child);
							}
						}

						if (isset($toSave->temp_menu)) {

							$menu = (array) $toSave->temp_menu;
							if ($nodeType == 'prodvalcarac' || $nodeType == 'technocat') {
								if ($nodeType == 'prodvalcarac') {
									$refNode = $NodeTypesList['technocat'][$lang]['update'][$menu['plid']];
								} elseif ($nodeType == 'technocat') {
									$refNode = $NodeTypesList['technoline'][$lang]['update'][$menu['plid']];
								}

								$menuMlid = (array) $refNode->menu;
								$menu['plid'] = $menuMlid['mlid'];
								$toSave->menu = $menu;

							} elseif ($nodeType == 'technoline') {
								$toSave->menu = $toSave->temp_menu;
							}
							unset($toSave->temp_menu);
						}
					}
				}




				//FILTERS - FILTERSTYPE
				//if filter :  retrieve ref it belongs to
				if ($strNodeType == 'filter' || $strNodeType == 'filterType') {

					if (isset($toSave->belongsTo) && !empty($toSave->belongsTo)) {
						$statusA[] = array('line' => 1, 'message' => $strNodeType . '  belongsTo retrival :  ' . $lang . ' -' . implode(', ', $toSave->belongsTo) . '.');
						$belongsTo = (array) $toSave->belongsTo;
						//unset it
						unset($toSave->belongsTo);
					}
					//if filterType : add filters, family : add filterType
				}


				//ARTICLES
				if ($strNodeType == 'article') {
					if ($action == 'create') {

						$toSave->temp_field_associated = (array) $toSave->field_associated;
						unset($toSave->field_associated);
					} else {
						if (isset($toSave->temp_field_associated)) {
							$toSave->field_associated = (array) $toSave->temp_field_associated;
						}
						$tempAssoc = $toSave->field_associated;
						$toSave->field_associated = array();
						if (!empty($tempAssoc)) {
							$i = 0;
							foreach ($tempAssoc as $tempAsso) {
								$i++;
								$tempNode = $toSaves[$tempAsso];
								$toSave->field_associated[] = array('nid' => $tempNode->nid);
							}
						}
					}

					if (isset($toSave->belongsTo)) {
						$belongsTo = (array) $toSave->belongsTo;
						$statusA[] = array('line' => 1, 'message' => $strNodeType . '  belongsTo retrival :  ' . $lang . ' -' . implode(', ', $toSave->belongsTo) . '.');
					}
				}






				if ($strNodeType == 'filterType' || $strNodeType == 'family' || $strNodeType == 'macromodel') {


					if (isset($refToArray[$lang][$key])) {
						$i = 0;
						//for filtertype the field may have not been set yet
						if ($strNodeType == 'filterType') {
							$toSave->field_filter_value_list = array();
							$filterOrder = array();
							foreach ($toSave->filterOrder as $rank => $tmnid) {
								if (is_array($tmnid)) {
									$query = db_query("	SELECT n.`nid`
														FROM {content_field_feature_codes} c 
														INNER JOIN {node} n using (nid)
														WHERE c.field_feature_codes_value = '%s'
														AND n.language = '%s'", array($tmnid[0], $lang));
									if ($result = db_result($query)) {
										$filterOrder[$result] = $rank;
									}
								} else {
									$filterOrder[$tmnid] = $rank;
								}
							}
						} elseif ($strNodeType == 'macromodel') {//reset it also for macromodels (not done before and node_delete doesn't delete ref. fieldd)
							$toSave->field_otherarticle = array();
						}
						unset($toSave->filterOrder);
						$ooops = array();
						foreach ($refToArray[$lang][$key] as $FilterNid) {
							$statusA[] = array('line' => 1, 'message' => 'The node nid[' . $FilterNid . '] ' . $lang . ' added to ' . $strNodeType . ' <em>' . $toSave->title . '</em>.');
							if ($strNodeType == 'filterType') {
								if (isset($filterOrder[$FilterNid])) {
									$toSave->field_filter_value_list[(int) $filterOrder[(int) $FilterNid]] = array('nid' => $FilterNid);
								} else {
									$ooops[] = array('nid' => $FilterNid);
								}
							} elseif ($strNodeType == 'family') {
								$toSave->field_filter_macro[$i] = array('nid' => $FilterNid);
							} elseif ($strNodeType == 'macromodel') {
								$toSave->field_otherarticle[$i] = array('nid' => $FilterNid);
							}
							$i++;
						}
						//
						//just in case we missed some...
						if (!empty($ooops)) {
							$tempA = $toSave->field_filter_value_list;
							$tempB = array_merge($tempA, $ooops);
							$toSave->field_filter_value_list = $tempB;
						}
						$unsorted = $toSave->field_filter_value_list;
						ksort($unsorted);
						$toSave->field_filter_value_list = $unsorted;
					} else {
						if (($strNodeType == 'filterType' || $strNodeType == 'family') || ($strNodeType == 'macromodel' && $action == 'update')) {
							$msg = 'No association was found for the ' . $strNodeType . ' <em>' . $toSave->title . '</em> (<em>' . $lang . '</em>), ';
							$msg .= ($action == 'update') ? 'node not updated : nid[<em>' . $toSave->nid . '</em>].' : 'node not created.';
							$warningsA[] = array('line' => 0, 'message' => $msg);

							//continue;
						}
					}
				}
				//filter which technologies allready saved
				if ($strNodeType == 'filter') {
					if (isset($refToArray[$lang][$key])) {
						$tempTechnoNode = node_load($refToArray[$lang][$key][0]);
						$tempTechnoNode->field_filter_main = (array) $toSave->field_filter_main;
						$tempTechnoNode->field_filter_title = (array) $toSave->field_filter_title;

						$toSave = $tempTechnoNode;
					}
				}

				//save it

				save_mavic_node($toSave);

				$statusA[] = array('line' => 1, 'message' => 'Node saved : <em>' . $toSave->title . '</em> nid[' . $toSave->nid . '], .');







				//store nid for belongsTo
				if (!empty($belongsTo)) {
					if ($nodeType == 'prodvalcarac' || $strNodeType == 'filterType') {
						if ($strNodeType == 'filter') {
							
						}
						$belongsToAr = $belongToArray[$lang];
						$nidX = $toSave->nid;
						foreach ($belongsTo as $target) {

							if (!array_key_exists($target, $belongsToAr))
								$belongsToAr[$target] = array();
							$belongsToAr[$target][] = $nidX;
						}
						$belongToArray[$lang] = $belongsToAr;
					}
					if ($nodeType == 'article' && $action == 'update') {
						$nidToSave = $toSave->nid;
						$modelCodeB = $belongsTo['model_code'];

						$belongsToAr = $belongToArray[$lang];

						if (!array_key_exists($modelCodeB, $belongsToAr))
							$belongsToAr[$modelCodeB] = array();

						$belongsToArB = $belongsToAr[$modelCodeB];

						if ($belongsTo['def_color'] == 'default' && !empty($belongsToArB)) {


							array_unshift($belongsToArB, $nidToSave);
						} else {
							array_push($belongsToArB, $nidToSave);
						}
						$belongsToAr[$modelCodeB] = $belongsToArB;
						$belongToArray[$lang] = $belongsToAr;
					}
				}



				if ($associate && $action == 'create') {
					$NodesList[$lang]['update'][$key] = $toSave;
				}


				if ($lang == 'en') {
					//dispatch nid for tnid
					$temmpLang = array('en', 'fr', 'de', 'it', 'es', 'ja');
					foreach ($temmpLang as $tmpla) {
						$tradnode = $NodesList[$tmpla][$action][$key];
						$tradnode->tnid = $toSave->nid;
						$NodesList[$tmpla][$action][$key] = $tradnode;
					}
				}

				$a++;
			}
		}
	}
	//store created nodes fot next updated for next step
	$NodeTypesList[$nodeType] = $NodesList;
	mavicimport_ajaxsubmit_set_data(false, $sheet, $NodeTypesList);

	//inform the end user if some new warnings have been raised
	if (!empty($warningsA))
		$xlsx_file->morewarnings = true;

	//sum up

	$xlsx_file->xlsxReport[$sheet]['statusMsg'] = array_merge($xlsx_file->xlsxReport[$sheet]['statusMsg'], $statusA);
	$xlsx_file->xlsxReport[$sheet]['warnings'] = array_merge($xlsx_file->xlsxReport[$sheet]['warnings'], $warningsA);
	$xlsx_file->xlsxReport[$sheet]['errors'] = array_merge($xlsx_file->xlsxReport[$sheet]['errors'], $errorsA);

	$xlsx_file->xlsxReport['next']['refTo'] = $refToArray;
	$xlsx_file->xlsxReport['next']['belongsTo'] = $belongToArray;
	//reactivate the syncronise for the node type :
	variable_set($syncFieldsVar, $syncFields);
}
