#!/bin/bash

export DEST=mike@ai-em.net:/var/www/empathy_site/public_html/manual


cd docs
make html
rsync -zvr build/html/ ${DEST}
