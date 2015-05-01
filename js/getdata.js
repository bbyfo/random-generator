/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// This script retrieves the data via ajax/json from the php file
function getData(params) {

  $.getJSON('get-data-as-object.php', params)
  .done(function(data) {

    gen_data = data;
    // In the .done method, we know that there has been a successful trip to get the data.
    // We must call some other function and pass in the data.
    workWithGenData(gen_data);
  });
  
}



