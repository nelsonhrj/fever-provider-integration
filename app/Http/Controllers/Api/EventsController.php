<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetEventsRequest;
use App\Http\Resources\EventResource;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

class EventsController extends Controller
{
    public function __construct(
        protected EventRepositoryInterface $eventRepository
    ) {}

    /**
     * Get events in the specified date range
     *
     * @param GetEventsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(GetEventsRequest $request): AnonymousResourceCollection
    {
        $startsAt = Carbon::parse($request->validated('starts_at'));
        $endsAt   = Carbon::parse($request->validated('ends_at'));

        $events = $this->eventRepository->findInDateRange($startsAt, $endsAt);

        return EventResource::collection($events);
    }
}
