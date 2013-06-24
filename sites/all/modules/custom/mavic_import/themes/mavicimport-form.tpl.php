<?php
function bytesConvert($bytes) {
        $s = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB');
        $e = floor(log($bytes)/log(1024));
      
        return sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));

}
  $output = '<div class="messages error">Before using this import system, please make sure you have : <ul>';
  $output .= '<li>updated accordingly the <a href="mavicimport/config">settings</a>.</li>';
  $output .= '<li>read and understood the <a title="Download instructions" href="/en/mavicimport/download?file=mavic_import_instr_01.pdf">instructions</a>.</li>';
  $output .= '</ul></div>';
  
  $output .= '<div id="choice_upload"><input type="radio" name="choice_upload" value="xlsx" /><label>Import Linelist Excel file</label><br /><br /><input id="xml_field_show" type="radio" name="choice_upload" value="xml" checked="checked" /><label>Import Prologue extracted XML files</label></div>';
  $action = $form['#action'] ? 'action="' . check_url($form['#action']) . '" ' : ''; 
  $output .= '<form ' . $action . ' accept-charset="UTF-8" method="' . $form['#method'] . '" id="' . $form['#id'] . '"' . drupal_attributes($form['#attributes']) . '><div id="upload_fields">';
      
      $output .= '<div id="xlsx_field"><h3>XLSX file</h3><table>';
  //var_dump($list_xml_files);
   
      $output .= '<tr class="form-item xls-file"';

    $output .= ' id="' . $xls_file_upload['#id'] . '-wrapper"';

    $output .= '>';
    
    $xlstitle = $xls_file_upload['#title'];
    $output .= ' <td><label for="' . $xls_file_upload['#id'] . '"><a href="/en/mavicimport/download?file=' . $xlstitle . '" title="Download '. $xlstitle .'" >'. $xlstitle .'</a></label></td>';
  
    $output .= '<td><input type="file" name="'. $xls_file_upload['#name'] .'"'. ($xls_file_upload['#attributes'] ? ' '. drupal_attributes($xls_file_upload['#attributes']) : '') .' id="'. $xml_file['#id'] .'" size="'. $xls_file_upload['#size'] ."\" /></td>";
  
    $output .= ' <td class="description">' . $xls_file_upload['#description'] . '</td>';
          $xmlHeader = (count($list_xml_files) > 1) ? 'XML files' : 'XML file';    
    $output .= '</tr></table></div><div id="xml_fields"><h3>'. $xmlHeader .'</h3><div>'. theme_select($seasons) .'</div><table>';          
  $i = 0;   
  foreach ($list_xml_files as $xml_file) {
    $output .= '<tr class="form-item xml-files"';

    $output .= ' id="' . $xml_file['#id'] . '-wrapper"';

    $output .= '>';
    
    $title = $xml_file['#title'];
    
    $output .= ' <td><label for="' . $xml_file['#id'] . '">';
    
    if ($i) $output .= '<a href="/en/mavicimport/download?file=' . $title. '" title="Download '. $title .'" >';
    
    $output .= $title;

    if ($i) $output .= '</a>';
     
    $output .= '</label></td>';
  
    $output .= '<td><input type="file" name="'. $xml_file['#name'] .'"'. ($xml_file['#attributes'] ? ' '. drupal_attributes($xml_file['#attributes']) : '') .' id="'. $xml_file['#id'] .'" size="'. $xml_file['#size'] .'" /></td>';
  
    $output .= ' <td id="' . str_replace('-', '_', $xml_file['#id']) . '" class="description">' . $xml_file['#description'] . "</td>";
  
    $output .= '</tr>';
    
    $i++;
  }
 
  $output .= '</table></div>';
  $output .= '<div id="ajaxsubmit_report"></div>';  
  $output .= '<label>' . t('Ignore warnings') . '</label>';
  $output .= theme_checkbox($ignore_warnings);  
  $output .= '<label>' . t('Delete all features') . '</label>';
  $output .= theme_checkbox($delete_prodFeatures);
  $output .= '<div class="ajaxsubmit-message"></div>';
  $output .= '<div id="submit_button_wrapper">'. theme_submit($submit_xls_upload) .'</div>';  
  $output .= render($form);
  $output .= '<br /><span>*When submitting xml files, if the system does not find any error nor warning, it will try to import directly the data.</span>';
  $output .= '</div></form>'; 
  
   
  //uploaded XLSX - XML file report
  if (isset($xls_file_upload['#author']) ) {
        $output .= '<div id="uploaded_file_reports"><h2>Uploaded files report : </h2>';
        $output .= '<div id="'. $xls_file_upload['#fileshortname'] .'_warnings" class="admin-panel">';
        $output .= '<h3>'. $xls_file_upload['#title'] .'</h3>';
        $output .= '<table>';
        $output .= '<tr><td>Author</td><td><em>' . $xls_file_upload['#author'] . '</em></td></tr>';
        $output .= '<tr><td>Uploaded</td><td><em>' . date('D, d M Y H:i:s', $xls_file_upload['#upload']) . '</em></td></tr>';
        $output .= '<tr><td>Created</td><td><em>' . date('D, d M Y H:i:s', $xls_file_upload['#created']) . '</em></td></tr>';
        $output .= '<tr><td>Modified</td><td><em>' . date('D, d M Y H:i:s', $xls_file_upload['#modified']) . '</em></td></tr>';
        $output .= '<tr><td>Size</td><td><em>' . bytesConvert($xls_file_upload['#filesize']) . '</em></td></tr>';
        $output .= '</table>';
        foreach ($xls_file_upload['#warnings'] as $sheet => $sheetWarnings) {
            $output .= '<fieldset class="collapsible collapsed"><legend>Detailed report for '. $sheet . '</legend><table><tr><th>line</th><th>message</th></tr>';
            foreach ($sheetWarnings as $sheetWarning) {
                $output .= '<tr><td>' . $sheetWarning->line . '</td><td>' . $sheetWarning->message . '</td></tr>';
            }
            $output .= '</table></fieldset>';
        }
        $output .= '</div>';
  }
  $a = 0;
  if (isset($list_xml_files[1]['#upload'])  && $i) {
  
        foreach ($list_xml_files as $xml_file) {
            $a++;
            if ($a == 1) continue;
            $output .= '<div id="'. $xml_file['#fileshortname'] .'_warnings" class="admin-panel">';
            $output .= '<h3>'. $xml_file['#fileshortname'] .'</h3>';
            $output .= '<table>';
            $output .= '<tr><td>Uploaded</td><td><em>' . date('D, d M Y H:i:s', $xml_file['#upload']) . '</em></td></tr>';
            $output .= '<tr><td>Size</td><td><em>' . bytesConvert($xml_file['#filesize']) . '</em></td></tr>';
            $output .= '</table>';

            $output .= '<fieldset class="collapsible collapsed"><legend>Detailed report</legend><table><tr><th>line</th><th>message</th></tr>';
            foreach ($xml_file['#warnings'] as $sheetWarning) {
                $output .= '<tr><td>' . $sheetWarning->line . '</td><td>' . $sheetWarning->message . '</td></tr>';
            }
            $output .= '</table></fieldset>';

            $output .= '</div>';
            
            
        }

        $output .= '</div>';
  }

  //uploaded XML file report
print $output;
