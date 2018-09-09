module.exports = [
  {
    title: 'Getting Started',
    collapsable: false,
    children: [
      'installation', 
      'configuration',
      'usage',
    ],
  },
  {
    title: 'ORM',
    collapsable: false,
    children: [
      'model',
      'queries',
      'facet',
    ],
  },
  {
    title: 'Ideology',
    collapsable: false,
    children: prefix('ideology', [
      'entity',
      'attribute',
      'attribute-set',
      'attribute-group',
      'value',
      'static-attribute',
      'flat-table',
      'attribute-options',
    ]),
  },
  {
    title: 'Advance',
    collapsable: false,
    children: [
      'export-import',
      'snippet',
      'custom-table',
      'er',
    ],
  },
]

function prefix(prefix, children) {
  return children.map(child => `${prefix}/${child}`)
}