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
      <h1>Add data to RPGAid</h1>
      <div class="row">
        <h2>Values</h2>
        Add value to template <select id="addValueToTemplate"></select> called <input type="text" id="newvalue" />
        <button id="addValue">Add value</button>
      </div>
      <div class="row">
        <h2>Templates</h2>
        Add new template called <input type="text" id="newtemplate" />
        <button id="addtemplatebutton">Add template</button>
      </div>
      <p>
        <a href="index.php">Generate data page</a>
      </p>
    </div>

    <div id="msg-validate"></div>

    <!-- recursive random generator script -->
    <script type="text/javascript" src="js/generator.js"></script>
    <!-- fetch the data from the database via php/ajax -->
    <script type="text/javascript" src="js/getdata.js"></script>
    <!-- send data to the database -->
    <script type="text/javascript" src="js/putdata.js"></script>


    <script>

      // Get all the data and set gen_data to the proper format
      // getData is in the getdata.js file
      getData({
        dataset:'all'
      });
      // getData calls workWithGenData();
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
        // Values
        var addValueToTemplate = $("#addValueToTemplate");
        var newValue = $("#newvalue");
        var addValueButton = $("#addValue");
        var msgValidate = $("#msg-validate");

        // Templates
        var newTemplate = $("#newtemplate");
        var addTemplateButton = $("#addtemplatebutton");


        /********************************************************************************************************
         * Set up our Event Handlers
         ********************************************************************************************************/
        // Add a value to a template
        addValueButton.click(function(){
          $("#msg-validate").text("");
          if(newValue.val()){ // The value text field has something in it
            // Add and remove CSS classes for styling
            msgValidate.removeClass("invalid");
            msgValidate.addClass("valid");

            // addValue calls the adddata-action.php file and insrerts values into the database
            addValue(newValue.val(), addValueToTemplate.val());

          }else{ // Invalid
            msgValidate.text("Can't be blank");
            msgValidate.removeClass("valid");
            msgValidate.addClass("invalid showme");
            setTimeout(function(){
              $("#msg-validate").removeClass("showme");
            }, 2500);
          }
        });
        // Trigger the addValue button when the user presses the 'enter' key'
        newValue.keyup(function(e){
          if(e.keyCode === 13){
            addValueButton.trigger('click');
          }
        });

        // Add a new template
        addTemplateButton.click(function(){
          console.log("click addTemplateButton");
          if(newTemplate.val()){ // The template text field has something in it
            // Add and remove CSS classes for styling
            msgValidate.removeClass("invalid");
            msgValidate.addClass("valid");
            // addTemplate calls the adddata-action.php file and insrerts template into the database
            addTemplate(newTemplate.val());
          }else{ // The template text field is empty
            msgValidate.text("Can't be blank");
            msgValidate.removeClass("valid");
            msgValidate.addClass("invalid showme");
            setTimeout(function(){
              $("#msg-validate").removeClass("showme");
            }, 2500);
          }
        });

        // Trigger the addValue button when the user presses the 'enter' key'
        newTemplate.keyup(function(e){
          if(e.keyCode === 13){
            addTemplateButton.trigger('click');
          }
        });

        /********************************************************************************************************
         * Set up our UI
         *  - Populating form elements with dynamic data
         ********************************************************************************************************/

        // Apply the options to the "items of type ____" field.
        $.each(formHelper, function(datakey, string){
          //console.log(datakey, string);
          addValueToTemplate
          .append($("<option></option>")
          .attr("value", datakey)
          .text(string));
        });

      }; // End of workWithGenData

      /********************************************************************************
       * Perform the work of adding data to the database
       *  - This is shameful. I'm violating the DRY principle in a criminal way.
       *  - addValue and addTemplate need to be one function addData() or something.
       ********************************************************************************/


      /*
       * Add the new value to the database
       * @param newValue string The actual value to add
       * @param addToTemplate string The template to add the value to
       */
      function addValue(newValue, addToTemplate){
        //console.log("newValue in addValue()", newValue);
        //console.log("addTo in addValue()", addTo);
        //console.log("Add " + newValue + " to " + addTo);
       
        var params = {};
        params.newValue = newValue;
        params.addToTemplate = addToTemplate;
        // putData is in the putdata.js file
        putData(params);
        $("#newvalue").focus();
      }

      /*
       * Add the new template to the database
       * @param newTemplate string The actual template string to add
       */
      function addTemplate(newTemplate){
        //console.log("newValue in addValue()", newValue);
        //console.log("addTo in addValue()", addTo);
        //console.log("Add " + newValue + " to " + addTo);

        var params = {};
        params.newTemplate = newTemplate;
        // putData is in the putdata.js file
        putData(params);
        $("#newtemplate").focus();
        $("#newtemplate").val("");
      }

      /********************************************************************************
       * Let the user know data has been added.
       ********************************************************************************/
      function workWithAddedData(data){
        //console.log("data in workWithAddedData", data);
        $("#newvalue").val("");
        $("#msg-validate")
        .addClass("valid showme")
        .text("'" + data + "'" + " has been added.");

        setTimeout(function(){
          $("#msg-validate").removeClass("showme");
        }, 2500);
      }

    </script>

  </div>
</body>
</html>


