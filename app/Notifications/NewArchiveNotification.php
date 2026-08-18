<?php

namespace App\Notifications;

use App\Models\Archive;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewArchiveNotification extends Notification
{
    use Queueable;

    public $archive;

    /**
     * Create a new notification instance.
     */
    public function __construct(Archive $archive)
    {
        $this->archive = $archive;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $categoryName = $this->archive->category ? $this->archive->category->name : 'Umum';
        $uploaderName = $this->archive->user ? $this->archive->user->name : 'Sistem';

        return (new MailMessage)
            ->subject("Arsip Baru Diunggah: {$this->archive->title}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Arsip baru telah diunggah ke dalam sistem oleh **{$uploaderName}**.")
            ->line("**Judul Arsip:** {$this->archive->title}")
            ->line("**Kategori:** {$categoryName}")
            ->line("**Tanggal Arsip:** " . ($this->archive->archive_date ?? date('Y-m-d')))
            ->action('Lihat Arsip', route('admin.archives.preview', $this->archive->hash_token))
            ->line('Terima kasih telah menggunakan sistem Arsip DLH!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'archive_id' => $this->archive->id,
            'title' => $this->archive->title,
        ];
    }
}
