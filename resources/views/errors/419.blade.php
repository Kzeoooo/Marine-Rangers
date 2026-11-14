@extends('errors.layout')

@section('title', 'Sesi Telah Berakhir (419)')
@section('code', '419')

@section('illustration')
    <x-icon name="shield-check" class="w-12 h-12 text-[#1C7293]" />
@endsection

@section('message', 'Sesi Halaman Berakhir')

@section('description', 'Demi keamanan data Anda, sesi halaman ini telah kedaluwarsa. Silakan muat ulang halaman atau masuk kembali.')
