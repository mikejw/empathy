

.PHONY: all grunt install clean

all: grunt

grunt: install
	./node_modules/grunt-cli/bin/grunt

install: clean
	npm install --bin-links=false

clean:
	rm -rf node_modules

