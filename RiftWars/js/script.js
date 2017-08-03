// JavaScript Document

$( document ).ready(function() {
	/**
	* It'd be nice to learn how to refresh just a certain div.
	*/
	
	$(".talent-container").draggable({
		cursor : "crosshair",
		stack  : ".talent-container",
		revert : function(dropped){
			console.log("dropped: ", dropped);
		}
	});
	/*
	$(".talent").draggable({
		stack  : ".talent",
	});
	*/   
	
	$(".talent-container").droppable({
		drop: function(event, ui){
			console.log("Dropped!");
			//console.log("event: ", event);
			//console.log("ui: ", ui);
			//console.log("ui.helper: ", ui.helper);
			//console.log("this: ", this);
			//ui.draggable.css({"background-color":"lime"});
			//$(this).children(".talent").css({"background-color":"blue"});

			
			//console.log("ui.draggable", ui.draggable);
			var newTalent = ui.draggable;
			
			
			var oldTalent = $(this).find(".talent");
			
			var newTalentTitle = newTalent.find(".talent-title").text();
			var newTalentText  = newTalent.find(".talent-desc").html();
			var newTalentCostNumber = oldTalent.closest(".row").data("row-cost");
			console.log("newTalentCostNumber", newTalentCostNumber);
			var newTalentCostText = "COST " + newTalentCostNumber;
			var newTalentCost  = newTalent.find(".talent-cost").text(newTalentCostText).text();
			console.log("newTalentCost", newTalentCost);
			
			//console.log(oldTalent.closest(".row").css({"background-color":"lime"}));
			//console.log(oldTalent.closest(".row").data("row-cost"));
			//console.log("newTalent: ", newTalent);
			//console.log("newTalentTitle: ", newTalentTitle);
			
			
			
			oldTalent.children(".talent-title").text(newTalentTitle);
			oldTalent.children(".talent-desc").html(newTalentText);
			oldTalent.children(".talent-cost").text(newTalentCost);
			oldTalent.closest(".talent-container.placeholder").removeClass("placeholder");
			
			//oldTalent.css({"background-color":"blue"});
			//oldTalent.find(".talent-cost").text("COST " . );
			
			newTalent.remove();
			
		}
	});
	
  console.log("Ready!");
});