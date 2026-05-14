<?php

namespace App\Jobs;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendContactNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        private readonly Contact $contact
    ) {}

    public function handle(): void
    {
        $this->sendEmailNotification();
        $this->sendWhatsAppNotification();
    }

    private function sendEmailNotification(): void
    {
        try {
            Mail::send(
                'emails.contact-notification',
                ['contact' => $this->contact],
                function ($mail) {
                    $mail->to(config('agency.contact.notify_email'))
                         ->subject("[New Contact] {$this->contact->name}: {$this->contact->subject}")
                         ->replyTo($this->contact->email, $this->contact->name);
                }
            );
        } catch (\Throwable $e) {
            Log::error('SendContactNotificationJob::sendEmailNotification failed', [
                'exception'  => get_class($e),
                'message'    => $e->getMessage(),
                'contact_id' => $this->contact->id,
                'trace'      => $e->getTraceAsString(),
            ]);
        }
    }

    private function sendWhatsAppNotification(): void
    {
        $apiKey = config('agency.whatsapp_api_key');
        $apiUrl = config('services.whatsapp.api_url');

        if (!$apiKey || !$apiUrl) {
            return;
        }

        try {
            $text = implode("\n", [
                "🔔 *New Contact Form Submission*",
                "Name: {$this->contact->name}",
                "Email: {$this->contact->email}",
                "Phone: {$this->contact->phone ?? '-'}",
                "Subject: {$this->contact->subject ?? '-'}",
                "Message: " . str($this->contact->message)->limit(200),
            ]);

            Http::withToken($apiKey)
                ->timeout(15)
                ->post($apiUrl, [
                    'number'  => config('agency.whatsapp'),
                    'message' => $text,
                ]);

        } catch (\Throwable $e) {
            Log::warning('SendContactNotificationJob::sendWhatsAppNotification failed', [
                'exception'  => get_class($e),
                'message'    => $e->getMessage(),
                'contact_id' => $this->contact->id,
            ]);
            // Non-critical — don't rethrow
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendContactNotificationJob completely failed', [
            'contact_id' => $this->contact->id,
            'exception'  => get_class($exception),
            'message'    => $exception->getMessage(),
            'trace'      => $exception->getTraceAsString(),
        ]);
    }
}
