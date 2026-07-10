<?php

if (! function_exists('asset_versioned')) {
    /**
     * Asset URL stamped with the file's modification time (?v=...), so
     * browsers can cache hard (see public/.htaccess) yet instantly pick up
     * a replaced file — no more hard refreshes after changing the logo/CSS.
     */
    function asset_versioned(string $path): string
    {
        $full = public_path($path);
        $version = is_file($full) ? filemtime($full) : null;

        return asset($path).($version ? '?v='.$version : '');
    }
}
