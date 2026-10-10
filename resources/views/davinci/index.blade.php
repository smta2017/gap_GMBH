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
    <br>
    <br>
    <button id="reloag-data">RELOAD (REFRESH)</button>
    <br>
    <br>

    <table>
      <tbody>
        <tr>
          <td> <span>Jobs count : </span></td>
          <td><span style="color: red; font-weight: bold;" id="jobs-count"> </span></td>
        </tr>
        <tr>
          <td> <span>Total service : </span></td>
          <td><span style="color: red; font-weight: bold;" id="total-service"> </span></td>
        </tr>
        <tr>
          <td> <span>Oldest service date : </span></td>
          <td><span style="color: red; font-weight: bold;" id="lowest-date"> </span></td>
        </tr>
        <tr>
          <td> <span>Need to clean count : </span></td>
          <td><span style="color: red; font-weight: bold;" id="need-to-clean-cound"> </span></td>
        </tr>
      </tbody>
    </table>


  </div>

  <br>

  <div id="loader" style="display: none;">
    <img src="images/loader.gif" alt="loader"> Loading...
  </div>


  <br>
  <input type="text" id="booking_code" name="booking_code" placeholder="spezifischen Buchungscode" style="width: 200px !important">
  <br>
  <br>

  <button id="import-data">Import davinci packages (AJAX)</button>


  <br>
  <br>
  <br>

  <button id="cleanup">Clean davinci packages</button>
  <br>
  <br>
  <br>


  <hr>

  <h4>Deletetion</h4>

  <button id="delete-pacjage">Delete davinci packages</button>





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
        let bookcode = $('#booking_code').val();

        if (bookcode == '') {
          if (!confirm('Are you sure do you want to import all data')) {
            return;
          }
        }
        $.ajax({
          url: '/import-davinci-packages',
          method: 'POST',
          data: {
            booking_code: bookcode,
          },
          beforeSend: showLoader, // Show the loader before the request is sent
          complete: hideLoader, // Hide the loader after the request is completed (success or error)
          success: function(response) {
            if (response.status == true) {
              $('#msg').html('jobs added : ' + response.jobs_count);
            } else {
              $('#msg').html(response.message);
            }
          },
          error: function(xhr, status, error) {
            console.log('Error occurred while calculating the difference: ' + error);
          }
        });
      });


      $('#delete-pacjage').on('click', function() {
        let bookcode = $('#booking_code').val();

        if (!confirm('Are you sure do you want to delete data for ' + bookcode)) {
          return;
        }

        $.ajax({
          url: '/davinci-delete-package',
          method: 'DELETE',
          data: {
            booking_code: $('#booking_code').val(),
          },
          beforeSend: showLoader, // Show the loader before the request is sent
          complete: hideLoader, // Hide the loader after the request is completed (success or error)
          success: function(response) {
            if (response.status == true) {
              $('#msg').html('sent request : ' + response.jobs_count);
            } else {
              $('#msg').html(response.message);
            }
          },
          error: function(xhr, status, error) {
            console.log('Error occurred while calculating the difference: ' + error);
          }
        });
      });

      $('#cleanup').on('click', function() {
        $.ajax({
          url: '/clean-davinci-packages',
          method: 'POST',
          beforeSend: showLoader, // Show the loader before the request is sent
          complete: hideLoader, // Hide the loader after the request is completed (success or error)
          success: function(response) {
            if (response.status == true) {
              $('#msg').html(response.message);
            } else {
              $('#msg').html(response.message);
            }
          },
          error: function(xhr, status, error) {
            console.log('Error occurred while calculating the difference: ' + error);
          }
        });
      });

      function get_info() {
        $.ajax({
          url: '/get-import-info',
          method: 'POST',
          success: function(response) {
            $('#jobs-count').html(response.info[0]);
            $('#total-service').html(response.info[1]);
            $('#lowest-date').html(response.info[2]);
            $('#need-to-clean-cound').html(response.info[3]);
            $('#server-time').html(response.info[4]);
          },
          error: function(xhr, status, error) {
            console.log('Error occurred while calculating the difference: ' + error);
          }
        });
      };


      $('#reloag-data').on('click', function() {
        get_info();
      });

      get_info();

      setInterval(() => {
        get_info();
      }, 60000);

    })
  </script>

</body>

</html>