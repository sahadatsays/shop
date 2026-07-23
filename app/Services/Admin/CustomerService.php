<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\AdminCustomerRepositoryInterface;
use App\DTOs\Admin\Customer\CustomerFormData;
use App\Enums\AddressType;
use App\Models\Customer;
use App\Models\CustomerNote;
use App\Services\AuditService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function __construct(
        private AdminCustomerRepositoryInterface $customers,
        private AuditService $audit,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->customers->paginate($filters);
    }

    public function formData(?Customer $customer = null): CustomerFormData
    {
        if ($customer) {
            $customer->load('addresses');
        }

        return new CustomerFormData(customer: $customer);
    }

    public function show(int $id): Customer
    {
        return $this->customers->find($id);
    }

    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data): Customer {
            $customer = $this->customers->create($this->prepareAttributes($data));
            $this->syncAddresses($customer, $data['addresses'] ?? []);

            if (filled($data['note'] ?? null)) {
                $this->addNote($customer, $data['note'], $data['note_author'] ?? 'Admin', audit: false);
            }

            $created = $this->customers->find($customer->id);
            $this->audit->logCustomerCreated($created);

            if (filled($data['note'] ?? null)) {
                $this->audit->logCustomerNoteAdded($created, $data['note']);
            }

            return $created;
        });
    }

    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data): Customer {
            $before = $customer->only(['name', 'email', 'phone', 'status', 'internal_notes']);
            $customer = $this->customers->update($customer, $this->prepareAttributes($data));
            $this->syncAddresses($customer, $data['addresses'] ?? []);

            if (filled($data['note'] ?? null)) {
                $this->addNote($customer, $data['note'], $data['note_author'] ?? 'Admin', audit: false);
            }

            $updated = $this->customers->find($customer->id);
            $after = $updated->only(array_keys($before));
            $changes = $this->audit->diffAttributes($before, $after, array_keys($before));

            if ($changes !== []) {
                $this->audit->logCustomerUpdated($updated, $changes);
            }

            if (filled($data['note'] ?? null)) {
                $this->audit->logCustomerNoteAdded($updated, $data['note']);
            }

            return $updated;
        });
    }

    public function delete(Customer $customer): void
    {
        $this->customers->delete($customer);
        $this->audit->logCustomerDeleted($customer);
    }

    public function restore(int $id): Customer
    {
        $customer = $this->customers->restore($id);
        $this->audit->logCustomerRestored($customer);

        return $customer;
    }

    public function addNote(Customer $customer, string $body, ?string $authorName = null, bool $audit = true): CustomerNote
    {
        $note = $customer->notes()->create([
            'body' => $body,
            'author_name' => $authorName ?: 'Admin',
        ]);

        if ($audit) {
            $this->audit->logCustomerNoteAdded($customer, $body);
        }

        return $note;
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
            'internal_notes' => $data['internal_notes'] ?? null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $addresses
     */
    private function syncAddresses(Customer $customer, array $addresses): void
    {
        $customer->addresses()->delete();

        $rows = collect($addresses)
            ->filter(fn (array $row): bool => filled($row['line1'] ?? null) && filled($row['city'] ?? null) && filled($row['postal_code'] ?? null))
            ->values();

        $defaultSet = false;

        foreach ($rows as $index => $row) {
            $isDefault = (bool) ($row['is_default'] ?? false);

            if ($isDefault) {
                $defaultSet = true;
            } elseif (! $defaultSet && $index === 0) {
                $isDefault = true;
                $defaultSet = true;
            }

            $customer->addresses()->create([
                'label' => $row['label'] ?? null,
                'type' => $row['type'] ?? AddressType::Shipping->value,
                'name' => $row['name'] ?? $customer->name,
                'phone' => $row['phone'] ?? $customer->phone,
                'line1' => $row['line1'],
                'line2' => $row['line2'] ?? null,
                'city' => $row['city'],
                'state' => $row['state'] ?? null,
                'postal_code' => $row['postal_code'],
                'country' => $row['country'] ?? 'US',
                'is_default' => $isDefault,
            ]);
        }
    }
}
