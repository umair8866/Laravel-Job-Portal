<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\admin\JobController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\JobApplicationController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\VacancyNotificationEmail;



// Route::get('/', function () {
//     return view('welcome');
// });

route::get('/', [HomeController::class, 'index'])->name('home');
route::get('/jobs', [JobsController::class, 'index'])->name('jobs');
route::get('/jobs/detail/{id}', [JobsController::class, 'detail'])->name('jobDetail');
route::post('/apply-job', [JobsController::class, 'applyJob'])->name('applyjob');
route::post('/saved-job', [JobsController::class, 'saveVacancy'])->name('savejob');





route::get('/account/register', [AccountController::class, 'registration'])->name('account.registration');

route::post('/account/process-register', [AccountController::class, 'processRegistration'])->name('account.processRegistration');
route::get('/account/login', [AccountController::class, 'login'])->name('account.login');

route::post('/account/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');

route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');

route::get('/account/logout', [AccountController::class, 'logout'])->name('account.logout');

route::put('/account/updateProfile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');

route::post('/account/update-Profile-Pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');

route::get('/account/create-Job', [AccountController::class, 'createJob'])->name('account.createJob');

route::post('/account/save-Job', [AccountController::class, 'saveJob'])->name('account.saveJob');

route::get('/account/my-Jobs', [AccountController::class, 'myjobs'])->name('account.myjobs');

route::get('/account/my-Jobs/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');

route::post('/account/update-job', [AccountController::class, 'updateJob'])->name('account.updateJob');

route::post('/account/delete-job', [AccountController::class, 'deleteJob'])->name('account.deleteJob');

route::get('/account/myJobApplications', [AccountController::class, 'myJobApplications'])->name('account.myJobApplications');

route::post('/account/remove-job-application', [AccountController::class, 'removeVacancy'])->name('account.removeVacancy');

route::get('/account/saved-Jobs', [AccountController::class, 'savedVacancy'])->name('account.savedVacancy');

route::post('/account/remove-saved-job', [AccountController::class, 'removeSavedVacancy'])->name('account.removeSavedVacancy');

route::post('/account/change-password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');

//Admin routes start here 
// Routes to create user edit and delete starts here
route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
route::get('/admin/users/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
route::delete('/admin/users', [UserController::class, 'destroy'])->name('admin.users.destroy');
// Routes to create user edit and delete starts here

// Routes for admin jobs starts here

route::get('/admin/jobs', [JobController::class, 'index'])->name('admin.jobs');
route::get('/admin/edit/{id}', [JobController::class, 'edit'])->name('admin.jobs.edit');
route::put('/admin/jobs/{id}', [JobController::class, 'update'])->name('admin.jobs.update');
route::delete('/admin/jobs', [UserController::class, 'destroy'])->name('admin.jobs.destroy');

// Routes for admin jobs starts here

// Routes for admin jobs application show starts here
route::get('/admin/job-applications', [JobApplicationController::class, 'index'])->name('admin.jobApplications');
route::delete('/admin/jobApplications', [JobApplicationController::class, 'destroy'])->name('admin.jobApplications.destroy');



























