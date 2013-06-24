<?php


global $parameters;

$parameters = array();

$parameters['excel_default'] = array(
            'LINELIST' => array(
                    'col_range' => 'N',
                    'max_row' => 370, 
                    'col_name' => array(
                        'A' => 'LINE',
                        'B' => 'FAMILY',
                        'C' => 'TAB',
                        'D' => 'ARTICLE_CODE',
                        'E' => 'MODEL_NAME_EN',
                        'F' => 'MODEL_CODE',
                        'G' => 'SEASON',
                        'H' => 'DEF_COLOR',
                        'I' => 'ASSOC_ARTICLE_1',
                        'J' => 'ASSOC_ARTICLE_2',
                        'K' => 'ASSOC_ARTICLE_3',
                        'L' => 'DEF_WEIGHT',
                        'M' => 'LANDSCAPE',
                        'N' => 'STATUS')
                    ),
            'LINELIST_TRANSLATION' => array(
                    'col_range' => 'F',
                    'max_row' => 244,
                    'col_name' => array(
                        'A' => 'en',
                        'B' => 'fr',
                        'C' => 'de',
                        'D' => 'it',
                        'E' => 'es',
                        'F' => 'ja')
                    ),
            'RANGE_FILTER' => array(
                    'col_range' => 'X',
                    'max_row' => 70,
                    'col_name' => array(
                        'A' => 'LINE',
                        'B' => 'FAMILY',
                        'C' => 'TAB',
                        'D' => 'FILTER_NAME',
                        'E' => 'VALUE',
                        'F' => 'FEATURE_ID')
                    ),
            'RANGE_FILTER_TRANSLATION' => array(
                    'col_range' => 'G',
                    'max_row' => 159,
                    'col_name' => array(
                        'A' => 'systemName',
                        'B' => 'en',
                        'C' => 'fr',
                        'D' => 'de',
                        'E' => 'it',
                        'F' => 'es',
                        'G' => 'ja')
                    ),
            'RANGE_RANK_LANDSCAPE' => array(
                    'col_range' => 'Y',
                    'max_row' => 19,
                    'col_name' => array(
                        'A' => 'LINE_ORDER',
                        'B' => 'LINE_SYSTEM',
                        'C' => 'LINE_en',
                        'D' => 'LINE_fr',
                        'E' => 'LINE_de',
                        'F' => 'LINE_it',
                        'G' => 'LINE_es',
                        'H' => 'LINE_ja',
                        'I' => 'CATEGORY_ORDER',
                        'J' => 'CATEGORY_SYSTEM',
                        'K' => 'CATEGORY_en',
                        'L' => 'CATEGORY_fr',
                        'M' => 'CATEGORY_de',
                        'N' => 'CATEGORY_it',
                        'O' => 'CATEGORY_es',
                        'P' => 'CATEGORY_ja',
                        'Q' => 'TAB_ORDER',
                        'R' => 'TAB_SYSTEM',
                        'S' => 'TAB_en',
                        'T' => 'TAB_fr',
                        'U' => 'TAB_de',
                        'V' => 'TAB_it',
                        'W' => 'TAB_es',
                        'X' => 'TAB_ja',
                        'Y' => 'LANDSCAPE')
                    ),
            'TECHNO_IMPORT' => array(
                    'col_range' => 'F',
                    'max_row' => 142,
                    'col_name' => array(
                        'A' => 'LINE',
                        'B' => 'SEASON',
                        'C' => 'CLASSIFICATION',
                        'E' => 'FEATURE_PARENT_CODE',
                        'F' => 'FEATURE_CODE') 
                    )
        );

        $AzAr = range('A','Z');
        $AzArR = array_flip($AzAr);
        $parameters_default = $parameters['excel_default'];
	$res = db_query("SELECT * FROM {mavicimport_settings}");
  $res = db_select('mavicimport_settings', 'ms')
    ->fields('ms')
    ->execute();
    
	foreach ($res as $settings){
            if (!empty($settings->system_name) && !empty($settings->settings)) {
                $parameters_default = unserialize($settings->settings);
            }
        }
	foreach ($parameters_default as $sheet => $sheetA){
            if(!isset($parameters['sheets'][$sheet])) {
                $parameters['sheets'][$sheet] = array();
            }
            $parameters['sheets'][$sheet]['col_range'] = (string) $sheetA['col_range'];
            
            $parameters['sheets'][$sheet]['max_row'] = (int) $sheetA['max_row'];
            $parameters['sheets'][$sheet]['col_name'] = array();
            foreach ($sheetA['col_name'] as $col_name_index => $col_name_str) {
                //$col_name_index_int = (int) array_keys($AzAr, $col_name_index, true);
                $parameters['sheets'][$sheet]['col_name'][$AzArR[$col_name_index]] = $col_name_str;
            }          
        }
      
//
// excel nb columns
//
$parameters['nb_xls_cols_shop'] = 28;
$parameters['nb_xls_cols_distributor'] = 28;
$parameters['nb_xls_cols_news'] = 12;
$parameters['max_rows'] = 1001; // maximum allowed rows in same time (cause of max execution time PHP)
$parameters['techno_trunc_length'] = 250;
ini_set('max_execution_time',960);
ini_set('memory_limit','1536M');
ini_set('upload_max_filesize','5M'); // upload XML

//
// landscape
// must match with the landscape list in CCK and name of jpg file
//
$parameters['landscape'] = array();
$parameters['landscape']['all'] = array(array('value'=>'track'),array('value'=>'road_aero'),array('value'=>'road_mountain'),array('value'=>'MTB_extreme'),array('value'=>'MTB_cross-country'),array('value'=>'MTB_all_mountain'));
$parameters['landscape']['company'] = $parameters['landscape']['all'];
$parameters['landscape']['road'] = array(array('value'=>'road_aero'),array('value'=>'road_mountain'));
$parameters['landscape']['triathlon'] = $parameters['landscape']['road'];
$parameters['landscape']['roadaero']  = array(array('value'=>'road_aero'));
$parameters['landscape']['roadmountain']  = array(array('value'=>'road_mountain'));
$parameters['landscape']['mtb'] = array(array('value'=>'MTB_extreme'),array('value'=>'MTB_cross-country'),array('value'=>'MTB_all_mountain'));
$parameters['landscape']['mtbextreme']  = array(array('value'=>'MTB_extreme'));
$parameters['landscape']['mtbcross-country']  = array(array('value'=>'MTB_cross-country'));
$parameters['landscape']['mtballmountain']  = array(array('value'=>'MTB_all_mountain'));
$parameters['landscape']['track'] = array(array('value'=>'track'));
$parameters['landscape']['road_aero'] = $parameters['landscape']['roadaero'];
$parameters['landscape']['road_mountain'] = $parameters['landscape']['roadmountain'];
$parameters['landscape']['mtb_extreme'] = $parameters['landscape']['mtbextreme'];
$parameters['landscape']['mtb_cross-country'] = $parameters['landscape']['mtbcross-country'];
$parameters['landscape']['mtb_all_mountain'] = $parameters['landscape']['mtballmountain'];



//
// lang // CAUTION : this parameter exceed the mavic_import module functionalities : 
//                  it is widely used accross all the mavic theme : do not modify it unless you know what you're doing !!
//
$parameters['langs'] = array('en','fr','de','it','es','ja');

//
// correspondance filiale prologue/language drupal (attention l ordre est important)
// prendre l'ordre des excel
//
$parameters['filiale'] = array();
$parameters['filiale']['en'] = 0;
$parameters['filiale']['fr'] = 1;
$parameters['filiale']['de'] = 10;
$parameters['filiale']['it'] = 7;
$parameters['filiale']['es'] = 4;
$parameters['filiale']['ja'] = 18;
$parameters['filiale'][0] = 'en';
$parameters['filiale'][1] = 'fr';
$parameters['filiale'][4] = 'es';
$parameters['filiale'][7] = 'it';
$parameters['filiale'][10] = 'de';
$parameters['filiale'][18] = 'ja';
$parameters['nb_Lang'] = 6; // number of language


//
// list of soft type (for technologies/feature)
//
$parameters['soft']['maa']=1;
$parameters['soft']['mez']=1;
$parameters['soft']['maf']=1;
$parameters['soft']['mag']=1;
$parameters['soft']['mhe']=1;

