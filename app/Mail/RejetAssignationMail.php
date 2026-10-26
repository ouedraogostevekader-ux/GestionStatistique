<?php

namespace App\Mail;

use App\Models\Assignation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejetAssignationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Assignation $assignation;
    public string $motif;

    /**
     * Create a new message instance.
     */
    public function __construct(Assignation $assignation, string $motif)
    {
        $this->assignation = $assignation->loadMissing(['tableau', 'structure', 'version']);
        $this->motif = $motif;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tableauNom = $this->assignation->tableau->libelle ?? 'Tableau statistique';

        return new Envelope(
            subject: "[MID / DGESS] Soumission rejetée pour correction : {$tableauNom}",
            from: config('mail.from.address', 'dsi.dgess@mid.gov.bf')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $urlSaisie = route('tableaux.saisie', [
            'id' => $this->assignation->tableau_id,
            'assignation_id' => $this->assignation->id
        ]);

        return new Content(
            markdown: 'emails.rejet-assignation',
            with: [
                'assignation' => $this->assignation,
                'structure' => $this->assignation->structure,
                'tableau' => $this->assignation->tableau,
                'version' => $this->assignation->version,
                'motif' => $this->motif,
                'url' => $urlSaisie,
            ]
        );
    }
}
