<?php 
	global $listFeature;
	if (!isset($field_technologie[0]['value']) || $field_technologie[0]['value']<4){
		$rightImgFeature = 0;
		if (count($field_feature_codes) > 1){
			foreach($field_feature_codes as $featureCodeValue){
				if(file_exists($features_path_sys.'/zoom/'.$featureCodeValue['value'].'_1.jpg')  || file_exists($features_path_sys.'/zoom/'.$featureCodeValue['value'].'_1_black.jpg')) { 
					break;
				}
				$rightImgFeature++;
			}
		}
		$listFeature[] = array('type'=>$field_type[0]['value'], 'img'=>$field_feature_codes[$rightImgFeature]['value'], 'feature'=>$title);
	};