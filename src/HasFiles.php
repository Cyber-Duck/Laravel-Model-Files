<?php

namespace Cyberduck\ModelFiles;

trait HasFiles
{
    use InteractsWithStorage;

    public static function bootHasFiles()
    {
        static::observe(new FileAttributesObserver);
    }

    public function disk($attribute)
    {
        return array_get($this->files, "$attribute.disk", 'default');
    }

    public function folder($attribute)
    {
        return array_get($this->files, "$attribute.folder", '');
    }

    public function isPrunable($attribute)
    {
        return array_get($this->files, "$attribute.prunable", true);
    }

    public function hasDirtyFileAttributes()
    {
        return ! empty($this->getDirtyFileAttributes());
    }

    public function getDirtyFileAttributes()
    {
        return array_intersect($this->getFileAttributes(), array_keys($this->getDirty()));
    }

    public function getFileAttributes()
    {
        return array_keys($this->files) ?: [];
    }
}