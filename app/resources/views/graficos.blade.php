@extends('layouts.app')

@section('title', 'Indicadores de Impressao')
@section('subtitle', 'Graficos para dimensionamento de contrato')

@section('content')
    <livewire:graficos />
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endpush
