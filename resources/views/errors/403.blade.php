@extends('errors.layout')

@section('title', 'Akses Dibatasi (403)')
@section('code', '403')

@section('illustration')
    <x-icon name="lock" class="w-12 h-12 text-[#B8721A]" />
@endsection

@section('message', 'Akses Halaman Dibatasi')

@section('description', 'Anda tidak memiliki hak akses untuk membuka halaman ini. Silakan masuk menggunakan akun dengan peran yang sesuai.')
