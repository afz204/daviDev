function updatetimeslot(id) {
    $.ajax({
        url: '../php/ajax/bd.php?type=updatetimeslot',
        method: 'post',
        data: { 'ID': id },

        success: function(msg) {
            var data = JSON.parse(msg);
            alert(data['msg']);
            location.reload();
        }
    });
}

function deletetimeslot(id) {
    $.ajax({
        url: '../php/ajax/bd.php?type=deletetimeslot',
        method: 'post',
        data: { 'ID': id },

        success: function(msg) {
            var data = JSON.parse(msg);
            alert(data['msg']);
            location.reload();
        }
    });
}

function formSlotShow() {
    $('#formSlot').removeClass('hidden');
    $('#btnSlots').addClass('hidden');
    $('#listSlot').addClass('hidden');
}

function btn_submit(id) {
    $('#' + id).html('<span class="badge badge-primary text-center" style="font-size: 14px; margin-left: 40%;">Please wait while loading!!!!</span>');
}

function formCard() {
    $('#formCard').removeClass('hidden');
    $('#btn_add_card').addClass('hidden');
}
$(document).ready(function() {
    $('#level_1').select2({ width: '100%', theme: "bootstrap4" });

    $('#formCardMessages').on('submit', function(e) {
        e.preventDefault();
        btn_submit('btn_submit_card');

        var level_1 = $('#level_1 option:selected').val();
        var level_2 = $('#level_2').val();
        var level_3 = $('#isi_template').val();

        $.ajax({
            url: '../php/ajax/bd.php?type=newCard',
            method: 'post',
            data: { head: level_1, template: level_2, isi: level_3 },

            success: function(msg) {
                alert(msg);

                location.reload();
            }
        });


    });

    $('#time_slotForm').on('submit', function(e) {
        e.preventDefault();

        var range = $('#hidde_date_field').val();
        var checkboxValues = [];
        $('input[name=time_slot]:checked').map(function() {
            checkboxValues.push($(this).val());
        });
        var data = JSON.stringify(checkboxValues);
        var checkedCount = $('input[class="time_slot"]:checked').length;

        $.ajax({
            url: '../php/ajax/bd.php?type=newTimeSlot',
            method: 'post',
            data: { date_range: range, values: data },

            success: function(msg) {
                var data = JSON.parse(msg);
                alert(data['msg']);
                location.reload();
            }
        });

    });
})

$(function() {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#range_slot span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#hidde_date_field').val(start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD'));
    }

    $('#range_slot').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

});