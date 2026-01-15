<x-ui.layouts::minimal>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{ get_logo() }}" width="110" height="32" alt="Tabler" class="navbar-brand-image">
                </a>
            </div>

            <form class="card card-md" action="{{ route('password.update') }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="card-title">{{ trans('core/base::auth.title_reset_password') }}</h2>
                        <p class="text-secondary">{{ trans('core/base::auth.description_reset_password') }}</p>
                    </div>

                    @include('core/base::auth.status')
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <x-ui.form.input
                        name="email"
                        type="email"
                        :value="old('email', request()->route('email'))"
                        :label="trans('core/base::auth.email')"
                        :placeholder="trans('core/base::auth.enter_email')"
                        icon="mail"
                        autocomplete="email"
                        required
                    />

                    <div class="mb-3">
                        <label class="form-label">{{ trans('core/base::auth.new_password') }}</label>
                        <div class="input-group input-group-flat">
                            <input name="password" type="password" class="form-control" placeholder="{{ trans('core/base::auth.your_password') }}" required>
                            <span class="input-group-text">
                                <a href="#" onclick="viewPassword(this)" class="link-secondary" title="{{ trans('core/base::auth.show_password') }}">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ trans('core/base::auth.confirm_password') }}</label>
                        <div class="input-group input-group-flat">
                            <input name="password_confirmation" type="password" class="form-control" placeholder="{{ trans('core/base::auth.your_password') }}" required>
                            <span class="input-group-text">
                                <a href="#" onclick="viewPassword(this)" class="link-secondary" title="{{ trans('core/base::auth.show_password') }}">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="form-footer mt-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-send-2 me-1"></i>
                            {{ trans('core/base::auth.submit_reset_password') }}
                        </button>
                    </div>
                </div>
            </form>
            <div class="text-center text-secondary mt-3">
                {!! trans('core/base::auth.link_back_login', ['route' => route('login')]) !!}
            </div>
        </div>
    </div>
</x-ui.layouts::minimal>
