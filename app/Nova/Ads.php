<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Date;

class Ads extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Ads>
     */
    public static $model = \App\Models\Ads::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('admin')->hideWhenCreating()->hideWhenUpdating(),

            Text::make('Title','title')->displayUsing(function ($value) {
                return mb_substr($value,0,40);
            })->onlyOnIndex(),
            Text::make('Title','title')->hideFromIndex(),

            Image::make('image')
                ->store(
                    function(Request $request,$model){
                        $image=$request->image->store('Adsph','do');
                        return [
                            'image_location'=>$image,
                            'image_name'=>str_replace("Ads/","",$image),
                            'image_path'=>"Ads"
                        ];
                    }
                )->hideFromDetail()->hideFromIndex()->creationRules('required'),
            Image::make('Image','image_location')->disk('do')->hideWhenCreating()->hideWhenUpdating(),
            Text::make('Description','description')->displayUsing(function ($value) {
                return mb_substr($value,0,40);
            })->onlyOnIndex(),
            Text::make(__('Content'), 'description'),
            Text::make('Type','type')->hideFromIndex(),
            Text::make('Url','url')->hideFromIndex(),
            Date::make("Upload At","created_at")->hideWhenCreating()->hideWhenUpdating(),
            // AuditableLog::make()
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}