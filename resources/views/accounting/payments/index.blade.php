@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-start">
            @include('layouts.left-menu')
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
                <div class="d-sm-flex align-items-center justify-content-between mb-4 pt-3">
                    <h1 class="h3 mb-0 text-gray-800">Fee Payments</h1>
                    <a href="{{ route('accounting.payments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Collect Payment
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Ref No</th>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Session/Term</th>
                                        <th>Amount Paid</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->reference_no }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->transaction_date)->format('d M Y') }}</td>
                                            <td>{{ $payment->student->first_name ?? 'Unknown' }}
                                                {{ $payment->student->last_name ?? '' }}
                                            </td>
                                            <td>{{ $payment->schoolClass->class_name ?? '-' }}</td>
                                            <td>
                                                <small>{{ $payment->session->session_name ?? '' }}</small><br>
                                                <small class="text-muted">{{ $payment->semester->semester_name ?? '' }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">Total:
                                                    ₦{{ number_format($payment->total_fees, 2) }}</small><br>
                                                <span
                                                    class="font-weight-bold text-success">₦{{ number_format($payment->amount_paid, 2) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-danger font-weight-bold">₦{{ number_format($payment->outstanding_balance, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($payment->outstanding_balance <= 0)
                                                    <span class="badge bg-success">Fully Settled</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Owing</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('accounting.payments.show', $payment->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="bi bi-eye"></i> Receipt
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $payments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection