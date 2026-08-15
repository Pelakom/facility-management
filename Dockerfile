FROM node:lts-alpine AS fm-builder

WORKDIR /app

COPY package.json pnpm-lock.yaml pnpm-workspace.yaml ./

RUN corepack enable

RUN pnpm install --frozen-lockfile

COPY . .

RUN pnpm run build



FROM php:8.4-fpm-alpine AS fm-php

WORKDIR /var/www/html

RUN apk add --no-cache oniguruma-dev && \
    docker-php-ext-install pdo_mysql mbstring

COPY --from=fm-builder /app/public .

EXPOSE 9000

FROM nginx:alpine AS fm-nginx

COPY nginx.conf /etc/nginx/conf.d/default.conf

COPY --from=fm-builder /app/public /var/www/html

EXPOSE 80