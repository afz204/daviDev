$(document).ready(function() {
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

        if(value != 4){
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
        }else{
            $('#subCatProduct').append('<option value="0">none</option>');
        }
    });
})