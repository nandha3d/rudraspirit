@extends('backend.layouts.app')

@section('content')
    <div class="col-12 col-sm-12 col-lg-12 mx-auto">
        <div class="aiz-titlebar text-left pb-5px">
            <div class="row align-items-center">
                <div class="col-auto">
                    <h1 class="h3 fw-bold">{{ translate('Unverified Customers') }}</h1>
                </div>
            </div>
        </div>

        <div class="card">
            <form class="" id="sort_customers" action="" method="GET">
                <div class="card-header row border-0 pb-0 mt-2">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <select class="form-control aiz-selectpicker" name="docs_submited" onchange="this.form.submit()">
                            <option value="">{{ translate('All Documents') }}</option>
                            <option value="submitted" @if ($docs_submited == 'submitted') selected @endif>{{ translate('Documents Submitted') }}</option>
                            <option value="not_submitted" @if ($docs_submited == 'not_submitted') selected @endif>{{ translate('Documents Not Submitted') }}</option>
                        </select>
                    </div>
                    <div class="col pl-0 pl-md-3">
                        <div class="input-group mb-0 border border-light px-3 bg-light rounded-1">
                            <input type="text" class="form-control form-control-sm border-0 px-2 bg-transparent" name="search"
                                value="{{ $sort_search }}" placeholder="{{ translate('Search by name or email') }}">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-link" type="submit"><i class="las la-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Name') }}</th>
                            <th data-breakpoints="md">{{ translate('Email') }}</th>
                            <th data-breakpoints="md">{{ translate('Phone') }}</th>
                            <th data-breakpoints="md">{{ translate('Documents') }}</th>
                            <th data-breakpoints="md">{{ translate('Registered At') }}</th>
                            <th class="text-right">{{ translate('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $key => $user)
                            <tr>
                                <td>{{ ($key + 1) + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    @if ($user->verification_info != null)
                                        <span class="badge badge-inline badge-info">{{ translate('Submitted') }}</span>
                                    @else
                                        <span class="badge badge-inline badge-secondary">{{ translate('Not Submitted') }}</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at ? $user->created_at->format('d M, Y') : '' }}</td>
                                <td class="text-right">
                                    @can('customer_login')
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('customers.login', encrypt($user->id)) }}" title="{{ translate('Log in as this Customer') }}">
                                            <i class="las la-sign-in-alt"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">{{ translate('No unverified customers found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="aiz-pagination mt-3">
                    {{ $users->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
