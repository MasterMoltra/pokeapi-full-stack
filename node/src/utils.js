'use strict'

const diacriticsMap = require('../lib/diacriticsMap')

const wait = ms => {
  let start = new Date().getTime()
  let end = start
  while (end < start + ms) {
    end = new Date().getTime()
  }
  console.log('done I waited ' + (end - start) / 1000.0 + ' seconds.')
}

const sanitizePathUrl = string => {
  // This convert special chars (àèìòù) in friendly ascii chars
  for (let i = 0; i < Object.keys(diacriticsMap).length; i++) {
    string = string.replace(diacriticsMap[i].letters, diacriticsMap[i].base)
  }

  // This remove urls and tags, convert spaces to -, set string lowercase
  return string
    .replace(/(?:https?|ftp):\/\/[\n\S]+/gi, '')
    .replace(/[^A-Za-zÀ-ÖØ-öø-ÿ-\s]/gi, '')
    .replace(/\s/g, '-')
    .toLowerCase()
}

const pokemonRenderBoxData = data => {
  if (!(data instanceof Object)) {
    return
  }

  let name = data.name
    ? data.name.charAt(0).toUpperCase() + data.name.slice(1)
    : ''

  let image =
    data.sprites.front_default ||
    data.sprites.back_default ||
    data.sprites.front_shiny ||
    data.sprites.back_shiny
  image = image ? `<div><img src="${image}" width="150"></div>` : ''

  let indicies = ''
  for (let v in data.game_indices) {
    indicies += `${data.game_indices[v].version.name} | `
  }
  indicies = indicies ? indicies.replace(/\|\s$/, '') : 'None!'

  let isCached = data.__from__
    ? `<div class="block-highlight">${data.__from__}</div>`
    : ''

  return `
    <div class="box">
      <h2>${name}</h2>
      ${image}
      ID.${data.id} - Weight ${data.weight} - Height${data.height}
      <p>
        <strong>GAMES INDICIES:</strong><br>
        ${indicies}
      </p>    
      ${isCached}
    </div>
    `
}

module.exports = {
  wait,
  sanitizePathUrl,
  pokemonRenderBoxData,
}
