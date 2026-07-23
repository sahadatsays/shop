<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function show(Collection $collection): View
    {
        abort_unless($collection->is_active, 404);

        $collection->load(['products' => fn ($query) => $query->published()->with(['category', 'images'])]);

        return view('collections.show', [
            'collection' => $collection,
        ]);
    }
}
