module.exports = [
  {
    title: 'Getting Started',
    collapsable: false,
    children: ['installation'],
  }
]

function prefix(prefix, children) {
  return children.map(child => `${prefix}/${child}`)
}