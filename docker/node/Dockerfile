ARG NODE_VERSION
FROM node:${NODE_VERSION}

WORKDIR /app/dist

COPY package.json .
RUN npm install

COPY . .
# CMD ["npm", "run", "dev"]
CMD ["npm", "start"]

EXPOSE 3000