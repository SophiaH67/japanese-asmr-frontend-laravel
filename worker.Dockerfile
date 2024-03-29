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

FROM ubuntu:latest AS runner
WORKDIR /app
COPY --from=composer-builder /app/ /app/
RUN mkdir resources/views/errors
RUN chmod 0777 -R /app/storage/

# Install dependencies
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update
RUN apt-get install -y software-properties-common ffmpeg gconf-service libasound2 libatk1.0-0 libatk-bridge2.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator3* libnss3 lsb-release xdg-utils wget libcairo-gobject2 libxinerama1 libgtk2.0-0 libpangoft2-1.0-0 libthai0 libpixman-1-0 libxcb-render0 libharfbuzz0b libdatrie1 libgraphite2-3 libgbm1 curl gpg gpg-agent dirmngr apache2 libapache2-mod-php
RUN add-apt-repository ppa:deadsnakes/ppa -y
RUN add-apt-repository ppa:ondrej/php -y
RUN apt-get update
RUN apt-get install -y python3.10 python3.10-distutils php8.0-common php8.0-cli php8.0-bz2 php8.0-curl php8.0-intl php8.0-mysql php8.0-readline php8.0-xml php8.0-dev php8.0-pgsql
RUN curl -sS https://bootstrap.pypa.io/get-pip.py | python3.10
RUN python3.10 -m pip install japanese-asmr

# Make sure chromium is installed
RUN echo "from requests_html import HTMLSession; session = HTMLSession(); res = session.get('https://www.google.com/'); res.html.render()" | python3.10

CMD ["bash", "-c", "php artisan storage:link && php artisan config:cache && php artisan route:clear && php artisan migrate --force && php artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=6000"]
