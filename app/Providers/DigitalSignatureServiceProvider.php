<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DocuSignService;
use App\Services\DigitalSignatureService;

class DigitalSignatureServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Optional integration: only resolvable when explicitly requested.
        $this->app->singletonIf(DocuSignService::class, function ($app) {
            return new DocuSignService(config('services.docusign', []));
        });

        $this->app->singleton(DigitalSignatureService::class);
    }
}
