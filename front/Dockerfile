# --- base ---
FROM node:22-alpine AS base
WORKDIR /app
COPY package*.json ./
RUN npm ci

# --- dev ---
FROM base AS dev
EXPOSE 5173
CMD ["npx", "vite", "--host"]

# --- build ---
FROM base AS build
COPY . .
RUN npm run build

# --- prod ---
FROM nginx:alpine AS prod
COPY --from=build /app/dist /usr/share/nginx/html
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
