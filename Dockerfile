FROM wordpress:php7.3

RUN apt-get update && apt-get install -y wget zip && apt-get clean

ENV WOOCOMMERCE_VERSION 3.6.4
ENV STOREFRONT_VERSION 2.5.0

RUN wget https://downloads.wordpress.org/plugin/woocommerce.${WOOCOMMERCE_VERSION}.zip &&\
     unzip woocommerce.${WOOCOMMERCE_VERSION}.zip -d /usr/src/wordpress/wp-content/plugins/ \
     && rm woocommerce.${WOOCOMMERCE_VERSION}.zip

RUN wget https://downloads.wordpress.org/theme/storefront.${STOREFRONT_VERSION}.zip &&  \
    unzip storefront.${STOREFRONT_VERSION}.zip -d /usr/src/wordpress/wp-content/themes/ \
    && rm storefront.${STOREFRONT_VERSION}.zip
