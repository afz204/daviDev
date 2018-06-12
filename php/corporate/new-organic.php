<div class="card card-body" style="">

    <h4 class="mb-3" style="border-bottom: 2px dashed #ebebeb; padding-bottom: 1%;">New Customer</h4>
    <form id="formCustomer" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">first_name</label>
                <input type="text" class="form-control" id="first_name" autocomplete="text" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">last_name</label>
                <input type="text" class="form-control" id="last_name" autocomplete="text" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid last name is required.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">email_customer</label>
                <input type="text" class="form-control" id="email_customer" placeholder="" autocomplete="email" value="" data-parsley-type="email" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">jenis_kelamin</label>
                <select class="custom-select my-1 mr-sm-2" id="jenis_kelamin" required="">
                    <option value="">Choose...</option>
                    <option value="0">Female</option>
                    <option value="1">Male</option>
                </select>
                <div class="invalid-feedback">
                    Valid last name is required.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">mobile_phone</label>
                <input type="text" class="form-control" data-parsley-type="number" id="mobile_phone" autocomplete="text" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">phone_number</label>
                <input type="text" class="form-control" data-parsley-type="number" id="phone_number" autocomplete="text" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid last name is required.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">birth_day</label>
                <input type="text" class="form-control" id="birth_day" autocomplete="text" placeholder="" value="" required="">
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">password_login</label>
                <input type="text" data-parsley-type="password" class="form-control" id="password_login" autocomplete="text" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid last name is required.
                </div>
            </div>
        </div>


        <hr class="mb-4">
        <button class="btn btn-success btn-lg btn-block" type="submit">save customer</button>
    </form>

</div>