<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// use App\Models\Email;

class GenericEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;
    public $view;

    public function __construct(string $view, array $data)
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function build()
    {
        $defaultfrom = config("email.from", "web@tseyor.org");
        return $this->from($this->data['from'] ?? $defaultfrom)
            ->replyTo($this->data['replyTo'] ?? null)
            ->subject($this->data['subject'] ?? 'Mensaje desde tseyor.org')
            ->markdown($this->view)
            ->with($this->data);
    }


    /**
 * Get the attachments for the message.
 *
 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
 */
public function attachments(): array
{
    return $this->data['attachments'] ?? [];
}

}
