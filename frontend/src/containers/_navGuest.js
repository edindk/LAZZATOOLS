export default [
    {
        _name: 'CSidebarNav',
        _children: [
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
                name: 'Map scraper',
                to: '/mapscraper',
                icon: 'cilMap'
            },
        ]
    }
]