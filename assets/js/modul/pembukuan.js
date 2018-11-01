$.fn.onEnter = function(func) {
    this.bind('keypress', function(e) {
        if (e.keyCode == 13) func.apply(this, [e]);
    });
    return this;
};
$(document).ready(function() {

    $('#send-email').on('click', function(e) {
        e.preventDefault();
        var trx = $(this).data('trx');
        $("#send-email").attr("disabled", true);
        $.ajax({
            url: '../php/ajax/send_email.php?type=savedate',
            type: 'post',
            data: { 'transactionID': trx },

            success: function(msg) {
                var data = JSON.parse(msg);
                alert(data['msg']);
                location.reload();
            }
        });
    });

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
    $('[name="residate"]').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
    });
    $('[name="paidDatemultiple"]').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
    });
    getpembukuan('no');
    getpiutang('no');
    getbonus('no');
    gethardcopy('no');
    getsoftcopy('no');

    // $('[name="passwordpushtoken"]').on('keypress', function(e) {
    //     var password = $('[name="passwordpushtoken"]').val();
    //     alert(password);
    // });
    $('[name="passwordpushtoken"]').onEnter(function() {
        submitgeneratepushtoken();
    });

    $('[name="nomorresi"]').onEnter(function() {
        submitresi();
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

    $('#HardCopy').on('submit', function(e) {
        e.preventDefault();

        var range = $('#daterangerevenue').val();

        $('#TableHardCopy').DataTable().destroy();
        gethardcopy('yes', range);
    });

    $('#SoftCopy').on('submit', function(e) {
        e.preventDefault();

        var range = $('#daterangerevenue').val();

        $('#TableSoftCopy').DataTable().destroy();
        getsoftcopy('yes', range);
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

function sendemail(trx) {
    $.ajax({
        url: '../php/ajax/send_email.php?type=savedate',
        type: 'post',
        data: { 'transactionID': trx },

        success: function(msg) {
            var data = JSON.parse(msg);
            alert(data['msg']);
            location.reload();
        }
    });
}

function exportrevenue(type) {
    var range = '';
    var statuspaid = '';
    if (type == 'exportrevenue') {
        range = $('#daterangerevenue').val();
        statuspaid = $('#StatusPaid option:selected').val();
    } else if (type == 'exportpiutang') {
        range = $('#daterangepiutang').val();
        statuspaid = $('#StatusPaid option:selected').val();
    } else {
        range = $('#daterangebonus').val();
        statuspaid = $('#adminbonus option:selected').val();
    }
    if (statuspaid == '') {
        alert('Select Status Paid!');
    } else {

        window.open('../php/ajax/exportrevenue.php?type=' + type + '&date_range=' + range + '&status_paid=' + statuspaid, "_blank");
    }
}

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
}

function submitresi() {
    var residate = $('[name="residate"]').val();
    var nomorresi = $('[name="nomorresi"]').val();
    var trx = $('[name="transactionIDpush"]').val();

    $.ajax({
        url: '../php/ajax/pembukuan.php?type=inputresi',
        method: 'post',
        data: { 'transactionID': trx, 'nomorresi': nomorresi, 'residate': residate },

        success: function(msg) {
            location.reload();
            alert(msg);
        }
    })
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
            var data = JSON.parse(msg);
            if (data['response'] == 'OK') {
                $.ajax({
                    url: '../php/ajax/send_email.php',
                    method: 'post',
                    data: { 'transactionID': trx },

                    success: function(msgg) {
                        var n = JSON.parse(msgg);
                        alert(n['msg']);
                        location.reload();
                    }
                })
            } else {
                location.reload();
                alert(data['msg']);

            }
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
            var data = JSON.parse(msg);
            if (data['response'] == 'OK') {
                console.log(data);
                $.ajax({
                    url: '../php/ajax/send_email_multiple.php',
                    method: 'post',
                    data: { 'transactionID': trx },

                    success: function(msgg) {
                        var n = JSON.parse(msgg);
                        console.log(n);
                        alert(n['msg']);
                        location.reload();
                    }
                })
            } else {
                location.reload();
                alert(data['msg']);

            }
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

function inputresi(obj) {
    $('#modalinputresi').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });

    $('[name="transactionIDpush"]').val(obj);
}

function getsoftcopy(is_date_search, date_range) {

    var TableSoftCopy = $('#TableSoftCopy').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "pagging": true,
        "ajax": {
            url: "../php/ajax/pembukuan.php?type=softcopy", // json datasource
            type: "post", // method  , by default get
            data: {
                is_date_search: is_date_search,
                date_range: date_range
            },
            error: function() { // error handling
                $(".employee-grid-error").html("");
                $("#TableRevenue").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#employee-grid_processing").css("display", "none");

            }
        },
        drawCallback: function(settings) {
            var data = this.api().ajax.json();
        },
        "columns": [
            { "data": "0", "orderable": true },
            { "data": "1", "orderable": true },
            { "data": "2", "orderable": true },
            { "data": "3", "orderable": true },
            { "data": "4", "orderable": true },
            { "data": "5", "orderable": true },
            { "data": "6", "orderable": true },
            { "data": "7", "orderable": true },
            { "data": "8", "orderable": true }
        ],

    });
}

