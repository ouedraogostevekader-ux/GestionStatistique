<?php

namespace App\Mail;

use App\Models\Assignation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ValidationAssignationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Assignation $assignation;

    /**
     * Create a new message instance.
     */
    public function __construct(Assignation $assignation)
    {
        $this->assignation = $assignation->loadMissing(['tableau', 'structure', 'version']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tableauNom = $this->assignation->tableau->libelle ?? 'Tableau statistique';

        return new Envelope(
            subject: "[MID / DGESS] Validation définitive de votre soumission : {$tableauNom}",
            from: config('mail.from.address', 'dsi.dgess@mid.gov.bf')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $urlConsultation = route('assignations.show', $this->assignation->id);

        return new Content(
            markdown: 'emails.validation-assignation',
            with: [
                'assignation' => $this->assignation,
                'structure' => $this->assignation->structure,
                'tableau' => $this->assignation->tableau,
                'version' => $this->assignation->version,
                'url' => $urlConsultation,
            ]
        );
    }
}
