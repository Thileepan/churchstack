function trim(str) {
        return str.replace(/^\s+|\s+$/g,"");
}

$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback ){
    if ( typeof sNewSource != 'undefined' ){
        oSettings.sAjaxSource = sNewSource;
    }
    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;

    oSettings.fnServerData( oSettings.sAjaxSource, null, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );

        /* Got the data - add it to the table */
        for ( var i=0 ; i<json.aaData.length ; i++ ){
            that.oApi._fnAddData( oSettings, json.aaData[i] );
        }

        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
        that.fnDraw( that );
        that.oApi._fnProcessingDisplay( oSettings, false );

        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' ){
            fnCallback( oSettings );
        }
    });
}

function HandleAjaxError(obj,errorType)
{
	//var responseText = $.httpData(obj);
	alert("Error while processing your request!");
	//console.log(obj);
	alert("Status : "+obj.status + '\nError Type : ' + errorType+ "\nStatus Text : "+obj.statusText+"\nResponse Text : " +obj.responseText);
	return false;
	//console.log(obj);
}

function escString(encStr)
{
	encStr = escape(encStr);
	encStr = encStr.replace(/\//g,"%2F");
	encStr = encStr.replace(/\?/g,"%3F");
	encStr = encStr.replace(/=/g,"%3D");
	encStr = encStr.replace(/&/g,"%26");
	encStr = encStr.replace(/@/g,"%40");
	encStr = encStr.replace(/\+/g,"%2B");
	return encStr;
}
