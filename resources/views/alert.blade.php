@if (session('success'))
<div class="text-center alert alert-success alert-dismissible fade show" role="alert">
    <span class="font-bold">{{ session('success') }}</span>
</div>
@endif

@if (session('danger'))
<div class="text-center alert alert-danger alert-dismissible fade show" role="alert">
    <span class="font-bold">{{ session('danger') }}</span>
</div>
@endif

@if (session('warning'))
<div class="text-center alert alert-warning alert-dismissible fade show" role="alert">
    <span class="font-bold">{{ session('warning') }}</span>
</div>
@endif

@if (session('info'))
<div class="text-center alert alert-info alert-dismissible fade show" role="alert">
    <span class="font-bold">{{ session('info') }}</span>
</div>
@endif


 