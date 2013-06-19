<?php

require_once('phpexcel/PHPExcel.php');

//
// HTTP excel header
//
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="unlocated_shop.xls"');
header('Cache-Control: max-age=0');

//
// Excel init
//
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("blue-infinity")
							 ->setLastModifiedBy("blue-infinity")
							 ->setTitle("Mavic web site datas");
$xlsSheet = $objPHPExcel->setActiveSheetIndex(0)->setTitle('unlocated_shop');

							 
//
// cvs header
//
/*
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="unlocated_shop.csv"');
*/
$xlsSheet->setCellValue('A1', 'id');
$xlsSheet->setCellValue('B1', 'City');
$xlsSheet->setCellValue('C1', 'Country');
$xlsSheet->setCellValue('D1', 'Dealer_address');
$xlsSheet->setCellValue('E1', 'email');
$xlsSheet->setCellValue('F1', 'Fax');
$xlsSheet->setCellValue('G1', 'Phone');
$xlsSheet->setCellValue('H1', 'Shop_name');
$xlsSheet->setCellValue('I1', 'State');
$xlsSheet->setCellValue('J1', 'Website');
$xlsSheet->setCellValue('K1', 'Zip');
$xlsSheet->setCellValue('L1', 'mp3');
$xlsSheet->setCellValue('M1', 'expert_plus');
$xlsSheet->setCellValue('N1', 'maviclab');
$xlsSheet->setCellValue('O1', 'premium shop');
$xlsSheet->setCellValue('P1', 'wheels');
$xlsSheet->setCellValue('Q1', 'rims');
$xlsSheet->setCellValue('R1', 'tyres');
$xlsSheet->setCellValue('S1', 'computers');
$xlsSheet->setCellValue('T1', 'pedals');
$xlsSheet->setCellValue('U1', 'footwear');
$xlsSheet->setCellValue('V1', 'apparel');
$xlsSheet->setCellValue('W1', 'accessories');
$xlsSheet->setCellValue('X1', 'Test Center');
$xlsSheet->setCellValue('Y1', 'Filtre 2');
$xlsSheet->setCellValue('Z1', 'Filtre 3');
$xlsSheet->setCellValue('AA1', 'Filtre 4');


$special_list = array(); // list of special tabs allready displayed (like accessories)
$nb_line = 2; // excel file line

//
// foreach line
//

$view_result = views_get_view_result('listshop','page_1');

foreach ($view_result as $line) {
	$countrycode = $line->location_country;
	$countryname = location_country_name($countrycode);
	$mp3 = ($line->node_data_field_mp3_field_mp3_value == 0)?'-1':'1';
	$dealer = ($line->node_data_field_tech_dealer_field_tech_dealer_value == 0)?'-1':'1';
	$lab = ($line->node_data_field_mavic_lab_field_mavic_lab_value == 0)?'-1':'1';
	$premium = ($line->node_data_field_premium_field_premium_value == 'mavic_yellow')?'1':'-1';
	$wheels = ($line->node_data_field_wheels_field_wheels_value == 0)?'-1':'1';
	$rims = ($line->node_data_field_rims_field_rims_value == 0)?'-1':'1';
	$tyres = ($line->node_data_field_tyres_field_tyres_value == 0)?'-1':'1';
	$computers = ($line->node_data_field_computers_field_computers_value == 0)?'-1':'1';
	$pedals = ($line->node_data_field_pedals_field_pedals_value == 0)?'-1':'1';
	$footwear = ($line->node_data_field_footwear_field_footwear_value == 0)?'-1':'1';
	$apparel = ($line->node_data_field_apparel_field_apparel_value == 0)?'-1':'1';
	$accessories = ($line->node_data_field_accessories_field_accessories_value == 0)?'-1':'1';
	$fun = ($line->node_data_field_filtre_un_field_filtre_un_value == 0)?'-1':'1';
	$fdeux = ($line->node_data_field_filtre_deux_field_filtre_deux_value == 0)?'-1':'1';
	$ftrois = ($line->node_data_field_filtre_trois_field_filtre_trois_value == 0)?'-1':'1';
	$fquatre = ($line->node_data_field_filtre_quatre_field_filtre_quatre_value == 0)?'-1':'1';

	$xlsSheet->setCellValueExplicit('A'.$nb_line, $line->node_title, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('B'.$nb_line, $line->location_city, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('C'.$nb_line, $countryname, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('D'.$nb_line, $line->location_street, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('E'.$nb_line, $line->node_data_field_email_field_email_email, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('F'.$nb_line, $line->location_fax_fax, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('G'.$nb_line, $line->location_phone_phone, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('H'.$nb_line, $line->node_revisions_body, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('I'.$nb_line, $line->location_province, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('J'.$nb_line, $line->node_data_field_website_field_website_value, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('K'.$nb_line, $line->location_postal_code, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('L'.$nb_line, $mp3, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('M'.$nb_line, $dealer, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('N'.$nb_line, $lab, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('O'.$nb_line, $premium, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('P'.$nb_line, $wheels, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('Q'.$nb_line, $rims, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('R'.$nb_line, $tyres, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('S'.$nb_line, $computers, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('T'.$nb_line, $pedals, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('U'.$nb_line, $footwear, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('V'.$nb_line, $apparel, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('W'.$nb_line, $accessories, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('X'.$nb_line, $fun, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('Y'.$nb_line, $fdeux, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('Z'.$nb_line, $ftrois, PHPExcel_Cell_DataType::TYPE_STRING);
	$xlsSheet->setCellValueExplicit('AA'.$nb_line, $fquatre, PHPExcel_Cell_DataType::TYPE_STRING);

	$nb_line++;
} // foreach line

//
// write xls on output
//
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');