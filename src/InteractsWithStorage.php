<?php

namespace Cyberduck\ModelFiles;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait InteractsWithStorage
{
    /**
     * Handles the URL generation for the multiple types of files in the system
     *
     * @param $attribute
     * @return string
     */
    public function url($attribute)
    {
        $urlType = array_get($this->files, "$attribute.url", 'public');

        if ($urlType == 'custom') {
            return $this->customUrl($attribute);
        }

        if (! method_exists($this, $urlType . 'Url')) {
            throw new \LogicException('Invalid url type received');
        }

        return call_user_func([$this, $urlType . 'Url'], $attribute);
    }

    /**
     * Get public URLs stored on public disk
     *
     * @param $attribute
     * @return string
     */
    protected function publicUrl($attribute)
    {
        return Storage::disk($this->disk($attribute))->url($this->{$attribute});
    }

    /**
     * Gets the temporary Url for external services
     * e.g. s3, rackspace
     *
     * @param $attribute
     * @return string
     */
    protected function temporaryUrl($attribute)
    {
        $expiresAt = array_get($this->files, "$attribute.expiresAt", now()->addMinutes(5));

        return Storage::disk($this->disk($attribute))
            ->temporaryUrl($this->{$attribute}, $expiresAt);
    }

    /**
     * Used for private/custom files
     * Create method "getUrl{attribute}"
     *
     * @param $attribute
     * @return string
     */
    protected function customUrl($attribute)
    {
        $methodName = camel_case("get_url_$attribute");

        if (! method_exists($this, $methodName)) {
            throw new \LogicException("Please specify the Url Generation for custom File: $methodName");
        }

        return call_user_func([$this, $methodName], $this->{$attribute});
    }

    /**
     * @param UploadedFile $file
     * @param string       $attribute
     * @return bool
     */
    public function storeFile(UploadedFile $file, $attribute)
    {
        $path = $file->store($this->folder($attribute), [
            'disk' => $this->disk($attribute),
        ]);

        if (! $path) {
            return false;
        }

        $this->{$attribute} = $path;

        return true;
    }

    /**
     * Deletes current file in stored in the attribute
     *
     * @param $attribute
     * @return bool
     */
    public function deleteFile($attribute)
    {
        if (! $this->{$attribute}) {
            return false;
        }

        if (! Storage::disk($this->disk($attribute))->delete($this->{$attribute})) {
            return false;
        }

        $this->{$attribute} = null;

        return true;
    }

    /**
     * Deletes current file in stored in the attribute
     *
     * @param $attribute
     * @return bool
     */
    public function deleteOriginalFile($attribute)
    {
        if (! $filePath = $this->getOriginal($attribute)) {
            return false;
        }

        if (! Storage::disk($this->disk($attribute))->delete($filePath)) {
            return false;
        }

        return true;
    }
}
