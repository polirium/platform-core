<x-ui.layouts::minimal>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{ get_logo() }}" width="110" height="32" alt="Tabler" class="navbar-brand-image">
                </a>
            </div>

            <form class="card card-md" action="{{ route('password.email') }}" method="post" autocomplete="off" novalidate>
                @csrf
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="card-title">{{ trans('core/base::auth.title_forgot_password') }}</h2>
                        <p class="text-secondary">{{ trans('core/base::auth.description_forgot_password') }}</p>
                    </div>

                    @include('core/base::auth.status')

                    <x-ui.form.input
                        name="email"
                        type="email"
                        :label="trans('core/base::auth.email')"
                        :placeholder="trans('core/base::auth.enter_email')"
                        icon="mail"
                        required
                    />

                    <div class="form-footer mt-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-mail me-1"></i>
                            {{ trans('core/base::auth.send_new_password') }}
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
