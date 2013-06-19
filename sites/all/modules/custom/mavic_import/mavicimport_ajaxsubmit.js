/**
 * IE Patch : Submit event not load on submit button
*/
$('document').ready(function() {
    $('#xml_field_show').trigger('click');
})

Drupal.ajaxSubmit = Drupal.ajaxSubmit || {};

/**
 * Attaches the initial ajaxsubmit behaviour to forms.
 */
Drupal.behaviors.ajaxSubmit = {
    attach: function (context) {
        $('#edit-ignore-warnings', context).change(function () {
            Drupal.ajaxSubmit.enable_submit();
        });
        $('#edit-ignore-warnings-wrapper').hide();
        $('#edit-delete-prodFeatures').click(function () {
            if (this.checked){
                //alert('Warning : you are about to delete all features : once deleted, those nodes will not be recoverable and you will need to import all necessary xml !');
                 Drupal.t('Warning : you are about to delete all features : once deleted, those nodes will not be recoverable and you will need to import all necessary xml !');
            }
                
        });
        $('#edit-delete-prodFeatures-wrapper').hide();
        $('#choice_upload input').click(function(){
            if ($('#xml_field_show').is(':checked')) {
                $('#xml_fields').show();
                $('#xlsx_field').hide();
            } else {
                $('#xml_fields').hide();
                $('#xlsx_field').show();
            }
        });
            
        $('form.ajaxsubmit:not(.ajaxsubmit-processing)').each(function () {
            var form = $(this);
            var options = Drupal.ajaxSubmit.addOptions();
            $(this)
            .addClass('ajaxsubmit-processing')
            .ajaxForm(options)
            .find('input[name=ajaxsubmit]')
            .attr('value', '1')
            .end()
            .find('input.form-submit');
        });
   };
};

/**
 * Handler for displaying current process.
 */
Drupal.ajaxSubmit.beforeSubmit = function (stepMsg) {
    if ($('#step_msg').length != 1){
        $('<span></span>').attr('id','step_msg').appendTo('.ajaxsubmit-message');
    }
    if (!stepMsg){
        var stepMsg = "Submitting data...";
    }
    $('form :input').removeClass('error');
    $('#step_msg').html(stepMsg);
    $('#edit-submit-xls-upload').addClass('throbbing').attr('disabled','disabled');
};

/**
 * Handler for successful response.
 */
