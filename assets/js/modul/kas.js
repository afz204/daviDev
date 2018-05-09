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
}

function addKasOut(admin) {
    $('#listKasKeluar').addClass('hidden');
    $('#form-kasKeluar').removeClass('hidden');
}
$(document).ready(function() {
    $('#tableKasOut').DataTable();
    $('#kasMasuk').DataTable();
    $('#tablePayKurir').DataTable();
    $('#table_kas_out').DataTable();
    var listOutKas = $('#listKasKeluar').show();
    var listPayKurir = $('#listPayKurir').show();
    var listInKas = $('#listKasIn').hide();
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

    monitoringKas.on('click', '.showListKasIn', function() {
        listInKas.show();
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
        var noTrx = $('#no_trxCharge').val();

        //alert(adm + kurir + kel);

        $.ajax({
            url: '../php/ajax/payment.php?type=addPayCharge',
            method: 'post',
            data: { admin: adm, namaKurir: kurir, kelurahan: kel, trx: noTrx },

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

        $.ajax({
            url: '../php/ajax/payment.php?type=kasBesar',
            method: 'post',
            data: { admin: admin, judul: title, keterangan: ket, biaya: total, tipe: type },

            success: function(msg) {
                alert(msg);

                location.reload();
            }
        });
    });
})