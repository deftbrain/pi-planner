# use this self-generated certificate only in dev, IT IS NOT SECURE!


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG NGINX_VERSION=1.17


FROM nginx:${NGINX_VERSION}-alpine

# persistent / runtime deps
RUN apk add --no-cache \
		nss-tools \
	;

WORKDIR /certs

ARG MKCERT_VERSION=1.4.1
ARG COMMON_HOST
ARG CERT_FILE=${COMMON_HOST}.crt
ARG KEY_FILE=${COMMON_HOST}.key
RUN set -eux; \
	wget -O /usr/local/bin/mkcert https://github.com/FiloSottile/mkcert/releases/download/v$MKCERT_VERSION/mkcert-v$MKCERT_VERSION-linux-amd64; \
	chmod +x /usr/local/bin/mkcert; \
	mkcert --cert-file $CERT_FILE --key-file $KEY_FILE ${COMMON_HOST} "api.${COMMON_HOST}" "mercure.${COMMON_HOST}" "admin.${COMMON_HOST}"; \
	# the file must be named server.pem - the default certificate path in webpack-dev-server
	cat $KEY_FILE $CERT_FILE > server.pem; \
	# export the root CA cert, but not the root CA key
	cp "$(mkcert -CAROOT)/rootCA.pem" /certs/localCA.crt

VOLUME /certs
