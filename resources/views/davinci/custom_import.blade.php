<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Davinci Manual Import Packages</title>
</head>

<body>
  <h1 style="color: #3f51b5;">Davinci Manual Import Packages Interface</h1>



  <div class="alert alert-success">
    <h3 style="color: green;" id="msg"> </h3>
  </div>

  <br>

  <div class="alert alert-success">
    <h2 id="server-time"></h2>
    <table>
      <tbody>
        <tr>
          <td> <span>BookingCode : </span></td>
          <td> <input type="text" value="LIS20001IP" id="bookingCode" name="bookingCode"> </td>
        </tr>
        <tr>
          <td> <span>duration_request : </span></td>
          <td> <input type="text" value="7" id="duration_request" name="duration_request"> </td>
        </tr>
        <tr>
          <td> <span>fromDate : </span></td>
          <td> <input type="text" value="2024-08-18" id="fromDate" name="fromDate"> </td>
        </tr>
        <tr>
          <td> <span>toDate : </span></td>
          <td> <input type="text" value="2024-08-25" id="toDate" name="toDate"> </td>
        </tr>
        <tr>
          <td> <span>adult : </span></td>
          <td> <input type="text" value="2" id="adult" name="adult"> </td>
        </tr>

      </tbody>
    </table>
  </div>

  <br>

  <div id="loader" style="display: none;">
    <img src="images/loader.gif" alt="loader"> Loading...
  </div>


  <br>

  <button id="import-data">Import davinci packages (AJAX)</button>


  <br>
  <br>
  <br>
  <p id="main-par"></p>

  <br>

  <script src="js/jquery.min.js"></script>
  <script>
    $(document).ready(function() {
      // Function to show the loader
      function showLoader() {
        $('#loader').show();
      }

      // Function to hide the loader
      function hideLoader() {
        $('#loader').hide();
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $('#import-data').on('click', function() {
        $.ajax({
          url: '/custom-import-packages',
          method: 'POST',
          data: {
            bookingCode: $('#bookingCode').val(),
            duration_request: $('#duration_request').val(),
            fromDate: $('#fromDate').val(),
            toDate: $('#toDate').val(),
            adult: $('#adult').val(),
          },
          beforeSend: showLoader, // Show the loader before the request is sent
          complete: hideLoader, // Hide the loader after the request is completed (success or error)
          success: function(response) {
            console.info(response);
             $("#main-par").html(response);
          },
          error: function(xhr, status, error) {
            console.log('Error occurred while calculating the difference: ' + error);
          }
        });
      });



    })
  </script>

</body>

</html>