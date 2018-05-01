function resetForm() {
    location.reload();
}
$(document).ready(function () {

    var url = 'http://localhost/bungdav/php/ajax/uploadImagesProduct.php';


    $('#simple-select2').select2({
        theme: 'bootstrap4',
        placeholder: "Select an option",
        allowClear: true
    });

    $("#images").fileinput({
        //'theme': 'explorer-fa',
        theme: 'fa',
        overwriteInitial: false,
        initialPreviewAsData: true,
        maxFilePreviewSize: 60,
        previewFileType: "image",
        allowedFileExtensions: ["jpg"],
        uploadAsync: false,
        minFileCount: 1,
        maxFileCount: 1,
        uploadUrl: url,
        uploadExtraData: function() {
            return {
                imagesid: $('#ImagesProductID').val()
            };
        }
    }).on('filebatchuploadsuccess', function(event, data) {
        var buttonSuccessProduct = $('<button class="btn btn-block btn-outline-success" onclick="resetForm()">Done !</button>');
        // $.each(data.files, function(key, file) {
        //     var fname = file.name;
        //     out = out + '<li>' + 'Uploaded file # ' + (key + 1) + ' - '  +  fname + ' successfully.' + '</li>';
        // });
        $('#kv-success-2').append(buttonSuccessProduct);
        $('#kv-success-2').fadeIn('slow');
    });

    $('#listLokasi').on('change', function () {
        var id = $(this).find('option:selected').val();

        if(id != '1'){
            $('#lokasiProduct').removeClass('hidden');
            $('.select2-container--bootstrap4').removeAttr('style');
            $('.select2-search__field').removeAttr('style');

        }else{
            $('#lokasiProduct').addClass('hidden');

        }

    });

    $('#newProduct').on('submit', function(e){
        e.preventDefault();

        var cat = $('#categoryProduct option:selected').val();
        var sub = $('#subCatProduct option:selected').val();
        var title = $('#nameProduct').val();
        var tags = $('#tagsProduct').val();
        var cost = $('#costProduct').val();
        var sell = $('#sellProduct').val();
        var city = $('#simple-select2').select2('val');
        var short = $('#shortDesc').val();
        var full = $('#fullDesc').val();
        var admin = $('#adminProduct').val();
        var note = $('#noteProduct').val();
        var list = $('#listLokasi option:selected').val();

        $.ajax({
            url  : '../php/ajax/product.php?type=newProd',
            type : 'post',
            data : 'cat='+cat+'&sub='+sub+'&title='+title+'&tags='+tags+'&cost='+cost+'&sell='+sell+
            '&city='+city+'&short='+short+'&full='+full+'&admin='+admin+'&note='+note+'&type='+list,

            success: function (msg) {
                alert(msg);
                $('#ImagesProductID').val(title);
                $('#imagesProduct').removeClass('hidden');
                $('#detailProduct').addClass('hidden');
            }
        });
    });
})