Drupal.ajaxSubmit.success = function (data) {
    var thedata = JSON.parse(data);
    if (thedata.message){
        for (var name in thedata.message) {
            switch (name) {
                case 'status' :
                    for (var status in thedata.message.status){
                        $('<p></p>').addClass('status-msg').html(thedata.message.status[status]).appendTo('.ajaxsubmit-message');
                    }
                    break;
                case 'warning' :
                    for (var warning in thedata.message.warning){
                        $('<p></p>').addClass('warning-msg').html(thedata.message.warning[warning]).appendTo('.ajaxsubmit-message');
                    }
                    break;
                case 'error' :
                    for (var error in thedata.message.error){
                        $('<p></p>').addClass('error-msg').html(thedata.message.error[error]).appendTo('.ajaxsubmit-message');
                    }
                    break;
		
            }
        }
    }
   
    if (thedata.files != null && thedata.step == null){
	   
        for (var name in thedata.files) {
            if (thedata.files.hasOwnProperty(name)) {
                //add link to file upload description
                if (thedata.files[name].source != null) {
                    $('input[name="files[' + thedata.files[name].source + ']"]').attr('disabled', 'disabled');
                    $('#edit_' + thedata.files[name].source).html('Check <a href="#' + name + '">file report</a> below');
                }
			   
                //file bloc report
                if ($('#' + name).length != 0){
                    $('#' + name).remove();
                }
                var fileErrorSct = $('<div></div>')
                .attr('id',name)
                .addClass('admin-panel')
                .appendTo('#ajaxsubmit_report');
                $('<h2></h2>').html(thedata.files[name].filename).appendTo(fileErrorSct);
			   	
                if (thedata.files[name].xlsxReport != null && thedata.files[name].xlsxReport.length != 0) {
                    //file status
                    for (var sheet in thedata.files[name].xlsxReport) {
                        if (sheet != "next") {
                            var sheetBloc = $('<div></div>')
                            .attr('id','sheetBloc_' + sheet)
                            .addClass('')
                            .appendTo(fileErrorSct);
                            $('<h3></h3>').html(sheet).appendTo(sheetBloc);

                            var statusBloc = $('<div></div>')
                            .attr('id','status_' + sheet)
                            .addClass('file_status')
                            .appendTo(sheetBloc);
                            var statusFieldset = $('<fieldset></fieldset>').attr('id','status_Fieldset_' + sheet).addClass('collapsible collapsed').appendTo(statusBloc);
                            $('<legend></legend>').html('status').appendTo(statusFieldset);
                            if (thedata.files[name].xlsxReport[sheet].statusMsg != null && thedata.files[name].xlsxReport[sheet].statusMsg.length != 0) {
                                var xma = new Array(), x = -1;
                                for (var stsmsg in thedata.files[name].xlsxReport[sheet].statusMsg) {
                                    xma[++x] = '<li>';
                                    xma[++x] = thedata.files[name].xlsxReport[sheet].statusMsg[stsmsg].message;
                                    xma[++x] = '<li>';
                                }
                                $('<ul>'+xma.join('')+'</ul>').attr('id','statusMsg_' + sheet).appendTo(statusFieldset);
                            }

                            //build warnings table
                            if (thedata.files[name].xlsxReport[sheet].warnings && thedata.files[name].xlsxReport[sheet].warnings.length != 0) {
                                Drupal.ajaxSubmit.buildTable(thedata.files[name].xlsxReport[sheet].warnings, 'warnings', sheetBloc);
                            }

                            //build errors table
                            if (thedata.files[name].xlsxReport[sheet].errors && thedata.files[name].xlsxReport[sheet].errors.length != 0) {
                                Drupal.ajaxSubmit.buildTable(thedata.files[name].xlsxReport[sheet].errors, 'errors', sheetBloc);
                            }
                        }
                    }
                } else {
                    //xml
                    //file status
                    var statusBloc = $('<div></div>')
                    .attr('id','status_' + name)
                    .addClass('file_status')
                    .appendTo(fileErrorSct);
                    var statusFieldset = $('<fieldset></fieldset>').attr('id','status_Fieldset_' + name).addClass('collapsible collapsed').appendTo(statusBloc);
                    $('<legend></legend>').html('status').appendTo(statusFieldset);
                    if (thedata.files[name].statusMsg != null && thedata.files[name].statusMsg.length != 0) {
                        
                        var sma = new Array(), s = -1;
                        for (var stsmsg in thedata.files[name].statusMsg) {
                            sma[++s] = '<li>';
                            sma[++s] = thedata.files[name].statusMsg[stsmsg].message;
                            sma[++s] = '<li>';
                        }
                        $('<ul>'+sma.join('')+'</ul>').attr('id','statusMsg_' + name).appendTo(statusFieldset);
                    }
				   
                    //build warnings table
                    if (thedata.files[name].warnings && thedata.files[name].warnings.length != 0) {
                        Drupal.ajaxSubmit.buildTable(thedata.files[name].warnings, 'warnings', fileErrorSct);
                    }
				   
                    //build errors table
                    if (thedata.files[name].errors && thedata.files[name].errors.length != 0) {
                        Drupal.ajaxSubmit.buildTable(thedata.files[name].errors, 'errors', fileErrorSct);
                    }
                }
                //re-attach drupal js behavior
                Drupal.attachBehaviors(fileErrorSct);
            }
        }
    }
    // Invoke any callback functions.

    if (thedata.callbacks != null && thedata.step != null) {
        Drupal.ajaxSubmit.auto_submit(thedata.callbacks);
    } else {
        $('#step_msg').remove();
        $('form input.throbbing').removeClass('throbbing');
  
    }
    if (thedata.callbacks != null && thedata.confirm != null) {
        Drupal.ajaxSubmit.confirm_process(thedata);
    }
    if (thedata.thisIsTheEnd) {
        Drupal.ajaxSubmit.end_process();
    }
    // Redirect.
    if (data.destination) {
        window.location = Drupal.url(data.destination);
    }
  
};


