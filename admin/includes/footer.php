      <!-- Core JS files -->
      <script type="text/javascript" src="../assets/js/plugins/loaders/pace.min.js"></script>
      <script type="text/javascript" src="../assets/js/core/libraries/jquery.min.js"></script>
      <script type="text/javascript" src="../assets/js/core/libraries/bootstrap.min.js"></script>
      <script type="text/javascript" src="../assets/js/plugins/loaders/blockui.min.js"></script>
      <!-- /core JS files -->

      <!-- /theme JS files -->
      <script type="text/javascript">
          if ('serviceWorker' in navigator) {
              navigator.serviceWorker.register('/service-worker.js')
                  .then(function(registration) {
                      console.log("OCC COOPERATIVE Service Worker Registered:", registration.scope);
                  })
                  .catch(function(error) {
                      console.log("Service Worker Registration Failed:", error);
                  });
          }

          $(window).on('load', function() {
              $("#spinner_div").fadeOut("slow");
          });

          // $(function() {  
          //     // Safely pass PHP variable to JS
          //     var session = <?php echo json_encode($check_session); ?>;

          //     if (!session) {
          //         // Only redirect if session is empty
          //         window.location = '../index.php';
          //     }
          // });

          // Allow numbers only
          function numbersonly(e) {
              var unicode = e.charCode ? e.charCode : e.keyCode;
              if (unicode != 8) { // backspace
                  if (unicode < 48 || unicode > 57) // not a number
                      return false;
              }
          }


          $('.filterme').keypress(function(eve) {
              if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
                  eve.preventDefault();
              }
          });

          $('.filterme').keyup(function(eve) {
              if ($(this).val().indexOf('.') == 0) {
                  $(this).val($(this).val().substring(1));
              }
          });
      </script>