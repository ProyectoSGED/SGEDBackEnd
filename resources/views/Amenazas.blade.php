@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h1>{{ __('Amenazas') }}</h1></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('Amenazas') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                </div>
                            </div>
                        </div>
                                @if ($Amenazas)
                                	@foreach($Amenazas as $AmenazasItem)
                                		<li>{{ $AmenazasItem ['title'] }}</li>
                                	@endforeach
                                @else
                                		<li> No Hay Amenazas para mostrar. </li>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection