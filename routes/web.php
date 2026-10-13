<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaProxyController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Livewire\BookingCalendar;
use App\Livewire\CalculatorsComponent;
use App\Livewire\NewsDetail;
use App\Livewire\NewsList;
use App\Livewire\PropertyBooking;
use App\Livewire\PropertyComparison;
use App\Livewire\PropertyDetail;
use App\Livewire\PropertyList;
use App\Livewire\PropertyRequestForm;
use App\Livewire\PropertySubmissionForm;
use App\Livewire\RentalApplicationForm;
use App\Livewire\WishlistManager;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'store'])->middleware('throttle:login')->name('login.store');
    Route::get('/register', [\Laravel\Fortify\Http\Controllers\RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [\Laravel\Fortify\Http\Controllers\RegisteredUserController::class, 'store'])->middleware('throttle:register')->name('register.store');

    // Password reset. Fortify's own routes are switched off (the Filament
    // panels register their own auth), so like login/register above these
    // are declared here; without them the public login had no way to
    // recover a forgotten password.
    Route::get('/forgot-password', [\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.update');
});
Route::post('/logout', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
        \Illuminate\Support\Facades\Cookie::queue('locale', $locale, 60 * 24 * 365);
    }
    return redirect()->back(fallback: '/');
})->name('lang.switch');

Route::get('/sitemap.xml', function () {
    // Regenerated daily by the scheduler; built on first request if absent.
    $sitemapPath = \App\Console\Commands\GenerateSitemapCommand::path();
    if (! file_exists($sitemapPath)) {
        \Illuminate\Support\Facades\Artisan::call('sitemap:generate');
    }

    return response()->file($sitemapPath, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// A route rather than public/robots.txt so it follows the deployment: a
// non-indexable one (demo, staging - see config/branding.php) tells crawlers
// to stay out entirely, and the Sitemap line always carries the real host.
Route::get('/robots.txt', function () {
    $lines = ['User-agent: *'];

    if (! app(\App\Support\Brand::class)->indexable()) {
        $lines[] = 'Disallow: /';
    } else {
        foreach (['/admin', '/app', '/agent', '/buyer', '/contractor', '/landlord', '/seller', '/staff', '/tenant', '/api', '/livewire'] as $path) {
            $lines[] = "Disallow: {$path}";
        }
        $lines[] = '';
        $lines[] = 'Sitemap: '.url('/sitemap.xml');
    }

    return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('robots');

Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:10,1');
Route::middleware('auth')->group(function () {
    Route::get('/bookings/{booking}/calendar.ics', [BookingController::class, 'downloadIcs'])->name('booking.ics');
    Route::get('/appointments/{appointment}/calendar.ics', [AppointmentController::class, 'downloadIcs'])->name('appointment.ics');
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::get('/bookings', [BookingController::class, 'index']);
});
Route::get('/properties/{propertyId}/book', PropertyBooking::class)->name('property.book');
Route::post('/payments/session', [PaymentController::class, 'createSession'])->middleware(['auth', 'throttle:10,1']);
// Stripe-signed webhook: no auth middleware (signature is the credential), CSRF-exempt in bootstrap/app.php.
Route::post('/payments/webhook', [PaymentController::class, 'handleWebhook'])->middleware('throttle:60,1');
Route::get('/payments/success', [PaymentController::class, 'handlePaymentSuccess'])->middleware('auth');
Route::get('/booking-calendar', BookingCalendar::class)->middleware('auth')->name('booking.calendar');
Route::get('/properties', PropertyList::class)->name('property.list');
Route::get('/properties/search', [PropertyController::class, 'search'])->name('property.search');
Route::get('/properties/{propertyId}', PropertyDetail::class)->name('property.detail');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/apply/{property_id}', RentalApplicationForm::class)->name('rental.apply');
    Route::get('/applications', [TenantController::class, 'applications'])->name('tenant.applications');
    Route::get('/wishlist', WishlistManager::class)->name('wishlist');
    Route::get('/custom-reports', [CustomReportController::class, 'index'])->name('custom-reports.index');
    Route::post('/custom-reports/generate', [CustomReportController::class, 'generateReport'])->name('custom-reports.generate');
});

Route::get('/properties/compare/{propertyIds}', PropertyComparison::class)->name('property.compare');
Route::get('/submit-property', PropertySubmissionForm::class)->name('property.submit');
Route::get('/property-request', PropertyRequestForm::class)->name('property.request');

Route::controller(ContactController::class)->group(function () {
    Route::get('/contact', 'show')->name('contact');
    Route::get('/contact-us', 'show')->name('contact.show');
    Route::post('/contact', 'submit')->name('contact.submit');
});

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms-and-conditions');
Route::get('/terms', [PageController::class, 'terms'])->name('termsandconditions');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacypolicy');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/calculators', CalculatorsComponent::class)->name('calculators');

Route::get('/news', NewsList::class)->name('news');
Route::get('/news-list', NewsList::class)->name('news.list');
Route::get('/news/{slug}', NewsDetail::class)->name('news.detail');

/*
 * Public delivery for the private media bucket. `media` disk URLs are built
 * against this prefix (MEDIA_URL), so this route is what every property
 * photo resolves to once object storage is switched on. It is intentionally
 * unauthenticated -- these are the same images already shown to anonymous
 * visitors on the public listings -- and it can only reach the media disk,
 * never the documents one.
 */
Route::get('/media/{path}', MediaProxyController::class)
    ->where('path', '.*')
    ->name('media.proxy');

require __DIR__.'/socialstream.php';
