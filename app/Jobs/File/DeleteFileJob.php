<?php

namespace App\Jobs\File;

use App\Exceptions\CustomException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;

class DeleteFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $path)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return bool
     * @throws CustomException
     */
    public function handle(): bool
    {
        try {
            return Storage::delete($this->path);
        } catch (FileException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
