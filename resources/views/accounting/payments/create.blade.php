@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-start">
            @include('layouts.left-menu')
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
                <div class="d-sm-flex align-items-center justify-content-between mb-4 pt-3">
                    <h1 class="h3 mb-0 text-gray-800">Collect Payment</h1>
                    <a href="{{ route('accounting.payments.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to History
                    </a>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">New Payment Record</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('accounting.payments.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <!-- Student Selection -->
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">Select Student <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="student_id" required>
                                        <option value="">-- Choose Student --</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->first_name }}
                                                {{ $student->last_name }} (ID: {{ $student->id }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Class Selection -->
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label">Class <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="class_id" required>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->class_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Session & Term -->
                                <div class="col-md-6 mb-3">
                                    <label for="school_session_id" class="form-label">Academic Session <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="school_session_id" required>
                                        @foreach($sessions as $s)
                                            <option value="{{ $s->id }}">{{ $s->session_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="semester_id" class="form-label">Term <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="semester_id" required>
                                        @foreach($semesters as $sem)
                                            <option value="{{ $sem->id }}">{{ $sem->semester_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Payment Details -->
                                <div class="col-md-6 mb-3">
                                    <label for="transaction_date" class="form-label">Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="transaction_date"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="amount_paid" class="form-label">Amount (₦) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="amount_paid"
                                        placeholder="0.00" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="reference_no" class="form-label">Reference No (Optional)</label>
                                    <input type="text" class="form-control" name="reference_no"
                                        placeholder="Leave blank to auto-generate">
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Save
                                    Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection