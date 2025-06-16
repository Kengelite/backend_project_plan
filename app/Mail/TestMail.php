<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;
    public  $details;
    public  $type;
    public  $reponsible;
    /**
     * Create a new message instance.
     */
    public function __construct($details, $type, $reponsible)
    {
        //
        $this->details = $details;
        $this->type = $type;
        $this->reponsible = $reponsible;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        switch ($this->type) {
            case '1':
                $subject = 'แจ้งเตือนจากระบบ E-plan ความคืบหน้ากิจกรรม';
                break;
            case '2':
                $subject = 'แจ้งเตือนจากระบบ E-plan รายงานผลการดำเนินงานกิจกรรม';
                break;
            default:
                $subject = 'แจ้งเตือนจากระบบ E-plan รายงานผล OKRs';
                break;
        }
        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'sendActivityemail',
            with: [
                'details' => $this->details,
                'reponsible' => $this->reponsible,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
