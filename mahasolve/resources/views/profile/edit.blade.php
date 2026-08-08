@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 border border-[#14162B14] shadow-sm">
        <h2 class="text-xl font-bold text-[#16182B] mb-4">Informasi Profil</h2>
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="bg-white rounded-2xl p-6 border border-[#14162B14] shadow-sm">
        <h2 class="text-xl font-bold text-[#16182B] mb-4">Pembaruan Password</h2>
        @include('profile.partials.update-password-form')
    </div>

    <div class="bg-white rounded-2xl p-6 border border-[#14162B14] shadow-sm">
        <h2 class="text-xl font-bold text-red-600 mb-4">Hapus Akun</h2>
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
