// This script retrieves the data via ajax/json from the php file
function putData(params) {
//console.log("params in putData",params);
  $.getJSON('adddata-action.php', params)
  .done(function(data) {
    //console.log("data in putData() after json call", data);
    workWithAddedData(data);
  });
  
}



