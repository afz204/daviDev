function formStock() {
    $('#formStokcs').removeClass('hidden');
    $('#listStok').addClass('hidden');
    $('#listTmp').addClass('hidden');
}

function addBelanja() {
    $('#form_belanja').removeClass('hidden');
    $('#listbelanja').addClass('hidden');
}

function editStock(id) {

    $.ajax({
        url: '../php/ajax/productions.php?type=editStocks',
        method: 'post',
        data: { idStock: id },

        success: function(msg) {
            console.log(msg);
            $('#stockTmp').removeClass('hidden');
            $('#specSatuan').addClass('hidden');
            $('#catSatuan').addClass('hidden');
            $('#specSatuan').prop("required", false);
            $('#catSatuan').prop("required", false);
            for (var i = 0; i < msg.length; i++) {
                $('#idStock').val(msg[i].id);
                $('#nameStock').val(msg[i].nama_barang).prop("readonly", true);
                $('#specStock').val(msg[i].spec).attr("style", "pointer-events: none;");
                $('#qtyStock').attr("placeholder", "total barang yang terpakai");
                $('#tmpStock').val(msg[i].qty).prop("readonly", true);
                $('#satuanStock').val(msg[i].satuan).attr("style", "pointer-events: none;");
                $('#hargaStock').val(msg[i].harga).prop('readonly', true);

            }
            $('#formStokcs').removeClass('hidden');
            $('#listStok').addClass('hidden');
        }
    })


}

function viewStock(id) {
    
    
    $.ajax({
        url: '../php/ajax/productions.php?type=listStoks&id=' + id,
        data: "",
        dataType: 'json',

        success: function(data) {
            // console.log(data);
            var dt = "";
            $.each(data, function(key, value) {
                dt += '<tr>';
                dt += '<td>' + value.category + ' > ' + value.subcategory + ' </td>';
                dt += '<td>' + value.nama_barang + '</td>';
                dt += '<td>' + value.qty + '</td>';
                dt += '<td>' + value.ket + '</td>';
                dt += '<td>' + value.name + '</td>';
                dt += '<td>' + value.created + '</td>';
                dt += '</tr>';
            });
            
            $('#listTmp').removeClass('hidden');
            $('#listTmpTable').append(dt);
            $('#listTmpTable').DataTable();
        }
    })
}

function delBelanja(id, adm) {
    if (!confirm('Are you sure want to delete this?')) {
        return false;
    } else {
        $.ajax({
            url: '../php/ajax/productions.php?type=delBelanja',
            method: 'post',
            data: { admin: adm, keterangan: id },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    }
}

$(document).ready(function() {
    $('#tableStok').DataTable();
    $('#tableBelanja').DataTable();


    $('#stock-form').on('submit', function(e) {
        e.preventDefault();
        $('#btn_add_stock').addClass('hidden');

        var ID = $('#idStock').val();
        var cat = $('#specSatuan option:selected').val();
        var subcat = $('#catSatuan option:selected').val();
        var admin = $('#adminStock').val();
        var nama = $('#nameStock').val();
        var qty = $('#qtyStock').val();
        var tmp = $('#tmpStock').val();
        var satuan = $('#satuanStock option:selected').val();
        var harga = $('#hargaStock').val();
        var ket = $('#ketStock').val();
        var tipe = '';
        if (ID === '') {
            tipe = 'addStocks';
        } else {
            tipe = 'updateStocks';
        }
        //alert(tipe);
        //alert(admin + nama + spec + qty + satuan + harga + ket);
        $.ajax({
            url: '../php/ajax/productions.php?type=' + tipe,
            method: 'post',
            data: { idStocks: ID, category: cat, sub_category: subcat, admin: admin, title: nama, quantity: qty, tmpQty: tmp, satuan: satuan, harga: harga, keterangan: ket },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    });

    $('#belanjaForm').on('submit', function(e) {
        e.preventDefault();
        
        var admin = $('#adminBelanja').val();
        var cat = $('#specSatuan option:selected').val();
        var subcat = $('#catSatuan option:selected').val();
        var subsubcat = $('#subCatSatuan option:selected').val();
        var name = $('#nameBelanja').val();
        var qty = $('#qtyBelanja').val();
        var satuan = $('#satuanBelanja option:selected').val();
        var price = $('#hargaBelanja').val();
        var ket = $('#ketBelanja').val();

        //alert(admin + title + total + ket);
        $('#btn_prod_belanja').addClass('hidden');
        $.ajax({
            url: '../php/ajax/productions.php?type=addBelanja',
            method: 'post',
            data: { admin: admin, category: cat, subcategory: subcat, title: name, quantity: qty, satuan: satuan, harga: price, keterangan: ket },

            success: function(msg) {
                location.reload();
                alert(msg);
            }
        })
    });


    // $('#catSatuan').on('change', function() {

    //     var opt = $(this).find("option:selected");
    //     var id = opt.val();
    //     var text = opt.text();
    //     var type = opt.data("type");



    //     var data = ["1"];
    //     if (jQuery.inArray('1', data) != -1) {
    //         $.ajax({
    //             url: '../php/ajax/management.php?type=subCatSatuan',
    //             method: 'post',
    //             data: { data: id },

    //             success: function(data) {
    //                 $('#subCatSatuan').empty();
    //                 $('#satuanSubCat').removeClass('hidden');
    //                 $.each(data, function(index, value) {
    //                     $('#subCatSatuan').append('<option value="' + value.id + '">' + value.subcategory + '</option>');
    //                 })
    //             }
    //         });
    //         console.log("is in array");
    //     } else {

    //     }
    // });
})