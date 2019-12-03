FROM nginx:1.17.3-alpine

ADD ./nginx/nginx.conf /etc/nginx/conf.d/default.conf