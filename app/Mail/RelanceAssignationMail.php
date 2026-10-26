<?php

namespace App\Mail;

use App\Models\Assignation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RelanceAssignationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Assignation $assignation;
    public string $typeRelance;

    /**
     * Create a new message instance.
     */
    public function __construct(Assignation $assignation, string $typeRelance = 'retard')
    {
        $this->assignation = $assignation->loadMissing(['tableau', 'structure', 'version']);
        $this->typeRelance = $typeRelance; // 'retard' ou 'echeance_proche'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tableauNom = $this->assignation->tableau->libelle ?? 'Tableau statistique';
        $sujet = $this->typeRelance === 'echeance_proche'
            ? "[MID / DGESS] Rappel : Échéance proche pour le tableau {$tableauNom}"
            : "[MID / DGESS] Relance : Retard de soumission pour le tableau {$tableauNom}";

        return new Envelope(
            subject: $sujet,
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
            markdown: 'emails.relance-assignation',
            with: [
                'assignation' => $this->assignation,
                'structure' => $this->assignation->structure,
                'tableau' => $this->assignation->tableau,
                'version' => $this->assignation->version,
                'typeRelance' => $this->typeRelance,
                'url' => $urlSaisie,
            ]
        );
    }
}
