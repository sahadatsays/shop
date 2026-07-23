<x-layouts.app :title="$title" description="Track your Valor Supply Co. order with your order number and email address.">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <nav aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-500">
                <li><a href="{{ route('home') }}" class="transition-colors duration-200 hover:text-navy-900">Home</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="font-medium text-navy-900">Track order</li>
            </ol>
        </nav>

        <div class="mx-auto mt-10 max-w-xl">
            <h1 class="font-display text-3xl font-bold text-navy-900 sm:text-4xl">Track your order</h1>
            <p class="mt-3 text-navy-600">Enter your order number and the email address used at checkout.</p>

            @if ($errors->any())
                <div class="mt-6 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('track-order.store') }}" class="mt-8 space-y-5 rounded-card bg-surface p-7 shadow-soft">
                @csrf

                <div>
                    <label for="order_number" class="block text-sm font-semibold text-navy-900">Order number</label>
                    <input
                        id="order_number"
                        name="order_number"
                        type="text"
                        value="{{ old('order_number') }}"
                        placeholder="VS-10482"
                        required
                        class="mt-2 block w-full rounded-xl border border-navy-200 bg-white px-4 py-3 text-sm text-navy-900 placeholder:text-navy-400 focus:border-navy-900 focus:outline-none focus:ring-2 focus:ring-navy-900/10"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-navy-900">Email address</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        required
                        class="mt-2 block w-full rounded-xl border border-navy-200 bg-white px-4 py-3 text-sm text-navy-900 placeholder:text-navy-400 focus:border-navy-900 focus:outline-none focus:ring-2 focus:ring-navy-900/10"
                    >
                </div>

                <x-ui.button type="submit" class="w-full">Track order</x-ui.button>
            </form>

            <p class="mt-6 text-center text-sm text-navy-500">
                Signed in?
                <a href="{{ route('account.orders') }}" class="font-semibold text-navy-900 hover:text-bronze-700">View your order history</a>
            </p>
        </div>
    </div>
</x-layouts.app>
