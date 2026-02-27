<div>
    @if(session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif(session()->has('success'))
        <div class="alert alert-success text-black">{{ session('success') }}</div>
    @endif
</div>
