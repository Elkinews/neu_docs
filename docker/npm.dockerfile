FROM node:16

RUN mkdir /.npm \
    && chmod 0777 /.npm \
    && corepack enable \
    && npm install -g npm
