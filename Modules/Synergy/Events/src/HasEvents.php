<?php

namespace Synergy\Events;

use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasEvents
{
    protected array $queuedEvents = [];

    public static function getEventClassName(): string
    {
        return config('events.event_model', Event::class);
    }

    public function getEventgableMorphName(): string
    {
        return config('events.eventgable.morph_name', 'eventgable');
    }

    public function getEventgableTableName(): string
    {
        return config('events.eventgable.table_name', 'eventgables');
    }

    public static function bootHasEvents()
    {
        static::created(function (Model $eventgableModel) {
            if (count($eventgableModel->queuedEvents) === 0) {
                return;
            }

            $eventgableModel->attachEvents($eventgableModel->queuedEvents);

            $eventgableModel->queuedEvents = [];
        });

        static::deleted(function (Model $deletedModel) {
            $events = $deletedModel->events()->get();

            $deletedModel->detachEvents($events);
        });
    }

    public function events(): MorphToMany
    {
        return $this
            ->morphToMany(self::getEventClassName(), $this->getEventgableMorphName())
            ->ordered();
    }

    public function eventsTranslated(string | null $locale = null): MorphToMany
    {
        $locale = ! is_null($locale) ? $locale : self::getEventClassName()::getLocale();

        return $this
            ->morphToMany(self::getEventClassName(), $this->getEventgableMorphName())
            ->select('*')
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"')) as name_translated")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) as slug_translated")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.\"{$locale}\"')) as slug_translated")
            ->ordered();
    }

    public function setEventsAttribute(string | array | ArrayAccess | Event $events)
    {
        if (! $this->exists) {
            $this->queuedEvents = $events;

            return;
        }

        $this->syncEvents($events);
    }

    public function scopeWithAllEvents(
        Builder $query,
        string | array | ArrayAccess | Event $events,
        string $type = null,
    ): Builder {
        $events = static::convertToEvents($events, $type);

        collect($events)->each(function ($event) use ($query) {
            $query->whereHas('events', function (Builder $query) use ($event) {
                $query->where('events.id', $event->id ?? 0);
            });
        });

        return $query;
    }

    public function scopeWithAnyEvents(
        Builder $query,
        string | array | ArrayAccess | Event $events,
        string $type = null,
    ): Builder {
        $events = static::convertToEvents($events, $type);

        return $query
            ->whereHas('events', function (Builder $query) use ($events) {
                $eventIds = collect($events)->pluck('id');

                $query->whereIn('events.id', $eventIds);
            });
    }

    public function scopeWithoutEvents(
        Builder $query,
        string | array | ArrayAccess | Event $events,
        string $type = null
    ): Builder {
        $events = static::convertToEvents($events, $type);

        return $query
            ->whereDoesntHave('events', function (Builder $query) use ($events) {
                $eventIds = collect($events)->pluck('id');

                $query->whereIn('events.id', $eventIds);
            });
    }

    public function scopeWithAllEventsOfAnyType(Builder $query, $events): Builder
    {
        $events = static::convertToEventsOfAnyType($events);

        collect($events)
            ->each(function ($event) use ($query) {
                $query->whereHas(
                    'events',
                    fn (Builder $query) => $query->where('events.id', $event ? $event->id : 0)
                );
            });

        return $query;
    }

    public function scopeWithAnyEventsOfAnyType(Builder $query, $events): Builder
    {
        $events = static::convertToEventsOfAnyType($events);

        $eventIds = collect($events)->pluck('id');

        return $query->whereHas(
            'events',
            fn (Builder $query) => $query->whereIn('events.id', $eventIds)
        );
    }

    public function eventsWithType(string $type = null): Collection
    {
        return $this->events->filter(fn (Event $event) => $event->type === $type);
    }

    public function attachEvents(array | ArrayAccess | Event $events, string $type = null): static
    {
        $className = static::getEventClassName();

        $events = collect($className::findOrCreate($events, $type));

        $this->events()->syncWithoutDetaching($events->pluck('id')->toArray());

        return $this;
    }

    public function attachEvent(string | Event $event, string | null $type = null)
    {
        return $this->attachEvents([$event], $type);
    }

    public function detachEvents(array | ArrayAccess $events, string | null $type = null): static
    {
        $events = static::convertToEvents($events, $type);

        collect($events)
            ->filter()
            ->each(fn (Event $event) => $this->events()->detach($event));

        return $this;
    }

    public function detachEvent(string | Event $event, string | null $type = null): static
    {
        return $this->detachEvents([$event], $type);
    }

    public function syncEvents(string | array | ArrayAccess $events): static
    {
        if (is_string($events)) {
            $events = Arr::wrap($events);
        }

        $className = static::getEventClassName();

        $events = collect($className::findOrCreate($events));

        $this->events()->sync($events->pluck('id')->toArray());

        return $this;
    }

    public function syncEventsWithType(array | ArrayAccess $events, string | null $type = null): static
    {
        $className = static::getEventClassName();

        $events = collect($className::findOrCreate($events, $type));

        $this->syncEventIds($events->pluck('id')->toArray(), $type);

        return $this;
    }

    protected static function convertToEvents($values, $type = null, $locale = null)
    {
        if ($values instanceof Event) {
            $values = [$values];
        }

        return collect($values)->map(function ($value) use ($type, $locale) {
            if ($value instanceof Event) {
                if (isset($type) && $value->type != $type) {
                    throw new InvalidArgumentException("Type was set to {$type} but event is of type {$value->type}");
                }

                return $value;
            }

            $className = static::getEventClassName();

            return $className::findFromString($value, $type, $locale);
        });
    }

    protected static function convertToEventsOfAnyType($values, $locale = null)
    {
        return collect($values)->map(function ($value) use ($locale) {
            if ($value instanceof Event) {
                return $value;
            }

            $className = static::getEventClassName();

            return $className::findFromStringOfAnyType($value, $locale);
        })->flatten();
    }

    protected function syncEventIds($ids, string | null $type = null, $detaching = true): void
    {
        $isUpdated = false;

        // Get a list of event_ids for all current events
        $current = $this->events()
            ->newPivotStatement()
            ->where($this->getEventgableMorphName() . '_id', $this->getKey())
            ->where($this->getEventgableMorphName() . '_type', $this->getMorphClass())
            ->when($type !== null, function ($query) use ($type) {
                $eventModel = $this->events()->getRelated();

                return $query->join(
                    $eventModel->getTable(),
                    $this->getEventgableTableName() . '.event_id',
                    '=',
                    $eventModel->getTable() . '.' . $eventModel->getKeyName()
                )
                    ->where($eventModel->getTable() . '.type', $type);
            })
            ->pluck('event_id')
            ->all();

        // Compare to the list of ids given to find the events to remove
        $detach = array_diff($current, $ids);
        if ($detaching && count($detach) > 0) {
            $this->events()->detach($detach);
            $isUpdated = true;
        }

        // Attach any new ids
        $attach = array_unique(array_diff($ids, $current));
        if (count($attach) > 0) {
            collect($attach)->each(function ($id) {
                $this->events()->attach($id, []);
            });
            $isUpdated = true;
        }

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
        if ($isUpdated) {
            $this->events()->touchIfTouching();
        }
    }
}
