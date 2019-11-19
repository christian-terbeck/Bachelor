/*---------- Page listener events ----------*/

$(document).ready(function() {
	
	setTabHeights();
	refreshQuickLinks();
	checkTopButton();
	
		if ($(".overlay[data-active='true']").length > 0)
		{			
		var action = '$(".overlay").attr("data-active", "false");';
		setTimeout(action, 3000);
		}
		
		if ($(".map__marker").length == 2)
		{
		//-->Read the data from DOM elements and paste them to the routing function
		
		var start = [parseFloat($(".map__marker[data-style='location']").data("level")), [parseFloat($(".map__marker[data-style='location']").data("x")), parseFloat($(".map__marker[data-style='location']").data("y"))]];
		var end = [parseFloat($(".map__marker[data-style='destination']").data("level")), [parseFloat($(".map__marker[data-style='destination']").data("x")), parseFloat($(".map__marker[data-style='destination']").data("y"))]];
		
		route(start, end);
		}
});

$(window).scroll(function() {
	checkTopButton();
});

window.onresize = function(event) {
	checkTopButton();
};

/*---------- API ----------*/

function api(type, data)
{
var authorization_key = '1234';
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
	error: function(data) {
		
		console.log('unable to connect: ' + data);
	}
});
}

/*---------- Global functions ----------*/

function setTabHeights()
{
var navHeight = $(".nav").outerHeight() - 1;
	
$(".content__tab").css("top", navHeight + "px");
}

function refreshQuickLinks()
{
//-->Hide all quick links that do not have a corresponding tab
	
$(".content__shortlinks > li").each(function() {
	
		if ($(".content__tab[data-name='" + $(this).data("name") + "']").length < 1)
		{
		$(this).removeClass("clickable").addClass("readonly").addClass("disabled");
		}
});
}

function checkTopButton()
{
var scrollPosition = $(document).scrollTop();

	if (scrollPosition > 150 && !$(".go-top").hasClass("go-top--active"))
	{
	$(".go-top").removeClass("go-top--inactive").addClass("go-top--active");
	}
	else if (scrollPosition <= 150 && $(".go-top").hasClass("go-top--active"))
	{
	$(".go-top").removeClass("go-top--active").addClass("go-top--inactive");	
	}
}

function switchCategory(obj)
{
//-->Get category

var category = $(obj).data('name');

//-->Disable current category and page and display new ones

	if ($("li[data-active='true']").length > 0)
	{
	$("li[data-active='true']").attr("data-active", "false");
	$(".content__page[data-active='true']").attr("data-active", "false");
	}
	
//-->Close all opened accordion boxes

	if ($(".accordion-box[data-active='true']").length > 0)
	{
	$(".accordion-box[data-active='true']").attr("data-active", "false");	
	}
	
$("li[data-name='" + category + "']").attr("data-active", "true");
$(".content__page[data-name='" + category + "']").attr("data-active", "true");
}

function setLanguage(language)
{
//-->Set contents to this language for each element that has the data-lang attributes

$("*[data-" + language + "]").each(function() {
	$(this).html($(this).data(language));
});

//-->Update QR code images as well

var curLanguage;
var curSource;

$(".qr-code").each(function() {
	curLanguage = $(this).attr("alt");
	curSource = $(this).attr("src");
	
	curSource = curSource.replace("language=" + curLanguage, "language=" + language);
	$(this).attr("src", curSource).attr("alt", language);
});
}

function goTop()
{
$("html, body").animate({scrollTop: 0}, "500", "swing", function(){});
}

function tab(obj)
{
//-->Scroll to matching tab element and keep distance to the top

	if ($(".content__tab[data-name='" + $(obj).data("name") + "']").length > 0)
	{
	var topDistance = $(".content__tab[data-name='" + $(obj).data("name") + "']").offset().top - ($(".nav").outerHeight() - 1);

	$("html, body").animate({scrollTop: topDistance}, "500", "swing", function(){});
	}
}

function accordionBox(box)
{
//-->Switch between active and inactive - depending on the current state

	if ($(box).parent().attr("data-active") != "true")
	{
	//-->Close other boxes if there are any
	
		if ($(".accordion-box[data-active='true']").length > 0)
		{
		$(".accordion-box[data-active='true']").attr("data-active", "false");
		}
		
	$(box).parent().attr("data-active", "true");
	}
	else
	{
	$(box).parent().attr("data-active", "false");	
	}
}

function confirmNewLevel(object)
{
//-->Switch between active and inactive - depending on the current state and trigger switchLevel function
	
	if ($(object).attr("data-triggered") != "true")
	{
	$(object).attr("data-triggered", "true");
	switchLevel(parseInt($(object).attr("data-level")));
	}
	else
	{
	$(object).attr("data-triggered", "false");
	switchLevel(0);
	}
}

function switchLevel(level)
{
//-->Displays another level map and also updated the level quick navigation bar

var curLevel = parseInt($(".map__level[data-active='true']").attr("data-level"));
	
	if (level != curLevel)
	{
	$(".map__plan[data-active='true']").attr("data-active", "false");
	$(".map__plan[data-level='" + level + "']").attr("data-active", "true");
	
	$(".instruction__step[data-level='" + curLevel + "']").css("display", "none");
	$(".instruction__step[data-level='" + level + "']").css("display", "block");
	
	$(".instruction__distance[data-level='" + curLevel + "']").css("display", "none");
	$(".instruction__distance[data-level='" + level + "']").css("display", "block");
	
	$(".instruction").scrollTop(0);
	
	$(".map__level[data-active='true']").attr("data-active", "false");	
	$(".map__level[data-level='" + level + "']").attr("data-active", "true");	
	}
}

function route(start, end)
{
var curLevel = $(".map__level[data-active='true']").data("level");
	
//-->Move markers to their position

$(".map__marker[data-style='location']").css({"top": start[1][1] + "%", "left": start[1][0] + "%"});
$(".map__marker[data-style='destination']").css({"top": end[1][1] + "%", "left": end[1][0] + "%"});

	if (end[0] != start[0])
	{
	//-->Switch level
	
	
	}
	else
	{
	//-->Destination is on the same level like the user
	
	
	}
}

function getData(type, data)
{
var authorization_key = '1234';
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
	error: function(data) {
		
		console.log('unable to connect: ' + data);
	}
});
}