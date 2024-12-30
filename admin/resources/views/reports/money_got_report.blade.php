@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h4 class="card-title">Money Got & Give Report</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-12 grid-margin">
                        <div class="card d-flex align-items-center">
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                    <div class="">
                                        <h6 class="text-facebook">Total Users</h6>
                                        <p class="mt-2 text-muted text-center card-text"><b> {{ $totalUsers }}</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 grid-margin">
                        <div class="card d-flex align-items-center">
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                    <div class="">
                                        <h6 class="text-facebook">Total Give Money</h6>
                                        <p class="mt-2 text-muted text-center card-text"><b>{{ FinalPrice($totalGiveAmount) }}</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 grid-margin">
                        <div class="card d-flex align-items-center">
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                    <div class="">
                                        <h6 class="text-facebook">Total Got Money</h6>
                                        <p class="mt-2 text-muted text-center card-text"><b>{{ FinalPrice($totalGotAmount) }}</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12 grid-margin">
                        <div class="card d-flex align-items-center">
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                    <div class="">
                                        <h6 class="text-facebook">Percentage</h6>
                                        <p class="mt-2 text-muted text-center card-text"><b>{{ number_format($percentage, 2) }} %</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('money-got-report') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <!-- Date Range Filter -->
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date:</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request()->start_date ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date:</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request()->end_date ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>

                        <!-- User Filter -->
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">User:</label>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach(App\Models\User::where('user_type', 'customer')->get() as $item)
                                    <option value="{{ $item->id }}" {{ request()->user_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->phone }} - ({{ $item->name ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Transaction Type Filter (GOT or GIVE) -->
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type:</label>
                            <select name="type" class="form-select">
                                <option value="GOT" {{ request()->type == 'GOT' ? 'selected' : '' }}>GOT</option>
                                <option value="GIVE" {{ request()->type == 'GIVE' ? 'selected' : '' }}>GIVE</option>
                            </select>
                        </div>

                        <!-- Submit and Export Buttons -->
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <a href="{{ route('money-got-export', request()->query()) }}" class="btn btn-success w-100">
                                Export
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Transactions Table -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="money-got-list" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Business Name</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $key => $transaction)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $transaction->user->name ?? '-' }}</td>
                                            <td>{{ $transaction->business->bus_name ?? '-' }}</td>
                                            <td>{{ $transaction->amount }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                {{ $transactions->links("pagination::bootstrap-4") }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
