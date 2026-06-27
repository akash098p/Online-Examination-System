<section>
    <header class="border-b border-white/10 pb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Security</p>
        <h2 class="mt-2 text-2xl font-semibold text-white">
            {{ __('Update Password') }}
        </h2>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-slate-200" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="profile-control mt-2 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-slate-200" />
            <x-text-input id="update_password_password" name="password" type="password" class="profile-control mt-2 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-slate-200" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="profile-control mt-2 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="!rounded-xl !border !border-amber-300 !bg-transparent !px-5 !py-2.5 !text-sm !font-semibold !text-amber-300 transition-all duration-300 hover:!bg-amber-300 hover:!text-slate-950 hover:!shadow-[0_0_16px_rgba(252,211,77,0.95),0_0_36px_rgba(252,211,77,0.75)] focus:!bg-amber-300 focus:!text-slate-950 focus:!shadow-[0_0_16px_rgba(252,211,77,0.95),0_0_36px_rgba(252,211,77,0.75)]">
                {{ __('Save Password') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-200"
                >{{ __('Password updated.') }}</p>
            @endif
        </div>
    </form>
</section>
