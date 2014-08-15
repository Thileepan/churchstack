var doUser = 'server/douser.php';


function listAllUsers()
{
	oTable = $('#usersTable').dataTable( {
		"aoColumns": [
			{ "sWidth": "5%" },
			{ "sWidth": "10%"  },
			{ "sWidth": "30%" },
			{ "sWidth": "30%" },
			{ "sWidth": "15%" },
			{ "sWidth": "10%" },
		],
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doUser,
		"iDisplayLength":25,
//		"aaSorting": 2,
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=1",
                "error": HandleAjaxError,
                "success": fnCallback
            } );
        }
	});
}