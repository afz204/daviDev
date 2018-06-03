function formPrevillage() {
    $('#form-previllage').removeClass('hidden');
    $('#listPrevillages').hide();
}

function delPrevillage(id, admin, user) {
    var id = id;
    var aId = admin;
    var idUser = user;

    $.ajax({
        url: '../php/ajax/management.php?type=delPrevillages',
        method: 'post',
        data: { data: id, adminI: aId, user: idUser },

        success: function(msg) {
            location.reload();
            alert(msg);
        }
    })
}

function formSatuan() {
    $('#listSatuan').addClass('hidden');
    $('#form-satuan').removeClass('hidden');
}
$(document).ready(function() {

    $('#tbListSatuan').DataTable();
    $('#tableLogs').DataTable();

    var listAdmin = $('#listAdmin').show();
    var listMenu = $('#listMenu').show();
    var detailMenu = $('#detailMenu').hide();


    listAdmin.on('click', '.addAdmin', function() {
        listAdmin.hide();
        $('#form-admin').removeClass('hidden');
    });

    $('#admin-form').on('submit', function(e) {
        e.preventDefault();

        var admin = $('#adminID').val();
        var nama = $('#nameAdmin').val();
        var email = $('#emailAdmin').val();
        var pass = $('#passwordAdmin').val();
        var level = $('#levelAdmin option:selected').val();
        var role = $('#roleAdmin option:selected').val();

        //alert(nama + email + level + role);

        $.ajax({
            url: '../php/ajax/management.php?type=addAdmin',
            method: 'post',
            data: { nama: nama, email: email, pass: pass, levels: level, roles: role, adm: admin },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    });

    listMenu.on('click', '.subMenu', function() {
        var idMenu = $(this).data('id');
        var title = $(this).data('name');

        $('#detailMenu').hide().load('../php/ajax/submenu.php?id=' + idMenu + '&title=' + title).fadeIn(700);
    });

    detailMenu.on('click', '.addSubmenu', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');


        listMenu.hide();
        detailMenu.hide();
        $('#menuID').val(id);
        $('#titleSubmenu').innerHTML = +title;
        $('#form-submenu').removeClass('hidden');
    });

    $('#form-submenu').on('submit', function(e) {
        e.preventDefault();

        var adm = $('#adminSub').val();
        var menu = $('#menuID').val();
        var submenu = $('#nameSub').val()
        var link = $('#linkSub').val();

        $.ajax({
            url: '../php/ajax/management.php?type=addSubmenu',
            method: 'post',
            data: { admin: adm, menu: menu, submenu: submenu, link: link },

            success: function(msg) {
                alert(msg);
                $('#nameSub').val("");
                $('#linkSub').val("");
                $('#form-submenu').addClass('hidden');
                listMenu.show();
                detailMenu.hide();
            }
        })
    });

    $('#listMenuPrev').on('change', function(e) {
        e.preventDefault();
        var id = $(this).find("option:selected");
        var value = id.val();
        var text = id.text();

        $.ajax({
            url: '../php/ajax/management.php?type=menu',
            type: 'post',
            data: 'id=' + value,

            success: function(msg) {
                console.log(msg);
                $('#listSubmenuPrev').empty();

                $.each(msg, function(index, value) {
                    $('#listSubmenuPrev').append('<option value="' + value.id + '">' + value.submenu + '</option>');
                })
            }
        });
    });

    $('#form-previllage').on('submit', function(e) {
        e.preventDefault();

        var adm = $('#adminPrevillage').val();
        var menu = $('#listMenuPrev option:selected').val();
        var sub = $('#listSubmenuPrev option:selected').val();
        var user = $('#userPrevillage').val();

        var previllage = [];
        $('.previllageUser:checked').each(function() {
            previllage.push($(this).val());
        });

        $.ajax({
            url: '../php/ajax/management.php?type=addPrevillagUser',
            method: 'post',
            data: { admin: adm, menu: menu, submenu: sub, previllage: previllage, users: user },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })

    });

    $('#listPrevillages').on('click', '.updatePrevillages', function() {
        var id = $(this).data('id');
        $('#idUpdatePrevillage').val(id);
    });

    $('#form-updatePrevillage').on('submit', function(e) {
        e.preventDefault();

        var adm = $('#adminUpdatePrevillage').val();
        var id = $('#idUpdatePrevillage').val();

        var previllage = [];
        $('.updatePrevillage:checked').each(function() {
            previllage.push($(this).val());
        });

        $.ajax({
            url: '../php/ajax/management.php?type=updatePrevillageUser',
            method: 'post',
            data: { admin: adm, id: id, previllage: previllage },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    });

    detailMenu.on('click', '.delSubMenu', function() {
        var id = $(this).data('id');
        var admid = $(this).data('admin');

        $.ajax({
            url: '../php/ajax/management.php?type=deleteSubmenu',
            method: 'post',
            data: { admin: admid, id: id },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        });
    });

    $('#satuanForm').on('submit', function(e) {
        e.preventDefault();

        var spec = $('#specSatuan option:selected').val();
        var cat = $('#catSatuan option:selected').val();
        var sub = $('#subCatSatuan option:selected').val();
        var isi = $('#namaSatuan').val();
        var adm = $('#adminSatuan').val();

        $.ajax({
            url: '../php/ajax/management.php?type=addSatuan',
            method: 'post',
            data: { admin: adm, content: spec, category: cat, subcategory: sub, isi: isi },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        });
    });
    $('#specSatuan').on('change', function() {
        var opt = $(this).find("option:selected");
        var id = opt.val();
        var text = opt.text();


        var data = ["1", "2", "3"];
        if (jQuery.inArray(id, data) != -1) {
            $.ajax({
                url: '../php/ajax/management.php?type=catSatuan',
                method: 'post',
                data: { data: id },

                success: function(data) {
                    $('#satuanCat').removeClass('hidden');
                    $.each(data, function(index, value) {
                        $('#catSatuan').append('<option value="' + value.id + '" data-type="' + value.content_id + '">' + value.category + '</option>');
                    })
                }
            });
            //console.log("is in array");
        } else {
            //console.log("is NOT in array");
        }
    });
    $('#form-logs').on('submit', function(e) {
        e.preventDefault();

        var tgl = $('#hidde_date_field').val();
        var listAdm = $('#adminLogs option:selected').val();


        window.location.href = '?p=log_user&type=logs&range=' + tgl + '&admin=' + listAdm;

    });

})

$(function() {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#logsRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#hidde_date_field').val(start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD'));
    }

    $('#logsRange').daterangepicker({
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