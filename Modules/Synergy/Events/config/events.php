<?php

return [

    /*
     * The given function generates a URL friendly "slug" from the tag name property before saving it.
     * Defaults to Str::slug (https://laravel.com/docs/master/helpers#method-str-slug)
     */
    'slugger' => null,

    /*
     * The fully qualified class name of the tag model.
     */
    'event_model' => Synergy\Events\Event::class,

    /*
     * The name of the table associated with the taggable morph relation.
     */
    'eventgable' => [
        'table_name' => 'eventgables',
        'morph_name' => 'eventgable',
    ],
    'translatable'=>[]
];
