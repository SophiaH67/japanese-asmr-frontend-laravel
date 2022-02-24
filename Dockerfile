# Install node dependencies
FROM node:16-alpine AS node-builder
WORKDIR /app
COPY . .
RUN npm install
RUN npm run dev
RUN npm run production

# Install composer dependencies
FROM composer:latest AS composer-builder
WORKDIR /app
COPY --from=node-builder /app/ /app/
RUN composer i --optimize-autoloader --no-dev
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan optimize

FROM webdevops/php-apache:ubuntu-16.04 AS runner
WORKDIR /app
COPY --from=composer-builder /app/ /app/
RUN mkdir resources/views/errors
RUN chmod 0777 -R /app/storage/
ENV WEB_DOCUMENT_ROOT /app/public/

# Install japanese-asmr pypi package
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update
RUN apt-get install software-properties-common -y
RUN add-apt-repository ppa:deadsnakes/ppa -y
RUN apt-get update
RUN apt-get install -y python3.10 python3.10-distutils
RUN curl -sS https://bootstrap.pypa.io/get-pip.py | python3.10
RUN python3.10 -m pip install japanese-asmr
# Make sure it install chromium
RUN echo "from requests_html import HTMLSession; session = HTMLSession(); session.get('https://www.google.com/');" | python3.10

# This is stupid, I know this stupid, but I have to do this
# because this framework loads environment variables at
# build time instead of at runtime like ANY properly written software
# or framework. If you have a better solution, please make a PR.
CMD ["bash", "-c", "php artisan config:cache && php artisan route:clear && php artisan migrate --force && /usr/bin/python3 /usr/bin/supervisord -c /opt/docker/etc/supervisor.conf --logfile /dev/null --pidfile /dev/null --user root"]
