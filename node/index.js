'use strict'

const configs = require('./_configs')

const port = process.env.PORT || configs.NodePort

const app = require('./src/app')

app.listen(port, () => {
  console.log(`Listening on port ${port}`)
})
