/*---------- API DEMO ----------*/

function api(type, data)
{
var authorization_key = 'GEO1';
var url = 'https://christian-terbeck.de/projects/ba/request.php';

$.ajax({
	type: 'POST',
	url: url,
	data: {authorization_key: authorization_key, type: type, data: data},
	timeout: 60000,
	success: function(data) {
		
		if (data.status == 'success')
		{
		console.log(data);	
		}
		else
		{
		console.log(data.message);
		}
	},
	error: function(err) {
		
		console.log('unable to connect: ' + err);
	}
});
}