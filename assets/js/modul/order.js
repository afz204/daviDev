function dataCheckout(data)
{
    $.ajax({
            url: '../php/ajax/order.php?type=listCheckout',
            type: 'post',
            data: { transctionID: data },

            success: function(msg) {
                var data = JSON.parse(msg);
                console.log(data);      
                var listProduct = $('#checkoutData').html(" ");
                listProduct.hide().append(data.product).fadeIn(1000);
            }
        });
}
function changeQtyProduct(id, field, type, count)
{
    var input       = $("input[name='"+ id +"']");
    var currentVal  = parseInt(input.val());
    //alert(currentVal);
    $.ajax({
            url: '../php/ajax/order.php?type=changeQty',
            type: 'post',
            data: 'id=' + type + '&types='+ field + '&count='+ count,

            success: function(msg) {
                console.log(msg);
                
               //  //alert(id);
               //  var data = JSON.parse(msg);
               //  // location.reload();
               // price = parseInt(data.price);
               //  input.val(price);
            }
        });
}
function modalListProduct(){
    $('#modalAddProducts').modal({ backdrop: 'static', keyboard: false });
}
function formRedeemPromo(){
    $('#linkRedem').addClass('hidden');
    $('#redeemPromo').removeClass('hidden').fadeIn(700);
}

function formAddProduct() {
    $('#addProductCheckout')[0].reset();
    $('#codeSearch').removeClass('parsley-success');
    $('#checkProduct').html('');
    $('#checkProduct').html('<button type="submit"  class="btn btn-block btn-primary ">submit</button>');
}

