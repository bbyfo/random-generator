// JavaScript Document

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

					activity += '<div class="progress"><div class="progress-bar progress-bar-' + progressType + '" style="width:' + progressPct + ';">';
					activity += "Day " + a.activityProgress + " of " + a.activityDuration;
					activity += "</div></div>";
					

				}); // End of forEach loop over activities
				// The "Add new activity" 'link'
				activity += '<div class="activity-add-new">Add new activity</div>';
				$("#row-" + k + " .col-xs-9").append($(activity));


			}); // end Activities loop
		}); // end NPC loop
	