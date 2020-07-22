'use strict'

const configs = require('../_configs')

const cacache = require('cacache')

class Cache {
  constructor() {
    this.cache = cacache
  }

  async set(key, value, duration) {
    console.log(`Chached key '${key}' content saved.`)
    return await this.cache.put(configs.CacheDir, key, value, {
      metadata: { expires: Date.now() + duration * 1000 },
    })
  }

  async get(key) {
    if (configs.CacheEnabled === false) {
      return null
    }

    return await this.cache
      .get(configs.CacheDir, key)
      .then(data => {
        // Check it'snt expired
        if (Date.now() - data.metadata.expires > 0) {
          this.del(key)
          return null
        }
        console.log(`Chached key '${key}' content retrieved.`)
        return JSON.parse(data.data.toString('utf-8'))
      })
      .catch(() => null)
  }

  del(key) {
    this.cache.rm.entry(configs.CacheDir, key).then(() => {
      console.log(`Chached key '${key}' content deleted.`)
    })
  }

  flushByChannel(channel) {
    this.cache.ls(configs.CacheDir).then(data => {
      const keys = Object.keys(data)
      keys.forEach(el => {
        if (data[el].key.match(`^${channel}-.+$`)) {
          this.del(data[el].key)
        }
      })
    })
  }
}

module.exports.Cache = Cache
