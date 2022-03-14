build_phpstan_image:
	cd docker && docker build . -f phpstan.Dockerfile -t php-clean-code/phpstan:latest && cd -

phpstan:
	docker run -v ${PWD}:/app --rm php-clean-code/phpstan:latest analyse -c /app/build/config/phpstan.neon

phpunit:
	composer phpunit

test: phpstan
	composer testall

psalm:
	composer psalm

infection:
	composer infection

infection-after-phpunit:
	composer infection-after-phpunit