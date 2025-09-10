<x-guest-layout>
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">

        <!-- Titre -->
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-3">
            CONNEXION
        </h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Formulaire -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Mot de passe')" />
                <div class="relative">
                    <x-text-input id="password" name="password" type="password" class="block mt-1 w-full pr-10"
                        required autocomplete="current-password" />
                    <button type="button" onclick="togglePassword('password', this)"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Se souvenir -->
            <div class="flex items-center mt-4">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" name="remember">
                <label for="remember_me" class="ms-2 text-sm text-gray-600">
                    {{ __('Se souvenir de moi') }}
                </label>
            </div>

            <!-- Lien + Bouton -->
            <div class="flex items-center justify-between mt-6">
                @if (Route::has('password.request'))
                    <a class="text-sm text-red-600 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif

                <button type="submit"
                    class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded shadow">
                    {{ __('Se connecter') }}
                </button>
            </div>
        </form>
    </div>


</x-guest-layout>
