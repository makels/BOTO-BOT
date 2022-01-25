@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Products') }}</div>

                <div class="card-body">
                    <!-- Products list -->
                    @foreach($products as $product)
                        @include('parts.product')
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