//
// list of size (apparel)
//
$parameters['size']['XXS']['en'] = 'XXXS';
$parameters['size']['XXS']['fr'] = 'XXS';
$parameters['size']['XXS']['de'] = 'XXXS';
$parameters['size']['XXS']['es'] = 'XXS';
$parameters['size']['XXS']['it'] = 'XXS';
$parameters['size']['XXS']['ja'] = 'XS';
$parameters['size']['XS']['en'] = 'XXS';
$parameters['size']['XS']['fr'] = 'XS';
$parameters['size']['XS']['de'] = 'XXS';
$parameters['size']['XS']['es'] = 'XS';
$parameters['size']['XS']['it'] = 'XS';
$parameters['size']['XS']['ja'] = 'S';
$parameters['size']['S']['en'] = 'XS';
$parameters['size']['S']['fr'] = 'S';
$parameters['size']['S']['de'] = 'XS';
$parameters['size']['S']['es'] = 'S';
$parameters['size']['S']['it'] = 'S';
$parameters['size']['S']['ja'] = 'M';
$parameters['size']['S/M']['en'] = 'XS/S';
$parameters['size']['S/M']['fr'] = 'S/M';
$parameters['size']['S/M']['de'] = 'XS/S';
$parameters['size']['S/M']['es'] = 'S/M/M';
$parameters['size']['S/M']['it'] = 'S/M/M';
$parameters['size']['S/M']['ja'] = 'M/L';
$parameters['size']['M']['en'] = 'S';
$parameters['size']['M']['fr'] = 'M';
$parameters['size']['M']['de'] = 'S';
$parameters['size']['M']['es'] = 'M/M';
$parameters['size']['M']['it'] = 'M/M';
$parameters['size']['M']['ja'] = 'L';
$parameters['size']['L']['en'] = 'M';
$parameters['size']['L']['fr'] = 'L';
$parameters['size']['L']['de'] = 'M';
$parameters['size']['L']['es'] = 'L';
$parameters['size']['L']['it'] = 'L';
$parameters['size']['L']['ja'] = 'O';
$parameters['size']['L/XL']['en'] = 'M/L';
$parameters['size']['L/XL']['fr'] = 'L/XL';
$parameters['size']['L/XL']['de'] = 'M/L';
$parameters['size']['L/XL']['es'] = 'L/XL';
$parameters['size']['L/XL']['it'] = 'L/XL';
$parameters['size']['L/XL']['ja'] = 'O/XO';
$parameters['size']['XL']['en'] = 'L';
$parameters['size']['XL']['fr'] = 'XL';
$parameters['size']['XL']['de'] = 'L';
$parameters['size']['XL']['es'] = 'XL';
$parameters['size']['XL']['it'] = 'XL';
$parameters['size']['XL']['ja'] = 'XO';
$parameters['size']['XXL']['en'] = 'XL';
$parameters['size']['XXL']['fr'] = 'XXL';
$parameters['size']['XXL']['de'] = 'XL';
$parameters['size']['XXL']['es'] = 'XXL';
$parameters['size']['XXL']['it'] = 'XXL';
$parameters['size']['XXL']['ja'] = 'XXO';
$parameters['size']['XXXL']['en'] = 'XXL';
$parameters['size']['XXXL']['fr'] = 'XXXL';
$parameters['size']['XXXL']['de'] = 'XXL';
$parameters['size']['XXXL']['es'] = 'XXXL';
$parameters['size']['XXXL']['it'] = 'XXXL';
$parameters['size']['XXXL']['ja'] = 'XXXO';

