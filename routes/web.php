<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TicketCategoryController;

Auth::routes(['register' => false, 'reset' => false]);

// OR add only the password reset routes manually:
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/health', fn () => response('OK', 200));

Route::get('/', function () {
    return view('index');
});


Route::get('/session-expired', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('session.expired');


Route::middleware('guest')->group(function () {
    // Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('change-password');

    // Admin-only management pages
    Route::middleware('admin')->group(function () {
        // Users
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('users/{user}/purge',        [UserController::class, 'purge'])->name('users.purge');
        Route::delete('users-purge-all',           [UserController::class, 'purgeAll'])->name('users.purge-all');

        // Departments
        Route::resource('departments', DepartmentController::class)->except(['show']);

        Route::resource('categories', TicketCategoryController::class)->except(['show']);

        // Roles
        Route::resource('roles', UserRoleController::class)->except(['show']);

        // Clients
        Route::resource('clients', ClientController::class)->except(['show']);

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/tickets', [ReportController::class, 'tickets'])->name('tickets');
            Route::get('/tickets/export', [ReportController::class, 'exportTickets'])->name('tickets.export');
            Route::get('/clients', [ReportController::class, 'clients'])->name('clients');
            Route::get('/clients/export', [ReportController::class, 'exportClients'])->name('clients.export');
            Route::get('/leaves',  [ReportController::class, 'leaves'])->name('leaves');
            Route::get('/leaves/export', [ReportController::class, 'exportLeaves'])->name('leaves.export');
            Route::get('/users',   [ReportController::class, 'users'])->name('users');
            Route::get('/users/export', [ReportController::class, 'exportUsers'])->name('users.export');
        });
    });

    // Tickets
    Route::get('/tickets',                   [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create',            [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets',                  [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}',          [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/assign',  [TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/status',  [TicketController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/comment', [TicketController::class, 'comment'])->name('tickets.comment');
    Route::post('/tickets/{ticket}/reopen',      [TicketController::class, 'reopen'])->name('tickets.reopen');
    Route::post('/tickets/{ticket}/attachments', [TicketController::class, 'uploadAttachment'])->name('tickets.attachments');
    Route::patch('/tickets/{ticket}/chargeable', [TicketController::class, 'updateChargeable'])->name('tickets.chargeable');

    // Notifications
    Route::post('/notifications/{id}/read', function ($id) {
        \App\Models\TicketNotification::where('id', $id)
            ->where('user_id', auth()->user()->user_id)
            ->first()?->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.read');

    Route::post('/notifications/read-all', function () {
        \App\Models\TicketNotification::where('user_id', auth()->user()->user_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    })->name('notifications.read-all');

    // Leave Requests
    Route::get('/leaves',                  [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create',           [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves',                 [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leave}',          [LeaveController::class, 'show'])->name('leaves.show');
    Route::get('/leaves/{leave}/print',    [LeaveController::class, 'print'])->name('leaves.print');
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject',  [LeaveController::class, 'reject'])->name('leaves.reject');
    Route::post('/leaves/{leave}/cancel',  [LeaveController::class, 'cancel'])->name('leaves.cancel');
    Route::delete('/leaves/{leave}',       [LeaveController::class, 'destroy'])->name('leaves.destroy');

    Route::get('/assistant', [App\Http\Controllers\AiAssistantController::class, 'index'])
        ->name('assistant.index');

    // Attachments — stream files through PHP (no storage symlink needed)
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'stream'])->name('attachments.view');

    // Small AI assistant endpoint (internal)
    Route::post('/ai/assist/suggest', [\App\Http\Controllers\AiAssistantController::class, 'suggest'])->middleware('auth')->name('ai.assist.suggest');

    // Wildcard fallback — MUST be last
    Route::get('{any}', [DashboardController::class, 'index'])
        ->where('any', '^(?!login|logout|api|client|admin).*$');
});

// =========================================
// CLIENT PORTAL ROUTES
// =========================================
// Separate authentication for external clients

Route::prefix('client')->name('client.')->group(function () {

    Route::get('/', function () {
        return auth('client')->check()
            ? redirect()->route('client.dashboard')
            : redirect()->route('client.login');
    })->name('home');

    // Guest client routes (not authenticated)
    Route::middleware('guest.client')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Auth\ClientAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Auth\ClientAuthController::class, 'login'])->name('login.post');
        Route::get('/forgot-password', [\App\Http\Controllers\Auth\ClientAuthController::class, 'showForgotPasswordForm'])->name('forgot-password');
        Route::post('/forgot-password', [\App\Http\Controllers\Auth\ClientAuthController::class, 'sendPasswordResetLink'])->name('forgot-password.post');
    });

    // Authenticated client routes
    Route::middleware('auth.client')->group(function () {
        // Logout
        Route::post('/logout', [\App\Http\Controllers\Auth\ClientAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Client\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Client\DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/profile/change-password', [\App\Http\Controllers\Client\DashboardController::class, 'showChangePassword'])->name('profile.change-password');
        Route::put('/profile/change-password', [\App\Http\Controllers\Client\DashboardController::class, 'updatePassword'])->name('profile.change-password.update');

        // Tickets
        Route::get('/tickets', [\App\Http\Controllers\Client\TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/create', [\App\Http\Controllers\Client\TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [\App\Http\Controllers\Client\TicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\Client\TicketController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{ticket}/comment', [\App\Http\Controllers\Client\TicketController::class, 'addComment'])->name('tickets.comment');
        Route::post('/tickets/{ticket}/attach', [\App\Http\Controllers\Client\TicketController::class, 'uploadAttachment'])->name('tickets.attach');

        // Knowledge Base
        Route::get('/knowledge', [\App\Http\Controllers\Client\KnowledgeController::class, 'index'])->name('knowledge.index');
        Route::get('/knowledge/{article}', [\App\Http\Controllers\Client\KnowledgeController::class, 'show'])->name('knowledge.show');

        // Announcements
        Route::get('/announcements', [\App\Http\Controllers\Client\AnnouncementController::class, 'index'])->name('announcements.index');

        // Ticket Ratings
        Route::get('/tickets/{ticket}/rate', [\App\Http\Controllers\Client\RatingController::class, 'create'])->name('ratings.create');
        Route::post('/tickets/{ticket}/rate', [\App\Http\Controllers\Client\RatingController::class, 'store'])->name('ratings.store');

        // Messages (SIMPLIFIED - just one view)
        Route::get('/messages', [\App\Http\Controllers\Client\MessageController::class, 'index'])->name('messages.index');
        Route::post('/messages/send', [\App\Http\Controllers\Client\MessageController::class, 'send'])->name('messages.send');

        // Real-time chat
        Route::get('/chat', [\App\Http\Controllers\Client\ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{conversation}', [\App\Http\Controllers\Client\ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{conversation}/message', [\App\Http\Controllers\Client\ChatController::class, 'store'])->name('chat.message.store');
        Route::post('/chat/{conversation}/typing', [\App\Http\Controllers\Client\ChatController::class, 'typing'])->name('chat.typing');

        // General Messages to HR & Admin (DISABLED - use /messages instead)
        // Route::get('/inbox', [\App\Http\Controllers\Client\GeneralMessageController::class, 'index'])->name('inbox.index');
        // Route::get('/inbox/create', [\App\Http\Controllers\Client\GeneralMessageController::class, 'create'])->name('inbox.create');
        // Route::post('/inbox', [\App\Http\Controllers\Client\GeneralMessageController::class, 'store'])->name('inbox.store');
        // Route::get('/inbox/{message}', [\App\Http\Controllers\Client\GeneralMessageController::class, 'show'])->name('inbox.show');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Client\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Client\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Client\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
});

// =========================================
// ADMIN EXTERNAL CLIENT MANAGEMENT ROUTES
// =========================================

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // External Client Management
    Route::get('/external-clients', [\App\Http\Controllers\Admin\ExternalClientController::class, 'index'])->name('external-clients.index');
    Route::get('/external-clients/create', [\App\Http\Controllers\Admin\ExternalClientController::class, 'create'])->name('external-clients.create');
    Route::post('/external-clients', [\App\Http\Controllers\Admin\ExternalClientController::class, 'store'])->name('external-clients.store');
    Route::get('/external-clients/{externalClient}', [\App\Http\Controllers\Admin\ExternalClientController::class, 'show'])->name('external-clients.show');
    Route::get('/external-clients/{externalClient}/edit', [\App\Http\Controllers\Admin\ExternalClientController::class, 'edit'])->name('external-clients.edit');
    Route::put('/external-clients/{externalClient}', [\App\Http\Controllers\Admin\ExternalClientController::class, 'update'])->name('external-clients.update');
    Route::delete('/external-clients/{externalClient}', [\App\Http\Controllers\Admin\ExternalClientController::class, 'destroy'])->name('external-clients.destroy');

    // Client Actions
    Route::post('/external-clients/{externalClient}/reset-password', [\App\Http\Controllers\Admin\ExternalClientController::class, 'resetPassword'])->name('external-clients.reset-password');
    Route::post('/external-clients/{externalClient}/suspend', [\App\Http\Controllers\Admin\ExternalClientController::class, 'suspend'])->name('external-clients.suspend');
    Route::post('/external-clients/{externalClient}/activate', [\App\Http\Controllers\Admin\ExternalClientController::class, 'activate'])->name('external-clients.activate');
    Route::post('/external-clients/{externalClient}/reassign', [\App\Http\Controllers\Admin\ExternalClientController::class, 'reassign'])->name('external-clients.reassign');

    // Admin client message inbox (SIMPLIFIED)
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{clientId}', [\App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{clientId}/send', [\App\Http\Controllers\Admin\MessageController::class, 'send'])->name('messages.send');

    // Real-time chat
    Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [\App\Http\Controllers\Admin\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/message', [\App\Http\Controllers\Admin\ChatController::class, 'store'])->name('chat.message.store');
    Route::post('/chat/{conversation}/typing', [\App\Http\Controllers\Admin\ChatController::class, 'typing'])->name('chat.typing');
    Route::post('/chat/{conversation}/archive', [\App\Http\Controllers\Admin\ChatController::class, 'archive'])->name('chat.archive');
});
