@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-start">
            @include('layouts.left-menu')
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
                <div class="d-sm-flex align-items-center justify-content-between mb-4 pt-3">
                    <h1 class="h3 mb-0 text-gray-800">Class Fee Assignments</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignFeeModal">
                        <i class="bi bi-plus-circle"></i> Assign Fee to Class
                    </button>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    @foreach($classes as $class)
                        <div class="col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">{{ $class->class_name }}</h6>
                                    <span class="badge bg-info text-white">Total:
                                        ₦{{ number_format($class->classFees->sum('amount'), 2) }}</span>
                                </div>
                                <div class="card-body">
                                    @if($class->classFees->isEmpty())
                                        <p class="text-muted small">No fees assigned to this class.</p>
                                    @else
                                        <ul class="list-group list-group-flush">
                                            @foreach($class->classFees as $fee)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $fee->feeHead->name ?? 'Unknown Fee' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $fee->description }}</small>
                                                    </div>
                                                    <div class="text-right d-flex align-items-center">
                                                        <span
                                                            class="badge bg-secondary me-2">₦{{ number_format($fee->amount, 2) }}</span>
                                                        <form action="{{ route('accounting.fees.class.destroy', $fee->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Remove this fee assignment?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link text-danger p-0 border-0"
                                                                title="Delete">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="assignFeeModal" tabindex="-1" role="dialog" aria-labelledby="assignFeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignFeeModalLabel">Assign Fee to Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('accounting.fees.class.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="class_id" class="form-label">Select Class</label>
                            <select class="form-select" name="class_id" required>
                                <option value="">-- Choose Class --</option>
                                @foreach($allClasses as $c)
                                    <option value="{{ $c->id }}">{{ $c->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="fee_head_id" class="form-label">Select Fee Type</label>
                            <select class="form-select" name="fee_head_id" required>
                                <option value="">-- Choose Fee Head --</option>
                                @foreach($feeHeads as $h)
                                    <option value="{{ $h->id }}">{{ $h->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="amount" class="form-label">Amount (₦)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <input type="text" class="form-control" name="description" placeholder="e.g. Terms 1-3">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign Fee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection