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

  <!-- Custom RPGAid CSS -->
  <link rel="stylesheet" href="css/rpgaid.css">

  <title>Quick Generate - RPGAid</title>
</head>
<body>
  <div class="container-fluid">
    <!-- PHP Code Begin -->
    <?php
      // Connect to database server
    $dbhost = (getenv('MYSQL_HOST') ? getenv('MYSQL_HOST') : "localhost");
    $dbuser = (getenv('MYSQL_USER') ? getenv('MYSQL_USER') : "root");
    $dbpwd = (getenv('MYSQL_PASSWORD') ? getenv('MYSQL_PASSWORD') : "root");
    $dbname = (getenv('MYSQL_DATABASE') ? getenv('MYSQL_DATABASE') : "rpgaid");
    $mysqli = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);
      // Generate the SQL
    $campaigns_sql = "SELECT DISTINCT cid, campaign_name, campaign_desc
    FROM campaigns
    ORDER BY campaign_name";
      // Execute the SQL query
    $campaigns_results = $mysqli->query($campaigns_sql);

    $chosen_campaigns = array();
    // Determine if any checkboxes should be pre-checked
    if(!isset($_GET['campaign'])){
      $chosen_campaigns[] = 2;
      var_dump($_GET);
    }else{
      echo "begin else<pre>";
      //var_dump($chosen_campaigns);
      var_dump($_GET);
      //var_dump($_GET['campaign']);
      echo "</pre>end else<br />";
      // */
     foreach($_GET as $k => $v){
      echo $v . "<br />";
      foreach($v as $campaign){
        $chosen_campaigns[] = $campaign;
      }
      
     }
    }
   
       

      ?>
      <!-- PHP Code End -->
      
      <div class="row" id="campaign-chooser">
        <strong>Campaigns to use</strong>
        <?php
          // Use the query results
        
        while ($row = $campaigns_results->fetch_array(MYSQLI_ASSOC)) {
          $coChecked = "";
          $coCheckedClass = "";
            //var_dump($row);
          $cid = $row['cid'];
          $campaign_name = $row['campaign_name'];
          if (in_array($cid, $chosen_campaigns)){
            $coChecked = 'checked';
            $coCheckedClass = "co-checked";
          } else if(is_null($chosen_campaigns) && $cid == 2){
            $coChecked = 'checked';
            $coCheckedClass = "co-checked";
          }
          echo "[<span class='$coCheckedClass'> <input type='checkbox' name='campaign[]' $coChecked value='$cid' class='campaign-checkbox' /> $campaign_name</span>] ";
        }
        ?>
        <button class="btn btn-secondary" type="button" id="set-campaigns">Set Campaigns</button>
        <span id="campaign-select-options">Select <span class="faux-link" data-co="all">all</span> / <span class="faux-link" data-co="none">none</span> </span>
      </div>
      <div class="row">
        <button id="generate" class="btn btn-primary">Generate</button> <input type="text" size="5" id="numgen" value="3" /> items of type
        <select id="typegen">
        </select>
        <a href="adddata.php">Add some new values</a>

      </div>

      <div class="row">
        <div class="col-xs-8">
          <div id="data-holder">
            <h2>Generated data</h2>
            <button id="genclear">Clear</button>
            <button id="regen">Regenerate</button>
            <div id="data-content"></div>
          </div>
        </div>
        <div class="col-xs-4">
          <div id="preset-holder">
            <h2>Template code</h2>
            <div id="preset-examples">Template code: <ol></ol></div>
          </div>
        </div>
      </div>

      <!-- recursive random generator script -->
      <script type="text/javascript" src="js/generator.js"></script>
      <!-- fetch the data from the database via php/ajax -->
      <script type="text/javascript" src="js/getdata.js"></script>



      <script>

        // Get all the data and set gen_data to the proper format.
        // getData() is located in /js/getdata.js
        // A campaign of '2' means "all" templates will be retrieved.
        // We start at 2 instead of 0 or 1 because we use checkboxes
        // in the gui and 0 = unchecked and 1 = checked...which screws
        // with things.  Why fix code when we can modify data to suit
        // our needs?  *sigh*

        /****** Getting query parameters BEGIN *******/
        
        var qs = window.location.search;
        
        var post_split = qs.split("&");
        
        var campaign = [];
        var daLen = post_split.length;
        for (var i = 0; i < daLen; i++) {
         
          campaign.push(post_split[i].substr(post_split[i].indexOf("=")+1));
        }
        
        /****** Getting query parameters END *******/

        // Set default campaign if none have been chosen.
        if(campaign.length == 1 && campaign[0] == ""){
          campaign = ["2"];
        } 

        getData({
          campaign: campaign
        });
        // gen_data will not have the data yet.  
        // I know, I know, it bites.  getOverIt().

        // Receive the data and do something with it.
        function workWithGenData(gen_data){

          /*********************************************************************
           * We want to get the formHelper out of the gen_data variable because
           * it could cause problems later on if we try to loop over the
           * properties.
           ********************************************************************/
           var formHelper = gen_data.formHelper;
           delete gen_data.formHelper;

          /*********************************************************************
           * Set up our Variables
           ********************************************************************/
           var typeGenField = $("#typegen");
           var numGenField = $("#numgen");
           var generateButton = $("#generate");
           var dataHolderContent = $("#data-content");
           var presetInput = $("#presets-input");
           var presetExamples = $("#preset-examples");
           var clearButton = $("#genclear");
           var regenButton = $("#regen");
           var setCampaigns = $("#set-campaigns")

          /********************************************************************
           * Set up our Event Handlers
           *******************************************************************/

          // Set Campaigns
          // This inspects the checkboxes and creates a query string based
          // on the checked boxes.
          setCampaigns.click(function(){
            var reloadTo = "./index.php?";
            var campaignQueryString = $('input[name="campaign[]"]:checked').serialize();
            window.location.href = reloadTo + campaignQueryString;
          });

          // Item of type select list
          typeGenField.change(function(){
            var optionSelected = $(this).find("option:selected");
            presetsUI(optionSelected);
          });

          // Perform the generation of items per the config of the form
          generateButton.click(function(){
            var numGenValue = numGenField.val();
            var typeGenValue = typeGenField.val();
            
            var presetsGenValue = (presetInput.val() ? presetInput.val() : {});

            // Ugh, really James?  Get rid of this stuff
            presetsGenValue.pc = ["Andrelion", "Ossian", "Akiva", "Hash", "Isilme "];

            // Take the values in the form and generate some items.
            var genlist = generate_list(typeGenValue, numGenValue, presetsGenValue);

            // Take the generated items and spit it out to the screen.
            for(var i=0;i<genlist.length;i++){

              // Some types can use some extra processing.  Call the functions here.
              switch(typeGenValue){
                case 'npc_name':
                var post_randomized = "",
                name = genlist[i];
                
                post_randomized = randomizeVowels(name);

                genlist[i] = post_randomized;
                break;
              }
              dataHolderContent.append("<p>" + genlist[i] + "</p>");
            };
          });

          // Clear the text in the data holder area
          clearButton.click(function(){
            dataHolderContent.text("");
          });
          // Clear and repopulate the text in the data holder area
          regenButton.click(function(){
            dataHolderContent.text("");
            generateButton.trigger('click');
          });

          /********************************************************************
           * Set up our UI
           *  - Populating form elements with dynamic data
           *  - Creating form areas
           *******************************************************************/

          // Select All/None campaigns
          $("#campaign-select-options .faux-link").click(function(){
            // co = campaign option
            var co = $(this).data("co");
            var coBoxes = $('input[name="campaign[]"]');
            for (i = 0; i < coBoxes.length; i++){
              if (co == "all"){
                $(coBoxes[i]).prop('checked', true);
              }else if (co == "none"){
                $(coBoxes[i]).prop('checked', false);
              }
            }

          });

          // Apply the options to the "items of type ____" field.
          $.each(formHelper, function(datakey, string){
            typeGenField
            .append($("<option></option>")
              .attr("value", datakey)
              .text(string));
          });

          // Set up the presets UI
          function presetsUI(optionSelected){
            var valueSelected = optionSelected.val();
            // var textSelected = optionSelected.text(); // ??unused??
            var presetValues = gen_data[valueSelected];
            
            var presetValuesArray = [];
            for (prop in presetValues) {
              presetValuesArray.push(presetValues[prop]);
            }
            presetValuesArray.sort();
            
            var presetFormContainer = $("#preset-form");

            presetValues = presetValuesArray.reduce(function(o,v,i){
              o[i] = v;
              return o;
            }, {});

            // Clear out the list items from the examples list
            $(presetExamples).find("li").remove();
            // Show (up to) the first 3 elements of the values for the currently selected "type".
            var i = 1;
            var maxShow = 99;
            for(var prop in presetValues){
              if(i <= maxShow){
                $(presetExamples).find("ol").append("<li>" + presetValues[prop] + "</li>");
                i++;
              }else{
                break;
              }
            }
          }
          // When the page loads, set up the presets UI
          presetsUI($(typeGenField.find("option:selected")));

        }; // End of workWithGenData

        // Take a name and return it with the vowels randomized.
        // The idea here is to multiply the number of name varients.
        function randomizeVowels(name){
          // chars = the characters of the original name
          // vowels = array to choose replacement vowles from
          // retNameArray = an Array we use to assemble the modified name
          // retNameString = the string we return
          // patt = the regular expression pattern
          var chars = name.split(''),
          vowels = ["a", "e", "i", "o", "u"],
          retNameArray = new Array,
          retNameString = "",
          patt = /^[aeiou]$/i;
          // Loop over the array and look for vowels.  If we find a vowel, replace
          // it with a random vowel.  Replacing with the same vowel is perfectly fine.
          for(i=0;i<chars.length;i++){
            if(patt.test(chars[i])){ // We have a vowel
              var vowelOriginal = chars[i];
              var vowelNew = vowels[Math.floor(Math.random()*vowels.length)];
              retNameArray.push(vowelNew);
            }else{ // No vowel
              retNameArray.push(chars[i]);
            }
          }
          retNameString = retNameArray.join("");
          var retNameStringCap = capitalizeFirstLetter(retNameString);
          return retNameStringCap;
        }

        // Names should always be capitalized.
        function capitalizeFirstLetter(string) {
          return string.charAt(0).toUpperCase() + string.slice(1);
        }

      </script>

    </div>
  </body>
  </html>


