/* * What is this JS file supposed to do?
 * 1. Gather data from the data source.
 * 2. Display data in a pretty format on screen.
 * 
 *
 */

const NPC_OPINIONS = ['Hatered', 'Great Aversion', 'Aversion', 'Slight Aversion', 'Nothing', 'Neutral', 'Slight Favor', 'Favor', 'Great Favor', 'Devotion'];

/*
 * Activity "Class".
 */
function Activity(npc, activityType, activityDuration, activityProgress, activityDescription) {
	this.npc = npc;
	this.activityType = activityType;
	this.activityDuration = activityDuration;
	this.activityProgress = activityProgress;
	this.activityDescription = activityDescription;
}

// Clean up the valueOf() for Activity.
Activity.prototype.valueOf = function () {
	var ret = "";
	ret += this.npc.name + " is performing the " + this.activityType + " activity. Progress is " + this.activityProgress + "/" + this.activityDuration + " days.";
	return ret;
};

/*
 * Npc "Class".
 */
function Npc(name, description, memberOfOrgs, opinionOfPcs, currentActivities) {
	this.name = name;
	this.description = description;
	this.memberOfOrgs = memberOfOrgs;
	this.opinionOfPcs = opinionOfPcs;


	if (typeof memberOfOrgs === "undefined") {
		this.memberOfOrgs = [];
	}

	// Npcs have specially formatted opinions of the Pcs
	if (typeof opinionOfPcs === "undefined") {
		this.opinionOfPcs = 4;
	}
	//Npcs have activities they can perform.
	this.currentActivities = currentActivities;
	if (typeof currentActivities === "undefined") {

		this.currentActivities = [];
	}



}
// Clean up the valueOf() for Npc.
Npc.prototype.markup = function () {


	var ret = '<div class="npc">';
	ret += "<strong>" + this.name + "</strong>";
	ret += "<p>" + this.description + "</p>";
	ret += this.memberOfOrgs;

	ret += "<p>Feels " + NPC_OPINIONS[this.opinionOfPcs] + " towards the PCs.</p>";
	ret += '</div>';
	return ret;
};




/*
Samples / Experiments
var nicholas_rise = new Npc("Nicholas Rise", ["Hell's Orphans", "Mars' Complication"], 0);

var nicholas_journey_home = new Activity(nicholas_rise, "Travel", 3, 1, "Limping and crawling home to Besh'Beya.");
var nicholas_revenge = new Activity(nicholas_rise, "Revenge", 21, 0, "Plotting his nefarious revenge!!");

nicholas_rise.currentActivities = [nicholas_journey_home, nicholas_revenge];

console.log(Activity.constructor);

console.log(nicholas_rise.valueOf());
console.log(nicholas_revenge.valueOf());

var alec_el_cazador = new Npc("Alec El Cazador", ["Spanish Inquisition"], 7);
//console.log(alec_el_cazador.valueOf());

var nofeels = new Npc("No Feels", ["Bobo's Raiders"]);
//console.log(nofeels.valueOf());

var noOrgs = new Npc("No Orgs", [], 4);
//console.log(noOrgs.valueOf());
*/ // End Samples / Experiments



