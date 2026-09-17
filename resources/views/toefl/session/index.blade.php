@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Riwayat Ujian TOEFL</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Test</th>
                <th>Status</th>
                <th>Skor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr>
                <td>{{ $session->test->test_name }}</td>
                <td>{{ $session->status }}</td>
                <td>{{ $session->total_score }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
