<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/admin/login', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login', ['admin' => '1']);
})->name('admin.login');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'user.only'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'user.only'])->group(function () {
    Route::get('/tickets', function () {
        return Inertia::render('Tickets/Index');
    })->name('tickets.index');

    Route::get('/tickets/create', function () {
        return Inertia::render('Tickets/Create');
    })->name('tickets.create');

    Route::get('/tickets/history', function () {
        return Inertia::render('Tickets/History');
    })->name('tickets.history');

    Route::get('/tickets/{ticket}', function ($ticket) {
        return Inertia::render('Tickets/Show', ['ticketId' => $ticket]);
    })->name('tickets.show');

    Route::post('/api/tickets', [TicketController::class, 'store'])->name('api.tickets.store');
    Route::get('/api/tickets', [TicketController::class, 'index'])->name('api.tickets.index');
    Route::get('/api/tickets/{ticket}', [TicketController::class, 'show'])->name('api.tickets.show');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');

        Route::get('/tickets/queue', function () {
            return Inertia::render('Admin/Queue');
        })->name('admin.tickets.queue');

        Route::get('/tickets/logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.tickets.logs');
        Route::get('/tickets/logs/export', [App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('admin.activity-logs.export');

        Route::get('/tickets/{id}/show', function ($id) {
            return Inertia::render('Admin/TicketDetails', ['ticketId' => $id]);
        })->name('admin.tickets.show');

        Route::get('/api/tickets/queue', [TicketController::class, 'adminQueue'])->name('api.admin.tickets.queue');
        Route::get('/api/dashboard/stats', [TicketController::class, 'adminDashboardStats'])->name('api.admin.dashboard.stats');
        Route::get('/api/tickets/logs/export', [TicketController::class, 'exportLogs'])->name('api.admin.tickets.export');
        Route::post('/api/tickets/{id}/status', [TicketController::class, 'adminChangeStatus'])->name('api.admin.tickets.status');
        Route::post('/api/tickets/{id}/priority', [TicketController::class, 'adminSetPriority'])->name('api.admin.tickets.priority');
        Route::get('/api/tickets/{id}', [TicketController::class, 'adminShow'])->name('api.admin.tickets.show');
    });

require __DIR__ . '/auth.php';

