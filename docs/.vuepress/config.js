module.exports = {
  title: 'EAV',
  description: 'Entity–attribute–value model (EAV) for Laravel Artisan',
  base: '/eav/',

  head: [    
  ],

  themeConfig: {    
    repo: 'sunel/eav',
    displayAllHeaders: true,
    sidebarDepth: 1,

    nav: [
      {
        text: 'Dashboard',
        link: '/master/dashboard.html'
      },
      {
        text: 'Documentation',
        items: [
          { text: 'master', link: '/master/' }
        ]
      }
    ],

    sidebar: {
      '/master/': require('./master'),
    },
  },
};