<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Worker\MyJobList;

// Worker routes
Route::prefix('worker')->name('worker.')->middleware(['auth:admin'])->group(function () {
    Route::get('/my-jobs', MyJobList::class)->name('jobs');
    Route::get('/jobs/{id}', \App\Livewire\Worker\JobDetail::class)->name('jobs.detail');
    Route::get('/tasks/{id}', \App\Livewire\Worker\TaskDetail::class)->name('tasks.detail');
    Route::get('/notifications', \App\Livewire\Worker\WorkerNotificationList::class)->name('notifications');
});
