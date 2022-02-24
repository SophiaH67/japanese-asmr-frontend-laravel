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

FROM ubuntu/apache2 AS runner
WORKDIR /app
COPY --from=composer-builder /app/ /app/
RUN mkdir resources/views/errors
RUN chmod 0777 -R /app/storage/
ENV WEB_DOCUMENT_ROOT /app/public/

ENV DEBIAN_FRONTEND noninteractive
# Install PHP
RUN apt-get update
RUN apt-get install -y --no-install-recommends \
    php7.0-cli php7.0-common php7.0-json php7.0-opcache php7.0-mysql php7.0-fpm php7.0-curl php7.0-gd php7.0-mbstring php7.0-xml php7.0-zip php7.0-bcmath php7.0-intl php7.0-soap php7.0-xsl php7.0-redis php7.0-gmp php7.0-imagick php7.0-xdebug php7.0-zip php7.0-bz2 php7.0-xmlrpc php7.0-dev php7.0-imap php7.0-pspell php7.0-readline php7.0-tidy php7.0-t1lib php7.0-mcrypt php7.0-curl php7.0-json php7.0-xsl php7.0-intl php7.0-mysql php7.0-opcache php7.0-redis php7.0-xdebug php7.0-xsl php7.0-mbstring php7.0-gd php7.0-mysql php7.0-curl php7.0-zip php7.0-bz2 php7.0-xmlrpc php7.0-dev php7.0-imap php7.0-pspell php7.0-readline php7.0-tidy php7.0-t1lib php7.0-mcrypt php7.0-curl php7.0-json php7.0-xsl php7.0-intl php7.0-mysql php7.0-opcache php7.0-redis php7.0-xdebug php7.0-xsl php7.0-mbstring php7.0-gd php7.0-mysql php7.0-curl php7.0-zip php7.0-bz2 php7.0-xmlrpc php7.0-dev php7.0-imap php7

# Install japanese-asmr pypi package
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
CMD ["bash", "-c", "php artisan config:cache && php artisan route:clear && php artisan migrate --force && apache2-foreground"]
