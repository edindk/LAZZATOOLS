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
                name: 'revenueWire',
                to: '/revenuewire',
                icon: 'cilText'
            },
        ]
    }
]