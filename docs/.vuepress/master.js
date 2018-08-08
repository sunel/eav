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
      'flat-table'
    ]),
  },
]

function prefix(prefix, children) {
  return children.map(child => `${prefix}/${child}`)
}