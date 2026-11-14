@extends('errors.layout')

@section('title', 'Kendala Teknis (500)')
@section('code', '500')

@section('illustration')
    <x-icon name="net" class="w-12 h-12 text-[#C0392B]" />
@endsection

@section('message', 'Terjadi Kendala Teknis')

@section('description', 'Server kami sedang mengalami kendala sementara di perairan. Tim teknis sedang memperbaikinya. Silakan coba kembali sesaat lagi.')
