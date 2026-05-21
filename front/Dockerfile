# syntax=docker/dockerfile:1.7

FROM node:20-alpine AS base
WORKDIR /app

# ============ dev ============
FROM base AS dev
ARG UID=1000
ARG GID=1000
COPY package.json package-lock.json ./
RUN npm ci && chown -R ${UID}:${GID} /app
COPY . .
EXPOSE 5173
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

# ============ build ============
FROM base AS build
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ============ prod ============
FROM nginx:alpine AS prod
COPY --from=build /app/dist /usr/share/nginx/html
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
