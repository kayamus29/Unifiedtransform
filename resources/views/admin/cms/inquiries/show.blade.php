@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Inquiry Details</h1>
            <a href="{{ route('admin.cms.inquiries.index') }}" class="btn btn-secondary shadow-sm">Back to List</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Message from {{ $submission->name }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3 font-weight-bold">From:</div>
                            <div class="col-md-9">{{ $submission->name }} ({{ $submission->email }})</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 font-weight-bold">Subject:</div>
                            <div class="col-md-9">{{ $submission->subject ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 font-weight-bold">Received At:</div>
                            <div class="col-md-9">{{ $submission->created_at->format('M d, Y H:i:s') }}</div>
                        </div>
                        <hr>
                        <div class="message-content p-3 bg-light rounded">
                            {!! nl2br(e($submission->message)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