//
// list of size (footwear)
//
$parameters['size']['3.5']['en'] = '3.5';
$parameters['size']['3.5']['fr'] = '36'; 
$parameters['size']['3.5']['de'] = '36';
$parameters['size']['3.5']['es'] = '36';
$parameters['size']['3.5']['it'] = '36';
$parameters['size']['3.5']['ja'] = '22';
$parameters['size']['4']['en'] = '4';
$parameters['size']['4']['fr'] = '36 2/3';
$parameters['size']['4']['de'] = '36 2/3';
$parameters['size']['4']['es'] = '36 2/3';
$parameters['size']['4']['it'] = '36 2/3';
$parameters['size']['4']['ja'] = '22.5';
$parameters['size']['4.5']['en'] = '4.5';
$parameters['size']['4.5']['fr'] = '37 1/3';
$parameters['size']['4.5']['de'] = '37 1/3';
$parameters['size']['4.5']['es'] = '37 1/3';
$parameters['size']['4.5']['it'] = '37 1/3';
$parameters['size']['4.5']['ja'] = '23';
$parameters['size']['5']['en'] = '5';
$parameters['size']['5']['fr'] = '38';
$parameters['size']['5']['de'] = '38';
$parameters['size']['5']['es'] = '38';
$parameters['size']['5']['it'] = '38';
$parameters['size']['5']['ja'] = '23.5';
$parameters['size']['5.5']['en'] = '5.5';
$parameters['size']['5.5']['fr'] = '38 2/3';
$parameters['size']['5.5']['de'] = '38 2/3';
$parameters['size']['5.5']['es'] = '38 2/3';
$parameters['size']['5.5']['it'] = '38 2/3';
$parameters['size']['5.5']['ja'] = '24';
$parameters['size']['6']['en'] = '6';
$parameters['size']['6']['fr'] = '39 1/3';
$parameters['size']['6']['de'] = '39 1/3';
$parameters['size']['6']['es'] = '39 1/3';
$parameters['size']['6']['it'] = '39 1/3';
$parameters['size']['6']['ja'] = '24.5';
$parameters['size']['6.5']['en'] = '6.5';
$parameters['size']['6.5']['fr'] = '40';
$parameters['size']['6.5']['de'] = '40';
$parameters['size']['6.5']['es'] = '40';
$parameters['size']['6.5']['it'] = '40';
$parameters['size']['6.5']['ja'] = '25';
$parameters['size']['7']['en'] = '7';
$parameters['size']['7']['fr'] = '40 2/3';
$parameters['size']['7']['de'] = '40 2/3';
$parameters['size']['7']['es'] = '40 2/3';
$parameters['size']['7']['it'] = '40 2/3';
$parameters['size']['7']['ja'] = '25.5';
$parameters['size']['7.5']['en'] = '7.5';
$parameters['size']['7.5']['fr'] = '41 1/3';
$parameters['size']['7.5']['de'] = '41 1/3';
$parameters['size']['7.5']['es'] = '41 1/3';
$parameters['size']['7.5']['it'] = '41 1/3';
$parameters['size']['7.5']['ja'] = '26';
$parameters['size']['8']['en'] = '8';
$parameters['size']['8']['fr'] = '42';
$parameters['size']['8']['de'] = '42';
$parameters['size']['8']['es'] = '42';
$parameters['size']['8']['it'] = '42';
$parameters['size']['8']['ja'] = '26.5';
$parameters['size']['8.5']['en'] = '8.5';
$parameters['size']['8.5']['fr'] = '42 2/3';
$parameters['size']['8.5']['de'] = '42 2/3';
$parameters['size']['8.5']['es'] = '42 2/3';
$parameters['size']['8.5']['it'] = '42 2/3';
$parameters['size']['8.5']['ja'] = '27';
$parameters['size']['9']['en'] = '9';
$parameters['size']['9']['fr'] = '43 1/3';
$parameters['size']['9']['de'] = '43 1/3';
$parameters['size']['9']['es'] = '43 1/3';
$parameters['size']['9']['it'] = '43 1/3';
$parameters['size']['9']['ja'] = '27.5';
$parameters['size']['9.5']['en'] = '9.5';
$parameters['size']['9.5']['fr'] = '44';
$parameters['size']['9.5']['de'] = '44';
$parameters['size']['9.5']['es'] = '44';
$parameters['size']['9.5']['it'] = '44';
$parameters['size']['9.5']['ja'] = '28';
$parameters['size']['10']['en'] = '10';
$parameters['size']['10']['fr'] = '44 2/3';
$parameters['size']['10']['de'] = '44 2/3';
$parameters['size']['10']['es'] = '44 2/3';
$parameters['size']['10']['it'] = '44 2/3';
$parameters['size']['10']['ja'] = '28.5';
$parameters['size']['10.5']['en'] = '10.5';
$parameters['size']['10.5']['fr'] = '45 1/3';
$parameters['size']['10.5']['de'] = '45 1/3';
$parameters['size']['10.5']['es'] = '45 1/3';
$parameters['size']['10.5']['it'] = '45 1/3';
$parameters['size']['10.5']['ja'] = '29';
$parameters['size']['11']['en'] = '11';
$parameters['size']['11']['fr'] = '46';
$parameters['size']['11']['de'] = '46';
$parameters['size']['11']['es'] = '46';
$parameters['size']['11']['it'] = '46';
$parameters['size']['11']['ja'] = '29.5';
$parameters['size']['11.5']['en'] = '11.5';
$parameters['size']['11.5']['fr'] = '46 2/3';
$parameters['size']['11.5']['de'] = '46 2/3';
$parameters['size']['11.5']['es'] = '46 2/3';
$parameters['size']['11.5']['it'] = '46 2/3';
$parameters['size']['11.5']['ja'] = '30';
$parameters['size']['12']['en'] = '12';
$parameters['size']['12']['fr'] = '47 1/3';
$parameters['size']['12']['de'] = '47 1/3';
$parameters['size']['12']['es'] = '47 1/3';
$parameters['size']['12']['it'] = '47 1/3';
$parameters['size']['12']['ja'] = '30.5';
$parameters['size']['12.5']['en'] = '12.5';
$parameters['size']['12.5']['fr'] = '48';
$parameters['size']['12.5']['de'] = '48';
$parameters['size']['12.5']['es'] = '48';
$parameters['size']['12.5']['it'] = '48';
$parameters['size']['12.5']['ja'] = '31';
$parameters['size']['13']['en'] = '13';
$parameters['size']['13']['fr'] = '48 2/3';
$parameters['size']['13']['de'] = '48 2/3';
$parameters['size']['13']['es'] = '48 2/3';
$parameters['size']['13']['it'] = '48 2/3';
$parameters['size']['13']['ja'] = '31.5';

