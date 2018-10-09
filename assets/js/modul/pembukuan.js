$.fn.onEnter = function(func) {
    this.bind('keypress', function(e) {
        if (e.keyCode == 13) func.apply(this, [e]);
    });
    return this;
};
$(document).ready(function() {

    $('#allpaid').change(function(e) {
        $("input:checkbox").prop("checked", $(this).prop("checked"));
    });
    $('.checkitem').change(function(e) {
        if ($(this).prop("checked") == false) {
            $("#allpaid").prop("checked", false);
        }
        if ($(".checkitem:checked").length == $(".checkitem").length) {
            $("#allpaid").prop("checked", true);
        }
        console.log($(".checkitem:checked").length);
    });
    $('[name="paidDate"]').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
    });
    $('[name="paidDatemultiple"]').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
    });
    getpembukuan('no');
    getpiutang('no');
    getbonus('no');

    // $('[name="passwordpushtoken"]').on('keypress', function(e) {
    //     var password = $('[name="passwordpushtoken"]').val();
    //     alert(password);
    // });
    $('[name="passwordpushtoken"]').onEnter(function() {
        submitgeneratepushtoken();
    });

    $('[name="passwordpushtokenmultiple"]').onEnter(function() {
        submitgeneratepushtokenmultiple();
    });

    $('#Bonus').on('submit', function(e) {
        e.preventDefault();

        var range = $('#daterangebonus').val();
        var admin = $('#adminbonus option:selected').val();

        $('#tableBonus').DataTable().destroy();
        getbonus('yes', range, admin);
    });

    $('#Revenue').on('submit', function(e) {
        e.preventDefault();

        var range = $('#daterangerevenue').val();
        var statuspaid = $('#StatusPaid option:selected').val();

        $('#TableRevenue').DataTable().destroy();
        getpembukuan('yes', range, statuspaid);
    });
    $('#Revenue').on('submit', function(e) {
        e.preventDefault();

        var range = $('#daterangerevenue').val();
        var statuspaid = $('#StatusPaid option:selected').val();

        $('#TableRevenue').DataTable().destroy();
        getpembukuan('yes', range, statuspaid);
    });

    $('#Piutang').on('submit', function(e) {
        e.preventDefault();

        var range = $('#daterangepiutang').val();
        var statuspaid = $('#StatusPaid option:selected').val();

        $('#TablePiutang').DataTable().destroy();
        getpiutang('yes', range, statuspaid);
    });
});

function allpaid() {
    var data = [];
    var value = $('[name="piutangpaid[]"]:checked').each(function() {
        data.push(this.value);
    });

    var selected;
    selected = data.join(',');

    if (selected.length > 0) {
        $('[name="transactionIDpush[]"]').val(data);
        $('#generatepushtokenmultiple').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    } else {
        alert("Please at least check one of the checkbox");
    }


    // $.ajax({
    //     url: '../php/ajax/pembukuan.php?type=changestatuspaidmultiple',
    //     method: 'post',
    //     data: { 'transactionID': trx, 'password': password, 'PaidDate': paiddate },

    //     success: function(msg) {
    //         location.reload();
    //         alert(msg);
    //     }
    // })
}

function submitgeneratepushtoken() {
    var paiddate = $('[name="paidDate"]').val();
    var password = $('[name="passwordpushtoken"]').val();
    var trx = $('[name="transactionIDpush"]').val();

    $.ajax({
        url: '../php/ajax/pembukuan.php?type=changestatuspaid',
        method: 'post',
        data: { 'transactionID': trx, 'password': password, 'PaidDate': paiddate },

        success: function(msg) {
            location.reload();
            alert(msg);
        }
    })
}

function submitgeneratepushtokenmultiple() {
    var paiddate = $('[name="paidDatemultiple"]').val();
    var password = $('[name="passwordpushtokenmultiple"]').val();
    var trx = $('[name="transactionIDpush[]"]').val();
    // console.log(paiddate);
    $.ajax({
        url: '../php/ajax/pembukuan.php?type=changestatuspaidmultiple',
        method: 'post',
        data: { 'transactionID': trx, 'password': password, 'PaidDate': paiddate },

        success: function(msg) {
            location.reload();
            alert(msg);
        }
    })
}

