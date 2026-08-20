<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! class_exists(\Dom\HTMLDocument::class)) {
            $this->app->scoped(HtmlSanitizerInterface::class, function () {
                return new class implements HtmlSanitizerInterface {
                    public function sanitize(string $input): string
                    {
                        return $input;
                    }

                    public function sanitizeFor(string $element, string $input): string
                    {
                        return $input;
                    }
                };
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);

        if (! class_exists(\Dom\HTMLDocument::class)) {
            Str::macro('sanitizeHtml', function (string $html): string {
                return $html;
            });

            Stringable::macro('sanitizeHtml', function (): Stringable {
                return $this;
            });
        }
    }
}
