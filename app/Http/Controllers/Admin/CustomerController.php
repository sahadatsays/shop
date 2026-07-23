<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerNoteRequest;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\Admin\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $customers) {}

    public function index(Request $request): View
    {
        return view('admin.customers.index', [
            'title' => 'Customers',
            'breadcrumbs' => [
                ['label' => 'Customers'],
            ],
            'customers' => $this->customers->list([
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
                'has_orders' => $request->string('has_orders')->toString() ?: null,
                'trashed' => $request->boolean('trashed'),
            ]),
            'filters' => $request->only(['search', 'status', 'has_orders', 'trashed']),
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.create', [
            'title' => 'Create Customer',
            'breadcrumbs' => [
                ['label' => 'Customers', 'href' => route('admin.customers.index')],
                ['label' => 'Create'],
            ],
            'form' => $this->customers->formData(),
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customers->create($request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer = $this->customers->show($customer->id);

        return view('admin.customers.show', [
            'title' => $customer->name,
            'breadcrumbs' => [
                ['label' => 'Customers', 'href' => route('admin.customers.index')],
                ['label' => $customer->name],
            ],
            'customer' => $customer,
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', [
            'title' => 'Edit Customer',
            'breadcrumbs' => [
                ['label' => 'Customers', 'href' => route('admin.customers.index')],
                ['label' => $customer->name, 'href' => route('admin.customers.show', $customer)],
                ['label' => 'Edit'],
            ],
            'form' => $this->customers->formData($customer),
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customers->update($customer, $request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->customers->delete($customer);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer moved to trash.');
    }

    public function restore(Customer $customer): RedirectResponse
    {
        $this->customers->restore($customer->id);

        return redirect()
            ->route('admin.customers.index', ['trashed' => 1])
            ->with('success', 'Customer restored successfully.');
    }

    public function storeNote(StoreCustomerNoteRequest $request, Customer $customer): RedirectResponse
    {
        $this->customers->addNote(
            $customer,
            $request->string('body')->toString(),
            $request->string('author_name')->toString() ?: 'Admin',
        );

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Note added successfully.');
    }
}
