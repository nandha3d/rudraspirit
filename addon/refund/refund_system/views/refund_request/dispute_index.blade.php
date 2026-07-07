@extends('backend.layouts.app')

@section('content')
    @php
        CoreComponentRepository::instantiateShopRepository();
        CoreComponentRepository::initializeCache();
    @endphp


    <div class="col-12 col-sm-12 col-lg-12 mx-auto">
        <div class="aiz-titlebar text-left pb-5px">
            <div class="row align-items-center">
                <div class="col-auto">
                    <h1 class="h3 fw-bold">{{ translate('All Dispute Refund Requests') }}</h1>
                </div>
            </div>
        </div>
        <div class="card">
            <!--Nav Tab -->
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom  border-light px-25px">
                <div class="table-tabs-container">
                    @php
                        $active_tab = $active_tab ?? 'all-dispute-refunds';
                    @endphp
                    <ul class="nav nav-tabs border-0 " id="myTab" role="tablist">
                        @foreach ($refund_tabs as $refund_tab)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-0 pb-15px fs-14 fw-500 {{ $active_tab == Str::slug($refund_tab) ? 'active' : '' }}" data-toggle="tab"  role="tab" aria-selected="{{ $active_tab == Str::slug($refund_tab) ? 'true' : 'false' }}"
                                id="{{ Str::slug($refund_tab) }}-tab"  onclick="changeTab(this, '{{ Str::slug($refund_tab) }}')" aria-controls="{{ Str::slug($refund_tab) }}">
                                {{ translate($refund_tab) }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!--Card Header (Search) Start-->
            <div class="tab-filter-bar">
                <form class="" id="sort_refund_requests" action="" method="GET">
                    <div class="card-header row  border-0 pb-0 mt-2">
                        <div class="col pl-0 pl-md-3">
                            <div class="input-group mb-0 border border-light px-3 bg-light rounded-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-0 bg-transparent px-0" id="search">
                                        <svg id="Group_38844" data-name="Group 38844" xmlns="http://www.w3.org/2000/svg"
                                            width="16.001" height="16" viewBox="0 0 16.001 16">
                                            <path id="Path_3090" data-name="Path 3090"
                                                d="M8.248,14.642a6.394,6.394,0,1,1,6.394-6.394A6.4,6.4,0,0,1,8.248,14.642Zm0-11.509a5.115,5.115,0,1,0,5.115,5.115A5.121,5.121,0,0,0,8.248,3.133Z"
                                                transform="translate(-1.854 -1.854)" fill="#a5a5b8" />
                                            <path id="Path_3091" data-name="Path 3091"
                                                d="M23.011,23.651a.637.637,0,0,1-.452-.187l-4.92-4.92a.639.639,0,0,1,.9-.9l4.92,4.92a.639.639,0,0,1-.452,1.091Z"
                                                transform="translate(-7.651 -7.651)" fill="#a5a5b8" />
                                        </svg>
                                    </span>
                                </div>
                                <input type="text" class="form-control form-control-sm border-0 px-2 bg-transparent"
                                    id="search_input" name="search"placeholder="{{translate('Search Request ...')}}">
                            </div>
                        </div>
                    </div>
                    <!-- Dynamic Tab Content -->
                    <div class="tab-content filter-tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="tab-content">
                            <!-- AJAX content will load here -->
                        </div>
                    </div>
                </form>
            </div>
            <!--Card Header (Search) End-->
        </div>
    </div>
@endsection

@section('modal')
    <!-- Offcanvas -->
    <div id="rightOffcanvas" class="right-offcanvas-lg position-fixed top-0 fullscreen bg-white  py-20px z-1045">
        <!-- content will here -->
    </div>
    <!-- Overlay -->
    <div id="rightOffcanvasOverlay" class="position-fixed top-0 left-0 h-100 w-100"></div>

    <div class="modal fade uploadModal" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                <!-- Header -->
                <div class="modal-header bg-dark text-white py-2 rounded-0">
                    <h6 class="modal-title mb-0">Image Preview</h6>
                    <button type="button" class="close text-white fs-3 border-0 bg-transparent" data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body text-center bg-light p-3">
                    <img id="modalImage"
                        src=""
                        class="img-fluid rounded shadow-sm"
                        style="max-height: 80vh;">
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        function setActionState(refund_id, activeBtn = null, extraSelector = null, text = 'Processing...') {

            const group = '.dispute-action-btn-' + refund_id;

            $(group).css({
                'pointer-events': 'none',
                'opacity': '0.6'
            });

            $(group).find('button, a').prop('disabled', true).addClass('disabled');

            if (extraSelector) {
                $(extraSelector).prop('disabled', true).addClass('disabled');
                $(extraSelector).data('original-text', $(extraSelector).html());

                $(extraSelector).html(`
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    ${text}
                `);
            }

            if (activeBtn) {
                activeBtn.data('original-text', activeBtn.html());
                activeBtn.html(`
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    ${text}
                `);
            }
        }

        function resetActionState(refund_id, btn = null) {

            const group = '.dispute-action-btn-' + refund_id;

            $(group).css({
                'pointer-events': '',
                'opacity': ''
            });

            $(group).find('button, a').prop('disabled', false).removeClass('disabled');

            if (btn && btn.data('original-text')) {
                btn.html(btn.data('original-text'));
            }

            $(`[data-refund-id="${refund_id}"]`).each(function () {
                const el = $(this);
                if (el.data('original-text')) {
                    el.prop('disabled', false).removeClass('disabled');
                    el.html(el.data('original-text'));
                }
            });
        }

        function refund_request_money(refund_id) 
        {
            showBulkActionModal();
            $('#confirmation-title').text('{{ translate('Approve Refund Request !') }}');
            $('#confirmation-question').text('{{ translate('Do you want to approve this refund request?') }}');
            $('#conform-yes-btn').attr("onclick", "approve_refund(" + refund_id + ")");
            $('.confirmation-icon').addClass('d-none');
            $('#approve-confirm-icon').removeClass('d-none');
        }

        function approve_refund(refund_id) 
        {
            hideBulkActionModal();
            $.ajax({
                url: "{{ route('refund_request_money_by_admin', ':id') }}".replace(':id', refund_id),
                type: 'POST',
                data: {
                    _token: AIZ.data.csrf,
                    refund_id: refund_id
                },
                success: function(response) {
                    if (response == 1) {
                        AIZ.plugins.notify('success', '{{ translate('Refund has been sent successfully.') }}');
                        closeRightcanvas();
                        getRefundRequests(currentTab);
                    }
                }
            });
        }

        function dispute_refund_request_money(refund_id, el) 
        {
            window.currentDisputeBtn = el;
            window.currentDisputeId = refund_id;
            showBulkActionModal();
            $('#confirmation-title').text('{{ translate('Approve Dispute Refund Request !') }}');
            $('#confirmation-question').text('{{ translate('Do you want to approve this dispute refund request?') }}');
            $('#conform-yes-btn').attr("onclick", "approve_dispute_refund(" + refund_id + ")");
            $('.confirmation-icon').addClass('d-none');
            $('#approve-confirm-icon').removeClass('d-none');
        }

        function approve_dispute_refund(refund_id) 
        {
            hideBulkActionModal();
            let btn = window.currentDisputeBtn;
            let groupClass = '.dispute-action-btn-' + refund_id;
            $(groupClass).css({
                'pointer-events': 'none',
                'opacity': '0.6'
            });
            if (btn) {
                $(btn).data('original-text', $(btn).html());
                $(btn).html(`
                    <span class="spinner-border spinner-border-sm mr-1"></span>
                    Processing...
                `);
            }
            $.ajax({
                url: "{{ route('dispute_refund_request_money_by_admin', ':id') }}".replace(':id', refund_id),
                type: 'POST',
                data: {
                    _token: AIZ.data.csrf,
                    refund_id: refund_id
                },
                success: function(response) {
                    if (response == 1) {
                        AIZ.plugins.notify('success', '{{ translate('Dispute Refund has been sent successfully.') }}');
                        $(groupClass).css({
                            'pointer-events': '',
                            'opacity': ''
                        });
                        if (btn) {
                            $(btn).html($(btn).data('original-text'));
                        }
                        closeRightcanvas();
                        getRefundRequests(currentTab);
                    }
                },
                error: function() {
                    $(groupClass).css({
                        'pointer-events': '',
                        'opacity': ''
                    });
                    if (btn) {
                        $(btn).html($(btn).data('original-text'));
                    }
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        }

        let currentTab = '{{ $active_tab }}';
        var searchTimer;

        $(document).on("change", ".check-all", function() 
        {
            if(this.checked) {
                // Iterate each checkbox                                                
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });
        function sort_refund_requests(el)
        {
            $('#sort_refund_requests').submit();
        }
        
        function getRefundRequests(slug, page = 1) 
        {
            var status = $('#status').val();
            var user_id = $('#user_id').val();
            currentTab = slug;
            var slug = slug.replace(/-/g, '_');
            let keyword = $('#search_input').val();
            $('#tab-content').html('<div class="footable-loader mt-5"><span class="fooicon fooicon-loader"></span></div>');
            $.ajax({
                url: `{{ route('dispute_refund_requests.filter') }}?page=${page}`,
                method: 'GET',
                data: { status: status, refund_request_status: slug, search: keyword },
                success: function(response) {
                    $('#tab-content').html(response.html);
                    initFooTable();
                },
                error: function() {
                    $('#tab-content').html('<div class="text-danger p-4">{{ translate("Failed to load data.") }}</div>');
                }
            });
        }

        function changeTab(button, statusSlug) 
        {
            document.querySelectorAll('#myTab .nav-link').forEach(el => el.classList.remove('active'));
            button.classList.add('active');
            getRefundRequests(statusSlug);
        }

        document.addEventListener('DOMContentLoaded', function() 
        {
            getRefundRequests(currentTab);
        });

        $('#search_input').on('keyup', function () 
        {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                getRefundRequests(currentTab);
            }, 500);
        });

        $(document).on('click', '.pagination a', function(e) 
        {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            getRefundRequests(currentTab, page);
        });

        const rightOffcanvas = document.getElementById('rightOffcanvas');
        const overlay = document.getElementById('rightOffcanvasOverlay');
        let selectedUserId = null;

        $(document).on('click', '#refund_request_view', function (e) {
            e.preventDefault();

            selectedRefundId = $(this).data('user-id');
            openRightcanvasView(selectedRefundId);
        });

        $(document).on('click', '#view_payment_info', function (e) {
            e.preventDefault();

            selectedRefundId = $(this).data('user-id');
            openRightcanvasPaymentInfo(selectedRefundId);
        });

        function openRightcanvasView(refundId) {

            rightOffcanvas.classList.add('active');
            overlay.classList.add('active');
            document.body.classList.add('body-no-scroll');

            rightOffcanvas.innerHTML =
                '<div class="footable-loader mt-5"><span class="fooicon fooicon-loader"></span></div>';

            $.ajax({
                type: "POST",
                url: "{{ route('refund_request_view') }}",
                data: {
                    _token: AIZ.data.csrf,
                    refund_id: refundId
                },
                success: function (html) {
                    rightOffcanvas.innerHTML = html;
                },
                error: function () {
                    rightOffcanvas.innerHTML =
                        '<p class="text-danger p-3">{{ translate("Failed to load") }}</p>';
                }
            });
        }

        function openRightcanvasPaymentInfo(refundId) {

            rightOffcanvas.classList.add('active');
            overlay.classList.add('active');
            document.body.classList.add('body-no-scroll');

            rightOffcanvas.innerHTML =
                '<div class="footable-loader mt-5"><span class="fooicon fooicon-loader"></span></div>';

            $.ajax({
                type: "POST",
                url: "{{ route('view_payment_info_modal') }}",
                data: {
                    _token: AIZ.data.csrf,
                    refund_id: refundId
                },
                success: function (html) {
                    rightOffcanvas.innerHTML = html;
                },
                error: function () {
                    rightOffcanvas.innerHTML =
                        '<p class="text-danger p-3">{{ translate("Failed to load") }}</p>';
                }
            });
        }

        function closeRightcanvas() {
            rightOffcanvas.classList.remove('active');
            overlay.classList.remove('active');
            document.body.classList.remove('body-no-scroll');
        }

        function closeOffcanvas() {
            closeRightcanvas();
        }

        if (overlay) {
            overlay.addEventListener('click', closeRightcanvas);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeRightcanvas();
            }
        });
        
        $(document).on('submit', '#aizSubmitForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const btn = form.find('button[type="submit"]');
            const refundId = form.find('input[name="refund_id"]').val();

            const trx_id = form.find('input[name="trx_id"]').val();

            if (!trx_id) {
                AIZ.plugins.notify('warning', 'Please fill transaction id');
                return;
            }

            const rejectBtn = $('.reject-btn.refund-action-btn-' + refundId);
            const disputeBtn = $('.offline-btn.refund-action-btn-' + refundId);

            setActionState(refundId, btn, btn, 'Processing...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(res) {
                    AIZ.plugins.notify('success', 'Dispute offline approved');
                    closeRightcanvas();
                    getRefundRequests(currentTab);
                },
                error: function() {

                    const group = '.dispute-action-btn-' + refundId;

                    $(group).css({
                        'pointer-events': '',
                        'opacity': ''
                    });

                    $(group).find('button, a').prop('disabled', false);

                    btn.prop('disabled', false).removeClass('disabled');
                    btn.html(btn.data('original-text'));
                }
            });
        });
         
        $(document).ready(function() {
    
            $(document).on('change', '.reason_select', function() {
                let form = $(this).closest('[id^="reject-form-"]');
                let selectedVal = $(this).val();
                let hiddenInput = form.find('.final_reason'); 

                if (selectedVal === 'others') {
                    form.find('.other_reason_wrapper').removeClass('d-none');
                    form.find('.other_reason').val(''); 
                    hiddenInput.val(''); 
                } else {
                    form.find('.other_reason_wrapper').addClass('d-none');
                    hiddenInput.val(selectedVal); 
                }
            });

            $(document).on('keyup', '.other_reason', function() {
                let form = $(this).closest('[id^="reject-form-"]');
                form.find('.final_reason').val($(this).val());
            });

            $(document).on('click', '.reject-dispute-refund-btn', function() {

                const btn = $(this);
                const refund_id = btn.data('refund-id');
                const form = btn.closest('[id^="reject-form-"]');

                const finalReason = form.find('.final_reason').val().trim();

                if (!finalReason) {
                    AIZ.plugins.notify('warning', 'Please provide a reject reason');
                    return;
                }

                const mainRejectBtn = $('.reject-btn.refund-action-btn-' + refund_id);
                const offlineBtn = $('.offline-btn.refund-action-btn-' + refund_id);

                setActionState(
                    refund_id,
                    btn,
                    btn,
                    'Processing...'
                );

                $.ajax({
                    url: "{{ route('admin.reject_refund_request') }}",
                    type: "POST",
                    data: {
                        _token: AIZ.data.csrf,
                        refund_id: refund_id,
                        reject_reason: finalReason
                    },
                    success: function(res) {
                        AIZ.plugins.notify('success', 'Refund rejected');
                        closeRightcanvas();
                        getRefundRequests(currentTab);
                    },
                    error: function() {

                        const group = '.dispute-action-btn-' + refund_id;

                        $(group).css({
                            'pointer-events': '',
                            'opacity': ''
                        });

                        $(group).find('button, a').prop('disabled', false);

                        btn.prop('disabled', false).removeClass('disabled');
                        btn.html(btn.data('original-text'));
                    }
                });
            });
        });

        function toggleOfflineForm(id) {
            let offlineEl = document.getElementById('offline-form-' + id);
            let rejectEl = document.getElementById('reject-form-' + id);
            offlineEl.style.display = (offlineEl.style.display === 'none') ? 'block' : 'none';
            if(rejectEl) rejectEl.style.display = 'none';
        }

        function toggleRejectForm(id) {
            let rejectEl = document.getElementById('reject-form-' + id);
            let offlineEl = document.getElementById('offline-form-' + id);
            rejectEl.style.display = (rejectEl.style.display === 'none') ? 'block' : 'none';
            if(offlineEl) offlineEl.style.display = 'none';
        }

        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
            myModal.show();
        }
    </script>
@endsection