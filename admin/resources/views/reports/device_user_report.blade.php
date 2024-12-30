@extends('layouts.app')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">iOS & Android Users Report</h4>
                <!-- Export Button -->
                <div class="row mb-3">
                    <div class="col">
                        <a href="{{ route('export-combined-user-report') }}" class="btn btn-success">
                            Export
                        </a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card d-flex align-items-center">
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                    <div class="ms-3">
                                        <h6 class="text-facebook">Total iOS Users</h6>
                                        <p class="mt-2 text-muted text-center card-text"><b>{{ $iosUsersCount }}</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card d-flex align-items-center">
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                    <div class="ms-3">
                                        <h6 class="text-facebook">Total Android Users</h6>
                                        <p class="mt-2 text-muted text-center card-text"><b>{{ $androidUsersCount }}</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Combined iOS & Android Users Table -->
                <div class="table-responsive mt-4">
                    <h5>All Users</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Device Type</th>
                                <th>Date Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $key => $user)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $user->name ?? '-' }}</td>
                                    <td>{{ $user->email ?? '-' }}</td>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                    <td>{{ $user->device_type }}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end">
                    {{ $users->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