function formValidate(id)
{
    var trx = $('#nomorTrx').val();

    if(id == 0)
    {
        var corp = $('#listCorporate option:selected').val();
        var cpic = $('#listPicCorp option:selected').val();
    }
    if(id == 1)
    {
        var namaPenerima = $('#nama_penerima').val();
        var namaPenerima = $('#email_penerima').val();
        var namaPenerima = $('#ProvinsiCorporate').val();
        var namaPenerima = $('#KotaCorporate').val();
        var namaPenerima = $('#kecamatanCorporate').val();
        var namaPenerima = $('#kelurahanCorporate').val();
        var namaPenerima = $('#alamat_lengkap').val();
    }

    //for submit ajax check
    //$('#step_number').val();
}
$( window ).load(function() {
  // Run code
  $('#SmartWizard').hide().fadeIn(1000);
});
$(document).ready(function() {
    // $('#delivery_charges').select2();

    $('#template_level1').select2({ width: '100%', theme: "bootstrap4" });
    $('#template_level2').select2({ width: '100%', theme: "bootstrap4" });

    $('#delivery_dates').datetimepicker({

        format: 'YYYY/MM/DD',
        minDate:new Date()

    });

    $('#template_level1').on('change', function(e) {
        e.preventDefault();
        var id = $(this).find("option:selected");
        var value = id.val();
        var text = id.text();

        $.ajax({
            url: '../php/ajax/order.php?type=cardTemplate',
            type: 'post',
            data: 'id=' + value,

            success: function(msg) {
                
                $('#template_level2').empty();
                $('#isi_pesan').empty();

                $.each(msg, function(a, b){
                    $('#template_level2').append('<option data-msg="' + b.level3 +'" value="' + b.id + '">' + b.level1 + '</option>');
                    $('#isi_pesan').val(b.level3);
                    $('#template_level2').on('change', function(e){
                        e.preventDefault();
                        var id = $(this).find("option:selected");
                        var value = id.val();
                        var text = id.text();
                        var msgg = id.data('msg');

                        $('#isi_pesan').val(msgg);
                    });
                });

                // $.each(msg, function(index, value) {
                //     $('#template_level2').append('<option value="' + value.id + '">' + value.level1 + '</option>');
                //     $('#isi_pesan').val(value.level3);

                // });
                

            }
        });
    });

    

    $('#kelurahanCorporate').on('change', function(e) {
        e.preventDefault();
        var id = $(this).find("option:selected");
        var value = id.val();
        var text = id.text();

        $.ajax({
            url: '../php/ajax/order.php?type=deliveryCharges',
            type: 'post',
            data: 'id=' + value,

            success: function(msg) {
                console.log(msg);
                $('#delivery_charges').empty();

                
                    $('#delivery_charges').append('<option value="' + msg.id + '">' + msg.kelurahan + ' ' + msg.price +' </option>');
               
            }
        });
    });

    
    $('#generateOrder').on('submit', function(e) {
        e.preventDefault();
        var type = $('#typeOrder option:selected').val();

        $.ajax({
            url: '../php/ajax/order.php?type=generate',
            type: 'post',
            data: 'type=' + type,

            success: function(msg) {
                if (type === '1') {

                    alert('Anda Memilih Corporate!');
                    var newLocation = '?p=neworder&trx=' + msg;
                    window.location = newLocation;
                    return false;

                } else {
                    alert('Anda Memilih Personal!');
                    var newLocation = '?p=neworder&trx=' + msg;
                    window.location = newLocation;
                    return false;
                }

            }
        });

    });

    $('#redeemPromo').on('submit', function(e){
        e.preventDefault();
        var isFormValid = true;

        $("#codePromoInput").each(function(){
            if ($.trim($(this).val()).length == 0){
                $(this).addClass("is-invalid");
                $('#validation-feedback').addClass('invalid-feedback').html('Tidak boleh kosong!');
                isFormValid = false;
            }
            else{
                $(this).removeClass("is-invalid");
                $(this).addClass("is-valid");
                $('#validation-feedback').removeClass('invalid-feedback');
                $('#validation-feedback').addClass('valid-feedback').html('Checking!');
            }
        });
    });

    //button plus minuts product

    $(document).on('click', '.btn-number-count', function(e){
        e.preventDefault();

        var field = $(this).data('field');
        var id = $(this).data('id');
        var type = $(this).data('type');
        var input = $("input[name='"+ field +"']");
        var currentVal = parseInt(input.val());
        
        if (!isNaN(currentVal)) {
            if(type == 'minus') {
                var count = currentVal - 1;
                changeQtyProduct(id, type, field, count);
                if(currentVal > input.attr('min')) {
                    input.val(currentVal - 1).change();
                } 
                if(parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if(type == 'plus') {
                var count = currentVal + 1;
                 changeQtyProduct(id, type, field, count);
                if(currentVal < input.attr('max')) {
                    input.val(currentVal + 1).change();
                }
                if(parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(0);
        }
    });
    $('.input-number').focusin(function(){
       $(this).data('oldValue', $(this).val());
    });
    $(document).on('change','.input-number',function() {
    
        minValue =  parseInt($(this).attr('min'));
        maxValue =  parseInt($(this).attr('max'));
        valueCurrent = parseInt($(this).val());
        
        name = $(this).attr('name');
        if(valueCurrent >= minValue) {
            $(".btn-number-count[data-type='minus']").removeAttr('disabled')
        } else {
            alert('Sorry, the minimum value was reached');
            $(this).val($(this).data('oldValue'));
        }
        if(valueCurrent <= maxValue) {
            $(".btn-number-count[data-type='plus']").removeAttr('disabled')
        } else {
            alert('Sorry, the maximum value was reached');
            $(this).val($(this).data('oldValue'));
        }
        
        
    });

    $(document).on('keydown', '.input-number', function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    //ends buttton plus minus product

    //add procuts
    $('#addProductCheckout').on('submit', function(e){
        e.preventDefault();
        btn_submit('checkProduct');
        var code = $('#codeSearch').val();
        var trx = $('#noTransaction').val();

        $.ajax({
            url: '../php/ajax/order.php?type=addProducts',
            type: 'post',
            data: 'id=' + code + '&trx=' +trx,

            success: function(msg) {
                dataCheckout(trx);
                var data = JSON.parse(msg);
                console.log(data);
                var count = parseInt(data.qty);
                $('#listProductsData').hide().append(data.data).fadeIn('fast');
                $('#countProduct').hide().html(count).fadeIn(800);
               
            }
        });
    });
    //modal add product close
    $('#modalAddProducts').on('hidden.bs.modal', function () {
    // do something…
        formAddProduct();
    });
    $(document).on('click', '.selling_price_btn', function(e){
        e.preventDefault();

        var btnName     = $(this).data('id');
        var input       = $("input[name='"+ btnName +"']");
        var currentVal  = parseInt(input.val());

        if($.isNumeric(currentVal) == true){
            
            if(! confirm('Are you want to change price?')){
                return false;
            }else{
                

                $.ajax({
                    url: '../php/ajax/order.php?type=changePriceProduct',
                    type: 'post',
                    data: 'id=' + btnName + '&new_price=' + currentVal,

                    success: function(msg) {
                        var data = JSON.parse(msg);
                        alert(data.msg);
                        var price = parseInt(data.price);
                        //location.reload();
                        console.log(price);
                        input.attr('value', price);

                    }
                });
            }
        }else{
            alert('Error!');
        }

        // if(! confirm('Are you want to change price?')){
        //     return false;
        // }else{
            

        //     $.ajax({
        //         url: '../php/ajax/order.php?type=changePriceProduct',
        //         type: 'post',
        //         data: 'id=' + btnName + '&new_price=' + currentVal,

        //         success: function(msg) {
        //             alert(msg);
        //             location.reload();
        //         }
        //     });
        // }
        
    });
    $(document).on('click', '.isi_remarks_btn', function(e){
        e.preventDefault();

        var btnName = $(this).data('id');
        var input = $("textarea[name='"+ btnName +"']");
        var currentVal = input.val();

        if(currentVal != '' ){
            if(! confirm('Are you want to add Remarks?')){
                return false;
            }else{
                

                $.ajax({
                    url: '../php/ajax/order.php?type=addRemarksProduct',
                    type: 'post',
                    data: 'id=' + btnName + '&remarks=' + currentVal,

                    success: function(msg) {
                        alert(msg);
                        //input.val(msg);
                        //location.reload();
                    }
                });
            }
        }else{
            alert('Error!');
        }
    });

    //delete product

    $(document).on('click', '.deleteListProduct', function(e){
        e.preventDefault();
        var id = $(this).data('id');

        alert(id);
    });



})

$(document).ready(function() {

    // // Toolbar extra buttons
    // var btnFinish = $('<button></button>').text('Finish')
    //     .addClass('btn btn-info')
    //     .on('click', function() {
    //         if (!$(this).hasClass('disabled')) {
    //             var elmForm = $("#myForm");
    //             if (elmForm) {
    //                 elmForm.validator('validate');
    //                 var elmErr = elmForm.find('.has-error');
    //                 if (elmErr && elmErr.length > 0) {
    //                     alert('Oops we still have error in the form');
    //                     return false;
    //                 } else {
    //                     alert('Great! we are ready to submit form');
    //                     elmForm.submit();
    //                     return false;
    //                 }
    //             }
    //         }
    //     });
    // var btnCancel = $('<button></button>').text('Cancel')
    //     .addClass('btn btn-danger')
    //     .on('click', function() {
    //         $('#smartwizard').smartWizard("reset");
    //         $('#myForm').find("input, textarea").val("");
    //     });



    // Smart Wizard
    $('#smartwizard').smartWizard({
        selected: 0,
        theme: 'default',
        transitionEffect: 'fade',
        toolbarSettings: {
            toolbarPosition: 'bottom'
            // toolbarExtraButtons: [btnFinish, btnCancel]
        },
        anchorSettings: {
            markDoneStep: true, // add done css
            markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
            removeDoneStepOnNavigateBack: true, // While navigate back done step after active step will be cleared
            enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
        }
    });

    $("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
        var elmForm = $("#form-step-" + stepNumber);
       //alert(stepNumber);
        // stepDirection === 'forward' :- this condition allows to do the form validation 
        // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
        if(stepDirection === 'forward'){
            elmForm.validator('validate'); console.log(elmForm);
            var elmErr = elmForm.children('.has-error');
            //alert(elmErr.length);
            if(elmErr.length > 0){
                        // Form validation failed
                        return false;    
            }else{
                $('#step_'+stepNumber).val('1');
                formValidate(stepNumber);
            }
            
        }
        return true;
    });

    $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
        // Enable finish button only on last step
        if (stepNumber == 4) {
            $('.btn-finish').removeClass('disabled');
        } else {
            $('.btn-finish').addClass('disabled');
        }
    });

});