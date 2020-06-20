@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h1>{{ __('Contacto') }}</h1></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('contact') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nombre') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nombre"value="{{ old('name') }}" required autocomplete="name" autofocus>
                                {!! $errors->first('name','<small>:message</small><br>') !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Correo electrónico" value="{{ old('email')}}" required autocomplete="name" autofocus>
                                {!! $errors->first('email','<small>:message</small><br>') !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Asunto') }}</label>
                            <div class="col-md-6">
                                    <input id="subject" type="text" class="form-control @error('subject') is-invalid @enderror"
                                    name="subject" placeholder="Asunto" value="{{ old('subject')}}" required autocomplete="name" autofocus>
                                    {!! $errors->first('subject','<small>:message</small><br>') !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Mensaje') }}</label>
                            <div class="col-md-6">
                                <textarea id="content" type="text" class="form-control @error('content') is-invalid @enderror" name="content" placeholder="Mesaje" value="{{ old('content') }}" required autocomplete="name" autofocus>
                                </textarea>
                                {!! $errors->first('content','<small>:message</small><br>') !!}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
