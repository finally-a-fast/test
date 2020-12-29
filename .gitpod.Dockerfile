FROM gitpod/workspace-full

ENV EXT_APCU_VERSION=5.1.19

USER root

RUN apt-get update -yqq \
    && apt-get install -yqq php-apcu

USER gitpod
