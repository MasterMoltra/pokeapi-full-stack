'use strict'

const { sanitizePathUrl } = require('../src/utils')

const dataProviderStings = [
  ['pokémon', 'pokemon'],
  ['pikachū', 'pikachu'],
  ['Porygon-Z', 'porygon-z'],
  ['Flabébé', 'flabebe'],
  ["Farfetch'd", 'farfetchd'],
  ['Tapu Bulu', 'tapu-bulu'],
  ['òàùèìü', 'oaueiu'],
  ['https://malware.domaìn.com', ''],
  ['<script></script>', 'scriptscript'],
]

// testConvertStringToValidAsciiUrlPath
test('Convert a string to a valid Ascii url path', () => {
  let string
  dataProviderStings.forEach(value => {
    string = sanitizePathUrl(value[0])
    expect(string).toBe(value[1])
  })
})