//
// wheels weight
// techdefnu / label
//
$parameters['techdefnu']['101'] = 'front wheel';
$parameters['techdefnu']['105'] = 'rear wheel M10';
$parameters['techdefnu']['106'] = 'rear wheel  ED11';
$parameters['techdefnu']['107'] = 'rear wheel Center Lock';
$parameters['techdefnu']['108'] = 'rear wheel 6 bolts';
$parameters['techdefnu']['112'] = 'rear wheel HG9';
$parameters['techdefnu']['113'] = 'front wheel 6 bolts';
$parameters['techdefnu']['114'] = 'front wheel Center Lock';
$parameters['techdefnu']['116'] = 'rear wheel';
$parameters['techdefnu']['140'] = 'pair of wheels';
$parameters['techdefnu']['145'] = 'pair of wheels with tyre - WTS';
$parameters['techdefnu']['146'] = 'front wheel with tyre - WTS';
$parameters['techdefnu']['147'] = 'rear wheel ED11 with tyre - WTS';
$parameters['techdefnu']['148'] = 'rear wheel M10 with tyre - WTS';
$parameters['techdefnu']['85'] = '700';
$parameters['techdefnu']['86'] = '26\' / 650';

//
//translation
//
$parameters['translate'][' grams (size ']['en']=' grams (size ';
$parameters['translate'][' grams (size ']['fr']=' grammes (taille ';
$parameters['translate'][' grams (size ']['de']=' gramm (Gr. ';
$parameters['translate'][' grams (size ']['it']=' grammi (taglia ';
$parameters['translate'][' grams (size ']['es']=' gramos (talla ';
$parameters['translate'][' grams (size ']['ja']=' グラム (サイズ ';
$parameters['translate'][' UK)']['en']=' UK)';
$parameters['translate'][' UK)']['fr']=')';
$parameters['translate'][' UK)']['de']=')';
$parameters['translate'][' UK)']['it']=')';
$parameters['translate'][' UK)']['es']=')';
$parameters['translate'][' UK)']['ja']=')';
$parameters['translate'][' grams / pedal']['en']=' grams / pedal';
$parameters['translate'][' grams / pedal']['fr']=' grammes / pédale';
$parameters['translate'][' grams / pedal']['de']=' Gramm / Pedal';
$parameters['translate'][' grams / pedal']['it']=' Grammi / pedale';
$parameters['translate'][' grams / pedal']['es']=' gramos / pedal';
$parameters['translate'][' grams / pedal']['ja']=' グラム/片側';
$parameters['translate'][' grams']['en']=' grams';
$parameters['translate'][' grams']['fr']=' grammes';
$parameters['translate'][' grams']['de']=' gramm';
$parameters['translate'][' grams']['it']=' grammi';
$parameters['translate'][' grams']['es']=' gramos';
$parameters['translate'][' grams']['ja']=' グラム';
