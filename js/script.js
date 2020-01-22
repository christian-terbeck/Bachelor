"use_strict";

const mapWidth = 45;
var route1, route2;
var searchTerm = "";

/*---------- Labels ----------*/

//stored as JSON object

const labels = {
	"navigation_started": {
		"de": "Navigation gestartet",
		"en": "Navigation started",
		"es": "Inicio de navegación"
	},
	"turn_right": {
		"de": "Rechts abbiegen",
		"en": "Turn right",
		"es": "Girar a la derecha"
	},
	"turn_left": {
		"de": "Links abbiegen",
		"en": "Turn left",
		"es": "Girar a la izquierda"
	},
	"drive_to_level": {
		"de": "Fahre auf Etage",
		"en": "Drive to level",
		"es": "Desplazarse a la planta"
	},
	"then_click_here": {
		"de": "Dann klicke hier",
		"en": "Then click here",
		"es": "Luego haga clic aquí"
	},
	"level": {
		"de": "Etage",
		"en": "Level",
		"es": "Planta"
	},
	"reached": {
		"de": "erreicht",
		"en": "reached",
		"es": "alcanzada"
	},
	"undo": {
		"de": "Rückgängig",
		"en": "Undo",
		"es": "Deshacer"
	}
};

var language = "en"; //default fallback language

/*---------- Global classes ----------*/

class Point
{
	constructor(x, y, level)
	{
	this.x = x;
	this.y = y;
	this.level = level;
	}
	
	setX(x)
	{
	this.x = x;
	}
	
	setY(y)
	{
	this.y = y;
	}
	
	setLevel(level)
	{
	this.level = level;
	}
	
	setClosestPointOnPath(point)
	{
	this.closestPoint = point;
	}
	
	setClosestPath(path)
	{
	this.closestPath = path;
	}
	
	getX()
	{
	return this.x;
	}
	
	getY()
	{
	return this.y;
	}
	
	getLevel()
	{
	return this.level;
	}
	
	getClosestPointOnPath()
	{
	return this.closestPoint;
	}
	
	getClosestPath()
	{
	return this.closestPath;
	}
	
	getDistance(givenPoint)
	{
	//-->returns the distance between a given point and this one
	
	return Math.abs(Math.sqrt(Math.pow(this.x - givenPoint.getX(), 2) + Math.pow(this.y - givenPoint.getY(), 2)));
	}
}

class Path
{
	constructor(pointA, pointB, level)
	{
	this.pointA = pointA;
	this.pointB = pointB;
	this.level = level;
	}
	
	getPointA()
	{
	return this.pointA;
	}
	
	getPointB()
	{
	return this.pointB;
	}
	
	getLevel()
	{
	return this.level;
	}
	
	getClosestPoint(givenPoint)
	{
	//-->returns the closest point on this path to a given point and also returns the distance
	
	var closestDistance, tmpDistance, x, y;
	
		if (this.pointA.getX() == this.pointB.getX()) //vertical path
		{
			for (var i = this.pointA.getY(); i < this.pointB.getY(); i++)
			{
			tmpDistance = Math.sqrt(Math.pow(this.pointA.getX() - givenPoint.getX(), 2) + Math.pow(i - givenPoint.getY(), 2));
			
				if (typeof closestDistance == 'undefined' || tmpDistance < closestDistance)
				{
				closestDistance = tmpDistance;
				x = this.pointA.getX();
				y = i;
				
				//-->Check if there is a point in straight angle
				
					if (givenPoint.getY() >= this.pointA.getY() && givenPoint.getY() <= this.pointB.getY())
					{
					tmpDistance = Math.sqrt(Math.pow(this.pointA.getX() - givenPoint.getX(), 2) + Math.pow(givenPoint.getY() - givenPoint.getY(), 2));
					
						if (tmpDistance < closestDistance)
						{
						closestDistance = tmpDistance;
						y = givenPoint.getY();
					
						break;
						}
					}
				}
			}
		}
		else if (this.pointA.getY() == this.pointB.getY()) //horizontal path
		{
			for (var i = this.pointA.getX(); i < this.pointB.getX(); i++)
			{
			tmpDistance = Math.sqrt(Math.pow(i - givenPoint.getX(), 2) + Math.pow(this.pointA.getY() - givenPoint.getY(), 2));
			
				if (typeof closestDistance == 'undefined' || tmpDistance < closestDistance)
				{
				closestDistance = tmpDistance;
				x = i;
				y = this.pointA.getY();
				}
				
			//-->Check if there is a point in straight angle
				
					if (givenPoint.getX() >= this.pointA.getX() && givenPoint.getX() <= this.pointB.getX())
					{
					tmpDistance = Math.sqrt(Math.pow(givenPoint.getX() - givenPoint.getX(), 2) + Math.pow(this.pointA.getY() - givenPoint.getY(), 2));
					
						if (tmpDistance < closestDistance)
						{
						closestDistance = tmpDistance;
						x = givenPoint.getX();
					
						break;
						}
					}
			}
		}
	
	return [closestDistance, new Point(x, y, givenPoint.getLevel())];
	}
	
