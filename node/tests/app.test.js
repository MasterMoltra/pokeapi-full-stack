'use strict'

const dataProviderUrls = new Map()
dataProviderUrls.set('/', { method: 'GET', params: '', statuCode: 200 })
dataProviderUrls.set('/wrongroute', {
  method: 'GET',
  params: '',
  statuCode: 404,
})
dataProviderUrls.set('/node/pokeinfos', {
  method: 'GET',
  params: 'name=bulbasaur',
  statuCode: 500,
})
dataProviderUrls.set('/node/pokeinfos', {
  method: 'POST',
  params: { name: 'bulbasaur' },
  statuCode: 200,
})

test('Get a right status code from the response', () => {
  // TODO:
  //   dataProviderUrls.forEach((value, key) => {
  //     console.log(`${key}: ${value.method}`)
  //   })
})

test('Get a valid json from the response', () => {
  // TODO:
})
