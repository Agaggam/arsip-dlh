<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountStatusChangedNotification extends Notification
{
    use Queueable;

    public $status;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $status, ?string $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
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
        $statusUpper = strtoupper($this->status);
        $mail = (new MailMessage)
            ->subject("Status Akun Arsip DLH: {$statusUpper}")
            ->greeting("Halo, {$notifiable->name}!");

        if ($this->status === 'approved' || $this->status === 'active') {
            $mail->line('Selamat! Akun Anda pada sistem Arsip DLH telah disetujui dan diaktifkan oleh Administrator.')
                 ->action('Login Sekarang', url('/login'))
                 ->line('Anda sekarang dapat mengakses arsip sesuai dengan hak akses yang telah ditentukan.');
        } else {
            $mail->line('Mohon maaf, permohonan status akun Anda pada sistem Arsip DLH ditolak atau dinonaktifkan.');
            if ($this->reason) {
                $mail->line("Alasan: {$this->reason}");
            }
            $mail->line('Jika terdapat pertanyaan, silakan hubungi Administrator Sistem DLH.');
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'status' => $this->status,
            'reason' => $this->reason,
        ];
    }
}