	getLength()
	{
		if (this.pointA.getX() == this.pointB.getX()) //vertical path
		{
		return Math.abs(this.pointB.getY() - this.pointA.getY());
		}
		else if (this.pointA.getY() == this.pointB.getY()) //horizontal path
		{
		return Math.abs(this.pointB.getX() - this.pointA.getX());
		}
	}
	
	covers(point)
	{
	// returns true if the line covers a given point
	
		if (point.getLevel() == this.level)
		{
			if (this.pointA.getX() == this.pointB.getX() && this.pointA.getX() == point.getX()) //vertical path
			{
				if (this.pointA.getY() <= point.getY() && this.pointB.getY() >= point.getY())
				{
				return true;
				}
			}
			else if (this.pointA.getY() == this.pointB.getY() && this.pointA.getY() == point.getY()) //horizontal path
			{
				if (this.pointA.getX() <= point.getX() && this.pointB.getX() >= point.getX())
				{
				return true;
				}
			}
		}
		
	return false;
	}
	
	intersects(path)
	{
	// returns the intersecting point if the given path intersects with this one (only horizontal and vertical paths on the same level can intersect!)
	
		if (this.getLevel() == path.getLevel())
		{
			if (this.pointA.getX() == this.pointB.getX() && path.pointA.getY() == path.pointB.getY()) //vertical path and horizontal path
			{
				if (this.pointA.getX() >= path.pointA.getX() && this.pointA.getX() <= path.pointB.getX() && this.pointA.getY() <= path.pointA.getY() && this.pointB.getY() >= path.pointA.getY())
				{					
				return new Point(this.pointA.getX(), path.pointA.getY(), this.level);
				}
				else
				{
				return false;
				}
			}
			else if (this.pointA.getY() == this.pointB.getY() && path.pointA.getX() == path.pointB.getX()) //horizontal path and vertical path
			{
				if (this.pointA.getX() <= path.pointA.getX() && this.pointB.getX() >= path.pointA.getX() && this.pointA.getY() >= path.pointA.getY() && this.pointA.getY() <= path.pointB.getY())
				{					
				return new Point(path.pointA.getX(), this.pointA.getY(), this.level);
				}
				else
				{
				return false;
				}
			}
			else
			{
			return false;
			}
		}
		else
		{
		return false;
		}
	}
	/*
	draw()
	{
	//-->Draws the line
		
		if ($(".map__path[data-level='" + this.level + "'][data-x1='" + this.pointA.getX() + "'][data-y1='" + this.pointA.getY() + "'][data-x2='" + this.pointB.getX() + "'][data-y2='" + this.pointB.getY() + "']").length < 1)
		{
			if (this.pointA.getX() == this.pointB.getX()) //vertical path
			{
			$("<div class='map__path' data-level='" + this.level + "' data-x1='" + this.pointA.getX() + "' data-y1='" + this.pointA.getY() + "' data-x2='" + this.pointB.getX() + "' data-y2='" + this.pointB.getY() + "' data-solution='true' style='height: " + this.getLength() + "%; width: 1%; top: " + this.pointA.getY() + "%; left: " + this.pointA.getX() + "%;'></div>").insertAfter($(".map__path").last());
			}
			else if (this.pointA.getY() == this.pointB.getY()) //horizontal path
			{
			$("<div class='map__path' data-level='" + this.level + "' data-x1='" + this.pointA.getX() + "' data-y1='" + this.pointA.getY() + "' data-x2='" + this.pointB.getX() + "' data-y2='" + this.pointB.getY() + "' data-solution='true' style='height: 1%; width: " + this.getLength() + "%; top: " + this.pointA.getY() + "%; left: " + this.pointA.getX() + "%;'></div>").insertAfter($(".map__path").last());
			}
		}
	}
	
	show()
	{
	//-->Shows the path on the map
	
	$(".map__path[data-level='" + this.level + "'][data-x1='" + this.pointA.getX() + "'][data-y1='" + this.pointA.getY() + "'][data-x2='" + this.pointB.getX() + "'][data-y2='" + this.pointB.getY() + "']").css("display", "block");
	}
	
	hide()
	{
	//-->Hides the path on the map
	
	$(".map__path[data-level='" + this.level + "'][data-x1='" + this.pointA.getX() + "'][data-y1='" + this.pointA.getY() + "'][data-x2='" + this.pointB.getX() + "'][data-y2='" + this.pointB.getY() + "']").css("display", "none");
	}*/
}

