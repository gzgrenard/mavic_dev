<?php
    /**
     * Retrieve landscape key for a node
     * @global array $parameters
     * @param int $nid
     * @return string 
     */
    function mavicimport_export_getLandscape($nid) {
        global $parameters;
        $landscapes = array();
        $landscapeQuery = db_query("	SELECT  n.`field_landscape_value`
                                        FROM {content_field_landscape} n
                                        WHERE n.nid = %d
                                        ORDER BY n.delta ASC", array($nid));
        while($result = db_fetch_array($landscapeQuery)) {
            $landscapes[] = $result['field_landscape_value'];
        }
        $landscapeV = 'all';
        foreach ($parameters['landscape'] as $landscapeName => $paramL) {
            $compare = array();
            foreach ($paramL as $paramC) {
                $compare[] = $paramC['value'];
            }
            $test = array_diff($compare, $landscapes);
            if (empty($test)) {
                return $landscapeName;
            }
        }
        return $landscapeV;       
    }
    
    /**
     * Recurcive function to feed the technologies sheet of $sheetlist
     * @param array $sheetsParam
     * @param array $sheetsList (reference)
     * @param int $nid
     * @param string $line
     * @param array $menu
     * @param string $classification
     * @param string $parent
     */
    function mavicimport_export_getTechno($sheetsParam, $sheetsList, $nid, $line, $menu = NULL, $classification = NULL, $parent = NULL) {
            $featureCode = array();
            $technoChild = array();
            $technoKOQuery = db_query("	SELECT n.`title`, n.`type`, c.`field_feature_codes_value`, d.`field_child_nid`, p.`field_feature_season_value`
                                        FROM {node} n
                                        LEFT JOIN {content_field_feature_codes} c using (nid)
                                        LEFT JOIN {content_field_child} d using (nid)
                                        LEFT JOIN {content_type_prodvalcarac} p using (nid)
                                        WHERE n.`nid` = %d", array($nid));
            while($result = db_fetch_array($technoKOQuery)) {
                $tTitle = $result['title'];
                $type = $result['type'];
                $season = $result['field_feature_season_value'];
                if (!in_array($result['field_feature_codes_value'], $featureCode) && !empty($result['field_feature_codes_value'])) $featureCode[] = $result['field_feature_codes_value'];
                if (!in_array($result['field_child_nid'], $technoChild) && !empty($result['field_child_nid'])) $technoChild[] = $result['field_child_nid'];
            }
            switch ($type) {
                case 'prodvalcarac' :
                    if (empty($technoChild)) {
                        $temp = array();
                        $temp[$sheetsParam['TECHNO_IMPORT']['LINE']] = $line;
                        $temp[$sheetsParam['TECHNO_IMPORT']['SEASON']] = $season;
                        $temp[$sheetsParam['TECHNO_IMPORT']['CLASSIFICATION']] = (empty($classification)) ? '' : $classification;
                        $temp[$sheetsParam['TECHNO_IMPORT']['FEATURE_PARENT_CODE']] = (empty($parent)) ? '' : implode(';', $parent);                      
                        $temp[$sheetsParam['TECHNO_IMPORT']['FEATURE_CODE']] = implode(';', $featureCode);
                        $sheetsList['TECHNO_IMPORT'][count($sheetsList['TECHNO_IMPORT'])+1] = $temp;
                    } else {
                        foreach($technoChild as $technoC){
                            mavicimport_export_getTechno($sheetsParam, &$sheetsList, $technoC, $line, '', $classification, $featureCode);
                        }
                    }
                    
                    break;
                case 'technocat' :
                    foreach ($menu['below'] as $techno) {
                        $technoNid = substr($techno['link']['href'],5);
                        mavicimport_export_getTechno($sheetsParam, &$sheetsList, $technoNid, $line, '', $tTitle);
                    }
                    break;
                default:
                    break;
            }

    }


    //main container which will feed the xlx writer obj
    $sheetsList = array();//$sheetsList[sheet][row_index_1_based][cell_index_0_based] = value;
    
    //Get correspondance excell column / value
    $sheetsParam = array();
    
    //populate $sheetsList and $sheetsParam
    foreach ($parameters['sheets'] as $sheet => $sparams) {
        $colCorU = $sparams['col_name'];
        $sheetsParam[$sheet] = array_flip($colCorU);
        $sheetsList[$sheet] = array();
    }
    
    //menu menu-primary-links-en as base to retrieve all data except techno
    $primary_links = menu_tree_all_data('menu-primary-links-en');
    
    $lineSysName = '';
    $familySysName = '';
    $tabSysName = '';
    $filtersTrad = array();

    $i = 0;//RANGE_RANK_LANDSCAPE row
    $f = 0;//RANGE_FILTER row
    $ft = 0;//RANGE_FILTER_TRANSLATION row
    $art = 0;//LINELIST row
    $mt = 0;//LINELIST_TRANSLATION row

    //line
    foreach ($primary_links as $line) {
        $lineNid = substr($line['link']['href'],5);
        $lineWeight = $line['link']['weight'];
        $exLine = array();
	$allQuery = db_query("	SELECT  n.`nid`, r.`body`, r.`title`, n.`language`
							FROM {node} n
                            INNER JOIN {node_revisions} r USING (nid)
							WHERE n.tnid = %d", array($lineNid));
	while($result = db_fetch_array($allQuery)) {
                $lineSysName = $result['body'];
		if (!isset($exLine[$result['language']])) $exLine[$result['language']] = $result['title'];
	}
        
        //family
        foreach ($line['below'] as $family){
            if ($family['link']['module'] != 'nodesymlinks') {
                $familyNid = substr($family['link']['href'],5);
                $familyWeight = $family['link']['weight'];
                $exFamily = array();
                $allQuery = db_query("	SELECT  n.`nid`, r.`body`, r.`title`, n.`language`
                                                                FROM {node} n
                                                                INNER JOIN {node_revisions} r USING (nid)
                                                                WHERE n.tnid = %d", array($familyNid));
                while($result = db_fetch_array($allQuery)) {
                    $familySysName = explode('/', $result['body'],2);
                    if (!isset($exFamily[$result['language']])) $exFamily[$result['language']] = $result['title'];
                }

                //tab
                foreach ($family['below'] as $tab){
                    if ($tab['link']['module'] != 'nodesymlinks') {
                        $tabNid = substr($tab['link']['href'],5);
                        $tabWeight = $tab['link']['weight'];
                        $exTab = array();
                        $allQuery = db_query("	SELECT  n.`nid`, r.`body`, r.`title`, n.`language`
                                                                       FROM {node} n
                                                                       INNER JOIN {node_revisions} r USING (nid)
                                                                       WHERE n.tnid = %d", array($tabNid));
                        while($result = db_fetch_array($allQuery)) {
                            $tabPath = $result['body'];
                            $tabSysNameT  = explode('/', $tabPath);
                            $tabSysName = array_pop($tabSysNameT);
                            if (!isset($exTab[$result['language']])) $exTab[$result['language']] = $result['title'];
                        }

                        //tab landscape
                        $landscapeV = mavicimport_export_getLandscape($tabNid);
                        //RANGE_RANK_LANDSCAPE row
                        $i++;
                        if (!isset($sheetsList['RANGE_RANK_LANDSCAPE'][$i])) $sheetsList['RANGE_RANK_LANDSCAPE'][$i] = array();
                        $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['LINE_ORDER']] = $lineWeight;
                         $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['LINE_SYSTEM']] = $lineSysName;
                        foreach ($exLine as $langKey => $lineTrad) {
                            $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['LINE_'.$langKey]] = $lineTrad;
                        }
                        $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['CATEGORY_ORDER']] = $familyWeight;
                        $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['CATEGORY_SYSTEM']] = $familySysName[1];
                        foreach ($exFamily as $langKey => $familyTrad) {
                            $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['CATEGORY_'.$langKey]] = $familyTrad;
                        }
                        $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['TAB_ORDER']] = $tabWeight;
                        $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['TAB_SYSTEM']] = $tabSysName;
                        foreach ($exTab as $langKey => $tabTrad) {
                            $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['TAB_'.$langKey]] = $tabTrad;
                        }               
                        $sheetsList['RANGE_RANK_LANDSCAPE'][$i][$sheetsParam['RANGE_RANK_LANDSCAPE']['LANDSCAPE']] = $landscapeV;

                        //tab Filters
                        $filtertype = array();
                        //retrieve filter type nid en
                        $filterQuery = db_query("   SELECT  n.`field_filter_macro_nid`
                                                    FROM {content_field_filter_macro} n
                                                    WHERE n.nid = %d
                                                    ORDER BY n.delta ASC", array($tabNid));
                        while($result = db_fetch_array($filterQuery)) {
                            $filtertype[] = $result['field_filter_macro_nid'];
                        }
                        $filters = array();
                        $filterName = '';
                        foreach ($filtertype as $filtertypeNid) {

                            if (!empty($filtertypeNid)){
                                //retrieve filter type node en + filter nid
                                $filterQuery = db_query("	SELECT r.`body`, c.`field_filter_value_list_nid`, n.`title` 
                                                                FROM {node} n
                                                                INNER JOIN {node_revisions} r USING (nid)
                                                                INNER JOIN {content_field_filter_value_list} c USING (nid)
                                                                WHERE n.nid = %d
                                                                ORDER BY c.delta ASC", array($filtertypeNid));
                                $filtersNids = array();
                                while($result = db_fetch_array($filterQuery)) {
                                    $filterName = str_replace($tabPath.'/', '', $result['body']);
                                    $filtersNids[] = $result['field_filter_value_list_nid'];
                                }
                                //retrieve filter type node all language
                                $filterTypeTradQuery = db_query("   SELECT n.`title`, n.`language` 
                                                                    FROM {node} n
                                                                    WHERE n.tnid = %d", array($filtertypeNid));
                                while($result = db_fetch_array($filterTypeTradQuery)) {
                                    if (!isset($filtersTrad[$filterName])) $filtersTrad[$filterName] = array();
                                    $filtersTrad[$filterName][$result['language']] = $result['title'];
                                }

                                $filterFeatureCode = array();
                                foreach($filtersNids as $filtersNid){
                                    $filterTitle = '';
                                    //retrive filter trad
                                    $filterTradQuery = db_query("   SELECT p.`field_filter_title_value`, n.`language` 
                                                                    FROM {node} n
                                                                    INNER JOIN {content_type_prodvalcarac} p USING (nid)
                                                                    WHERE n.tnid = %d", array($filtersNid));
                                     $ftd = 1;
                                     $tempT = array();
                                     while($result = db_fetch_array($filterTradQuery)) {
                                         $tempT[$result['language']] = $result['field_filter_title_value'];
                                     }
                                     $filterTitle = $tempT['en'];
                                     $tradSet = true;
                                     foreach ($tempT as $langF => $tradF) {
                                         if (!isset($filtersTrad[$filterTitle])) $filtersTrad[$filterTitle] = array();
                                         //if the system name (norm. en) is identical but the translations' set is not, duplicate and rename it before populating
                                         if ((isset($filtersTrad[$filterTitle][$langF])) && ($filtersTrad[$filterTitle][$langF] != $tradF) && $tradSet) {
                                             $temp = $filtersTrad[$filterTitle];
                                             $filterTitle = $filterTitle . '_' . $ftd;
                                             $ftd++;
                                             $tradSet = false;
                                             $filtersTrad[$filterTitle] = $temp;
                                         }
                                         $filtersTrad[$filterTitle][$langF] = $tradF;
                                     }
                                    //retrieve filter name + feature codes                                
                                    $filterDetailQuery = db_query("	SELECT n.`nid`, c.`field_feature_codes_value`
                                                                        FROM {node} n 
                                                                        INNER JOIN {content_field_feature_codes} c USING (nid)
                                                                        WHERE n.nid = %d", array($filtersNid));                            
                                    while($resultF = db_fetch_array($filterDetailQuery)) {
                                        if (!isset($filterFeatureCode[$filterTitle])) $filterFeatureCode[$filterTitle] = array();
                                        $filterFeatureCode[$filterTitle][] = $resultF['field_feature_codes_value'];
                                    }
                                }
                            }

                            //RANGE_FILTER row
                            $f++;
                            if (!isset($sheetsList['RANGE_FILTER'][$f])) $sheetsList['RANGE_FILTER'][$f] = array();
                            $sheetsList['RANGE_FILTER'][$f][$sheetsParam['RANGE_FILTER']['LINE']] = $lineSysName;;
                            $sheetsList['RANGE_FILTER'][$f][$sheetsParam['RANGE_FILTER']['FAMILY']] = $familySysName[1];
                            $sheetsList['RANGE_FILTER'][$f][$sheetsParam['RANGE_FILTER']['TAB']] = $tabSysName;
                            if (!empty($filterName)) {
                                $sheetsList['RANGE_FILTER'][$f][$sheetsParam['RANGE_FILTER']['FILTER_NAME']] = $filterName;
                                foreach ($filterFeatureCode as $value => $features) {
                                    $sheetsList['RANGE_FILTER'][$f][] = $value;
                                    $sheetsList['RANGE_FILTER'][$f][] = implode(';', $features);
                                }
                            }
                        }
                        //LINELIST and LINELIST_TRANSLATION
                        foreach($tab['below'] as $macromodel){
                            $mt++;
                            $macroNid = substr($macromodel['link']['href'],5);
                            $landscape = mavicimport_export_getLandscape($macroNid);
                            $macroQuery = db_query("	SELECT c.`field_modelco_value`, n.`title`, c.`field_macro_season_value`, c.`field_new_product_value`, c.`field_default_weight_value`
							FROM {node} n 
							INNER JOIN {content_type_macromodel} c using (nid) 
							WHERE n.`nid` = %d", array($macroNid));
                            while($resultM = db_fetch_array($macroQuery)) {
                                $macroTitle = $resultM['title'];
                                $macroCode = $resultM['field_modelco_value'];
                                $macroNew = ($resultM['field_new_product_value']) ? 'new' : 'online';
                                $macroDefWeight = (empty($resultM['field_default_weight_value'])) ? '0' : $resultM['field_default_weight_value'];
                            }
                            if (!isset($sheetsList['LINELIST_TRANSLATION'][$mt])) $sheetsList['LINELIST_TRANSLATION'][$mt] = array();
                            $macroTradQuery = db_query("    SELECT n.`title`, n.`language`
                                                            FROM {node} n 
                                                            WHERE n.`tnid` = %d", array($macroNid));
                            while($resultT = db_fetch_array($macroTradQuery)) {
                                $sheetsList['LINELIST_TRANSLATION'][$mt][$sheetsParam['LINELIST_TRANSLATION'][$resultT['language']]] = $resultT['title'];
                            }
                            $articlesNid = array();
                            $articleQuery = db_query("	SELECT  n.`field_otherarticle_nid`
                                                        FROM {content_field_otherarticle} n
                                                        WHERE n.nid = %d
                                                        ORDER BY n.delta ASC", array($macroNid));
                            while($resultA = db_fetch_array($articleQuery)) {
                                $articlesNid[] = $resultA['field_otherarticle_nid'];
                            }
                            foreach ($articlesNid as $isDefault => $article) {
                                 $color = ($isDefault == 0) ? 'default' : 'color';
                                 $associated = array();
                                 $articleCodeQuery = db_query("	SELECT n.`title`, c.`field_season_value`, a.`field_associated_nid`
                                                                FROM {node} n 
                                                                INNER JOIN {content_type_article} c using (nid)
                                                                INNER JOIN {content_field_associated} a using (nid)
                                                                WHERE n.`nid` = %d
                                                                ORDER BY a.delta ASC", array($article));
                                 while($resultB = db_fetch_array($articleCodeQuery)) {                                  
                                    $season = $resultB['field_season_value'];
                                    $artCode = $resultB['title'];
                                    $associated[] = $resultB['field_associated_nid'];
                                }
                                $assocCode = array();
                                if (!empty($associated) || ($associated[0] != 'NULL')) {
                                    for ($a=0;$a<3;$a++) {
                                        if (!empty($associated[$a]) || ($associated[$a] != 'NULL')) {
                                            $assocQuery = db_query("	SELECT n.`title`
                                                                        FROM {node} n 
                                                                        WHERE n.`nid` = %d", array($associated[$a]));
                                            while($resultC = db_fetch_array($assocQuery)) {
                                                $assocCode[] = $resultC['title'];
                                            }
                                        }
                                    }
                                }
                                $art++;
                                if (!isset($sheetsList['LINELIST'][$art])) $sheetsList['LINELIST'][$art] = array();
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['LINE']] = $lineSysName;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['FAMILY']] = $familySysName[1];
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['TAB']] = $tabSysName;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['ARTICLE_CODE']] = $artCode;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['MODEL_NAME_EN']] = $macroTitle;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['MODEL_CODE']] = $macroCode;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['SEASON']] = $season;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['DEF_COLOR']] = $color;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['ASSOC_ARTICLE_1']] = (empty($assocCode[0])) ? '' : $assocCode[0];
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['ASSOC_ARTICLE_2']] = (empty($assocCode[1])) ? '' : $assocCode[1];
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['ASSOC_ARTICLE_3']] = (empty($assocCode[2])) ? '' : $assocCode[2];
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['DEF_WEIGHT']] = $macroDefWeight;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['LANDSCAPE']] = $landscape;
                                $sheetsList['LINELIST'][$art][$sheetsParam['LINELIST']['STATUS']] = $macroNew;                                
                            }
                        }
                    }                
                }//end tab
            }                 
        }//end family
    }//end line

    unset($primary_links);
   
    //RANGE_FILTER_TRANSLATION row
    foreach ($filtersTrad as $sysName => $trads) {
        $ft++;
        if (!isset($sheetsList['RANGE_FILTER_TRANSLATION'][$ft])) $sheetsList['RANGE_FILTER_TRANSLATION'][$ft] = array();
        $sheetsList['RANGE_FILTER_TRANSLATION'][$ft][$sheetsParam['RANGE_FILTER_TRANSLATION']['systemName']] = $sysName;
        foreach ($trads as $langK => $trad) {
            $sheetsList['RANGE_FILTER_TRANSLATION'][$ft][$sheetsParam['RANGE_FILTER_TRANSLATION'][$langK]] = $trad;
        }       
    }

    //TECHNO_IMPORT row
    $techno_links = menu_tree_all_data('menu-menu-technologies-en');
    foreach ($techno_links as $technoLine) {
        $technoLineName = $technoLine['link']['link_title'];
        foreach ($technoLine['below'] as $technoKindOf) {
            $technoKONid = substr($technoKindOf['link']['href'],5);
            mavicimport_export_getTechno($sheetsParam, &$sheetsList, $technoKONid, $technoLineName, $technoKindOf);
        }
    }
    unset($techno_links);
    
    // Excell
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Mavic Linelist Export System");
    $objPHPExcel->getProperties()->setLastModifiedBy("Mavic Linelist Export System");
    $objPHPExcel->getProperties()->setTitle("Mavic Linelist Export");
    $objPHPExcel->getProperties()->setSubject("Mavic Linelist Export");
    $objPHPExcel->getProperties()->setDescription("Mavic Linelist, filters, technologies and associated translations' data");
          
    $sh = 0;
    foreach ($sheetsList as $sheetName => $sheetData) {
        $sh++;
        if ($sh == 1){
            $objWorksheet = $objPHPExcel->getActiveSheet();
        } else {
            $objWorksheet = $objPHPExcel->createSheet();
        }        
        $objWorksheet->setTitle($sheetName);
        foreach ($sheetData as $row => $line) {
            foreach ($line as $cell => $data) {
                if ($row == 1) {
                    $objWorksheet->getCellByColumnAndRow($cell, $row)->setValue($parameters['sheets'][$sheetName]['col_name'][$cell]);
                }
                $objWorksheet->getCellByColumnAndRow($cell, $row + 1)->setValue($data);
            }
        }
    }

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->setPreCalculateFormulas(false);
    $docName = 'mavic_com_linelist_export_' . date('ymd') . '.xlsx';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'. $docName .'"');
    header('Cache-Control: max-age=0');  
    $objWriter->save('php://output');
