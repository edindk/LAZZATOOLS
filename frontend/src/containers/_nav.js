export default [
    {
        _name: 'CSidebarNav',
        _children: [
            {
                _name: 'CSidebarNavItem',
                name: 'Kontrolpanel',
                to: '/dashboard',
                icon: 'cil-speedometer',
            },
            {
                _name: 'CSidebarNavTitle',
                _children: ['Værktøjer']
            },
            {
                _name: 'CSidebarNavItem',
                name: 'Mixer',
                to: '/mixer',
                icon: 'cilText'
            },
            {
                _name: 'CSidebarNavItem',
                name: 'Domæne status',
                to: '/whois',
                icon: 'cilBell'
            },
            {
                _name: 'CSidebarNavItem',
                name: 'Map scraper',
                to: '/mapscraper',
                icon: 'cilMap'
            },
            {
                _name: 'CSidebarNavItem',
                name: 'Søgeordshøster',
                to: '/search',
                icon: 'cilSearch'
            },
        ]
    }
]