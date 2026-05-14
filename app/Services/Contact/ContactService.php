<?php

namespace App\Services\Contact;

use App\Models\Contact;
use App\Jobs\SendContactNotificationJob;
use App\Exceptions\ContactException;
use Illuminate\Support\Facades\Log;

class ContactService
{
    /**
     * Create contact submission and dispatch notification.
     */
    public function create(array $data): Contact
    {
        try {
            $contact = Contact::create([
                'name'    => $data['name'],
                'email'   => $data['email'],
                'phone'   => $data['phone'] ?? null,
                'subject' => $data['subject'] ?? 'General Inquiry',
                'message' => $data['message'],
                'source'  => $data['source'] ?? 'website',
                'status'  => 'unread',
                'ip'      => request()->ip(),
                'ua'      => request()->userAgent(),
            ]);

            // Dispatch notification (SMTP + optional WhatsApp)
            SendContactNotificationJob::dispatch($contact)->onQueue('emails');

            return $contact;

        } catch (\Throwable $e) {
            Log::error('ContactService::create failed', [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'input'     => $data,
                'trace'     => $e->getTraceAsString(),
            ]);

            throw ContactException::creationFailed($e->getMessage());
        }
    }

    /**
     * Get paginated contacts for admin inbox.
     */
    public function getPaginated(int $perPage = 20, ?string $status = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Contact::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Mark contact as read.
     */
    public function markRead(Contact $contact): Contact
    {
        $contact->update(['status' => 'read', 'read_at' => now()]);
        return $contact->fresh();
    }

    /**
     * Get unread count for admin badge.
     */
    public function getUnreadCount(): int
    {
        return Contact::where('status', 'unread')->count();
    }
}
