<?php

namespace Laravel\Nova\Trix;

use Illuminate\Support\Facades\Artisan;

class PruneStaleAttachments
{
    /**
     * Prune the stale attachments from the system.
     *
     * @return void
     */
    public function __invoke()
    {
        Artisan::call('model:prune', [
            '--model' => PendingAttachment::class,
            '--chunk' => 100,
        ]);
    }
}
