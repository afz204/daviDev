function formatState(state) {
    if (!state.id) {
        return state.text;
    }
    var baseUrl = "assets/images/product";
    var base_url = window.location.origin;
    var pathArray = window.location.pathname.split('/');
    var folder = '';
    if (pathArray[1] == 'bungdav') {
        folder = 'bungdav/';
    } else {
        folder = '';
    }

    var nameproduct = state.text.toLowerCase();
    nameproduct = nameproduct.split('_');
    // console.log(nameproduct);
    var $state = $(
        '<span><img style="width: 8%; border-radius: 4%;" src="' + base_url + '/' + folder + baseUrl + '/' + nameproduct[0].split(' ').join('_') + '.jpg" class="img-flag" /> ' + state.text + '</span>'
    );
    return $state;
};
$(document).ready(function() {

    $('[name="custom-remaks"]').keyup(function() {
        $('[name="radio-remarks"]').val(this.value);
    });

    $('#codeSearch').select2({
        width: '100%',
        theme: "bootstrap4",
        templateResult: formatState,
        placeholder: 'Select Product',
        allowClear: true
    });
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
        if (stepDirection === 'forward') {
            elmForm.validator('validate');
            console.log(elmForm);
            var elmErr = elmForm.children('.has-error');
            //alert(elmErr.length);
            if (elmErr.length > 0) {
                // Form validation failed
                return false;
            } else {
                $('#step_' + stepNumber).val('1');
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