$(document).ready(function() {
    $('#delivery_charges').select2();
    $('#delivery_dates').datetimepicker({

        format: 'YYYY/MM/DD'

    });
    $('#generateOrder').on('submit', function(e) {
        e.preventDefault();
        var type = $('#typeOrder option:selected').val();

        $.ajax({
            url: '../php/ajax/order.php?type=generate',
            type: 'post',
            data: 'type=' + type,

            success: function(msg) {
                if (type === '1') {

                    alert('Anda Memilih Corporate!');
                    var newLocation = '?p=neworder&trx=' + msg;
                    window.location = newLocation;
                    return false;

                } else {
                    alert('Anda Memilih Personal!');
                    var newLocation = '?p=neworder&trx=' + msg;
                    window.location = newLocation;
                    return false;
                }

            }
        });

    })
})