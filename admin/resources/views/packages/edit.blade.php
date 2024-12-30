@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Package Information</h4>
                <form class="forms-sample" method="POST" action="{{ route('packages.update', $packages->id) }}">
                    <input name="_method" type="hidden" value="PATCH">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputUsername1">Name</label>
                        <input type="text" class="form-control" placeholder="Name" value="{{ $packages->name }}" name="name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Package Calls</label>
                        <input type="text" class="form-control" placeholder="package Calls" value="{{ $packages->package_calls }}" name="package_calls">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Message</label>
                        <input type="text" class="form-control" placeholder="package Message" value="{{ $packages->package_message }}" name="package_message">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Details</label>
                        <input type="text" class="form-control" placeholder="package Details" value="{{ $packages->package_details }}" name="package_details">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Price</label>
                        <input type="number" step="0.001" class="form-control" placeholder="Price" value="{{ $packages->price }}" name="price">
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                    <a href="{{ route('packages.index') }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection