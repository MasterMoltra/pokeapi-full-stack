ARG NODE_VERSION
FROM node:${NODE_VERSION}

WORKDIR /app

COPY package.json .
RUN npm install

COPY . .
CMD ["npm", "start"]

EXPOSE 3000