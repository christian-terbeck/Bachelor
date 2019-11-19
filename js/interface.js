function newDataset()
{
//-->User has to confirm that he/she wants to cancel the editing process - the form will be reseted in this case
	
var r = confirm("Are you sure? Unsaved data will be lost.");

	if (r === true)
	{
	$(".form__head").html("New Dataset");
	
	$("input").val("");
	$("select").val("");
	$("textarea").val("");
	}
}

function deleteDataset()
{
//-->User has to confirm deleteion of dataset

	if ($("input[name='id']").val().length > 0 && $("input[name='id']").val() != "0")
	{
	var r = confirm("Are you sure? The data cannot be restored.");

		if (r === true)
		{
		var url = "update.php";
		var data = new FormData();

		data.append('delete', 'true');
		data.append('id', $("input[name='id']").val());

		$.ajax({
			type: "POST",
			url: url,
			data: data,
			timeout: 60000,
			processData: false,
			contentType: false,
			cache: false,
			success: function(data) {
				
				$(".form__head").html("New Dataset");
				
				$(".data__row[data-id='" + $("input[name='id']").val() + "']").remove();
				
				var dataAmount = parseInt($(".data__amount").html());
				dataAmount--;
				$(".data__amount").html(dataAmount);
				
				$("input").val("");
				$("select").val("");
				$("textarea").val("");
			},
			error: function() {
				alert("An unexpected error has occurred. Please try again.");
			}
		});
		}
	}
	else
	{
	alert("No valid dataset active! Please select a stored dataset first.");
	}
}

function saveDataset(action)
{
var url = "update.php";
var data = $("form[name='data-form']").serialize();

$.ajax({
	type: "POST",
	url: url,
	data: data,
	timeout: 60000,
	success: function(data) {
		
		if ($("input[name='id']").val().length < 1 || $("input[name='id']").val() == "0")
		{
		//-->Insert new dataset (append new row)
		
		var dataAmount = parseInt($(".data__amount").html());
		dataAmount++;
		$(".data__amount").html(dataAmount);
		}
		
		$(".data__container").load("datasets.php");
		
		$(".form__head").html("New Dataset");
		
			if (action == "default")
			{
			$("input").val("");
			$("select").val("");
			$("textarea").val("");
			}
			else if (action == "copy")
			{
			$("input[name='id']").val("");
			}
	},
	error: function() {
		alert("An unexpected error has occurred please try again.");
	}
});
}

function editDataset(obj)
{
//-->Loads the current dataset into the edit form

$(".form__head").html("Edit Dataset #" + $(obj).attr("data-id"));

var objData = $(obj).data();

	for (var i in objData)
	{
		if ($("input[name='" + i + "']").length > 0)
		{
		$("input[name='" + i + "']").val(objData[i]);
		}
		else if ($("select[name='" + i + "']").length > 0)
		{
		$("select[name='" + i + "']").val(objData[i]);
		}
		else if ($("textarea[name='" + i + "']").length > 0)
		{			
		$("textarea[name='" + i + "']").val(objData[i].replace("\\n", "\n"));
		}
		else if ($("input[data-name='" + i + "']").length > 0)
		{
		$("input[data-name='" + i + "']").val(objData[i]);
		}
	}
}