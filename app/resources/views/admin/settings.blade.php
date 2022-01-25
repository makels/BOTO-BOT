@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="settings card">
                <div class="card-header">{{ __('Settings') }}</div>
                    <form method="POST" action="{{ route('admin.settings.save') }}">
                        @csrf

                        <div class="row mb-8">
                            <label for="sheet_url" class="col-md-4 col-form-label text-md-end">{{ __('SpreadsheetID') }}</label>

                            <div class="col-md-5">
                                <input id="sheet_url" type="text" class="form-control @error('empty') is-invalid @enderror" name="url_sheet" value="@if(isset($url_sheet->value)) {{ $url_sheet->value }} @endif" required>
                                @if(isset($gs_message) && $status == -1)
                                    <span class="message-error"><strong>{{ $gs_message }}</strong></span>
                                @endif
                                @if(isset($gs_message) && $status == 0)
                                    <span class="message-success"><strong>{{ $gs_message }}</strong></span>
                                @endif

                                @error('empty')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('admin.import') }}'">
                                    {{ __('Import') }}
                                </button>
                            </div>
                        </div>

                        <div class="row mb-8">
                            <label for="telegram_header" class="col-md-4 col-form-label text-md-end">{{ __('Header text in Telegram Bot') }}</label>

                            <div class="col-md-7">
                                <input id="telegram_header" type="text" class="form-control @error('empty') is-invalid @enderror" name="telegram_header" value="@if(isset($telegram_header->value)) {{ $telegram_header->value }} @endif" required>

                                @error('empty')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                    </form>
                <div class="card-body">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
