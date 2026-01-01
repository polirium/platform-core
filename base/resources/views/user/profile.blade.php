<x-ui.layouts::app>
    <x-slot:title>{{ __('Hồ sơ cá nhân') }}</x-slot:title>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Thông tin cá nhân') }}</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('core.user.profile.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Họ và tên') }}</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Ngày tạo') }}</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y H:i') }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">{{ __('Cập nhật') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-ui.layouts::app>
