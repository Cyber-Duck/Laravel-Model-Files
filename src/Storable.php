<?php

namespace Cyberduck\ModelFiles;

use Illuminate\Http\UploadedFile;

/**
 * Interface Storable
 *
 * @todo Different name? Split interfaces? It cover's 2 different types of methods:
 * - Interaction with Storage
 * - Interaction with FileAttributes
 */
interface Storable
{
    public function url($attribute);

    public function storeFile(UploadedFile $file, $attribute);

    public function deleteFile($attribute);

    public function disk($attribute);

    public function folder($attribute);

    public function getFileAttributes();

    public function hasDirtyFileAttributes();

    public function getDirtyFileAttributes();
}