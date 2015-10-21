//Returns FULL Table HTML Requires table_name, and return_fields
//return_fields as string in format | field,field2,field3,field4...
//where_clause  as string in MYSQL Format | id='5' AND sum>'45'
//initial_sort_field as string in MYSQL Format | id DESC
//TODO : sort_buttons
function populateTable(table_name, return_fields, where_clause, initial_sort_field, sort_buttons){
	if (!/\S/.test(table_name) || !/\S/.test(return_fields)) {
    	tableInfo = "INITIAL ERROR";
	}
	if(!/\S/.test(where_clause)){
		where_clause = null;	
	}
	if(!/\S/.test(initial_sort_field)){
		initial_sort_field = null;	
	}
	
	$.ajax({
			dataType: "json",
    		type: 'POST',
			data: { tableName : table_name, returnFields : return_fields, whereClause : where_clause, initialSortField : initial_sort_field } ,
    		url: 'http://www.balkamp.com/formAssets/php/populateTable.php',
			async: false
		})
		.done(function(response) {
			tableInfo = response;
		})
		.fail(function(data) {		
			tableInfo = "RESPONSE ERROR";
		});
		
	return tableInfo;
}

function createExcel(data){
	csvContent = '';
	for(var i=0; i<data['fields'].length;i++){
		csvContent += data['fields'][i] + ', ';
	}
	
	csvContent = csvContent.slice(0, - 1);	
	csvContent += '\n';
	
	for(var i=0; i<data['data'].length;i++){
		for(var j=0; j<data['fields'].length;j++){
			csvContent += data['data'][i][data['fields'][j]] + ', ';
		}
		csvContent = csvContent.slice(0, - 1);
		csvContent += '\n';
	}
	
	
	blob = new Blob([csvContent], { type: 'text/csv' }); //new way
	var csvUrl = URL.createObjectURL(blob);
	var downloadLink = document.createElement("a");
	downloadLink.setAttribute("href", csvUrl);
	downloadLink.setAttribute("download", "Expo_Shirt_Data.csv");
	downloadLink.innerHTML = "Click Here To Download";
	$(downloadLink).addClass('subBtn').css({'color':'black'});

	return downloadLink;
}