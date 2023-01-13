<?php

namespace Laravel\Nova\Trix;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Trix;

/**
 * @property string $attachment
 * @property string $disk
 */
class PendingAttachment extends Model
{
    use Prunable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nova_pending_trix_attachments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Persist the given draft's pending attachments.
     *
     * @param  string  $draftId
     * @param  \Laravel\Nova\Fields\Trix  $field
     * @param  mixed  $model
     * @return void
     */
    public static function persistDraft($draftId, Trix $field, $model)
    {
        static::where('draft_id', $draftId)->get()->each->persist($field, $model);
    }

    /**
     * Persist the pending attachment.
     *
     * @param  \Laravel\Nova\Fields\Trix  $field
     * @param  mixed  $model
     * @return void
     */
    public function persist(Trix $field, $model)
    {
        $disk = $field->getStorageDisk();

        Attachment::create([
            'attachable_type' => $model->getMorphClass(),
            'attachable_id' => $model->getKey(),
            'attachment' => $this->attachment,
            'disk' => $disk,
            'url' => Storage::disk($disk)->url($this->attachment),
        ]);

        $this->delete();
    }

    /**
     * Get the prunable model query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(1));
    }

    /**
     * Prepare the model for pruning.
     *
     * @return void
     */
    protected function pruning()
    {
        Storage::disk($this->disk)->delete($this->attachment);
    }
}
