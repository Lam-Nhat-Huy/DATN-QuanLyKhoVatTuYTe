<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteSupplierMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fileExcel;

    public $supplierName;

    /**
     * Create a new message instance.
     */
    public function __construct($fileExcel, $supplierName)
    {
        $this->fileExcel = $fileExcel;
        $this->supplierName = $supplierName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kho Thiết Bị Bệnh Viện Đa Khoa Beesoft Gửi Yêu Cầu Báo Giá',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'supplier.template_mail_quote',
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
