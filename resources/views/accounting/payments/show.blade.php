@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-start">
            @include('layouts.left-menu')
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
                <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                    <h1 class="h3">Payment Receipt</h1>
                    <div>
                        <a href="{{ route('accounting.payments.index') }}" class="btn btn-secondary">Back</a>
                        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i>
                            Print</button>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-center mb-4">
                                <h3>{{ $site_setting->school_name ?? config('app.name') }}</h3>
                                <p>Official Receipt</p>
                            </div>
                        </div>
                        <div class="row border-top border-bottom py-3 mb-3">
                            <div class="col-md-6">
                                <strong>Reference No:</strong> {{ $payment->reference_no }}<br>
                                <strong>Date:</strong>
                                {{ \Carbon\Carbon::parse($payment->transaction_date)->format('d M Y') }}
                            </div>
                            <div class="col-md-6 text-end">
                                <strong>Student:</strong> {{ $payment->student->first_name }}
                                {{ $payment->student->last_name }}<br>
                                <strong>Class:</strong> {{ $payment->schoolClass->class_name }}
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        School Fees Payment<br>
                                        <small class="text-muted">{{ $payment->session->session_name }} -
                                            {{ $payment->semester->semester_name }}</small>
                                    </td>
                                    <td class="text-end">₦{{ number_format($payment->amount_paid, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>Total Paid</strong></td>
                                    <td class="text-end font-weight-bold">₦{{ number_format($payment->amount_paid, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection