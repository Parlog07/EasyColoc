<x-guest-layout>
    <div class="mx-auto w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-900">Créer un compte</h2>
        <p class="mt-1 text-sm text-gray-500">Rejoignez EasyColoc pour gérer votre colocation.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="mt-1 block w-full rounded-lg border-gray-300" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full rounded-lg border-gray-300" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full rounded-lg border-gray-300"
                              type="password"
                              name="password"
                              required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-lg border-gray-300"
                              type="password"
                              name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                Register
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Déjà inscrit ?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Se connecter</a>
        </p>
    </div>
</x-guest-layout>