/**
* Handler for error.
*/
Drupal.ajaxSubmit.error = function () {
    $('input.form-submit').removeClass('throbbing');
};
/**
* Handler for enabling manual submit
*/
Drupal.ajaxSubmit.enable_submit = function (){
    if ($('#edit-ignore-warnings').is(':checked')){
        $('#edit-submit-xls-upload').removeAttr('disabled');
    } else {
        $('#edit-submit-xls-upload').attr('disabled','disabled');

    }
};
/**
* Handler for submitting next step
*/
Drupal.ajaxSubmit.auto_submit = function (data){
    var options = Drupal.ajaxSubmit.addOptions(data);
    //console.log('autosubmiting');
    //console.log(options);
    $('form.ajaxsubmit').ajaxForm(options).submit();
};
/**
* Handler for setting the confirm process
*/
Drupal.ajaxSubmit.confirm_process = function (data){
    $('#edit-ignore-warnings-wrapper').show();
                
    for (var name in data.confirm) {
				
        var btn_label = 'continue';
        if (data.confirm.hasOwnProperty(name)) {
            switch (data.confirm[name]) {
                case 'import_confirmed' :
                    btn_label = 'import xlsx data';
                    $('#edit-delete-prodFeatures-wrapper').show();
                    break;
                case 'import_xml_confirmed' :
                    btn_label = 'import xml data';
                    break;
            }
            $('#edit-submit-xls-upload').attr('value',btn_label);
        }
    }
    var options = Drupal.ajaxSubmit.addOptions(data.callbacks);
    $('form.ajaxsubmit').ajaxForm(options);
    if ($('#cancel_xls_bulk').length == 0){
        $('<input type="submit"/>').attr({
            id: 'cancel_xls_bulk', 
            'class': 'form-submit', 
            value: 'cancel'
        }).click(function (e) {
            e.preventDefault();
            window.location = '/en/mavicimport';
        }).prependTo('#submit_button_wrapper');			
    }



};
/**
 *end process
 */
Drupal.ajaxSubmit.end_process = function () {
    if ($('#cancel_xls_bulk').length == 0){
        $('#cancel_xls_bulk').remove();
    }

    $('#edit-submit-xls-upload').attr('value','Clear the cache').removeAttr('disabled').click(function (e) {
        e.preventDefault();
        window.location = '/en/mavicimport';
    });			

   
}


/**
* Handler for setting and retrieving ajax options
*/
Drupal.ajaxSubmit.addOptions = function (options){
    if(!options) {
        var options = {};
    }
    var baseOptions = {
        dataType: 'JSON',
        cache: false,
        forceSync: true,
        beforeSubmit: function () {
            Drupal.ajaxSubmit.beforeSubmit();
        },
        success: function (data) {
            Drupal.ajaxSubmit.success(data);
        },
        error: function(jqXHR, textStatus, error) {
        // console.log( "Request failed: ");
        // console.log( textStatus );
        // console.log(error);
        },
        complete: function(data, status) {
            if (status == 'error' || status == 'parsererror') {
                Drupal.ajaxSubmit.error();
                alert(Drupal.t('An error occurred.'));
            // console.log(status);
            }
        }
    };
    var optionsP = {};
    $.each(options, function(objN, objA) {
        switch (objN){
            case 'beforeSubmit':
                optionsP.beforeSubmit = function () {
                    Drupal.ajaxSubmit.beforeSubmit(objA);
                };
                break;
            default:
                break;
        }
    });
    return $.extend(baseOptions, optionsP);
};
/**
* Handler for building warnings tables
*/
Drupal.ajaxSubmit.buildTable = function (warnErrorObj, msgType, target){
    var warningsBloc = $('<div></div>')
    .attr('id',msgType + '_' + name)
    .addClass('file_' + msgType)
    .appendTo(target);	
    var warningFieldset = $('<fieldset></fieldset>').attr('id',msgType + 'Fieldset_' + name).addClass('collapsible collapsed').appendTo(warningsBloc);
    var warningsLegend = $('<legend></legend>').html(msgType).appendTo(warningFieldset);
    
    var gma = new Array(), j = -1;
    var wmt = new Array(), d = -1;
    wmt[++d] ='<tr><th>line</th><th>message</th></tr>';
    for (var stsmsg in warnErrorObj) {
        if (warnErrorObj[stsmsg].line == '0' || warnErrorObj[stsmsg].line == '-1'){
            gma[++j] = '<li>';
            gma[++j] = warnErrorObj[stsmsg].message;
            gma[++j] = '<li>';
        }else{
            wmt[++d] = '<tr><td>';
            wmt[++d] = warnErrorObj[stsmsg].line;
            wmt[++d] = '</td><td>';
            wmt[++d] = warnErrorObj[stsmsg].message;
            wmt[++d] = '</td></tr>';
        }
    }
    $('<ul>'+gma.join('')+'</ul>').appendTo(warningFieldset);
    $('<table>'+wmt.join('')+'</table>').attr('id',msgType + 'Msg_' + name).appendTo(warningFieldset);
};