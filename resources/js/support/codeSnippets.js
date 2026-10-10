/**
 * Sample backend code for the decorative code stream: one small report-export
 * feature, seen from the job, controller, routes, migration, model and test.
 */

const JOB = String.raw`<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportReportJob implements ShouldQueue
{
    use Queueable;

    public function handle(ExcelWriter $writer): void
    {
        $this->report->markAs('processing');

        // stream rows in chunks so memory stays flat
        Order::query()
            ->whereBetween('created_at', $this->report->range())
            ->lazyById(2000)
            ->each(fn ($order) => $writer->append($order->toRow()));

        $this->report->markAs('ready');
    }
}`;

const CONTROLLER = String.raw`class ReportController extends Controller
{
    public function store(StoreReportRequest $request): JsonResponse
    {
        $report = Report::create($request->validated());

        ExportReportJob::dispatch($report)->onQueue('exports');

        return response()->json(['status' => $report->status], 202);
    }
}`;

const ROUTES = String.raw`Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
});`;

const MIGRATION = String.raw`Schema::create('reports', function (Blueprint $table) {
    $table->id();
    $table->string('status')->default('queued');
    $table->json('filters')->nullable();
    $table->timestamp('ready_at')->nullable();
    $table->timestamps();
});`;

const MODEL = String.raw`class Report extends Model
{
    protected $casts = [
        'filters' => 'array',
        'ready_at' => 'datetime',
    ];

    public function markAs(string $status): void
    {
        $this->update(['status' => $status]);
    }
}`;

const TEST = String.raw`it('queues the export and answers 202', function () {
    Queue::fake();

    postJson('/api/reports', ['range' => 'last-30-days'])
        ->assertAccepted();

    Queue::assertPushedOn('exports', ExportReportJob::class);
});`;

/** Snippets per column, ordered so neighbouring columns never show the same file at once. */
export const CODE_COLUMNS = [
    [JOB, CONTROLLER, ROUTES],
    [MIGRATION, TEST, MODEL],
    [ROUTES, MODEL, JOB],
    [TEST, ROUTES, CONTROLLER, MIGRATION],
];
