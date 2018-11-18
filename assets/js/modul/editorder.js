function editcardmsg() {
    $('#card_from').attr('readonly', false);
    $('#card_to').attr('readonly', false);
    $('#card_isi').attr('readonly', false);
    $('.btn-msg').removeClass('hidden');
}

function editpenerima() {
    $('#nama_penerima').attr('readonly', false);
    $('#email').attr('readonly', false);
    $('#hp_penerima').attr('readonly', false);
    $('.btn-penerima').removeClass('hidden');
}

function editalamatempat() {
    $('#alamat_penerima').attr('readonly', false);
    $('#delivery_date').attr('readonly', false);
    $('#time_slot').attr('readonly', false);
    $('#delivery_charge').attr('readonly', false);
    $('#delivery_marks').attr('readonly', false);
    $('.btn-alamatempat').removeClass('hidden');
}

function editcustomer() {
    $('#invoice_name').attr('readonly', false);
    $('.btn-customer').removeClass('hidden');
}

function editproduct() {
    $('#btn-product').removeClass('hidden');
}

function submitmsg() {
    var from = $('#card_from').val();
    var to = $('#card_to').val();
    var isi = $('#card_isi').val();
    var trx = $('#TransactionNumber').val();

    $.ajax({
        url: '../php/ajax/updatetrx.php?type=cardmsg',
        method: 'post',
        data: { 'TransactionID': trx, 'CardFrom': from, 'CardTo': to, 'Msg': isi },

        success: function(msg) {
            alert(msg);
            location.reload();
        }
    });
}

function alamatempat() {
    var trx = $('#TransactionNumber').val();
    var alamat_penerima = $('#alamat_penerima').val();
    var kelurahan_id = $('#kelurahan_id option:selected').val();
    var delivery_date = $('#delivery_date').val();
    var delivery_charge = $('#delivery_charge').val();
    var delivery_marks = $('#delivery_marks').val();
    var time_slot = $('#time_slott option:selected').val();

    $.ajax({
        url: '../php/ajax/updatetrx.php?type=alamatempat',
        method: 'post',
        data: { 'TransactionID': trx, 'alamat_penerima': alamat_penerima, 'kelurahan_id': kelurahan_id, 'delivery_date': delivery_date, 'time_slot': time_slot, 'delivery_charge': delivery_charge, 'delivery_marks': delivery_marks },

        success: function(msg) {
            alert(msg);
            location.reload();
        }
    });
}

function penerima() {
    var trx = $('#TransactionNumber').val();
    var nama_penerima = $('#nama_penerima').val();
    var email = $('#email').val();
    var hp_penerima = $('#hp_penerima').val();

    $.ajax({
        url: '../php/ajax/updatetrx.php?type=penerima',
        method: 'post',
        data: { 'TransactionID': trx, 'nama_penerima': nama_penerima, 'email': email, 'hp_penerima': hp_penerima },

        success: function(msg) {
            alert(msg);
            location.reload();
        }
    });
}

function customer() {
    var invoice_name = $('#invoice_name').val();
    var trx = $('#TransactionNumber').val();

    $.ajax({
        url: '../php/ajax/updatetrx.php?type=customer',
        method: 'post',
        data: { 'TransactionID': trx, 'invoice_name': invoice_name },

        success: function(msg) {
            alert(msg);
            location.reload();
        }
    });
}

function hapusproduct(id) {
    if (confirm('Are you Sure want Delete ?')) {
        $.ajax({
            url: '../php/ajax/updatetrx.php?type=hapusproduct',
            method: 'post',
            data: { 'ProductID': id },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    }
    return false;
}

$(document).ready(function() {
    $('#kelurahan_id').select2({ readonly: true, width: '100%', theme: "bootstrap4" });

    var deldate = $('#delivery_date').val();

    $('#delivery_date').datetimepicker({
        format: 'YYYY/MM/DD',
        minDate: deldate,
    }).on('dp.change', function(e) {
        var times = e.date.format("YYYY-MM-DD");
        $.ajax({
            url: '../php/ajax/order.php?type=getTime',
            type: 'post',
            data: { 'Tanggal': times },

            success: function(msg) {
                var data = JSON.parse(msg);
                if (data['response'] == 'OK') {
                    var timeslot = '';
                    $.each(data['msg'], function(key, val) {
                        timeslot += '<option value="' + key + '" >' + val + '</option>';
                    });
                    $('[name="time_slot"]').removeAttr("disabled");
                    $('[name="time_slot"]').html(timeslot);

                } else {
                    $('[name="time_slot"]').val('');
                    alert(data['msg']);
                }
            }
        });
    });
})