class Level
{
	constructor(name)
	{
	this.name = name;
	
	var newPaths = new Array();
	
		if ($(".map__path[data-level='" + name + "']").length > 0)
		{
		$(".map__path[data-level='" + name + "']").each(function () {
			
			newPaths.push(new Path(new Point(parseFloat($(this).data("x1")), parseFloat($(this).data("y1")), name), new Point(parseFloat($(this).data("x2")), parseFloat($(this).data("y2")), name), name));
		});
		}
		
	this.paths = newPaths;
	}
	
	getName()
	{
	return this.name;
	}
	
	getPaths()
	{
	return this.paths;
	}
	
	getPointPaths(point)
	{
	//-->Return alls paths that intersect with the given point
	
	var intersectingPaths = new Array();
	
		for (var i = 0; i < this.paths.length; i++)
		{
			if (this.paths[i].covers(point))
			{
			intersectingPaths.push(this.paths[i]);
			}
		}
		
	return intersectingPaths;
	}
	
	getClosestPoint(point)
	{
	//-->Finds and returns the closest point located on a path
	
	var closestDistance, tmpDistance, x, y;
	
		for (var j = 0; j < this.paths.length; j++)
		{
		tmpDistance = this.paths[j].getClosestPoint(point);
		
			if (j < 1 || tmpDistance[0] < closestDistance)
			{
			closestDistance = tmpDistance[0];
			x = tmpDistance[1].getX();
			y = tmpDistance[1].getY();
			}
		}
	
	return new Point(x, y, this.name);
	}
	
	getIntersectingPaths(path)
	{
	//-->Returns the intersecting paths to a given path (on this level)
	
	var intersectingPaths = new Array();
	
		for (var i = 0; i < this.paths.length; i++)
		{
			if (this.paths[i] !== path && this.paths[i].intersects(path))
			{
			intersectingPaths.push(this.paths[i]);
			}
		}
		
	return intersectingPaths;
	}
}

class Route
{
	constructor(level, startPoint, endPoint)
	{
	this.level = level;
	this.originalStartPoint = startPoint;
	this.originalEndPoint = endPoint;
	
	//-->Immediately find the closest points on paths
	
	this.startPoint = level.getClosestPoint(startPoint);
	this.endPoint = level.getClosestPoint(endPoint);
	
	this.solutionPaths = new Array();
	this.solutions = new Array();
	this.solution;
	this.distance;
	}
	
	getStart()
	{
	return this.startPoint;
	}
	
	getEnd()
	{
	return this.endPoint;
	}
	
