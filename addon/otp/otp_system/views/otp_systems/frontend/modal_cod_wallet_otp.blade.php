<div class="modal fade" id="cod_wallet_otp-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('OTP Verfication') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-default" id="purchaseOtpForm" role="form" method="POST">
                @csrf
                @php
                    $user = auth()->user();
                    $phone = $user->phone;
                @endphp
                <input type="hidden" name="phone" value="{{ $phone }}">
                <div class="modal-body c-scrollbar-light">
                    <div class="p-1">
                        <div class="form-group">
                            <label class="fs-14 fw-700 text-soft-dark">
                                {{  translate('Verification Code') }} 
                                <span class="text-primary fs-10">({{ translate('OTP expires 5 minutes after being sent.') }})</span>
                            </label>
                            <input type="number" class="form-control rounded-0" placeholder="{{  translate('Verification Code') }}" name="otp_code" autocomplete="off">
                        </div>
                        <div class="mb-4 mt-4">
                            <button type="submit" class="btn btn-primary btn-block fw-700 fs-14 rounded-0 submit-button">{{  translate('Verify') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>