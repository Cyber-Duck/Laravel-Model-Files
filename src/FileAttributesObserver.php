<?php

namespace Cyberduck\ModelFiles;

use Illuminate\Http\UploadedFile;

class FileAttributesObserver
{
    /**
     * @param HasFiles $item
     */
    public function saving($item)
    {
        if ($item->hasDirtyFileAttributes()) {
            $this->handleDirtyFileAttributes($item);
        }
    }

    /**
     * @param HasFiles $item
     */
    public function handleDirtyFileAttributes($item)
    {
        foreach ($item->getDirtyFileAttributes() as $attribute) {
            if ($item->{$attribute} instanceof UploadedFile) {
                $item->storeFile($item->{$attribute}, $attribute);
            }

            if ($item->isPrunable($attribute)) {
                $item->deleteOriginalFile($attribute);
            }
        }
    }
}