	solve()
	{
	this.solutionPaths = this.findSolution();
	
	//-->Translate solutions into paths and (automatically) find the shortest one
	
		if (this.solutionPaths.length < 1)
		{
		return false;
		}
		else
		{
			for (var i = 0; i < this.solutionPaths.length; i++)
			{
			this.solutions.push(this.finalizeSolution(this.solutionPaths[i]));
			}
		}
	}
	
	findSolution(paths)
	{
	//-->Base routing algorithm
	
	paths = typeof paths === 'undefined' ? [this.level.getPointPaths(this.startPoint)] : paths; //set start paths if no parameter is passed
	
	var checkingCompleted = true;
	var curNeighbors, tmpPaths;
	
		for (var i in paths)
		{
			if (!paths[i][paths[i].length - 1].covers(this.endPoint)) //end point is not covered by the last path of current solution.. keep on searching
			{
			checkingCompleted = false;
			
			curNeighbors = this.level.getIntersectingPaths(paths[i][paths[i].length - 1]);
				
				for (var j = 0; j < curNeighbors.length; j++)
				{
					if (!this.pathChecked(paths[i], curNeighbors[j]))
					{
					tmpPaths = new Array();
					
						for (var k = 0; k < paths[i].length; k++)
						{
						tmpPaths.push(paths[i][k]);
						}
					
					tmpPaths.push(curNeighbors[j]);
					paths.push(tmpPaths);
					}
				}
				
			paths.splice(i, 1);
			}
		}
	
		if (checkingCompleted)
		{
		return paths;
		}
		else
		{
		return this.findSolution(paths);
		}
	}
	
	pathChecked(solution, path)
	{
	//-->Checks whether a given solution already covers a certain path
	
		for (var i = 0; i < solution.length; i++)
		{
			if (solution[i].pointA.getX() == path.pointA.getX() && solution[i].pointA.getY() == path.pointA.getY() && solution[i].pointB.getX() == path.pointB.getX() && solution[i].pointB.getY() == path.pointB.getY())
			{
			return true;
			}
		}
		
	return false;
	}
	
	finalizeSolution(solutionPaths)
	{
	//-->translate paths to coordinates and also determine the shortest path
	
	var tmpSolution = [this.originalStartPoint, this.startPoint];
	var tmpDistance = 0;
	
		for (var i = 0; i < (solutionPaths.length - 1); i++)
		{
		tmpSolution.push(solutionPaths[i].intersects(solutionPaths[i + 1]));
		}
		
	tmpSolution.push(this.endPoint);
	tmpSolution.push(this.originalEndPoint);
	
		for (var i = 0; i < (tmpSolution.length - 1); i++)
		{
		tmpDistance += tmpSolution[tmpSolution.length - 1].getDistance(tmpSolution[tmpSolution.length - 2]);
		}
	
		if (this.solutions.length < 1 || tmpDistance < this.distance)
		{
		this.solution = tmpSolution;
		this.distance = tmpDistance;
		}
		
	return tmpSolution;
	}
	
	measureDistance(solution)
	{
	//-->Calculates and returns the distance of a certain solution
	
	var distance = 0;
		
		for (var i = 0; i < (solution.length - 1); i++)
		{
		distance += solution[i].getDistance(solution[i + 1]);
		}
		
	return distance;
	}
	
	visualize()
	{
	//-->Set starting orientation
	
		if (this.originalStartPoint.getX() == this.startPoint.getX())
		{
			if (this.originalStartPoint.getY() > this.startPoint.getY())
			{
			this.orientation = 0;
			}
			else
			{
			this.orientation = 180;
			}
		}
		else if (this.originalStartPoint.getY() == this.startPoint.getY())
		{
			if (this.originalStartPoint.getX() > this.startPoint.getX())
			{
			this.orientation = 270;
			}
			else
			{
			this.orientation = 90;
			}
		}
	
	this.showSolution(this.solution);
	this.showInstruction(this.solution);
	}
	
