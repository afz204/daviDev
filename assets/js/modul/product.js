function resetForm()
{
    location.reload();
}
function productStatus(status, id)
{
    if(! confirm('Are you sure ?')){
        return false;
    }else{

        $.ajax({
            url  : '../php/ajax/product.php?type=changeStatusProduct',
            type : 'post',
            data : { kode_status: status, kode_product: id },

            success: function (msg) {
                alert(msg);
                location.reload();
            }
        });
    }
    
}
$(document).ready(function () {


    $('#tableProduct').DataTable();

    $('#simple-select2').select2({
        theme: 'bootstrap4',
        placeholder: "Select an option",
        allowClear: true
    });

    

    $('#newProduct').on('submit', function(e){
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
            url  : '../php/ajax/product.php?type=newProd',
            type : 'post',
            data : 'codeProduct='+code+'&cat='+cat+'&sub='+sub+'&title='+title+'&tags='+tags+'&cost='+cost+'&sell='+sell+
            '&city='+city+'&short='+short+'&full='+full+'&admin='+admin+'&note='+note+'&type='+list,

            success: function (msg) {
                if (msg == '0') {
                    alert('Code product telah terpakai!');
                    $('#codeProduct').addClass('parsley-error');
                } else {
                     // alert(msg);
                    location.reload();
                }
               
                // $('#ImagesProductID').val(title);
                // $('#imagesProduct').removeClass('hidden');
                // $('#detailProduct').addClass('hidden');
            }
        });
    });
})