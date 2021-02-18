export default [
    {
        _name: 'CSidebarNav',
        _children: [
            {
                _name: 'CSidebarNavItem',
                name: 'Kontrolpanel',
                to: '/dashboard',
                icon: 'cil-speedometer',
                // badge: {
                //     color: 'primary',
                //     text: 'NEW'
                // }
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
        ]
    }
]