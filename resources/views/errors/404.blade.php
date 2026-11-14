@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan (404)')
@section('code', '404')

@section('illustration')
    <x-icon name="wave" class="w-12 h-12 text-[#065A82]" />
@endsection

@section('message', 'Halaman Terbawa Arus Laut')

@section('description', 'Maaf, tautan yang Anda tuju tidak ditemukan atau mungkin sudah dipindahkan. Mari kembali ke perairan yang aman.')
