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
        uploadUrl: '../php/ajax/uploadcustomproductimages.php',
        uploadExtraData: function() {
            return {
                imagesid: $('#codeProduct').val(),
                imagesname: $('#nameProduct').val()
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


})