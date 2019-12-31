FROM alpine:latest

RUN addgroup -g 82 -S www-data ; \
  adduser -u 82 -D -S -G www-data www-data;

ARG APP_DIR

COPY . $APP_DIR

CMD ["sh", "-c", "chown www-data:www-data $APP_DIR -Rf; chmod 777 $APP_DIR/var/ -Rf"]
