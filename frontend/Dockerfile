# Set the base image
FROM node:20

# automatically creates the dir and sets it as the current working dir
WORKDIR /usr/src/app
# this will allow us to run vite and other tools directly
ENV PATH /usr/src/app/node_modules/.bin:$PATH

### inject all environment vars we'll need
### ARG VITE_BACKEND_URL
### expose the variable to the finished cotainer
### ENV VITE_BACKEND_URL=$VITE_BACKEND_URL

# Copy `package.json` and `package-lock.json`
COPY package*.json ./

RUN npm install

# use a more specific COPY, as this will include files like `Dockerfile`, we don't really need inside our containers.
COPY . ./

COPY .env.sample /usr/src/app/.env


# Expose the port Vite runs on
EXPOSE 3000

# Run server and expose it to the world
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "3000"]