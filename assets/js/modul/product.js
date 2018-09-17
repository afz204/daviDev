function resetForm() {
    location.reload();
}

function productStatus(status, id) {
    if (!confirm('Are you sure ?')) {
        return false;
    } else {

        $.ajax({
            url: '../php/ajax/product.php?type=changeStatusProduct',
            type: 'post',
            data: { kode_status: status, kode_product: id },

            success: function(msg) {
                alert(msg);
                location.reload();
            }
        });
    }

}
$(document).ready(function() {

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
        uploadUrl: '../php/ajax/uploadImagesProduct.php',
        uploadExtraData: function() {
            return {
                imagesid: $('#ImagesProductID').val(),
                imagesname: $('#ImagesName').val()
            };
        }
    }).on('fileloaded', function(event, file, previewId, index, reader) {
        console.log("fileloaded");
    }).on('filebatchuploadsuccess', function(event, data) {
        // var buttonSuccessProduct = $('<button class="btn btn-block btn-link">Done !</button>');
        // // $.each(data.files, function(key, file) {
        // //     var fname = file.name;
        // //     out = out + '<li>' + 'Uploaded file # ' + (key + 1) + ' - '  +  fname + ' successfully.' + '</li>';
        // // });
        // $('#kv-success-2').append(buttonSuccessProduct);
        // $('#kv-success-2').fadeIn('slow');
    });

    $('#tableProduct').DataTable();

    $('#simple-select2').select2({
        theme: 'bootstrap4',
        placeholder: "Select an option",
        allowClear: true
    });

    $('#tagsProduct').select2({
        theme: 'bootstrap4',
        placeholder: "Select an option",
        allowClear: true
    });

    $('[name="codeProduct"]').on("input", function() {
        var dInput = this.value;
        $('[name="ImagesProductID"]').val(dInput);
    });

    $('[name="nameProduct"]').on("input", function() {
        var dInput = this.value;
        var name = dInput.split(" ").join("_");
        $('[name="ImagesName"]').val(name);
    });

    $('#newProduct').on('submit', function(e) {
        e.preventDefault();

        var code = $('#codeProduct').val();
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
            url: '../php/ajax/product.php?type=newProd',
            type: 'post',
            data: 'codeProduct=' + code + '&cat=' + cat + '&sub=' + sub + '&title=' + title + '&tags=' + tags + '&cost=' + cost + '&sell=' + sell +
                '&city=' + city + '&short=' + short + '&full=' + full + '&admin=' + admin + '&note=' + note + '&type=' + list,

            success: function(msg) {
                if (msg == '0') {
                    alert('Code product telah terpakai!');
                    $('#codeProduct').addClass('parsley-error');
                } else {
                    // alert(msg);
                    window.location.href = '..?p=product';
                }

                // $('#ImagesProductID').val(title);
                // $('#imagesProduct').removeClass('hidden');
                // $('#detailProduct').addClass('hidden');
            }
        });
    });
})