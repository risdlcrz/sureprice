<?php

use App\Exceptions\UserFacingException;
use App\Support\SafeErrors;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

const LEAKY = 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'secret_col\' in C:\\xampp\\htdocs\\sureprice\\app\\Models\\Payment.php:88';

beforeEach(function () {
    SafeErrors::reset();
    Route::middleware('web')->get('/_test/boom', fn () => throw new RuntimeException(LEAKY));
    Route::middleware('web')->get('/_test/rule', fn () => throw new UserFacingException('This order cannot be shipped yet.'));
});

test('unexpected errors become a short message with a reference; the details go to the log once', function () {
    Log::spy();
    $e = new RuntimeException(LEAKY);

    $message = SafeErrors::message($e, 'Failed to verify payment', ['payment_id' => 7]);
    SafeErrors::message($e, 'Failed to verify payment');   // same exception again: not logged twice

    expect($message)->toBe('Failed to verify payment. Please try again or contact support (ref. ' . SafeErrors::reference() . ').')
        ->and($message)->not->toContain('SQLSTATE')->not->toContain('xampp')->not->toContain('secret_col');

    Log::shouldHaveReceived('error')->once()->withArgs(fn ($msg, $context) => $msg === 'Failed to verify payment'
        && $context['exception'] === $e && $context['payment_id'] === 7 && $context['ref'] === SafeErrors::reference());
});

test('messages written for users pass through unchanged and are not logged as errors', function () {
    Log::spy();

    expect(SafeErrors::message(new UserFacingException('Cannot remove more stock than available'), 'Failed to update stock'))
        ->toBe('Failed to update stock: Cannot remove more stock than available')
        ->and(SafeErrors::message(new UserFacingException('Purchase request is not pending admin approval.')))
        ->toBe('Purchase request is not pending admin approval.');

    Log::shouldNotHaveReceived('error');
});

test('JSON server errors are generic even with debug on, and the log has the full details under the same reference', function () {
    config(['app.debug' => true]);
    Log::spy();

    $json = $this->getJson('/_test/boom')->assertStatus(500)->json();

    expect(array_keys($json))->toBe(['message'])
        ->and($json['message'])->toBe(SafeErrors::GENERIC . ' (ref. ' . SafeErrors::reference() . ')')
        ->and(json_encode($json))->not->toContain('SQLSTATE')->not->toContain('xampp')->not->toContain('trace');

    Log::shouldHaveReceived('error')->withArgs(fn ($msg, $context = []) => str_contains($msg, 'SQLSTATE')
        && ($context['ref'] ?? null) === SafeErrors::reference());
});

test('the HTML error page shows a friendly message, the support address and the reference only', function () {
    config(['app.debug' => false]);

    $html = $this->get('/_test/boom')->assertStatus(500)->getContent();

    expect($html)->toContain('Something went wrong. Please try again or contact support at ' . config('app.support_email'))
        ->toContain('ref. ' . SafeErrors::reference())
        ->not->toContain('SQLSTATE')->not->toContain('xampp')->not->toContain('RuntimeException');
});

test('an uncaught business-rule exception answers with its own message', function () {
    $this->getJson('/_test/rule')->assertStatus(422)->assertExactJson(['message' => 'This order cannot be shipped yet.']);
    $this->from('/dashboard')->get('/_test/rule')->assertRedirect('/dashboard')->assertSessionHas('error', 'This order cannot be shipped yet.');
});

test('debug pages are switched off anywhere but a developer machine', function () {
    app()['env'] = 'production';
    config(['app.debug' => true]);
    Log::spy();

    (new App\Providers\AppServiceProvider(app()))->boot();

    expect(config('app.debug'))->toBeFalse();
    Log::shouldHaveReceived('error')->once();
});
