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
                    {{ trans('core/base::auth.title') }}
                </h2>

                @include('core/base::auth.status')

                <form action="{{ route('login') }}" method="post" autocomplete="off" novalidate>
                    @csrf
                    <x-ui.form.input
                        name="user"
                        value="{{ old('user') }}"
                        :label="trans('core/base::auth.user')"
                        :placeholder="trans('core/base::auth.user')"
                        icon="user"
                        autocomplete="off"
                        required
                    />

                    <div class="mb-3">
                        <label class="form-label">
                            {{ trans('core/base::auth.password') }}
                            <span class="form-label-description">
                                <a href="{{ route('password.request') }}">{{ trans('core/base::auth.forgot_password') }}</a>
                            </span>
                        </label>

                        <div class="input-group input-group-flat">
                            <input name="password" type="password" class="form-control" placeholder="{{ trans('core/base::auth.your_password') }}" autocomplete="off" required>
                            <span class="input-group-text" style="display: flex !important; align-items: center !important; justify-content: center !important; padding: 0 0.75rem !important; min-width: 2.5rem;">
                                <a href="#" onclick="viewPassword(this); return false;" class="link-secondary" style="display: flex; align-items: center; line-height: 1;" title="{{ trans('core/base::auth.show_password') }}" data-bs-toggle="tooltip">
                                    {{ tabler_icon('eye', ['width' => 16, 'height' => 16, 'style' => 'display: block;']) }}
                                </a>
                            </span>
                        </div>
                    </div>

                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }} />
                        <span class="form-check-label">{{ trans('core/base::auth.remember_me') }}</span>
                    </label>

                    <div class="form-footer mt-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-login me-1"></i>
                            {{ trans('core/base::auth.sign_in') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            <div class="bg-cover h-100 min-vh-100" id="background" style="background-color: #ecf0f1"></div>
        </div>
    </div>
</x-ui.layouts::minimal>
