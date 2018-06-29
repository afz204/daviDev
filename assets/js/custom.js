function btn_submit(id){
    $('#'+id).html('<span class="badge badge-primary text-center" style="font-size: 14px; margin-left: 40%;">Please wait while loading!!!!</span>');
}
$(document).ready(function() {
   
    $('#ProvinsiCorporate').select2({ width: '100%', theme: "bootstrap4" });
    $('#KotaCorporate').select2({ width: '100%', theme: "bootstrap4" });
    $('#kecamatanCorporate').select2({ width: '100%', theme: "bootstrap4" });
    $('#listCorporate').select2({ width: '100%', theme: "bootstrap4" });
    $('#listPicCorp').select2({ width: '100%', theme: "bootstrap4" });
    
    
    $('#ProvinsiCorporate').on('change', function(e) {
        e.preventDefault();
        var id = $(this).find("option:selected");
        var value = id.val();
        var text = id.text();

        $.ajax({
            url: '../php/ajax/states.php?type=provinsi',
            type: 'post',
            data: 'id=' + value,

            success: function(msg) {
                console.log(msg);
                $('#KotaCorporate').empty();

                $.each(msg, function(index, value) {
                    $('#KotaCorporate').append('<option value="' + value.id + '">' + value.name + '</option>');
                })
            }
        });
    })

    $('#KotaCorporate').on('change', function(e) {
        e.preventDefault();
        var id = $(this).find("option:selected");
        var value = id.val();
        var text = id.text();

        $.ajax({
            url: '../php/ajax/states.php?type=kota',
            type: 'post',
            data: 'id=' + value,

            success: function(msg) {
                console.log(msg);
                $('#kecamatanCorporate').empty();

                $.each(msg, function(index, value) {
                    $('#kecamatanCorporate').append('<option value="' + value.id + '">' + value.name + '</option>');
                })
            }
        });
    })
    $('#kecamatanCorporate').on('change', function(e) {
        e.preventDefault();
        var id = $(this).find("option:selected");
        var value = id.val();
        var text = id.text();

        $.ajax({
            url: '../php/ajax/states.php?type=kecamatan',
            type: 'post',
            data: 'id=' + value,

            success: function(msg) {
                console.log(msg);
                $('#kelurahanCorporate').empty();

                $.each(msg, function(index, value) {
                    $('#kelurahanCorporate').append('<option value="' + value.id + '">' + value.name + '</option>');
                })
            }
        });
    });
    $('#categoryProduct').on('change', function(e) {
        e.preventDefault();
        var id = $(this).find("option:selected");
        var value = id.val();
        var text = id.text();

        if (value != 4) {
            $.ajax({
                url: '../php/ajax/states.php?type=subCat',
                type: 'post',
                data: 'id=' + value,

                success: function(msg) {
                    console.log(msg);
                    $('#subCatProduct').empty();

                    $.each(msg, function(index, value) {
                        $('#subCatProduct').append('<option value="' + value.id + '">' + value.name + '</option>');
                    })
                }
            });
        } else {
            $('#subCatProduct').append('<option value="0">none</option>');
        }
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
                    $('#catSatuan').empty();
                    $('#satuanCat').removeClass('hidden');

                    $.each(data, function(index, value) {
                        $('#catSatuan').append('<option value="' + value.id + '" data-type="' + value.content_id + '">' + value.category + '</option>');
                    })
                }
            });
            //console.log("is in array");
        } else {
            //console.log("is NOT in array");
            $('#catSatuan').empty();
            $('#satuanCat').addClass('hidden');
        }
    });

    $('#listCorporate').on('change', function(e) {
        e.preventDefault();
        var id = $(this).find("option:selected");
        var value = id.val();
        var text = id.text();

        $.ajax({
            url: '../php/ajax/states.php?type=listCorporate',
            type: 'post',
            data: 'id=' + value,

            success: function(msg) {
                console.log(msg);
                $('#listPicCorp').empty();

                $.each(msg, function(index, value) {
                    $('#listPicCorp').append('<option value="' + value.id + '">' + value.name + '</option>');
                })
            }
        });
    });
})