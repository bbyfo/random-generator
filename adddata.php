<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/underscore-min.js"></script>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    <!-- Custom RPGAid CSS -->
    <link rel="stylesheet" href="css/rpgaid.css">

    <title>Add Data - RPGAid</title>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
        Add new <select id="typegen"></select> called <input type="text" id="newvalue" />
        <button id="addit">Add it</button><span id="msg-validate"></span>
      </div>
      <a href="quickgen.php">Generate some data</a>
    </div>

    <!-- recursive random generator script -->
    <script type="text/javascript" src="js/generator.js"></script>
    <!-- fetch the data from the database via php/ajax -->
    <script type="text/javascript" src="js/getdata.js"></script>
    <!-- send data to the database -->
    <script type="text/javascript" src="js/putdata.js"></script>


    <script>

      // Get all the data and set gen_data to the proper format
      getData({
        dataset:'all'
      });
      // gen_data will not have the data yet.  I know, I know, it bites.  getOverIt() #suckItUpButterCup.



      // Receive the data and do something with it.
      function workWithGenData(gen_data){
        //console.log("gen_data", gen_data);
        /********************************************************************************************************
         * We want to get the formHelper out of the gen_data variable because it could cause problems later on
         * if we try to loop over the properties.
         ********************************************************************************************************/
        var formHelper = gen_data.formHelper;
        delete gen_data.formHelper;

        /********************************************************************************************************
         * Set up our Variables
         ********************************************************************************************************/
        var typeGenField = $("#typegen");
        var newValue = $("#newvalue");
        var addItButton = $("#addit");
        var msgValidate = $("#msg-validate");
        /********************************************************************************************************
         * Set up our Event Handlers
         ********************************************************************************************************/
        addItButton.click(function(){
          $("#msg-result").text("");
          if(newValue.val()){
            msgValidate.removeClass("invalid");
            msgValidate.addClass("valid");
            addIt(newValue.val(), typeGenField.val());
          }else{
            msgValidate.text("Can't be blank");
            msgValidate.addClass("invalid");
            msgValidate.removeClass("valid");
            msgValidate.position({
              my: "left top",
              at: "left bottom",
              of: "#newvalue"
            });
          }
        });

        newValue.keyup(function(e){
          if(e.keyCode === 13){
            addItButton.trigger('click');
          }
        });

        /********************************************************************************************************
         * Set up our UI
         *  - Populating form elements with dynamic data
         ********************************************************************************************************/

        // Apply the options to the "items of type ____" field.
        $.each(formHelper, function(datakey, string){
          //console.log(datakey, string);
          typeGenField
          .append($("<option></option>")
          .attr("value", datakey)
          .text(string));
        });

      }; // End of workWithGenData

      function addIt(newValue, addTo){
        //console.log("newValue in addIt()", newValue);
        //console.log("addTo in addIt()", addTo);
        //console.log("Add " + newValue + " to " + addTo);
       
        var params = {};
        params.newValue = newValue;
        params.addTo = addTo;
        putData(params);
        $("#newvalue").focus();
      }

      function workWithAddedData(data){
        //console.log("data in workWithAddedData", data);
         $("#newvalue").val("");
        $("#msg-validate")
        .addClass("valid")
        .text("'" + data + "'" + " has been added.")
        .position({
          my: "left top",
          at: "left bottom",
          of: "#newvalue"
        });
      }

    </script>

  </div>
</body>
</html>


