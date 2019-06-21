run:
	docker-compose up

pack:
	cd src && zip -r ../LinkitWoocommerce.zip *

clean:
	rm LinkitWoocommerce.zip
