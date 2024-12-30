@extends('layouts.app')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Call SMS & Email Report</h4>

                <!-- Filters -->
                <form action="{{ route('call-sms-report') }}" method="GET">
                    <div class="row">

                        <div class="col-md-4 grid-margin">
                            <div class="card d-flex align-items-center">
                                <div class="card-body">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="ms-3">
                                            <h6 class="text-facebook">Total Call</h6>
                                            <p class="mt-2 text-muted text-center card-text"><b>{{ $totalCalls }}</b></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 grid-margin">
                            <div class="card d-flex align-items-center">
                                <div class="card-body">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="ms-3">
                                            <h6 class="text-facebook">Total SMS</h6>
                                            <p class="mt-2 text-muted  text-center card-text"><b>{{ $totalMessages }}</b></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 grid-margin">
                            <div class="card d-flex align-items-center">
                                <div class="card-body">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="ms-3">
                                            <h6 class="text-facebook">Total Email</h6>
                                            <p class="mt-2 text-muted  text-center card-text"><b>{{ $totalEmail }}</b></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Date Range Filter -->
                        <div class="col-md-3">
                            <label for="start_date">Start Date:</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request()->start_date ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">End Date:</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request()->end_date ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>

                        <!-- User Filter -->
                        <div class="col-md-3">
                            <label for="user_id">User:</label>
                            <select name="user_id" class="form-control">
                                <option value="">All User</option>
                                @foreach(App\Models\User::where('user_type', 'customer')->get() as $item)
                                    <option value="{{ $item->id }}" {{ request()->user_id == $item->id ? 'selected' : '' }}> {{  $item->phone }} - ( {{ $item->name ?? '-' }} )</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Type Filter (CALL or SMS) -->
                        <div class="col-md-3">
                            <label for="type">Type:</label>
                            <select name="type" class="form-control">
                                <option value="">All</option>
                                <option value="CALL" {{ request()->type == 'CALL' ? 'selected' : '' }}>CALL</option>
                                <option value="SMS" {{ request()->type == 'SMS' ? 'selected' : '' }}>SMS</option>
                                <option value="EMAIL" {{ request()->type == 'EMAIl' ? 'selected' : '' }}>EMAIL</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            <a href="{{ route('export-call-sms-report', request()->query()) }}" class="btn btn-success">
                                Export
                            </a>
                        </div>
                    </div>
                </form>


                <!-- Table of Transactions -->
                <div class="table-responsive mt-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Count</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $key => $transaction)
                                <tr>
                                    <td>{{ ($key + 1) }}</td>
                                    <td>{{ $transaction->user->name ?? '-' }}</td>
                                    <td>{{ $transaction->customer->name  ?? '-' }}</td>
                                    <td>{{ $transaction->type }}</td>
                                    <td>{{ $transaction->count }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end">
                    {{ $transactions->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
