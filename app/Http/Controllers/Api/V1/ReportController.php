<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function store(Request $request, PortfolioItem $portfolioItem): JsonResponse
    {
        $this->authorize('create', Report::class);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $report = $portfolioItem->reports()->create([
            'reporter_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
        ]);

        return response()->json([
            'id' => $report->id,
            'status' => $report->status->value,
        ], 201);
    }

    public function update(Request $request, Report $report): JsonResponse
    {
        $this->authorize('update', $report);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(ReportStatus::class)],
            'moderator_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $report->update($validated);

        if ($report->status === ReportStatus::Actioned && $report->reportable instanceof PortfolioItem) {
            $report->reportable->update(['published_at' => null]);
        }

        return response()->json([
            'id' => $report->id,
            'status' => $report->status->value,
        ]);
    }
}