function gethardcopy(is_date_search, date_range) {

    var TableHardCopy = $('#TableHardCopy').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "pagging": true,
        "ajax": {
            url: "../php/ajax/pembukuan.php?type=hardcopy", // json datasource
            type: "post", // method  , by default get
            data: {
                is_date_search: is_date_search,
                date_range: date_range
            },
            error: function() { // error handling
                $(".employee-grid-error").html("");
                $("#TableRevenue").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#employee-grid_processing").css("display", "none");

            }
        },
        drawCallback: function(settings) {
            var data = this.api().ajax.json();
        },
        "columns": [
            { "data": "0", "orderable": true },
            { "data": "1", "orderable": true },
            { "data": "2", "orderable": true },
            { "data": "3", "orderable": true },
            { "data": "4", "orderable": true },
            { "data": "5", "orderable": true },
            { "data": "6", "orderable": true },
            { "data": "7", "orderable": true },
            { "data": "8", "orderable": true },
            { "data": "9", "orderable": true }
        ],

    });
}

function getpembukuan(is_date_search, date_range, statuspaid) {

    var tablePaymentKurir = $('#TableRevenue').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
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
            { "data": "0", "orderable": true },
            { "data": "1", "orderable": true },
            { "data": "2", "orderable": true },
            { "data": "3", "orderable": true },
            { "data": "4", "orderable": true },
            { "data": "5", "orderable": true },
            { "data": "6", "orderable": true },
            { "data": "7", "orderable": true },
            { "data": "8", "orderable": true },
            { "data": "9", "orderable": true }
        ],

    });
}

function getpiutang(is_date_search, date_range, statuspaid) {

    var TablePiutang = $('#TablePiutang').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "pagging": false,
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
            $('#GrandTotalpayment').html(data['subtotal']);
            console.log(data);
        },
        "columns": [
            { "data": "0", "orderable": true },
            { "data": "1", "orderable": true },
            { "data": "2", "orderable": true },
            { "data": "3", "orderable": true },
            { "data": "4", "orderable": true },
            { "data": "5", "orderable": true },
            { "data": "6", "orderable": true },
            { "data": "7", "orderable": true },
            { "data": "8", "orderable": true },
            { "data": "9", "orderable": true },
            { "data": "10", "orderable": true }
        ],

    });
}

function getbonus(is_date_search, date_range, admin) {

    var TablePiutang = $('#tableBonus').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
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
            { "data": "0", "orderable": true },
            { "data": "1", "orderable": true },
            { "data": "2", "orderable": true },
            { "data": "3", "orderable": true },
            { "data": "4", "orderable": true },
            { "data": "5", "orderable": true },
            { "data": "6", "orderable": true },
            { "data": "7", "orderable": true },
            { "data": "8", "orderable": true },
            { "data": "9", "orderable": true }
        ],

    });
}

$(function() {

    var start = moment().startOf('month');
    var end = moment().endOf('month');

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