<x-ui.layouts::minimal>
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
            <div class="container container-tight my-5 px-lg-5">
                <div class="text-center mb-4">
                    <a href="." class="navbar-brand navbar-brand-autodark">
                        <img src="{{ get_logo() }}" height="70" alt="">
                    </a>
                </div>
                <h2 class="h3 text-center mb-3">
                    {{ trans('core/base::auth.login_to_your_account') }}
                </h2>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="list-unstyled mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('login') }}" method="post" autocomplete="off" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">
                            {{ trans('core/base::auth.email_address') }}
                        </label>
                        <input type="text" name="email" class="form-control" value="{{ old('email') }}" placeholder="Username or Email" autocomplete="off">
                        @error('user')
                            {{-- @include('core/base::components.forms.error', ['message' => $message]) --}}
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            {{ trans('core/base::auth.password') }}
                            <span class="form-label-description">
                                <a href="...........................">
                                    {{ trans('core/base::auth.i_forgot_my_password') }}
                                </a>
                            </span>
                        </label>
                        <div class="input-group input-group-flat">
                            <input name="password" type="password" class="form-control" placeholder="{{ trans('core/base::auth.your_password') }}" autocomplete="off">
                            <span class="input-group-text">
                                <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                                    <x-tabler-icons::eye />
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember" value="{{ old('remember') }}" />
                            <span class="form-check-label">{{ trans('core/base::auth.remember_me') }}</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">{{ trans('core/base::auth.sign_in') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            <!-- Photo -->
            <div class="bg-cover h-100 min-vh-100" style="background-image: url(https://picsum.photos/1280/853)"></div>
        </div>
    </div>
</x-ui.layouts::minimal>
