<?php

use App\Http\Controllers\ContactController;
use App\Livewire\Survey;
use App\Models\Office;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $offices = Office::active()
        ->with('services')
        ->withCount(['surveyResponses as responses_count' => fn ($q) => $q->where('is_complete', true)])
        ->get()
        ->map(function ($office) {
            $office->avg_satisfaction = round(
                SurveyResponse::where('office_id', $office->id)
                    ->where('is_complete', true)
                    ->whereNotNull('sqd0')
                    ->avg('sqd0') ?? 0,
                2
            );
            return $office;
        });

    $officesJson = $offices->map(fn ($o) => [
        'id' => $o->id,
        'name' => $o->name,
        'code' => $o->code,
        'services' => $o->services->map(fn ($s) => $s->name),
        'responses_count' => $o->responses_count,
        'avg_satisfaction' => $o->avg_satisfaction,
    ]);

    return view('welcome', compact('offices', 'officesJson'));
});

Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');

Route::livewire('/survey', Survey::class)->name('survey');
