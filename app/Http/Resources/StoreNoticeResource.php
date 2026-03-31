<?php

namespace Modules\Vendor\Http\Resources;

use Illuminate\Http\Request;
use Modules\Core\Http\Resources\Resource;

class StoreNoticeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'priority' => $this->priority,
            'notice' => $this->notice,
            'description' => $this->description,
            'effective_from' => $this->effective_from,
            'expired_at' => $this->expired_at,
            'creator_role' => $this->creator_role,
            'is_read' => $this->is_read,
            'creator' => $this->whenLoaded('creator', fn () => ['id' => $this->creator->id, 'name' => $this->creator->name, 'email' => $this->creator->email], ['id' => null, 'name' => null, 'email' => null]),
            'users' => $this->whenLoaded('users', fn () => getResourceCollection($this->users, ['email']), []),
            'shops' => $this->whenLoaded('shops', fn () => getResourceCollection($this->shops), []),
            'read_status' => $this->whenLoaded('read_status', fn () => $this->readStatusRecourseData($this->read_status), []),
        ];
    }

    private function readStatusRecourseData($read_status): array
    {
        return collect($read_status)->map(function ($value) {
            return [
                'id' => $value->id,
                'name' => $value->name,
                'email' => $value->email,
                'is_read' => $value->pivot->is_read,
                'pivot' => $value->pivot,
            ];
        })->toArray();
    }
}
