<x-ui.layouts::minimal>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{ get_logo() }}" width="110" height="32" alt="Tabler" class="navbar-brand-image">
                </a>
            </div>

            <form class="card card-md" action="{{ route('password.update') }}" method="post" autocomplete="off" novalidate>
                @csrf
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Update password</h2>
                    <p class="text-secondary mb-4">Enter your email address and your password will be reset and emailed to you.</p>

                    @include('core/base::auth.status')
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" value="{{ old('email', request()->route('email')) }}">
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Enter Password</label>
                        <div class="input-group input-group-flat">
                            <input name="password" type="password" class="form-control" placeholder="{{ trans('core/base::auth.your_password') }}" autocomplete="off">
                            <span class="input-group-text">
                                <a href="#" onclick="viewPassword(this)" class="link-secondary" title="{{ trans('core/base::auth.show_password') }}">
                                    <x-tabler-icons::eye />
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Enter Password</label>
                        <div class="input-group input-group-flat">
                            <input name="password_confirmation" type="password" class="form-control" placeholder="{{ trans('core/base::auth.your_password') }}" autocomplete="off">
                            <span class="input-group-text">
                                <a href="#" onclick="viewPassword(this)" class="link-secondary" title="{{ trans('core/base::auth.show_password') }}">
                                    <x-tabler-icons::eye />
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <x-tabler-icons::mail /> Change Password
                        </button>
                    </div>
                </div>
            </form>
            <div class="text-center text-secondary mt-3">
                Forget it, <a href="{{ route('login') }}">send me back</a> to the sign in screen.
            </div>
        </div>
    </div>
</x-ui.layouts::minimal>