	showSolution(solution)
	{
	//-->Move markers
	
	$(".map__marker[data-style='location']").css({"display": "block", "top": this.originalStartPoint.getY() + "%", "left": this.originalStartPoint.getX() + "%"});
	$(".map__marker[data-style='destination']").css({"display": "block", "top": this.originalEndPoint.getY() + "%", "left": this.originalEndPoint.getX() + "%"});
	
	//-->Draw path via canvas
	
	var tmpPath;
	
	var c = document.getElementById("canvas");
	var ctx = c.getContext("2d");
	c.width = $("#canvas").width();
	c.height = $("#canvas").height();
	ctx.clearRect(0, 0, c.width, c.height);
	
	ctx.lineWidth = 5;
	ctx.beginPath();
	
		for (var i = 0; i < solution.length; i++)
		{
			if (i < 1)
			{
			ctx.moveTo(($("#canvas").width() / 100) * solution[i].getX(), ($("#canvas").width() / 100) * solution[i].getY());
			}
			else
			{
			ctx.lineTo(($("#canvas").width() / 100) * solution[i].getX(), ($("#canvas").width() / 100) * solution[i].getY());
			}
		}
		
	ctx.strokeStyle = "#00DD23";
	ctx.stroke();
	}
	
	showInstruction(solution)
	{
	//-->Display textual navigation instructions	
	
	$(".instruction").empty().scrollTop(0);
	
	var tmpDistance;
	
		for (var i = 1; i < solution.length; i++)
		{
		tmpDistance = parseFloat((mapWidth / 100) * solution[i].getDistance(solution[i - 1])).toFixed(1);
		
			if (tmpDistance >= 1) //only show first decimal if distance is less than a meter otherwise just round
			{
			tmpDistance = Math.round(tmpDistance);
			}
		
			if (i < 2)
			{
				if (this.level.getName() != 0)
				{
				$(".map__marker[data-style='destination']").find(".map__marker-icon").removeClass("fa-arrows-alt-v").addClass("fa-map-pin");
				
				$(".instruction").append("<div class='instruction__step'><div class='instruction__symbol instruction__symbol--marker fa fa-location-arrow'></div><div class='instruction__label'>" + labels["level"][language] + " " + route2.level.getName() + " " + labels["reached"][language] + "</div></div><div class='instruction__button clickable' data-triggered='true' data-level='" + route2.level.getName() + "' onclick='confirmNewLevel(this)'><span class='fa fa-check'></span><span class='fa fa-undo'></span><span data-text='done'>Level " + route2.level.getName() + " reached</span><span data-text='undo'>" + labels["undo"][language] + "</span></div>");
				}
				else
				{			
				$(".instruction").append("<div class='instruction__step'><div class='instruction__symbol instruction__symbol--marker fa fa-location-arrow'></div><div class='instruction__label'>" + labels["navigation_started"][language] + "</div></div>");
				}
			}
		
		$(".instruction").append("<div class='instruction__distance'>" + tmpDistance + " m</div>");
		
			if (solution[i] == this.originalEndPoint)
			{
				if (typeof route2 !== "undefined" && route2.level.getName() != this.level.getName())
				{
					if (route2.level.getName() > this.level.getName())
					{
					var elevSymbol = "fa-caret-up";	
					}
					else
					{
					var elevSymbol = "fa-caret-down";
					}
				
				$(".map__marker[data-style='destination']").find(".map__marker-icon").removeClass("fa-map-pin").addClass("fa-arrows-alt-v");
				$(".instruction").append("<div class='instruction__step'><div class='instruction__symbol instruction__symbol--marker fa " + elevSymbol + "'></div><div class='instruction__label'>" + labels["drive_to_level"][language] + " " + route2.level.getName() + "<br /><small>" + labels["then_click_here"][language] + ":</small></div></div><div class='instruction__button clickable' data-triggered='false' data-level='" + route2.level.getName() + "' onclick='confirmNewLevel(this)'><span class='fa fa-check'></span><span class='fa fa-undo'></span><span data-text='done'>" + labels["level"][language] + " " + route2.level.getName() + " " + labels["reached"][language] + "</span><span data-text='undo'>" + labels["undo"][language] + "</span></div>");
				}
				else
				{
				$(".instruction").append("<div class='instruction__step'><div class='instruction__symbol instruction__symbol--marker fa fa-map-pin'></div><div class='instruction__label'>" + $(".overlay__head").html() + " " + labels["reached"][language] + "</div></div>");
				}
			}
			else
			{
			/*conditions for "turning right"
			
			walking "up": Y same but X2 greater
			walking "right": X same but Y2 greater
			walking "down": Y same but X2 smaller
			walking "left": X same but Y2 small*/
			
				if ((this.orientation == 0 && solution[i].getY() == solution[i + 1].getY() && solution[i].getX() < solution[i + 1].getX()) || (this.orientation == 90 && solution[i].getX() == solution[i + 1].getX() && solution[i].getY() < solution[i + 1].getY()) || (this.orientation == 180 && solution[i].getY() == solution[i + 1].getY() && solution[i].getX() > solution[i + 1].getX()) || (this.orientation == 270 && solution[i].getX() == solution[i + 1].getX() && solution[i].getY() > solution[i + 1].getY()))
				{
				$(".instruction").append("<div class='instruction__step'><div class='instruction__symbol fa fa-arrow-right'></div><div class='instruction__label'>" + labels["turn_right"][language] + "</div></div>");
				
				this.orientation = (this.orientation + 90) % 360;
				}
				else
				{
				$(".instruction").append("<div class='instruction__step'><div class='instruction__symbol fa fa-arrow-left'></div><div class='instruction__label'>" + labels["turn_left"][language] + "</div></div>");
				
					if (this.orientation > 0)
					{
					this.orientation = this.orientation - 90;
					}
					else
					{
					this.orientation = 270;
					}
				}
			}
		}
	}
}

