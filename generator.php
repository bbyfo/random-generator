<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="js/underscore-min.js"></script>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>



  </head>
  <body>


    Generate <input type="text" size="5" id="numgen" value="3" /> items of type
    <select id="typegen">
    </select>
    <button id="generate">Generate</button>
    <button id="genclear">Clear</button>



    <div id="data-holder"></div>



    <script type="text/javascript" src="js/generator.js"></script>
    <script type="text/javascript" src="js/getdata.js"></script>



    <script>

      // Get all the data and set gen_data to the proper format
      getData({
        dataset:'all'
      });
      // gen_data will not have the data yet.  I know, I know, it bites.  getOverIt().

      // Clear the text in the data holder area
      $("#genclear").click(function(){
        $("#data-holder").text("");
      });

      // Receive the data and do something with it.
      function workWithGenData(gen_data){

        // Apply the options to the "items of type ____" field.
        // We want to get the formHelper out of the gen_data variable because it could cause problems later on
        // if we try to loop over the properties.
        var formHelper = gen_data.formHelper;
        delete gen_data.formHelper;

        var typeGenField = $("#typegen");
        var numGenField = $("#numgen");

        var generateButton = $("#generate");
        var holder = $("#data-holder");

        //console.log("formHelper: ", formHelper);

        $.each(formHelper, function(datakey, string){
          //console.log(datakey, string);
          typeGenField
          .append($("<option></option>")
          .attr("value", datakey)
          .text(string));
        });



        // Perform the generation of items per the config of the form
        generateButton.click(function(){
          var numGenValue = numGenField.val();
          var typeGenValue = typeGenField.val();
          var presetsGenValue = {};

          // Take the values in the form and generate some items.
          var genlist = generate_list(typeGenValue, numGenValue, presetsGenValue);
          // Take the generated items and spit it out to the screen.
          for(var i=0;i<genlist.length;i++){
            holder.append("<p>" + genlist[i] + "</p>");
          };
        });
      };
    </script>


  </body>
</html>


