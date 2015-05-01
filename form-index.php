<html>
  <head>




  </head>
  <body>

    <button id="mybutton1" value="1">Get 1Sample Data</button>
    <button id="mybutton2" value="2">Get 2 Sample Data</button>

    <div id="data-holder"></div>

    <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="js/underscore-min.js"></script>

    <script>
      function getData(amt) {
        console.log("amt in getData(): ", amt);
        if (amt.length == 0) {
          console.log("premature return in getData()");
          $("#data-holder").html = "";
          return;
        } else {
          var xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
              console.log("responseText: ", xmlhttp.responseText);
              $("#data-holder").html(xmlhttp.responseText);
            }
          }
          xmlhttp.open("GET", "form-get-data.php?amt=" + amt, true);
          xmlhttp.send();
        }
      }
    </script>

    <script>
      var myButton = $("#mybutton1, #mybutton2");
      myButton.click(function(){
        console.log("I been clicked, bobo!");
        console.log("value in click handler", this.value);
        getData(this.value);
      });
    </script>

  </body>
</html>


