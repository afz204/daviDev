function showListKasIn(id) {
    // $('#listKasIn').removeClass('hidden');
    window.location.href = '?p=kasIn&types=' + id;
}

function showKasBesar() {
    $('#listKasBesar').removeClass('hidden');
}

function delKasOut(id, admin) {

    //alert('id: '+id + 'admin: '+adm);
    if (!confirm('Are you sure want to delete this?')) {
        return false;
    } else {
        $.ajax({
            url: '../php/ajax/payment.php?type=delKasOut',
            method: 'post',
            data: { admin: admin, keterangan: id },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    }
}

function delKasBesar(id, admin) {
    if (!confirm('Are you sure want to delete this?')) {
        return false;
    } else {
        $.ajax({
            url: '../php/ajax/payment.php?type=delKasBesar',
            method: 'post',
            data: { admin: admin, keterangan: id },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    }
}

function addKasBesar(admin, type) {
    $('#form_kas_Besar').removeClass('hidden');
    $('#monitoringKasIn').addClass('hidden');

    $('#typeKasB').val(type);
    if (type == 'kredit') {
        $('#kasStatus').removeClass('hidden');
        $('#statusKas').prop('required', true);
    } else {
        $('#kasStatus').addClass('hidden');
        $('#statusKas').prop('required', false);
    }
}

function addKasOut(admin) {
    $('#listKasKeluar').addClass('hidden');
    $('#form-kasKeluar').removeClass('hidden');
}
$(document).ready(function() {
    $('#starDateReport').datetimepicker();
    $('#endDateReport').datetimepicker();
    $('#tableKasOut').DataTable();
    $('#kasMasuk').DataTable();
    $('#tablePayKurir').DataTable();
    $('#table_kas_out').DataTable();
    var listOutKas = $('#listKasKeluar').show();
    var listPayKurir = $('#listPayKurir').show();

    var monitoringKas = $('#monitoringKasIn').show();

    $('#listPengeluaranKas').on('click', '.addOutKas', function() {
        $('#form-kasKeluar').removeClass('hidden');
        listOutKas.hide();
    });

    $('#belanja-form').on('submit', function(e) {
        e.preventDefault();

        console.log('form masuk');

        var admin = $('#adminBelanja').val();
        var cat = $('#specSatuan option:selected').val();
        var subcat = $('#catSatuan option:selected').val();
        // var subsubcat = $('#subCatSatuan option:selected').val();
        var name = $('#nameBelanja').val();
        var qty = $('#qtyBelanja').val();
        var satuan = $('#satuanBelanja option:selected').val();
        var price = $('#hargaBelanja').val();
        var ket = $('#ketBelanja').val();

        // alert(cat + admin + subcat);


        $.ajax({
            url: '../php/ajax/payment.php?type=kasOut',
            method: 'post',
            data: { admin: admin, category: cat, subcategory: subcat, title: name, quantity: qty, satuan: satuan, harga: price, keterangan: ket },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    });

    $('#reportKasOutAdmin').on('submit', function(e) {
        e.preventDefault();

        var admin = $('#reportOutAdminID').val();
        var user = $('#reportOutAdmin option:selected').val();

        var url = $('#reportOutURL').val();

        var link = url + 'php/ajax/pdfKasOut.php?user=' + user + '&admin=' + admin;
        //window.open(url, '_blank');



        if (!confirm('Are you sure want to report this?')) {
            return false;
        } else {

            $.ajax({
                url: '../php/ajax/payment.php?type=reportKasOut',
                method: 'post',
                data: { admin: admin, users: user },

                success: function(msg) {
                    if (msg == '0') {
                        alert('Failed');
                    } else if (msg == '1') {
                        window.open(link, '', 'Report Pengeluaran Kas', 'width=400, height=600, screenX=100');
                        alert('Berhasil report data!');

                    } else {
                        alert('Record belum ada!');
                    }
                    location.reload();
                }
            });

        }
    });

    $('#listPemasukanKas').on('click', '.addInKas', function() {
        monitoringKas.hide();
        $('#form-kasIn').removeClass('hidden');
    });


    $('#kasIn-form').on('submit', function(e) {
        e.preventDefault();

        var adm = $('#adminIn').val();
        var nama = $('#nameIn').val();
        var total = $('#biayaIn').val();
        var ket = $('#ketIn').val();

        $.ajax({
            url: '../php/ajax/payment.php?type=addKasIn',
            method: 'post',
            data: { admin: adm, title: nama, total: total, keterangan: ket },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    });

    $('#payKurir-form').on('submit', function(e) {
        e.preventDefault();
        var adm = $('#adminPay').val();
        var kurir = $('#namaKurir option:selected').val();
        var kel = $('#kelurahanCharge option:selected').val();
        var kelText = $('#kelurahanCharge option:selected').text();
        var prices = $('#kelurahanCharge option:selected').data('prices');
        var noTrx = $('#no_trxCharge').val();

        //alert(adm + kurir + kel); 

        $.ajax({
            url: '../php/ajax/payment.php?type=addPayCharge',
            method: 'post',
            data: { admin: adm, namaKurir: kurir, kelurahan: kel, trx: noTrx, price: prices, ket: kelText },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    });

    listPayKurir.on('click', '.addpayCharge', function() {
        listPayKurir.hide();
        $('#form-payKurir').removeClass('hidden');
    });

    listPayKurir.on('click', '.delPayCharge', function() {
        var adminI = $(this).data('admin');
        var id = $(this).data('id');

        // alert(adminI + id);
        if (!confirm('Are you sure want to report this?')) {
            return false;
        } else {

            $.ajax({
                url: '../php/ajax/payment.php?type=delPayCharge',
                method: 'post',
                data: { admin: adminI, id: id },

                success: function(msg) {
                    alert(msg);
                    location.reload();
                }
            });

        }

    });

    $('#reportPayCharge').on('submit', function(e) {
        e.preventDefault();
        var admin = $('#reportPayChargeAdminID').val();
        var url = $('#reportPayChargeURL').val();
        var kurir = $('#reportPayChargeAdmin option:selected').val();

        var link = url + 'php/ajax/pdfPayKurir.php?id=' + kurir + '&admin=' + admin;
        //window.open(url, '_blank');



        if (!confirm('Are you sure want to report this?')) {
            return false;
        } else {

            $.ajax({
                url: '../php/ajax/payment.php?type=reportPayCharge',
                method: 'post',
                data: { admin: admin, kurir: kurir },

                success: function(msg) {
                    if (msg == '0') {
                        alert('Failed');
                    } else if (msg == '1') {
                        window.open(link, '', 'Report Pembayaran Kurir', 'width=400, height=600, screenX=100');
                        alert('Berhasil report data!');

                    } else {
                        alert('Record belum ada!');
                    }

                    location.reload();
                }
            });

        }
    });

    $('#kas_besar_form').on('submit', function(e) {
        e.preventDefault();
        var admin = $('#adminKasB').val();
        var title = $('#nameKasB').val();
        var ket = $('#ketKasB').val();
        var total = $('#biayaKasB').val();
        var type = $('#typeKasB').val();
        var status = $('#statusKas option:selected').val();

        $.ajax({
            url: '../php/ajax/payment.php?type=kasBesar',
            method: 'post',
            data: { admin: admin, judul: title, keterangan: ket, biaya: total, tipe: type, status: status },

            success: function(msg) {
                alert(msg);

                location.reload();
            }
        });
    });

    $('#selectAdminR').on('change', function() {
        var id = $('#typeReport option:selected').val();

        if ($(this).is(":checked")) {
            if (id == '4') {
                $('#pilihKurirReport').removeClass('hidden');
                $('#kurirReport').prop('required', true);
            } else {
                $('#pilihAdminReport').removeClass('hidden');
                $('#adminReport').prop('required', true);
            }

        } else {
            $('#pilihAdminReport').addClass('hidden');
        }
    });
    $('#typeReport').on('change', function() {

    });

    $('#form-report').on('submit', function(e) {
        e.preventDefault();

        var types = $('#typeReport option:selected').val();
        var tgl = $('#hidde_date_field').val();
        var listAdm = $('#adminReport option:selected').val();
        var kurir = $('#kurirReport option:selected').val();

        if (listAdm == '') {
            optional = kurir;
        }
        if (kurir == '') {
            optional = listAdm;
        }

        switch (types) {
            case '1':
                urlLik = 'kasBesar';
                break;
            case '2':
                urlLik = 'kasIn';
                break;
            case '3':
                urlLik = 'kasOut';
                break;
            case '4':
                urlLik = 'kurir';
                break;
        }
        //alert(tgl + types + listAdm);

        window.location.href = '?p=report-payment&type=' + urlLik + '&range=' + tgl + '&admin=' + optional;
        // $('#listReport').hide().load('payment/?p=table-report&type=kasBesar').fadeIn();
        // $.ajax({
        //     url: '../php/payment/table-report.php?type=' + urlLik,
        //     method: 'post',
        //     data: { tipe: types, tanggal: tgl, admin: listAdm },

        //     success: function(data) {
        //         $('#tablePayKurir').append(data);
        //     }
        // });
    });
})


$(function() {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#hidde_date_field').val(start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD'));
    }

    $('#reportrange').daterangepicker({
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