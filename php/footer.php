<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Logs</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      In computing, a log file is a file that records either events that occur in an operating system or other software runs,[1] or messages between different users of a communication software.[citation needed] Logging is the act of keeping a log. In the simplest case, messages are written to a single log file.

A transaction log is a file (i.e., log) of the communications (i.e., transactions) between a system and the users of that system,[2] or a data collection method that automatically captures the type, content, or time of transactions made by a person from a terminal with that system.[3] For Web searching, a transaction log is an electronic record of interactions that have occurred during a searching episode between a Web search engine and users searching for information on that Web search engine.

Many operating systems, software frameworks, and programs include a logging system. A widely used logging standard is syslog, defined in Internet Engineering Task Force (IETF) RFC 5424). The syslog standard enables a dedicated, standardized subsystem to generate, filter, record, and analyze log messages. This relieves software developers of having to design and code their own ad hoc logging systems.[4][5][6]
      </div>
    </div>
  </div>
</div>
</main>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?=URL?>assets/vendors/dll/jquery.min.js"></script>
    <script type="text/javascript" src="<?=URL?>assets/vendors/datetime-picker4/js/moment_2_9_0.js"></script>
    <script src="<?=URL?>assets/vendors/parsley/parsley.min.js"></script>
    <script src="<?=URL?>assets/vendors/dll/popper.min.js"></script>
    <script src="<?=URL?>assets/js/bootstrap.min.js"></script>
    <script src="<?=URL?>assets/vendors/dll/holder.min.js"></script>
    <script src="<?=URL?>assets/vendors/dll/offcanvas.js"></script>
    <script type="text/javascript" src="<?=URL?>assets/vendors/dataTables/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?=URL?>assets/vendors/dataTables/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="<?=URL?>assets/vendors/select2/select2.min.js"></script>
    
    <script type="text/javascript" src="<?=URL?>assets/vendors/datetime-picker4/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="<?=URL?>assets/vendors/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="<?=URL?>assets/vendors/jquery.countdown-2.2.0/jquery.countdown.min.js"></script>


    <script src="<?=URL?>assets/js/custom.js"></script>
    <?php if($menu == 'corporate'){ ?>
    <script src="<?=URL?>assets/js/modul/corporate.js"></script>
    <?php } if($menu == 'order'){ ?>
    <script src="<?=URL?>assets/js/modul/order.js"></script>
    <?php } if($menu == 'payment'){ ?>
    <script src="<?=URL?>assets/js/modul/kas.js"></script>
    <?php } if($menu == 'management'){ ?>
        <script src="<?=URL?>assets/js/modul/management.js"></script>
    <?php } if($menu == 'kurir'){ ?>
        <script src="<?=URL?>assets/js/modul/kurir.js"></script>
    <?php } if($menu == 'production'){?>
        <script src="<?=URL?>assets/js/modul/stocks.js"></script>
    <?php } if($menu == 'bd'){ ?>

        <script src="<?=URL?>assets/vendors/krajeee/js/fileinput.js" type="text/javascript"></script>
        <script src="<?=URL?>assets/vendors/krajeee/js/locales/fr.js" type="text/javascript"></script>
        <script src="<?=URL?>assets/vendors/krajeee/js/locales/es.js" type="text/javascript"></script>
        <script src="<?=URL?>assets/vendors/krajeee/themes/explorer-fa/theme.js" type="text/javascript"></script>
        <script src="<?=URL?>assets/vendors/krajeee/themes/fa/theme.js" type="text/javascript"></script>
        <script src="<?=URL?>assets/vendors/lightbox/ekko-lightbox.js" type="text/javascript"></script>

        <script src="<?=URL?>assets/js/modul/product.js"></script>
        <script type="text/javascript">
            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox();
            });
        </script>
    <?php } ?>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
    $('#clock').countdown('2018/05/30').on('update.countdown', function(event) {
    var $this = $(this).html(event.strftime(''
        + '<span>%-w</span> week%!w '
        + '<span>%-d</span> day%!d '
        + '<span>%H</span> hr '
        + '<span>%M</span> min '
        + '<span>%S</span> sec'));
    }); 

</script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>-->
<!--<script>-->
<!--    var ctx = document.getElementById("myChart");-->
<!--    var myChart = new Chart(ctx, {-->
<!--        type: 'line',-->
<!--        data: {-->
<!--            labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],-->
<!--            datasets: [{-->
<!--                data: [15339, 21345, 18483, 24003, 23489, 24092, 12034],-->
<!--                lineTension: 0,-->
<!--                backgroundColor: 'transparent',-->
<!--                borderColor: '#007bff',-->
<!--                borderWidth: 4,-->
<!--                pointBackgroundColor: '#007bff'-->
<!--            }]-->
<!--        },-->
<!--        options: {-->
<!--            scales: {-->
<!--                yAxes: [{-->
<!--                    ticks: {-->
<!--                        beginAtZero: false-->
<!--                    }-->
<!--                }]-->
<!--            },-->
<!--            legend: {-->
<!--                display: false,-->
<!--            }-->
<!--        }-->
<!--    });-->
<!--</script>-->
</body>
</html>
