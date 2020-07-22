'use strict'

const configs = require('../_configs')

const express = require('express')
// const { check } = require('express-validator');
// const bodyParser = require('body-parser');
const cors = require('cors')

const { Pokemon } = require('./getterPokemon')
const { Cache } = require('./cacheNode')
const { wait, sanitizePathUrl, pokemonRenderBoxData } = require('./utils')

const app = express()
app.use(express.json())
// app.use(bodyParser.json());

// TODO: IMPROVE security with selected cors hosts withelist
// const corsOptions = {
//   origin: 'http://localhost:8000',
//   methods: ['GET', 'POST'],
// }
app.use(cors())

let getResource = () => {
  return async (req, res, next) => {
    const CacheService = new Cache()
    // simulating waiting
    wait(1000)

    // Do a better sanitization of received input[name]
    const pathName = sanitizePathUrl(req.body.name)

    // Vars to pass to the response (initial state 404 Not Found)
    res.locals.path = pathName
    res.locals.statusCode = 404
    res.locals.output = `
    <div class="box">
        <h2 class="error-bg">Sorry, Pok&#233;mon Not Found</h2>
    </div>`

    // Set channel "local" or "api"
    const channel =
      req.body.metadata && req.body.metadata.mode === 'api' ? 'api' : 'local'

    // Try to get chached data from CacheService
    // NOTICE: POST requests doesn't have to be cached normally, but in this specific case I can do it because I know this POST is idempotent.

    // Debug Flush All the cache for a specific channel
    // CacheService.flushByChannel(channel)

    const cacheKey = `${channel}-${pathName}`
    const chachedData = await CacheService.get(cacheKey)

    if (chachedData) {
      // Data retrieved from CacheService
      res.locals.statusCode = 200 // ONLY for debug
      // Return data inside a rendered HTML
      const renderedData = pokemonRenderBoxData(chachedData)
      res.locals.output = renderedData
    } else {
      // Try to get data from Local/Api channel
      const pokeapi = new Pokemon(pathName)

      try {
        let data
        if (channel === 'api') {
          data = await pokeapi.getByApi()
        } else {
          data = pokeapi.getByLocal()
        }

        if (data) {
          // Return data inside a rendered HTML
          const renderedData = pokemonRenderBoxData(data)
          // Before send the data cache it by CacheService with a notice DIV
          const cacheLifetime = (configs.CacheExpire[channel] / 60).toFixed()
          data.__from__ = `${channel.toUpperCase()} (by NODE) data will be get by internal CACHE for ${cacheLifetime} minutes!`

          await CacheService.set(
            cacheKey,
            JSON.stringify(data),
            configs.CacheExpire[channel]
          )

          res.locals.statusCode = 200
          res.locals.output = renderedData
        }
      } catch (error) {
        // ERROR - Something was wrong
        res.locals.statusCode = 500
        res.locals.output = `
        <div class="box error-bg">
          Something was wrong, sorry try again later!
        </div>`
      }
    }

    // Return the middleware
    return next()
  }
}

// Express route handlers
app.get('/', (req, res) => {
  res.send(`Hi, I'm a NODE server listening on ${req.headers.host}`)
})

app.post(
  '/node/pokeinfos',
  // [
  //     check('name')
  //         .whitelist(['/[A-Za-zÀ-ÖØ-öø-ÿ-s]/gim'])
  //         .stripLow()
  //         .trim()
  // ],
  getResource(),
  (req, res) => {
    res.status(res.locals.statusCode).send({
      path: res.locals.path,
      content: res.locals.output,
    })
  }
)

module.exports = app
