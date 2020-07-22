const defaults = {}

defaults.NodePort = 3000 // NOTICE: docker use 3000 | localhost use 5000

defaults.LogDir = '../var/log/node' // TODO: import node logger

defaults.CacheDir = '../var/cache/node'
defaults.CacheEnabled = true
defaults.CacheExpire = { local: 120, api: 300 } // 2 - 5 minutes

defaults.LocalDir = '../' // project root (parent of api/ directory)
defaults.LocalRootJson = defaults.LocalDir + 'api/v2/pokemon/index.json'
defaults.ApiUrl = 'https://pokeapi.co/api/v2/pokemon'

defaults.AxiosTimeout = 5000 // 5 seconds

module.exports = defaults
