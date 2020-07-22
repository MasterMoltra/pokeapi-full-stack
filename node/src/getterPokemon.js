'use strict'

const configs = require('../_configs')

const fs = require('fs')
const axios = require('axios')

class GetterJSON {
  constructor($path) {
    this.path = $path
  }

  getByLocal() {
    try {
      if (!fs.existsSync(configs.LocalRootJson)) {
        throw new Error('Local mode is disabled')
      }

      // Retrieve Pokèmon path from json root
      const pokemonRootFile = fs.readFileSync(configs.LocalRootJson)
      const pokemonPath = JSON.parse(pokemonRootFile).results.filter(
        v => v.name == this.path
      )

      const pokemonJsonFile =
        Object.keys(pokemonPath).length && pokemonPath[0].url
          ? configs.LocalDir + pokemonPath[0].url + 'index.json'
          : null

      if (!pokemonJsonFile) {
        return null
      }

      // Read data
      const pokemonDataFile = fs.readFileSync(pokemonJsonFile)
      const data = JSON.parse(pokemonDataFile)

      return data
    } catch (error) {
      console.log(error)
      throw new Error(error)
    }
  }

  async getByApi() {
    const url = `${configs.ApiUrl}/${this.path}`

    // Defaul request log message
    axios.interceptors.request.use(config => {
      // log a message before any HTTP request is sent
      console.log(`Request was sent to ${url}`)

      return config
    })

    try {
      let response = await axios.get(url, { timeout: 5000 })

      return response.data
    } catch (error) {
      if (error.response) {
        // Request made and server responded
        // console.log(error.response.data)
        console.log('Response', error.response)
        // Return before throw Error
        if (error.response.status === 404) {
          return null
        }
      } else if (error.request) {
        // The request was made but no response was received
        console.log('Request', error.request)
      } else {
        // Something happened in setting up the request that triggered an Error
        console.log('Error', error.message)
      }

      throw new Error(error.message)
    }
    // response = axios
    //     .get(url, { timeout: 5000 })
    //     .then((response) => response.data)
    //     .catch((error) => {
    //         throw new Error(error);
    //     });
  }
}

module.exports = {
  Pokemon: GetterJSON,
}
