<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Services\Contact\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService,
    ) {}

    public function index(): View
    {
        return view('pages.contact.index', [
            'seo' => [
                'title'       => 'Contact — ' . config('agency.name'),
                'description' => 'Get in touch with our team. Let\'s talk about your next project.',
                'canonical'   => route('contact'),
            ],
        ]);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $contact = $this->contactService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Thank you! We\'ll get back to you within 24 hours.',
            ]);

        } catch (\Throwable $e) {
            Log::error('ContactController::store failed', [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'input'     => $request->validated(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again or contact us via WhatsApp.',
            ], 500);
        }
    }
}
