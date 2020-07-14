FROM debian:9

USER root

ARG HOST_DOMAIN

RUN apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y nginx \
    ca-certificates

WORKDIR /var/www

# Replace the HOST_DOMAIN placeholder into NGINX conf files and use it
COPY ./config/nginx.conf  /etc/nginx/my-nginx.conf
RUN chmod 644 /etc/nginx/nginx.conf
RUN sed -i -r "s/\{HOST_DOMAIN\}/${HOST_DOMAIN}/g" /etc/nginx/my-nginx.conf

CMD ["/usr/sbin/nginx", "-c", "/etc/nginx/my-nginx.conf"]

# For debugging, rather than having the container exit immediately,
# being able to switch the command to be just 'follow that file' allows
# you to bash into the container to inspect the state after nginx has crashed.
# CMD ["tail", "-f", "/var/www/readme.MD"]