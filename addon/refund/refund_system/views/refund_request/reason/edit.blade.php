@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Edit Refund Reason Information') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('refund_reason_update', $refund_reason->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="col-form-label">{{ translate('Type') }} <span class="text-danger">*</span></label>
                            <select name="type" class="form-control aiz-selectpicker mb-2 mb-md-0" required>
                                <option class="text-uppercase">{{ translate('Select Type') }}</option>
                                <option value="customer_refund_reason" class="text-uppercase" {{ $refund_reason->type == 'customer_refund_reason' ? 'selected' : '' }}>{{ translate('Customer Refund Reason') }}</option>
                                <option value="admin/seller_reject_refund_reason" class="text-uppercase" {{ $refund_reason->type == 'admin/seller_reject_refund_reason' ? 'selected' : '' }}>{{ translate('Admin/Seller Reject Refund Reason') }}</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="col-from-label">
                                {{ translate('Reason') }} <span class="text-danger">*</span>
                                <span class="fs-10">({{ translate('Max 120 Character') }})</span>
                            </label>
                            <textarea name="reason" rows="3" class="form-control">{{ $refund_reason->reason }}</textarea>
                            @error('reason')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
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
