FROM node:lts-alpine AS builder

ENV PNPM_HOME="/usr/local/bin"

WORKDIR /app

COPY package.json pnpm-lock.yaml ./

COPY ./src ./src

RUN mkdir -p public/css

RUN pnpm install --frozen-lockfile

RUN pnpm run build