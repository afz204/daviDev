$(document).ready(function() {
    $('#tableKurir').DataTable();
    $('#tableDelivCharge').DataTable();

    var msg = $('#messageKurir').hide();
    var listCharge = $('#listDelivCharge').show();

    $('#newKurir').on('submit', function(e) {
        e.preventDefault();

        var adm = $('#adminKurir').val();
        var nama = $('#nameKurir').val();
        var email = $('#emailKurir').val();
        var phone = $('#phoneKurir').val();
        var wa = $('#waKurir').val();
        var prov = $('#ProvinsiCorporate option:selected').val();
        var kota = $('#KotaCorporate option:selected').val();
        var kec = $('#kecamatanCorporate option:selected').val();
        var kel = $('#kelurahanCorporate option:selected').val();
        var alamat = $('#alamatKurir').val();

        //alert(adm + nama + email + phone + wa + prov + kota + kec + kel + alamat);
        $.ajax({
            url: '../php/ajax/kurir.php?type=newKurir',
            method: 'post',
            data: { admin: adm, nama: nama, email: email, hp: phone, wa: wa, province: prov, kota: kota, kecamatan: kec, kelurahan: kel, alamat: alamat },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })

    });
    $('#listDelivCharge').on('click', '.addDeliveryCharge', function() {
        listCharge.hide();
        $('#formDelivCharge').removeClass('hidden');
    });
    $('#delivCharge-form').on('submit', function(e) {
        e.preventDefault();
        var admin = $('#adminCharge').val();
        var price = $('#priceCharge').val();
        var kel = $('#kelurahanCorporate').val();

        //alert(admin + price + '_kelurahan : ' + kel);
        $.ajax({
            url: '../php/ajax/kurir.php?type=addCharge',
            method: 'post',
            data: { admin: admin, harga: price, kelurahan: kel },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    });
    listCharge.on('click', '.deleteCharge', function() {
        var admin = $(this).data('admin');
        var id = $(this).data('id');
        if (!confirm('Are you sure want to delete this?')) {
            return false;
        } else {
            //alert(admin + ' idnya: ' + id);
            $.ajax({
                url: '../php/ajax/kurir.php?type=delCharge',
                method: 'post',
                data: { admin: admin, keterangan: id },

                success: function(msg) {
                    location.reload();
                    alert(msg);
                }
            })
        }

    });

    listCharge.on('click', '.updateCharge', function() {
        var id = $(this).data('id');
        var kel = $(this).data('kelurahan');
        var hrg = $(this).data('price');
        $('#modalCharges').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#modalChargesLabel').html('update delivery-charges <span class="badge badge-xs badge-primary">' + kel + '</span>');
        $('#updateCharges').attr("placeholder", hrg).val("").focus().blur();
        $('#idCharges').val(id);
    });

    $('#updateCharges-form').on('submit', function(e) {
        e.preventDefault();
        var id = $('#idCharges').val();
        var adm = $('#adminCharges').val();
        var hrg = $('#updateCharges').val();

        $.ajax({
            url: '../php/ajax/kurir.php?type=updateCharges',
            method: 'post',
            data: { admin: adm, harga: hrg, kelurahan: id },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    });
})