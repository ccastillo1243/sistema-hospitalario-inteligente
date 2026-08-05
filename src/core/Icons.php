<?php

/**
 * Íconos SVG inline minimalistas (stroke-based, estilo Feather/Lucide),
 * para no depender de una librería de íconos externa.
 */
class Icons
{
    private const PATHS = [
        'home' => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/>',
        'users' => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.7-3.3 3.2-5.5 6.5-5.5s5.8 2.2 6.5 5.5"/><circle cx="17" cy="8.5" r="2.6"/><path d="M15.5 14.7c2.6.4 4.5 2.3 5 5.3"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M8 3v4"/><path d="M16 3v4"/>',
        'bed' => '<path d="M2 18v-7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v7"/><path d="M2 18v2"/><path d="M22 18v2"/><path d="M2 13h20"/><circle cx="7" cy="9" r="1.3"/>',
        'flask' => '<path d="M9 3h6"/><path d="M10 3v6.5L4.8 18a2 2 0 0 0 1.7 3h11a2 2 0 0 0 1.7-3L14 9.5V3"/><path d="M7.5 15h9"/>',
        'pill' => '<rect x="3.5" y="9.5" width="17" height="7" rx="3.5" transform="rotate(-40 12 13)"/><path d="M9.2 8.3 15.7 15.8" stroke-dasharray="0"/>',
        'box' => '<path d="M3 8.5 12 4l9 4.5-9 4.5-9-4.5Z"/><path d="M3 8.5V16l9 4.5 9-4.5V8.5"/><path d="M12 13v7.5"/>',
        'scan' => '<path d="M4 8V5a1 1 0 0 1 1-1h3"/><path d="M20 8V5a1 1 0 0 0-1-1h-3"/><path d="M4 16v3a1 1 0 0 0 1 1h3"/><path d="M20 16v3a1 1 0 0 1-1 1h-3"/><circle cx="12" cy="12" r="3.5"/>',
        'card' => '<rect x="2.5" y="5.5" width="19" height="13" rx="2"/><path d="M2.5 10h19"/><path d="M6 14.5h4"/>',
        'alert' => '<path d="M12 3 22 20H2Z"/><path d="M12 10v4.5"/><circle cx="12" cy="17.3" r="0.4" fill="currentColor"/>',
        'chart' => '<path d="M4 20V10"/><path d="M11 20V4"/><path d="M18 20v-7"/><path d="M2.5 20h19"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
        'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
        'edit' => '<path d="M4 20h4L18.5 9.5a2.1 2.1 0 0 0-3-3L5 17v3Z"/><path d="M13.5 7 17 10.5"/>',
        'trash' => '<path d="M4 7h16"/><path d="M9 7V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V7"/><path d="M6 7l1 13a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1l1-13"/><path d="M10 11v6"/><path d="M14 11v6"/>',
        'close' => '<path d="M5 5l14 14"/><path d="M19 5 5 19"/>',
        'bell' => '<path d="M6 9a6 6 0 0 1 12 0c0 4.5 1.5 6 1.5 6h-15S6 13.5 6 9Z"/><path d="M10 19a2 2 0 0 0 4 0"/>',
        'menu' => '<path d="M3 6h18"/><path d="M3 12h18"/><path d="M3 18h18"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.8-4.8"/>',
        'file' => '<path d="M6 3h8l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M14 3v5h5"/>',
    ];

    public static function svg(string $name, string $class = 'icon'): string
    {
        $path = self::PATHS[$name] ?? self::PATHS['file'];
        return '<svg class="' . htmlspecialchars($class, ENT_QUOTES) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
    }
}
