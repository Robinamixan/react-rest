FROM nginx:1.14-alpine

RUN rm /etc/nginx/conf.d/*.conf
COPY front.conf /etc/nginx/conf.d/default.conf

# This must be the same location as index.php in the php container.
RUN mkdir -p /var/www/html/public
RUN touch /var/www/html/public/index.php
