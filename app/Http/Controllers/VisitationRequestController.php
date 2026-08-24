<?php

namespace App\Http\Controllers;

use App\Mail\VisitationRequestMail;
use App\Rules\PassivePdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class VisitationRequestController extends Controller
{
    private const ACTIVITIES = [
        'hiking' => 'Hiking or trekking',
        'wildlife-viewing' => 'Wildlife viewing',
        'research' => 'Research',
        'education' => 'Educational visit',
        'photography' => 'Photography or filming',
        'coordination' => 'Community or official coordination',
        'other' => 'Other',
    ];

    private const DOCUMENT_TYPES = [
        'request-letter' => 'Request letter',
        'itinerary' => 'Visitor itinerary',
        'proposal' => 'Research or activity proposal',
        'endorsement' => 'Endorsement or permit',
        'other' => 'Other supporting document',
    ];

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'organization' => ['nullable', 'string', 'max:255'],
            'activity' => ['required', Rule::in(array_keys(self::ACTIVITIES))],
            'intended_date' => ['required', 'date', 'after_or_equal:today'],
            'visitor_count' => ['required', 'integer', 'min:1', 'max:50'],
            'message' => ['nullable', 'string', 'max:3000'],
            'document_type' => [
                'nullable',
                'required_with:document',
                Rule::in(array_keys(self::DOCUMENT_TYPES)),
            ],
            'document' => [
                'nullable',
                'file',
                'mimes:pdf',
                'extensions:pdf',
                'max:5120',
                new PassivePdf,
            ],
            'privacy_consent' => ['accepted'],
            'website' => ['nullable', 'size:0'],
        ]);

        if (! $this->hasDeliverableMailer()) {
            report(new RuntimeException(
                'Visitation request delivery is disabled because MAIL_MAILER is not configured for external delivery.'
            ));

            return redirect(route('home').'#visitation-request')
                ->withInput()
                ->withErrors([
                    'delivery' => 'Online delivery is not configured yet. Please email the PAMO office directly.',
                ]);
        }

        $document = $request->file('document');
        $attachmentPath = $document?->getRealPath() ?: null;
        $attachmentMime = $document?->getMimeType();
        $attachmentName = $document
            ? 'visitation-supporting-document.pdf'
            : null;

        $details = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'activity' => self::ACTIVITIES[$validated['activity']],
            'intended_date' => $validated['intended_date'],
            'visitor_count' => $validated['visitor_count'],
            'message' => $validated['message'] ?? null,
            'document_type' => isset($validated['document_type'])
                ? self::DOCUMENT_TYPES[$validated['document_type']]
                : null,
        ];

        try {
            Mail::to(config('mail.visitation_requests.to', 'r4b.mibnp@denr.gov.ph'))
                ->send(new VisitationRequestMail(
                    details: $details,
                    attachmentPath: $attachmentPath,
                    attachmentName: $attachmentName,
                    attachmentMime: $attachmentMime,
                ));
        } catch (Throwable $exception) {
            report($exception);

            return redirect(route('home').'#visitation-request')
                ->withInput()
                ->withErrors([
                    'delivery' => 'We could not send your request right now. Please try again later or email the PAMO office directly.',
                ]);
        }

        return redirect(route('home').'#visitation-request')
            ->with('visitation_status', 'Your visitation request was sent to the PAMO office.');
    }

    private function hasDeliverableMailer(): bool
    {
        return ! in_array(config('mail.default'), ['array', 'log'], true);
    }
}