// Do stuff when the page is fully loaded
$(document).ready(function () {
	$("#npcs").animate({
		opacity: "1"
	}, 750);
	/*
	 * Get the data. 
	 * NOTE: malformed JSON will result in a silent error. Use http://www.jsonlint.com/ to ensure the JSON file is error free.

	 */

	// Get All NPCs
	var data = {
		q: 'npcsAll'
	};
	$.getJSON('./getdbdata.php', data, function (data) {
		//console.log("raw data: ", data);

		var ret = "";

		/*
		 * $.each() for looping over a jQuery object
		 * myArray.forEach() for looping over a javascript array (?object too?)
		 * object.property.length to check if an array has > 0 results
		 */

		// Looping over the NPCs
		$.each(data, function (k, v) {
			//console.log("k: ", k); // numeric index
			//console.log("v: ", v); // all the values

			// Create a row div
			$("<div/>", {
				class: "row",
				id: "row-" + k
			}).appendTo("#npcs");


			// Get the Activities for the current NPC.
			var data = {
				q: 'activitiesByNPC',
				npcID: v.npcID
			};
			$.getJSON('./getdbdata.php', data, function (data) {
				//console.log("data", data);


				//$.each(data, function (k, v){
				// Create an NPC.
				var npc = new Npc(v.name, v.description, v.memberOfOrgs, v.opinionOfPcs);
				//console.log("npc", npc);

				// Build the row and columns for this npc description and their activities
				$("<div/>", {
						class: "col-xs-3"
					}).appendTo("#row-" + k)
					.append(npc.markup())
					.parent()
					.append($("<div/>", {
						class: "col-xs-9"
					}));


				//console.log("building activity markup: ", data);
				var activity = "";
				var activityActiveIndicator = "";
				
				// Loop over the Activities for the current NPC.
				data.forEach(function (a) {
					//console.log("a", a);
					var actProgCalc = (a.activityProgress / a.activityDuration).toFixed(2);
					//console.log("actProgCalc " + typeof actProgCalc, actProgCalc);
					var progressType = "";
					if (actProgCalc === 1.00) {
						progressType = "inactive";
					} else if (actProgCalc >= 0.66) {
						progressType = "danger";
					} else if (actProgCalc >= 0.33) {
						progressType = "warning";
					} else if (actProgCalc >= 0.1) {
						progressType = "info";
					} else if (actProgCalc === '0.00') {
						progressType = "inactive";
					}


					//console.log("progressType", progressType);

					var progressPct = (actProgCalc * 100).toFixed(1) + "%";

					//console.log("a.activityActive", a.activityActive);
					// activityToggler is the link/button that makes an activity active or inactive

					var activityTogglerText = "";
					var activityTogglerID = "";
					if (a.activityActive === '1') {
						activityActiveIndicator = 'progress-bar-success';
						activityTogglerText = "Deactivate";
						activityTogglerID = "deactivateActivity";
					} else if (a.activityActive === '0') {
						activityActiveIndicator = 'progress-bar-inactive';
						activityTogglerText = "Activate";
						activityTogglerID = "activateActivity";
					}





					//console.log("activityActiveIndicator: ", activityActiveIndicator);

					activity += '<strong data-activityid="' + a.activityID + '" id="activity-id-' + a.activityID + '" class="activity-type ' + activityActiveIndicator + '">' + a.activityType + '</strong> ';


					activity += '<div class="activity-description">' + a.activityDescription + '</div>';
					activity += ' <div class="activity-toggler" id="' + activityTogglerID + '"><span>(' + activityTogglerText + ')</span></div>';
					activity += ' <div class="activity-sorter"><span class="activity-sort-up glyphicon glyphicon-hand-up"></span> <span class="activity-sort-down glyphicon glyphicon-hand-down"></span></div>';
					activity += ' <div class="activity-delete"><span class="activity-delete-btn glyphicon glyphicon-remove"></span>';
					activity += '<div class="progress"><div class="progress-bar progress-bar-' + progressType + '" style="width:' + progressPct + ';">';
					activity += "Day " + a.activityProgress + " of " + a.activityDuration;
					activity += "</div></div>";
					

				}); // End of forEach loop over activities
				// The "Add new activity" 'link'
				activity += '<div class="activity-add-new">Add new activity</div>';
				$("#row-" + k + " .col-xs-9").append($(activity));


			}); // end Activities loop
		}); // end NPC loop
	}); // end getJSON


	// TEST: WORKS!!
	// It works because I target a 'baked into the HTML, not created via scripts' element.
	/*
	$("#npcs").append('<div class="activityToggler"><span id="jones">Click me</span></div>');
	$(".activityToggler").on("click", "#jones", function(){
		console.log("click Jones!!");
	});
	*/

	// Move the current day forward or back.
	$(".moveDay").click(function () {
		var clickedID = $(this).attr('id');
		var data = {
			q: clickedID
		};
		$.getJSON('./getdbdata.php', data, function (data) {});
		location.reload(true);
	});


	// Testing for activityToggler
	/*
	$("#npcs").on("click", "strong", function(event){
		console.log("click npc", event);
	});
	*/

	// Activate or Deactivate an activity
	$("#npcs").on("click", ".activity-toggler", function (event) {
		console.log("activityToggler: clickey!!!");
		console.log("event: ", event);
		var clickedID = $(this).attr('id');

		// Useful when finding the correct element to get the activity id from.
		//$(this).prev().prev().css({backgroundColor: "#ff00ff"});

		var activityID = $(this).prev().prev().data('activityid');
		console.log("activityID: ", activityID);
		var data = {
			q: clickedID,
			actid: activityID
		};
		$.getJSON('./getdbdata.php', data, function (data) {});
		location.reload(true);

	});

	// Sort an Activity Up
	$("#npcs").on("click", ".activity-sort-up", function (event) {

		// Useful when finding the correct element to get the activity id from.
		//$(this).css({backgroundColor: "#ff00ff"});
		var activityID = $(this).parent().prev().prev().prev().data('activityid');
		var data = {
			q: 'activitySortUp',
			actid: activityID
		};
		$.getJSON('./getdbdata.php', data, function (data) {});
		location.reload(true);
	});
	// Sort an Activity Down
	$("#npcs").on("click", ".activity-sort-down", function (event) {

		// Useful when finding the correct element to get the activity id from.
		//$(this).css({backgroundColor: "#ff00ff"});
		var activityID = $(this).parent().prev().prev().prev().data('activityid');
		var data = {
			q: 'activitySortDown',
			actid: activityID
		};
		$.getJSON('./getdbdata.php', data, function (data) {});
		location.reload(true);
	});

	// Add a new activity form
	$("#npcs").on("click", ".activity-add-new", function(data){
		console.log("add a new activity!!!");
		var newActivityForm = $("<form/>", {class:"activity-add-new-form"});
		newActivityForm.append($("<input/>", {class:"activity-add-new-type", value:"Activity Type"}));
		newActivityForm.append($("<input/>", {class:"activity-add-new-desc", value:"Activity Description"}));
		newActivityForm.append($("<button/>", {class:"activity-add-new-btn"}).text("Add New Activity"));
		console.log("this", $(this));
		console.log("newActivityForm", newActivityForm);
		$(this).after(newActivityForm);
	});
	// Click event handler for the activity-add-new-btn button
	$("#npcs").on("click", ".activity-add-new-btn", function(event){
		event.preventDefault();
		console.log("clicky ad new button!!");
		// Useful when finding the correct element to get the activity id from.
		//$(this).parent().parent().prev().children().children("strong").css({backgroundColor: "#ff00ff"});
		var npc = encodeURI($(this).parent().parent().prev().children().children("strong").text());
		
		var activityType = encodeURI($(this).prev().prev().val());
		var activityDesc = encodeURI($(this).prev().val());
		
		var data = {
			q: 'activityAddNew',
			npc: npc,
			activityDesc: activityDesc,
			activityType: activityType
		};
		$.getJSON('./getdbdata.php', data, function(data){});
		location.reload(true);

	});
	

}); // document.ready