// This is an array of all paths that were added just to cover the main stairs on each level
// they should not be considered as a default hallway when navigating on a single level
// not used in the final algorithm!

const stairsPaths = [
	new Path(new Point(66.55, 24.04, 0), new Point(66.55, 51.39, 0), 0),
	new Path(new Point(66.55, 24.04, 1), new Point(66.55, 51.39, 1), 1),
	new Path(new Point(66.55, 18.58, 2), new Point(66.55, 51.39, 2), 2),
	new Path(new Point(66.55, 18.58, 3), new Point(66.55, 51.39, 3), 3),
	new Path(new Point(66.55, 24.04, 4), new Point(66.55, 51.39, 4), 4),
	new Path(new Point(66.55, 24.04, 5), new Point(66.55, 51.39, 5), 5)
];

/*---------- Page listener events ----------*/

$(document).ready(function() {
	
	setTabHeights();
	refreshQuickLinks();
	updateKeyboard();
	checkTopButton();
	
		if ($(".overlay[data-active='true']").length > 0)
		{
		//-->Remove overlay with a short delay
		
		var action = '$(".overlay").attr("data-active", "false");';
		setTimeout(action, 3000);
		}
		
		if ($(".map__marker").length == 2)
		{
		//-->Set user language in JS
		
		language = $("html").attr("lang");
		
		//-->Read the data from DOM elements and paste them to the routing function
		
		var routeStart = new Point(parseFloat($(".map__marker[data-style='location']").data("x")), parseFloat($(".map__marker[data-style='location']").data("y")), parseInt($(".map__marker[data-style='location']").data("level")));
		var routeEnd = new Point(parseFloat($(".map__marker[data-style='destination']").data("x")), parseFloat($(".map__marker[data-style='destination']").data("y")), parseInt($(".map__marker[data-style='destination']").data("level")));
		
		initRouting(routeStart, routeEnd);
		}
});

$(window).scroll(function() {
	checkTopButton();
});

window.onresize = function(event) {
	checkTopButton();
};

/*---------- API ----------*/

//this is just a function demonstrating how to use the API

function getData(type, data)
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
$(window).scrollTop(0);
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
	$(this).attr("alt", language).css("opacity", "0.1").attr("src", curSource).on("load", function() {
		$(this).css("opacity", "1");
	});
});
}

