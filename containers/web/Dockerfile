FROM nginx:1.20-alpine
SHELL ["/bin/ash", "-oeux", "pipefail", "-c"]

ARG APP_HOST=app

ENV TZ=UTC

RUN apk update && \
  apk add --update --no-cache --virtual=.build-dependencies g++ netcat-openbsd apache2-utils

# nginx config file
COPY ./containers/web/nginx.conf /etc/nginx/nginx.conf
COPY ./containers/web/default.conf /etc/nginx/conf.d/default.conf.template

# envsubst ARGS
RUN envsubst '$$APP_HOST' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# public directory
COPY ./backend/public/ /var/www/public

WORKDIR /var/www
