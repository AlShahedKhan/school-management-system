<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicTranslationRequest;
use App\Models\PublicTranslation;
use App\Support\PublicTranslationResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPublicTranslationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $group = trim($request->string('group')->toString());
        $search = trim($request->string('search')->toString());

        $query = PublicTranslation::query()
            ->when($group !== '', fn ($query) => $query->where('group', $group))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('key', 'like', "%{$search}%")
                        ->orWhere('en', 'like', "%{$search}%")
                        ->orWhere('bn', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('group')
            ->orderBy('key');

        return response()->json([
            'translations' => $query->paginate(30)->withQueryString(),
            'groups' => PublicTranslation::query()
                ->whereNotNull('group')
                ->distinct()
                ->orderBy('group')
                ->pluck('group')
                ->values(),
        ]);
    }

    public function store(PublicTranslationRequest $request, PublicTranslationResolver $resolver): JsonResponse
    {
        $translation = PublicTranslation::create($request->validated());
        $resolver->clearCache();

        return response()->json($translation, 201);
    }

    public function update(
        PublicTranslationRequest $request,
        PublicTranslation $publicTranslation,
        PublicTranslationResolver $resolver
    ): JsonResponse {
        $publicTranslation->update($request->safe()->except('key'));
        $resolver->clearCache();

        return response()->json($publicTranslation->refresh());
    }
}
