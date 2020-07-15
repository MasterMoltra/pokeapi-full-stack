FROM node:14.5-alpine

WORKDIR /app

COPY package.json .
RUN yarn

COPY . .
CMD ["yarn", "start"]

EXPOSE 3000