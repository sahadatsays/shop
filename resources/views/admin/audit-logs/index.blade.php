<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header
        title="Audit logs"
        description="Review login history, catalog changes, stock movements, orders, and customer activity."
    />

    <x-admin.data-table>
        <x-slot:toolbar>
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="flex w-full flex-col gap-2 lg:flex-row lg:items-center">
                <select name="category" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                    <option value="">All categories</option>
                    @foreach (\App\Enums\AuditCategory::cases() as $category)
                        <option value="{{ $category->value }}" @selected(($filters['category'] ?? null) === $category->value)>
                            {{ $category->label() }}
                        </option>
                    @endforeach
                </select>

                <select name="action" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                    <option value="">All actions</option>
                    @foreach (\App\Enums\AuditAction::cases() as $action)
                        <option value="{{ $action->value }}" @selected(($filters['action'] ?? null) === $action->value)>
                            {{ $action->label() }}
                        </option>
                    @endforeach
                </select>

                <div class="relative min-w-0 flex-1 lg:max-w-md">
                    <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search description, IP, or browser…"
                        class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                    >
                </div>

                <x-admin.button type="submit" variant="secondary" size="sm">Filter</x-admin.button>
            </form>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Timestamp</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">User</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Action</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Subject</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">IP address</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Browser</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($logs as $log)
                <tr class="group transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4 align-top">
                        <time datetime="{{ $log->created_at?->toIso8601String() }}" class="block text-sm admin-text">
                            {{ $log->created_at?->format('M j, Y g:i A') }}
                        </time>
                        <span class="text-xs admin-muted">{{ $log->created_at?->diffForHumans() }}</span>
                    </td>
                    <td class="px-6 py-4 align-top text-sm admin-text">{{ $log->causerName() }}</td>
                    <td class="px-6 py-4 align-top">
                        <x-admin.badge :variant="$log->category->badgeVariant()" dot>
                            {{ $log->action->label() }}
                        </x-admin.badge>
                        <p class="mt-2 text-sm admin-text-secondary">{{ $log->description }}</p>
                    </td>
                    <td class="px-6 py-4 align-top text-sm admin-text-secondary">
                        {{ $log->subjectLabel() ?? '—' }}
                    </td>
                    <td class="px-6 py-4 align-top font-mono text-sm admin-text-secondary">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                    <td class="px-6 py-4 align-top text-sm admin-text-secondary">
                        {{ $log->browser ?? '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-14">
                        <x-admin.empty-state
                            title="No audit logs yet"
                            description="Important actions such as sign-ins, product edits, stock changes, and order updates will appear here."
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if ($logs->hasPages())
            <x-slot:footer>
                {{ $logs->links() }}
            </x-slot:footer>
        @endif
    </x-admin.data-table>
</x-layouts.admin>
