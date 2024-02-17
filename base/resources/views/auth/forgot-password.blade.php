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
                    <h2 class="card-title text-center mb-4">{{ trans('core/base::auth.title_forgot_password') }}</h2>
                    <p class="text-secondary mb-4">{{ trans('core/base::auth.description_forgot_password') }}</p>

                    @include('core/base::auth.status')

                    <div class="mb-3">
                        <label class="form-label">{{ trans('core/base::auth.email') }}</label>
                        <input type="email" name="email" class="form-control" placeholder="{{ trans('core/base::auth.enter_email') }}">
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <x-tabler-icons::mail /> {{ trans('core/base::auth.send_new_password') }}
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
