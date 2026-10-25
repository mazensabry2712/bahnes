<?php

namespace App\Mail;

use App\Models\ProjectUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientProjectUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProjectUpdate $update)
    {
        $this->update->loadMissing('project.client');
    }

    public function build(): self
    {
        $project = $this->update->project;

        return $this->subject('Project update: '.$project->title)
            ->view('emails.client-project-update', [
                'update' => $this->update,
                'project' => $project,
                'client' => $project->client,
            ]);
    }
}
