@props(['name' => 'arrow'])
<svg {{ $attributes->class(['icon']) }} width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
@case('whatsapp')<path d="M21 11.5a9 9 0 0 1-13.3 7.9L3 21l1.6-4.7A9 9 0 1 1 21 11.5Z"/><path d="M8 7.5c0 4.5 4 8 8 8l1-2-3-1-1 1c-1.8-.7-3-1.9-3.5-3.5l1-1-1-2.5Z"/>@break
@case('mail')<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/>@break
@case('grid')<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>@break
@case('window')<rect x="3" y="3" width="18" height="18" rx="1"/><path d="M12 3v18M3 12h18m-2-6-3 3M9 15l-3 3"/>@break
@case('plus')<path d="M12 5v14M5 12h14"/>@break
@case('edit')<path d="m14 5 5 5M4 20l5-1L20 8a3.5 3.5 0 0 0-5-5L4 14Z"/>@break
@case('trash')<path d="M3 6h18M9 6V3h6v3M6 6l1 15h10l1-15M10 10v7m4-7v7"/>@break
@case('settings')<path d="M4 7h16M4 17h16M8 4v6m8 4v6"/>@break
@case('logout')<path d="M9 4H4v16h5m6-12 4 4-4 4M9 12h10"/>@break
@case('check')<path d="m5 12 4 4L19 6"/>@break
@case('menu')<path d="M4 6h16M4 12h16M4 18h16"/>@break
@case('lock')<rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/>@break
@default<path d="M5 12h14m-5-5 5 5-5 5"/>
@endswitch
</svg>
