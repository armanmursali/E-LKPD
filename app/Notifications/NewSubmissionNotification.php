<?php

namespace App\Notifications;

use App\Models\LkpdSubmission;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class NewSubmissionNotification extends Notification
{
    public function __construct(private readonly LkpdSubmission $submission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'Jawaban baru masuk',
            'message' => $this->submission->nama_peserta.' mengirim jawaban tugas '.$this->submission->kegiatan_nomor.'.',
            'url' => route('kelas.kegiatan-pembelajaran.submissions.show', [
                'kelas' => $this->submission->kelas_id,
                'nomor' => $this->submission->kegiatan_nomor,
                'submission' => $this->submission->id,
            ]),
        ]);
    }
}
