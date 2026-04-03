<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Settings\Models\Settings;
use Modules\User\Models\User;
use Modules\Vendor\Models\StoreNotice;

class StoreNoticeEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public StoreNotice $storeNotice,
        public ?string $action,
        public User $user
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $eventChannels = [];
        if (isset($this->storeNotice->users)) {
            foreach ($this->storeNotice->users as $user) {
                $eventChannels[] = new PrivateChannel('store_notice.created.'.$user->id);
            }
        }

        return $eventChannels;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'message' => '1 new store notice.',
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'store.notice.event';
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        try {
            $settings = Settings::first();
            $enableBroadCast = false;

            if (config('shop.pusher.enabled') === null) {
                return false;
            }

            if (isset($settings->options['pushNotification']['all']['storeNotice'])) {
                if ($settings->options['pushNotification']['all']['storeNotice'] === true && $this->action === 'create') {
                    $enableBroadCast = true;
                }
            }

            return $enableBroadCast;
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG, $th->getMessage());
        }
    }
}
