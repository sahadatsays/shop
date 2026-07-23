<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(private SearchService $search) {}

    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));

        $results = $this->search->search($query !== '' ? $query : null, $page);
        $popularSearches = $this->search->popularSearches();

        $categories = $results->getCollection()
            ->pluck('category.name')
            ->filter()
            ->unique()
            ->values();

        return view('search', [
            'query' => $query,
            'results' => $results,
            'popularSearches' => $popularSearches,
            'filterCategories' => $categories,
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        return response()->json([
            'query' => $query,
            ...$this->search->suggestions($query),
        ]);
    }
}
