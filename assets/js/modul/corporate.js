function removePIC(id) {
    if (!confirm("Are you sure want delete this ?")) {
        return false;
    } else {
        $.ajax({
            url: '../php/ajax/corporate.php?type=deletePIC',
            type: 'post',
            data: { kode_perusahaan: id },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    }
}
$(document).ready(function() {

    $('#birth_day').datetimepicker({
        viewMode: 'years',
        format: 'YYYY/MM/DD'

    });

    $('#listCorporateBD').DataTable();
    $('#listPersonal').DataTable();
    $('#listFlorist').DataTable();

    var message = $('#messageCorporate').hide();

    $('#newCorporate').on('submit', function(e) {
        e.preventDefault();
        var type = $('#typeFormCorporate').val();
        var nama = $('#nameCorporate').val();
        var bidang = $('#bidangCorporate option:selected').val();
        var telp = $('#telpCorporate').val();
        var hp = $('#hpCorporate').val();
        var fax = $('#faxCorporate').val();
        var email = $('#emailCorporate').val();
        var web = $('#webCorporate').val();
        var prov = $('#ProvinsiCorporate option:selected').val();
        var kota = $('#KotaCorporate option:selected').val();
        var kec = $('#kecamatanCorporate option:selected').val();
        var kel = $('#kelurahanCorporate option:selected').val();
        var addr = $('#alamatCorporate').val();
        var pos = $('#posCorporate').val();
        var cp = $('#cpCorporate').val();

        $.ajax({
            url: '../php/ajax/corporate.php?type=new',
            type: 'post',
            data: 'type=' + type + 'nama=' + nama + '&bidang=' + bidang + '&telp=' + telp + '&hp=' + hp + '&fax=' + fax + '&email=' + email + '&web=' + web + '&prov=' + prov + '&kota=' + kota +
                '&kec=' + kec + '&kel=' + kel + '&alamat=' + addr + '&pos=' + pos + '&cp=' + cp,

            success: function(msg) {
                if (msg === '1') {
                    $(':input', '#newCorporate')
                        .not(':button, :submit, :reset, :hidden')
                        .val('')
                        .removeAttr('checked')
                        .removeAttr('selected');
                    message.addClass('alert-success');
                    $('#isiPesan').append('<strong>Done!</strong> New Corporate Success input to Database.');
                    message.show();

                } else {
                    $(':input', '#newCorporate')
                        .not(':button, :submit, :reset, :hidden')
                        .val('')
                        .removeAttr('checked')
                        .removeAttr('selected');
                    message.addClass('success');
                    message.addClass('alert-danger');
                    $('#isiPesan').append('<strong>OOops!</strong> Something wrong with this page.');
                    message.show();
                    console.log(msg);
                }

            }
        });
    });

    $('.alert').on('click', '.close', function() {
        location.reload();
        $(this).closest('.alert').slideUp();

    });

    $('#modalPIC').on('hidden.bs.modal', function(e) {
        e.preventDefault();

        $('#formPIC')[0].reset();
        $('#namaPIC').removeClass('parsley-success');
        $('#typePIC').removeClass('parsley-success');
        $('#emailPIC').removeClass('parsley-success');
        $('#nomorPIC').removeClass('parsley-success');
        $('#namaPIC').removeClass('parsley-error');
        $('#nomorPIC').removeClass('parsley-error');
        $('#kelurahanCorporate').removeClass('parsley-error');
        $('#alamatCorporate').removeClass('parsley-error');
        $('.parsley-errors-list').addClass('hidden');

    });
    $('#formPIC').on('submit', function(e) {
        e.preventDefault();

        var id = $('#kodePerusahaan').val();
        var name = $('#namaPIC').val();
        var typePIC = $('#typePIC').val();
        var emailPIC = $('#emailPIC').val();
        var nomor = $('#nomorPIC').val();
        var provinsi = $('#ProvinsiCorporate option:selected').val();
        var kota = $('#KotaCorporate option:selected').val();
        var kecamatan = $('#kecamatanCorporate option:selected').val();
        var kelurahan = $('#kelurahanCorporate option:selected').val();
        var alamat = $('#alamatCorporate').val();

        $.ajax({
            url: '../php/ajax/corporate.php?type=savePIC',
            type: 'post',
            data: {
                'kode_perusahaan': id,
                'nama_pic': name,
                'typePIC': typePIC,
                'emailPIC': emailPIC,
                'nomor_hp': nomor,
                'provinsi': provinsi,
                'kota': kota,
                'kec': kecamatan,
                'kel': kelurahan,
                'alamat': alamat
            },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    });

    $('#formCustomer').on('submit', function(e) {

        e.preventDefault();
        var first = $('#first_name').val();
        var last = $('#last_name').val();
        var email = $('#email_customer').val();
        var sex = $('#jenis_kelamin option:selected').val();
        var mobile = $('#mobile_phone').val();
        var phone = $('#phone_number').val();
        var birthday = $('#birth_day').val();
        var pass = $('#password_login').val();

        $.ajax({
            url: '../php/ajax/corporate.php?type=saveCustomer',
            type: 'post',
            data: {
                first_name: first,
                last_name: last,
                email: email,
                jenis_kelamin: sex,
                mobile_phone: mobile,
                phone_number: phone,
                birth_day: birthday,
                password: pass
            },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    });

    $('#formFlorist').on('submit', function(e) {

        e.preventDefault();
        var FloristName = $('#FloristName').val();
        var Email = $('#Email').val();
        var Username = $('#Username').val();
        var Password = $('#Password').val();
        var mobile_phone = $('#mobile_phone').val();
        var ProvinsiCorporate = $('#ProvinsiCorporate option:selected').val();
        var KotaCorporate = $('#KotaCorporate option:selected').val();
        var kecamatanCorporate = $('#kecamatanCorporate option:selected').val();
        var kelurahanCorporate = $('#kelurahanCorporate option:selected').val();
        var alamatCorporate = $('#alamatCorporate').val();

        $.ajax({
            url: '../php/ajax/corporate.php?type=saveFlorist',
            type: 'post',
            data: {
                FloristName: FloristName,
                Email: Email,
                Username: Username,
                Password: Password,
                mobile_phone: mobile_phone,
                ProvinsiCorporate: ProvinsiCorporate,
                KotaCorporate: KotaCorporate,
                kecamatanCorporate: kecamatanCorporate,
                kelurahanCorporate: kelurahanCorporate,
                alamatCorporate: alamatCorporate
            },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    });
    $('#formEditFlorist').on('submit', function(e) {
        e.preventDefault();

        var IDFlorist = $('#IDFlorist').val();
        var FloristName = $('#FloristName').val();
        var Email = $('#Email').val();
        var Username = $('#Username').val();
        var mobile_phone = $('#mobile_phone').val();
        var ProvinsiCorporate = $('#ProvinsiCorporate option:selected').val();
        var KotaCorporate = $('#KotaCorporate option:selected').val();
        var kecamatanCorporate = $('#kecamatanCorporate option:selected').val();
        var kelurahanCorporate = $('#kelurahanCorporate option:selected').val();
        var alamatCorporate = $('#alamatCorporate').val();

        $.ajax({
            url: '../php/ajax/corporate.php?type=updateFlorist',
            type: 'post',
            data: {
                'IDFlorist': IDFlorist,
                'FloristName': FloristName,
                'Email': Email,
                'Username': Username,
                'mobile_phone': mobile_phone,
                'ProvinsiCorporate': ProvinsiCorporate,
                'KotaCorporate': KotaCorporate,
                'kecamatanCorporate': kecamatanCorporate,
                'kelurahanCorporate': kelurahanCorporate,
                'alamatCorporate': alamatCorporate
            },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    });
})