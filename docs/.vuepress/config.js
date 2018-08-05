module.exports = {
  title: 'EAV',
  description: 'Entity–attribute–value model (EAV) for Laravel Artisan',
  base: '/docs/',

  head: [    
  ],

  themeConfig: {    
    repo: 'sunel/eav',
    displayAllHeaders: true,
    sidebarDepth: 1,

    nav: [
      {
        text: 'Version',
        items: [
          { text: 'master', link: '/master/' },
          { text: '1.0', link: '/1.0/' }
        ]
      }
    ],

    sidebar: {
      '/master/': require('./master'),
    },
  },
};