function goTop()
{
//-->Scroll back to page top
	
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

function keyboard(obj)
{
	if (!$(obj).hasClass("disabled"))
	{
	searchTerm = searchTerm + $(obj).data("name");

		if (searchTerm.length > 0)
		{
		$(".content__tab[data-name='search']").css("display", "block").html(searchTerm.toUpperCase());
		}

	search();
	}
}

function clearKeyboard()
{
	if (searchTerm.length > 0)
	{
	searchTerm = searchTerm.substring(0, searchTerm.length - 1);
	
	$(".content__tab[data-name='search']").html(searchTerm.toUpperCase());
	
		if (searchTerm.length < 1)
		{
		$(".content__tab[data-name='search']").css("display", "none");
		}
	
	search();
	}
}

$.expr[':'].contains = $.expr.createPseudo(function(arg) {
	return function(elem) {
	return $(elem).text().toLowerCase().indexOf(arg.toLowerCase()) >= 0;
	};
});

function search()
{
$(".content__container[data-name='search']").find(".accordion-box").css("display", "none");

	if (searchTerm.length > 0)
	{
	$(".content__container[data-name='search']").find(".accordion-box__label:contains('" + searchTerm + "')").parent().parent().css("display", "block");
	}
	
updateKeyboard();
}

function updateKeyboard()
{
//-->Disable all keyboard buttons that do not lead to any results anymore
	
$(".content__keyboard > li").each(function() {
	
		if ($(this).data("name") != "CLEAR")
		{
			if ($(".content__container[data-name='search']").find(".accordion-box__label:contains('" + searchTerm + $(this).data("name") + "')").length < 1)
			{
			$(this).removeClass("clickable").addClass("readonly").addClass("disabled");
			}
			else
			{
				if ($(this).hasClass("disabled"))
				{
				$(this).removeClass("disabled").removeClass("readonly").addClass("clickable");
				}
			}
		}
		else
		{
			if (searchTerm.length > 0)
			{
				if ($(this).hasClass("disabled"))
				{
				$(this).removeClass("disabled").removeClass("readonly").addClass("clickable");
				}
			}
			else
			{
				if (!$(this).hasClass("disabled"))
				{
				$(this).removeClass("clickable").addClass("readonly").addClass("disabled");
				}
			}
		}
});
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
	$(".map__holder").attr("data-orientation", "180");
	route2.visualize();
	}
	else
	{
	$(object).attr("data-triggered", "false");
	switchLevel(0);
	$(".map__holder").attr("data-orientation", "270");
	route1.visualize();
	}
}

var curLevel;

function switchLevel(level)
{
//-->Displays another level map and also updated the level quick navigation bar

curLevel = parseInt($(".map__level[data-active='true']").attr("data-level"));
	
	if (level != curLevel)
	{
	$(".map__plan[data-active='true']").attr("data-active", "false");
	$(".map__plan[data-level='" + level + "']").attr("data-active", "true");
	
	$(".map__label").find("span").html(labels["level"][language] + " " + level).parent().css("display", "flex");
	var action = "$('.map__label').fadeOut(250);";
	setTimeout(action, 2000);
	
	$(".map__level[data-active='true']").attr("data-active", "false");	
	$(".map__level[data-level='" + level + "']").attr("data-active", "true");	
	}
}

function initRouting(start, end)
{
curLevel = $(".map__level[data-active='true']").data("level");

//-->Create level instances (they are the same if location and destination are on the same level)

var level = new Level(start.getLevel());
var levelSwitch;

	if (start.getLevel() != end.getLevel())
	{
	//-->Set end to elevator (stairs can be added later)
	
	var tmpEnd = end;
	end = new Point(parseFloat($(".map__level-switch").data("x")), parseFloat($(".map__level-switch").data("y")), start.getLevel()); //just the elevator for the study!
	
	levelSwitch = true;
	}

//-->Start routing

route1 = new Route(level, start, end);
route1.solve();

	if (levelSwitch == true)
	{
	level = new Level(tmpEnd.getLevel());
	start = end;
	start.setLevel(tmpEnd.getLevel());
	end = tmpEnd;
	
	route2 = new Route(level, start, end);
	route2.solve();
	}
	
//-->Display result
	
route1.visualize();
}