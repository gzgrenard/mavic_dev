<?php
			class mavicimport_ajaxsubmit_limitFilter implements PHPExcel_Reader_IReadFilter
			{
   				private $_sheetsLimit = array(); 

    			/**  Get the array listing the rows and columns to read according to sheets */ 
    			public function __construct($sheetsLimit) { 
        			$this->_sheetsLimit = $sheetsLimit; 
   				 } 
		
				public function readCell($column, $row, $worksheetName = '') {
		
					if (empty($worksheetName)) return true;
					$sheetsLimit = $this->_sheetsLimit;
					$colRange = range('A',$sheetsLimit[$worksheetName]['col_range']);
					$maxRow = $sheetsLimit[$worksheetName]['max_row'];
					if ($row > 1 && $row <= $maxRow) { 
        			  	if (in_array($column,$colRange)) { 
               				 return true; 
            			} 
        			} 
					return false;
				}
			}
