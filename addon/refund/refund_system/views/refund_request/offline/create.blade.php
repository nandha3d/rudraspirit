@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Offline Payout Method Information') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payout_method_for_customer_store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="col-form-label">{{ translate('Type') }} <span class="text-danger">*</span></label>
                            <select name="type" class="form-control aiz-selectpicker" required>
                                <option value="">{{ translate('Select Type') }}</option>
                                <option value="bank_payment">{{ ucfirst(translate('Bank Payment'))  }}</option>
                                <option value="others">{{ ucfirst(translate('Others'))  }}</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="col-form-label">{{ translate('name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" required class="form-control" placeholder="payment method name">
                        </div>

                        <div class="form-group mb-3">
                            <label class="col-from-label">{{ translate('Logo') }} <span class="text-danger">*</span></label>
                            <div class="add-product-page-content">
                                <div class="img-upload-container">
                                    <div class="input-group file-upload-input border border-dashed border-gray-400 rounded-1 w-120px h-120px d-flex align-items-center justify-content-center"
                                        data-toggle="aizuploader" data-type="image" data-multiple="false">
                                        <div
                                            class="form-control p-0 border-0 d-flex align-items-center justify-content-center">
                                            <img src="{{ static_asset('assets/img/plus-lg.svg') }}"
                                                class="w-40px h-40px w-md-64px h-md-64px" alt="generate Icon">
                                        </div>
                                        <input type="hidden" name="logo" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
