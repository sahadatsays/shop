<?php

namespace App\Http\Requests;

use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Review|null $review */
        $review = $this->route('review');

        if ($review === null || $review->product_id === null) {
            return false;
        }

        $customerId = $this->user('customer')?->id ?? session('customer_id');

        return $customerId !== null && (int) $customerId === $review->customer_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
