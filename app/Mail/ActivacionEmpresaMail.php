<?php

namespace App\Mail;

use App\Models\Empresa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivacionEmpresaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Empresa $empresa) {}

    public function build()
    {
        return $this->subject('Tu cuenta de Gestión Comercial está activa')
            ->view('emails.activacion_empresa');
    }
}