function changestatuspaid(obj) {
    $('#generatepushtoken').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });

    $('[name="transactionIDpush"]').val(obj);
}

function getpembukuan(is_date_search, date_range, statuspaid) {

    var tablePaymentKurir = $('#TableRevenue').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "pagging": true,
        "ajax": {
            url: "../php/ajax/pembukuan.php?type=revenue", // json datasource
            type: "post", // method  , by default get
            data: {
                is_date_search: is_date_search,
                date_range: date_range,
                status_paid: statuspaid
            },
            error: function() { // error handling
                $(".employee-grid-error").html("");
                $("#TableRevenue").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#employee-grid_processing").css("display", "none");

            }
        },
        drawCallback: function(settings) {
            var data = this.api().ajax.json();
            $('#totalPayment').html(data['totalData']);
            $('#totalPerKurir').html(data['totalKurir']);
            $('#selisih').html(data['subtotal']);
            console.log(data);
        },
        "columns": [
            { "data": "0", "orderable": false },
            { "data": "1", "orderable": false },
            { "data": "2", "orderable": false },
            { "data": "3", "orderable": false },
            { "data": "4", "orderable": false },
            { "data": "5", "orderable": false },
            { "data": "6", "orderable": false },
            { "data": "7", "orderable": false },
            { "data": "8", "orderable": false },
            { "data": "9", "orderable": false }
        ],

    });
}

function getpiutang(is_date_search, date_range, statuspaid) {

    var TablePiutang = $('#TablePiutang').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "pagging": true,
        "ajax": {
            url: "../php/ajax/pembukuan.php?type=piutang", // json datasource
            type: "post", // method  , by default get
            data: {
                is_date_search: is_date_search,
                date_range: date_range,
                status_paid: statuspaid
            },
            error: function() { // error handling
                $(".employee-grid-error").html("");
                $("#TableRevenue").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#employee-grid_processing").css("display", "none");

            }
        },
        drawCallback: function(settings) {
            var data = this.api().ajax.json();
            $('#totalPayment').html(data['totalKurir']);
            $('#totalPerKurir').html(data['totalKurir']);
            $('#selisih').html(data['subtotal']);
            console.log(data);
        },
        "columns": [
            { "data": "0", "orderable": false },
            { "data": "1", "orderable": false },
            { "data": "2", "orderable": false },
            { "data": "3", "orderable": false },
            { "data": "4", "orderable": false },
            { "data": "5", "orderable": false },
            { "data": "6", "orderable": false },
            { "data": "7", "orderable": false },
            { "data": "8", "orderable": false },
            { "data": "9", "orderable": false }
        ],

    });
}

function getbonus(is_date_search, date_range, admin) {

    var TablePiutang = $('#tableBonus').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "pagging": true,
        "ajax": {
            url: "../php/ajax/pembukuan.php?type=bonus", // json datasource
            type: "post", // method  , by default get
            data: {
                is_date_search: is_date_search,
                date_range: date_range,
                admin_id: admin
            },
            error: function() { // error handling
                $(".employee-grid-error").html("");
                $("#TableRevenue").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#employee-grid_processing").css("display", "none");

            }
        },
        drawCallback: function(settings) {
            var data = this.api().ajax.json();
            $('#totalPayment').html(data['totalKurir']);
            $('#totalPerKurir').html(data['totalKurir']);
            $('#selisih').html(data['subtotal']);
            console.log(data);
        },
        "columns": [
            { "data": "0", "orderable": false },
            { "data": "1", "orderable": false },
            { "data": "2", "orderable": false },
            { "data": "3", "orderable": false },
            { "data": "4", "orderable": false },
            { "data": "5", "orderable": false },
            { "data": "6", "orderable": false },
            { "data": "7", "orderable": false },
            { "data": "8", "orderable": false },
            { "data": "9", "orderable": false }
        ],

    });
}

$(function() {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#daterevenue span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#daterangerevenue').val(start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD'));
        $('#datepiutang span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#daterangepiutang').val(start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD'));
        $('#datebonus span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#daterangebonus').val(start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD'));
    }

    $('#datebonus').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    $('#datepiutang').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    $('#daterevenue').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

});