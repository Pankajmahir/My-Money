@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <form method="POST" action="{{ route('user.excel.download') }}">
            @csrf
            <div class="justify-content-end d-flex">
                <div class="form-outline">
                    <input type="date" class="form-control mr-2 mb-2"  name="start_date" @isset($start_date) value="{{ $start_date }}" @endisset>
                </div>
                <div class="form-outline">
                    <input type="date" class="form-control mr-2 mb-2" name="end_date" @isset($end_date) value="{{ $end_date }}" @endisset>
                </div>  
                <button type="submit"  class="btn btn-sm btn-primary mb-2"> Download Excel </button><br>
            </div>
            <div class="justify-content-end d-flex">
            @foreach ($errors->all() as $error)
               <p class="text-danger">{{ $error }}</p>
            @endforeach
            </div> 
        </form>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h4 class="card-title">User Information</h4>
                    </div>
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-end" style="min-width: 200px;">
                            <form class="" id="sort_categories" action="" method="GET">
                                <div class="form-outline">
                                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder=" Type name & Enter">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="order-ssss" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>User Name</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Phone</th>
                                        <th>Total Left Calls</th>
                                        <th>Total Left Message</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $key=>$user)
                                        <tr>
                                            <td>{{ ($key+1) + ($users->currentPage() - 1)*$users->perPage() }}</td>
                                            <td> <img src="{{ $user->image ?? asset('user.png') }}" /> </td>
                                            <td>{{ $user->username ?? '-' }}</td>
                                            <td>{{ $user->name ?? '-' }}</td>
                                            <td>{{ $user->lastname ?? '-' }}</td>
                                            <td>{{ $user->phone ?? '-' }}</td>
                                            <td> <button class="btn btn-success">{{ $user->total_call ?? '-' }}</button> </td>
                                            <td> <button class="btn btn-success"> {{ $user->total_message ?? '-' }} </button> </td>
                                            <td>{{ $user->created_at->toDateString() }}</td>
                                            <td>{{ $user->created_at->format('h:i A') }} </td>
                                            <td>
                                                <label class="switch">
                                                    <input onchange="update_status(this)" type="checkbox"  value="{{ $user->id }}" <?php if($user->status == 1) echo "checked";?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)"  class="btn btn-danger btn-sm"
                                                onclick="ConformAlerts({{$user->id}},'{{ route('users.destroy') }}' )" >
                                                <i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                {{ $users->links( "pagination::bootstrap-4") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')

<script>

    function update_status(el){
        if(el.checked){
            var status = 1;
        }
        else{
            var status = 0;
        }
        $.post('{{ route('users.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
            if(data == 1){
                toastr.success('Status Changed successfully!');
            }
            else{
                toastr.error('Something Wrong!');
            }
        });
    }

    function getExcel(params) {
       var start_date = $("#start_date").val();
       var end_date = $("#end_date").val();

       if(start_date == "" || end_date == ""){
            toastr.error('Please select start date and end date');
       }else{
            $.ajax({  
                type: "POST",  
                url: '{{ route('user.excel.download') }}', 
                data: {
                    _token:'{{ csrf_token() }}',
                    start_date:start_date,
                    end_date:end_date,
                },
                success: function(data) {
                   
                }
            });
       }
    }
    </script>
@endsection