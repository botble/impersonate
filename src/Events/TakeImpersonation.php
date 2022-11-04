<?php

namespace Botble\Impersonate\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TakeImpersonation
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var  Authenticatable
     */
    public $impersonator;

    /**
     * @var  Authenticatable
     */
    public $impersonated;

    public function __construct(Authenticatable $impersonator, Authenticatable $impersonated)
    {
        $this->impersonator = $impersonator;
        $this->impersonated = $impersonated;
    }
}
