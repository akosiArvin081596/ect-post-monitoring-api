<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Requests\UploadSurveyFileRequest;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyUploadResource;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SurveyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $surveys = $request->user()
            ->surveys()
            ->with('uploads')
            ->latest()
            ->paginate(20);

        return SurveyResource::collection($surveys);
    }

    public function store(StoreSurveyRequest $request): JsonResponse
    {
        // Idempotent: return existing survey if client_uuid matches
        $existing = Survey::query()
            ->where('client_uuid', $request->validated('client_uuid'))
            ->first();

        if ($existing) {
            return (new SurveyResource($existing->load('uploads')))
                ->response()
                ->setStatusCode(200);
        }

        $survey = $request->user()
            ->surveys()
            ->create($request->validated());

        return (new SurveyResource($survey->load('uploads')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Survey $survey): SurveyResource
    {
        $this->authorize('view', $survey);

        return new SurveyResource($survey->load('uploads'));
    }

    public function update(UpdateSurveyRequest $request, Survey $survey): SurveyResource
    {
        $this->authorize('update', $survey);

        $survey->update($request->validated());

        return new SurveyResource($survey->load('uploads'));
    }

    public function destroy(Request $request, Survey $survey): JsonResponse
    {
        $this->authorize('delete', $survey);

        $survey->delete();

        return response()->json(['message' => 'Survey deleted.']);
    }

    public function upload(UploadSurveyFileRequest $request, Survey $survey): JsonResponse
    {
        $this->authorize('update', $survey);

        $file = $request->file('file');
        $path = $file->store('survey-uploads/'.$survey->id, 'public');

        $upload = $survey->uploads()->create([
            'type' => $request->validated('type'),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return (new SurveyUploadResource($upload))
            ->response()
            ->setStatusCode(201);
    }
}
