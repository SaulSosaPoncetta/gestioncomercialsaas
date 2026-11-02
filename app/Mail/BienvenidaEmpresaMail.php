<?php

namespace App\Mail;

use App\Models\Empresa;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BienvenidaEmpresaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Empresa $empresa,
        public ?Plan $plan,
        public string $activationUrl,
        public string $nombreAdmin,
    ) {}

    public function build()
    {
        return $this->subject('Bienvenido a Gestión Comercial — Activá tu cuenta')
            ->view('emails.bienvenida_empresa');
    }
}
